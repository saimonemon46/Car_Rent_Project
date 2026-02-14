<?php
session_start();
include('includes/config.php');

// Must be logged in
if(empty($_SESSION['login'])) {
    header("Location: includes/login.php");
    exit();
}

// Must have booking session
if(empty($_SESSION['booking'])) {
    header("Location: search.php");
    exit();
}

// Load Stripe keys from .env
function loadEnv($path) {
    if(!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line) {
        if(strpos(trim($line), '#') === 0) continue;
        if(strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}
loadEnv(__DIR__ . '/.env');

$stripe_publishable = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
$stripe_secret      = $_ENV['STRIPE_SECRET_KEY'] ?? '';

$booking = $_SESSION['booking'];
$error   = '';

// Handle Stripe payment
if(isset($_POST['stripeToken'])) {
    require_once 'vendor/autoload.php'; // Stripe PHP SDK

    \Stripe\Stripe::setApiKey($stripe_secret);

    $amount_cents = (int)($booking['total'] * 100); // Stripe uses cents

    try {
        $charge = \Stripe\Charge::create([
            'amount'      => $amount_cents,
            'currency'    => 'usd',
            'source'      => $_POST['stripeToken'],
            'description' => 'Car Rental: ' . $booking['vehicle_title'] . ' (' . $booking['days'] . ' days)',
            'receipt_email' => $booking['user_email'],
        ]);

        if($charge->status === 'succeeded') {
            // Save booking to database
            $bsql = "INSERT INTO tblbooking 
                     (userEmail, VehicleId, PickupDate, ReturnDate, PickupLocation, TotalDays, TotalAmount, PaymentStatus, TransactionId, BookingDate)
                     VALUES (:email, :vid, :pickup, :return, :location, :days, :total, 'Paid', :txn, NOW())";
            $bquery = $conn->prepare($bsql);
            $bquery->bindParam(':email',    $booking['user_email']);
            $bquery->bindParam(':vid',      $booking['vid']);
            $bquery->bindParam(':pickup',   $booking['pickup_date']);
            $bquery->bindParam(':return',   $booking['return_date']);
            $bquery->bindParam(':location', $booking['pickup_location']);
            $bquery->bindParam(':days',     $booking['days']);
            $bquery->bindParam(':total',    $booking['total']);
            $bquery->bindParam(':txn',      $charge->id);
            $bquery->execute();

            // Save transaction ID to session for success page
            $_SESSION['payment_success'] = [
                'transaction_id'  => $charge->id,
                'amount'          => $booking['total'],
                'vehicle'         => $booking['vehicle_title'],
                'pickup_date'     => $booking['pickup_date'],
                'return_date'     => $booking['return_date'],
                'days'            => $booking['days'],
            ];

            // Clear booking session
            unset($_SESSION['booking']);

            header("Location: payment_success.php");
            exit();
        }

    } catch(\Stripe\Exception\CardException $e) {
        $error = 'Card error: ' . $e->getMessage();
    } catch(\Stripe\Exception\ApiErrorException $e) {
        $error = 'Payment failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Car Rental</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f8f9fa; color:#333; }

        .page-banner {
            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            color:white; padding:40px 20px; text-align:center;
        }
        .page-banner h1 { font-size:32px; margin-bottom:8px; }
        .page-banner p  { font-size:15px; opacity:0.9; }

        .container { max-width:800px; margin:40px auto; padding:0 20px; }

        .back-link {
            display:inline-block; margin-bottom:20px;
            color:#667eea; text-decoration:none; font-weight:600;
        }

        .grid { display:grid; grid-template-columns:1fr 1.2fr; gap:25px; align-items:start; }
        @media(max-width:650px){ .grid { grid-template-columns:1fr; } }

        /* Order summary */
        .summary-card {
            background:white; border-radius:12px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1); overflow:hidden;
        }
        .card-header {
            background:linear-gradient(135deg,#667eea,#764ba2);
            color:white; padding:18px 20px; font-size:17px; font-weight:bold;
        }
        .card-body { padding:20px; }
        .summary-row {
            display:flex; justify-content:space-between;
            padding:10px 0; border-bottom:1px solid #f0f0f0; font-size:14px;
        }
        .summary-row:last-child { border-bottom:none; }
        .summary-row span:first-child { color:#666; }
        .summary-row span:last-child  { font-weight:600; }
        .total-row {
            display:flex; justify-content:space-between; align-items:center;
            margin-top:15px; padding:15px; background:linear-gradient(135deg,#667eea,#764ba2);
            border-radius:8px; color:white;
        }
        .total-row .label { font-size:14px; opacity:0.9; }
        .total-row .amount { font-size:26px; font-weight:bold; }

        /* Payment form */
        .payment-card {
            background:white; border-radius:12px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1); overflow:hidden;
        }
        .payment-card .card-body { padding:25px; }

        .test-notice {
            background:#fff8e1; border:1px solid #ffcc02; border-radius:8px;
            padding:12px 15px; margin-bottom:20px; font-size:13px; color:#856404;
        }
        .test-notice strong { display:block; margin-bottom:4px; }

        .form-group { margin-bottom:18px; }
        .form-group label { display:block; margin-bottom:8px; font-weight:600; font-size:14px; }

        /* Stripe Element containers */
        .stripe-element {
            padding:12px 15px; border:2px solid #e0e0e0;
            border-radius:6px; background:white; transition:border-color 0.3s;
        }
        .stripe-element.StripeElement--focus { border-color:#667eea; }
        .stripe-element.StripeElement--invalid { border-color:#ff6b6b; }

        #card-errors {
            color:#c33; font-size:13px; margin-top:8px; min-height:18px;
        }

        .btn-pay {
            width:100%; padding:14px; border:none; border-radius:8px;
            background:linear-gradient(135deg,#ff6b6b,#ff5252);
            color:white; font-size:16px; font-weight:bold;
            cursor:pointer; transition:all 0.3s; margin-top:10px;
        }
        .btn-pay:hover { transform:translateY(-2px); box-shadow:0 5px 20px rgba(255,107,107,0.4); }
        .btn-pay:disabled { opacity:0.6; cursor:not-allowed; transform:none; }

        .secure-badge {
            text-align:center; margin-top:15px; font-size:12px; color:#999;
        }

        .alert-error {
            background:#fee; color:#c33; border:1px solid #fcc;
            padding:12px 15px; border-radius:6px; margin-bottom:20px; font-size:14px;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="page-banner">
    <h1>💳 Secure Payment</h1>
    <p>Complete your booking with a secure Stripe payment</p>
</div>

<div class="container">
    <a href="booking.php?vid=<?php echo htmlentities($booking['vid']); ?>" class="back-link">← Back to Booking</a>

    <div class="grid">

        <!-- Order Summary -->
        <div class="summary-card">
            <div class="card-header">🧾 Order Summary</div>
            <div class="card-body">
                <div class="summary-row">
                    <span>🚗 Vehicle</span>
                    <span><?php echo htmlentities($booking['vehicle_title']); ?></span>
                </div>
                <div class="summary-row">
                    <span>🏷️ Brand</span>
                    <span><?php echo htmlentities($booking['brand']); ?></span>
                </div>
                <div class="summary-row">
                    <span>📍 Pickup</span>
                    <span><?php echo htmlentities($booking['pickup_location']); ?></span>
                </div>
                <div class="summary-row">
                    <span>📅 From</span>
                    <span><?php echo date('M j, Y', strtotime($booking['pickup_date'])); ?></span>
                </div>
                <div class="summary-row">
                    <span>📅 To</span>
                    <span><?php echo date('M j, Y', strtotime($booking['return_date'])); ?></span>
                </div>
                <div class="summary-row">
                    <span>⏱️ Duration</span>
                    <span><?php echo $booking['days']; ?> day<?php echo $booking['days'] > 1 ? 's' : ''; ?></span>
                </div>
                <div class="summary-row">
                    <span>💰 Rate</span>
                    <span>$<?php echo number_format($booking['price_per_day'], 2); ?>/day</span>
                </div>

                <div class="total-row">
                    <span class="label">Total Due</span>
                    <span class="amount">$<?php echo number_format($booking['total'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="payment-card">
            <div class="card-header">💳 Card Details</div>
            <div class="card-body">

                <?php if(!empty($error)): ?>
                    <div class="alert-error">❌ <?php echo htmlentities($error); ?></div>
                <?php endif; ?>
<!-- 
                <div class="test-notice">
                    <strong>🧪 Test Mode</strong>
                    Use card number <strong>4242 4242 4242 4242</strong>, any future expiry, any 3-digit CVC.
                </div> -->

                <form id="payment-form" method="POST" action="">
                    <div class="form-group">
                        <label>Cardholder Name</label>
                        <input type="text" id="card-name" placeholder="John Doe"
                               style="width:100%;padding:12px 15px;border:2px solid #e0e0e0;border-radius:6px;font-size:14px;font-family:inherit;">
                    </div>

                    <div class="form-group">
                        <label>Card Number</label>
                        <div id="card-number" class="stripe-element"></div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <div id="card-expiry" class="stripe-element"></div>
                        </div>
                        <div class="form-group">
                            <label>CVC</label>
                            <div id="card-cvc" class="stripe-element"></div>
                        </div>
                    </div>

                    <div id="card-errors"></div>

                    <button type="submit" id="pay-btn" class="btn-pay">
                        🔒 Pay $<?php echo number_format($booking['total'], 2); ?>
                    </button>

                    <div class="secure-badge">
                        🔒 Payments secured by Stripe. We never store your card details.
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
const stripe = Stripe('<?php echo $stripe_publishable; ?>');
const elements = stripe.elements();

const style = {
    base: {
        color: '#333',
        fontSize: '15px',
        fontFamily: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
        '::placeholder': { color: '#aaa' }
    },
    invalid: { color: '#ff6b6b' }
};

const cardNumber = elements.create('cardNumber', { style });
const cardExpiry = elements.create('cardExpiry', { style });
const cardCvc    = elements.create('cardCvc',    { style });

cardNumber.mount('#card-number');
cardExpiry.mount('#card-expiry');
cardCvc.mount('#card-cvc');

// Show live card errors
cardNumber.on('change', ({ error }) => {
    document.getElementById('card-errors').textContent = error ? error.message : '';
});

// Handle form submit
const form   = document.getElementById('payment-form');
const payBtn = document.getElementById('pay-btn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    payBtn.disabled = true;
    payBtn.textContent = '⏳ Processing...';

    const { token, error } = await stripe.createToken(cardNumber, {
        name: document.getElementById('card-name').value
    });

    if(error) {
        document.getElementById('card-errors').textContent = error.message;
        payBtn.disabled = false;
        payBtn.textContent = '🔒 Pay $<?php echo number_format($booking['total'], 2); ?>';
    } else {
        // Inject token and submit
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'stripeToken';
        hidden.value = token.id;
        form.appendChild(hidden);
        form.submit();
    }
});
</script>

<?php include('includes/footer.php'); ?>
</body>
</html>