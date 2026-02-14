<?php
session_start();
include('includes/config.php');

// Must be logged in
if(empty($_SESSION['login'])) {
    header("Location: includes/login.php");
    exit();
}

// Must have a vehicle ID
if(!isset($_GET['vid'])) {
    header("Location: search.php");
    exit();
}

$vid = $_GET['vid'];
$email = $_SESSION['login'];

// Fetch vehicle
$sql = "SELECT tblvehicles.*, tblbrands.BrandName FROM tblvehicles 
        JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand 
        WHERE tblvehicles.id = :vid";
$query = $conn->prepare($sql);
$query->bindParam(':vid', $vid, PDO::PARAM_INT);
$query->execute();
$vehicle = $query->fetch(PDO::FETCH_ASSOC);

if(!$vehicle) {
    header("Location: search.php");
    exit();
}

// Fetch user info
$usql = "SELECT * FROM tblusers WHERE EmailId = :email";
$uquery = $conn->prepare($usql);
$uquery->bindParam(':email', $email, PDO::PARAM_STR);
$uquery->execute();
$user = $uquery->fetch(PDO::FETCH_ASSOC);

$error = '';

// Handle form submit — store booking in session and go to payment
if(isset($_POST['proceed_payment'])) {
    $pickup_date  = $_POST['pickup_date'] ?? '';
    $return_date  = $_POST['return_date'] ?? '';
    $pickup_location = trim($_POST['pickup_location'] ?? '');

    if(empty($pickup_date) || empty($return_date) || empty($pickup_location)) {
        $error = 'All fields are required.';
    } else {
        $d1 = new DateTime($pickup_date);
        $d2 = new DateTime($return_date);
        if($d2 <= $d1) {
            $error = 'Return date must be after pickup date.';
        } else {
            $days = $d1->diff($d2)->days;
            $total = $days * $vehicle['PricePerDay'];

            // Store booking details in session for payment page
            $_SESSION['booking'] = [
                'vid'             => $vid,
                'vehicle_title'   => $vehicle['VehiclesTitle'],
                'brand'           => $vehicle['BrandName'],
                'price_per_day'   => $vehicle['PricePerDay'],
                'pickup_date'     => $pickup_date,
                'return_date'     => $return_date,
                'pickup_location' => $pickup_location,
                'days'            => $days,
                'total'           => $total,
                'user_email'      => $email,
                'user_name'       => $user['FullName'] ?? '',
            ];

            header("Location: payment.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlentities($vehicle['VehiclesTitle']); ?> - Car Rental</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f8f9fa; color:#333; }

        .page-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 40px 20px; text-align: center;
        }
        .page-banner h1 { font-size:32px; margin-bottom:8px; }
        .page-banner p  { font-size:15px; opacity:0.9; }

        .container { max-width:900px; margin:40px auto; padding:0 20px; }

        .back-link {
            display:inline-block; margin-bottom:20px; color:#667eea;
            text-decoration:none; font-weight:600;
        }
        .back-link:hover { color:#764ba2; }

        .grid { display:grid; grid-template-columns:1fr 1.4fr; gap:30px; align-items:start; }
        @media(max-width:700px){ .grid { grid-template-columns:1fr; } }

        /* Vehicle summary card */
        .vehicle-card {
            background:white; border-radius:12px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1); overflow:hidden;
        }
        .vehicle-card img {
            width:100%; height:200px; object-fit:cover; display:block;
        }
        .no-img {
            width:100%; height:200px; display:flex; align-items:center;
            justify-content:center; background:linear-gradient(135deg,#e0e0e0,#f5f5f5);
            font-size:50px;
        }
        .vehicle-info { padding:20px; }
        .vehicle-info h3 { font-size:20px; margin-bottom:5px; }
        .vehicle-info .brand { color:#667eea; font-size:13px; font-weight:600; margin-bottom:15px; }
        .spec-row { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
        .spec-row:last-child { border-bottom:none; }
        .spec-row span:first-child { color:#666; }
        .spec-row span:last-child  { font-weight:600; }

        .price-badge {
            margin-top:15px; background:linear-gradient(135deg,#667eea,#764ba2);
            color:white; padding:12px 15px; border-radius:8px;
            display:flex; justify-content:space-between; align-items:center;
        }
        .price-badge .amount { font-size:22px; font-weight:bold; }
        .price-badge .label  { font-size:13px; opacity:0.85; }

        /* Booking form card */
        .form-card {
            background:white; border-radius:12px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1); overflow:hidden;
        }
        .form-card-header {
            background:linear-gradient(135deg,#667eea,#764ba2);
            color:white; padding:20px;
        }
        .form-card-header h2 { font-size:20px; }
        .form-card-body { padding:25px; }

        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:8px; font-weight:600; font-size:14px; }
        .form-group input, .form-group select {
            width:100%; padding:12px 15px; border:2px solid #e0e0e0;
            border-radius:6px; font-size:14px; font-family:inherit; transition:border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline:none; border-color:#667eea;
            box-shadow:0 0 0 3px rgba(102,126,234,0.1);
        }

        .total-box {
            background:#f8f9fa; border-radius:8px; padding:15px;
            margin-bottom:20px; border-left:4px solid #667eea;
        }
        .total-box .total-row { display:flex; justify-content:space-between; padding:5px 0; font-size:14px; }
        .total-box .total-row.grand { font-size:18px; font-weight:bold; color:#667eea; border-top:1px solid #ddd; margin-top:8px; padding-top:10px; }

        .btn-book {
            width:100%; padding:14px; background:linear-gradient(135deg,#ff6b6b,#ff5252);
            color:white; border:none; border-radius:8px; font-size:16px;
            font-weight:bold; cursor:pointer; transition:all 0.3s;
        }
        .btn-book:hover { transform:translateY(-2px); box-shadow:0 5px 20px rgba(255,107,107,0.4); }

        .alert-error {
            background:#fee; color:#c33; border:1px solid #fcc;
            padding:12px 15px; border-radius:6px; margin-bottom:20px; font-size:14px;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="page-banner">
    <h1>🚗 Book Your Car</h1>
    <p>Complete your booking details below</p>
</div>

<div class="container">
    <a href="vehicles_details.php?vid=<?php echo $vid; ?>" class="back-link">← Back to Vehicle</a>

    <div class="grid">

        <!-- Vehicle Summary -->
        <div class="vehicle-card">
            <?php if(!empty($vehicle['Vimage1'])): ?>
                <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle['Vimage1']); ?>"
                     alt="<?php echo htmlentities($vehicle['VehiclesTitle']); ?>">
            <?php else: ?>
                <div class="no-img">🚗</div>
            <?php endif; ?>

            <div class="vehicle-info">
                <h3><?php echo htmlentities($vehicle['VehiclesTitle']); ?></h3>
                <div class="brand"><?php echo htmlentities($vehicle['BrandName']); ?></div>

                <div class="spec-row"><span>⛽ Fuel</span><span><?php echo htmlentities($vehicle['FuelType']); ?></span></div>
                <div class="spec-row"><span>📅 Year</span><span><?php echo htmlentities($vehicle['ModelYear']); ?></span></div>
                <div class="spec-row"><span>👥 Seats</span><span><?php echo htmlentities($vehicle['SeatingCapacity']); ?></span></div>
                <div class="spec-row"><span>⚙️ Transmission</span><span><?php echo htmlentities($vehicle['Transmission'] ?? 'N/A'); ?></span></div>

                <div class="price-badge">
                    <span class="label">Price Per Day</span>
                    <span class="amount">$<?php echo htmlentities($vehicle['PricePerDay']); ?></span>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="form-card">
            <div class="form-card-header">
                <h2>📋 Booking Details</h2>
            </div>
            <div class="form-card-body">

                <?php if(!empty($error)): ?>
                    <div class="alert-error">❌ <?php echo htmlentities($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>👤 Full Name</label>
                        <input type="text" value="<?php echo htmlentities($user['FullName'] ?? ''); ?>" readonly
                               style="background:#f0f0f0; cursor:not-allowed;">
                    </div>

                    <div class="form-group">
                        <label>📧 Email</label>
                        <input type="email" value="<?php echo htmlentities($email); ?>" readonly
                               style="background:#f0f0f0; cursor:not-allowed;">
                    </div>

                    <div class="form-group">
                        <label for="pickup_location">📍 Pickup Location</label>
                        <input type="text" id="pickup_location" name="pickup_location"
                               placeholder="Enter pickup location" required>
                    </div>

                    <div class="form-group">
                        <label for="pickup_date">📅 Pickup Date</label>
                        <input type="date" id="pickup_date" name="pickup_date"
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="return_date">📅 Return Date</label>
                        <input type="date" id="return_date" name="return_date"
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    </div>

                    <!-- Live total calculation -->
                    <div class="total-box" id="totalBox" style="display:none;">
                        <div class="total-row"><span>Days</span><span id="totalDays">0</span></div>
                        <div class="total-row"><span>Price per day</span><span>$<?php echo $vehicle['PricePerDay']; ?></span></div>
                        <div class="total-row grand"><span>Total</span><span id="totalAmount">$0</span></div>
                    </div>

                    <button type="submit" name="proceed_payment" class="btn-book">
                        💳 Proceed to Payment
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
const pricePerDay = <?php echo (float)$vehicle['PricePerDay']; ?>;
const pickupInput = document.getElementById('pickup_date');
const returnInput = document.getElementById('return_date');
const totalBox    = document.getElementById('totalBox');
const totalDays   = document.getElementById('totalDays');
const totalAmount = document.getElementById('totalAmount');

function calcTotal() {
    const d1 = new Date(pickupInput.value);
    const d2 = new Date(returnInput.value);
    if(pickupInput.value && returnInput.value && d2 > d1) {
        const days = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
        totalDays.textContent   = days + ' day' + (days > 1 ? 's' : '');
        totalAmount.textContent = '$' + (days * pricePerDay).toFixed(2);
        totalBox.style.display  = 'block';

        // Make sure return is after pickup
        returnInput.min = pickupInput.value;
    } else {
        totalBox.style.display = 'none';
    }
}

pickupInput.addEventListener('change', calcTotal);
returnInput.addEventListener('change', calcTotal);
</script>

<?php include('includes/footer.php'); ?>
</body>
</html>