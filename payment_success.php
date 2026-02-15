<?php
session_start();
include('includes/config.php');

if(empty($_SESSION['login'])) {
    header("Location: includes/login.php");
    exit();
}

if(empty($_SESSION['payment_success'])) {
    header("Location: index.php");
    exit();
}

$success = $_SESSION['payment_success'];
unset($_SESSION['payment_success']); // clear after reading
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Car Rental</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f8f9fa; color:#333; }

        .container { max-width:600px; margin:60px auto; padding:0 20px; text-align:center; }

        .success-icon { font-size:80px; margin-bottom:20px; animation:pop 0.5s ease; }
        @keyframes pop {
            0%   { transform:scale(0); }
            80%  { transform:scale(1.1); }
            100% { transform:scale(1); }
        }

        .success-card {
            background:white; border-radius:16px;
            box-shadow:0 8px 30px rgba(0,0,0,0.12); overflow:hidden;
        }

        .success-header {
            background:linear-gradient(135deg,#43c678,#2ecc71);
            color:white; padding:35px 30px;
        }
        .success-header h1 { font-size:28px; margin-bottom:8px; }
        .success-header p  { font-size:15px; opacity:0.9; }

        .success-body { padding:30px; }

        .detail-row {
            display:flex; justify-content:space-between; align-items:center;
            padding:12px 0; border-bottom:1px solid #f0f0f0; font-size:14px;
        }
        .detail-row:last-of-type { border-bottom:none; }
        .detail-row span:first-child { color:#666; }
        .detail-row span:last-child  { font-weight:600; color:#333; }

        .txn-box {
            background:#f8f9fa; border-radius:8px; padding:12px 15px;
            margin:20px 0; font-size:12px; color:#666; word-break:break-all;
            border-left:4px solid #667eea;
        }
        .txn-box strong { display:block; margin-bottom:4px; color:#333; }

        .total-highlight {
            background:linear-gradient(135deg,#667eea,#764ba2);
            color:white; border-radius:8px; padding:15px 20px;
            display:flex; justify-content:space-between; align-items:center;
            margin:20px 0;
        }
        .total-highlight .label  { font-size:14px; opacity:0.9; }
        .total-highlight .amount { font-size:26px; font-weight:bold; }

        .btn-group { display:flex; gap:12px; margin-top:25px; }
        .btn {
            flex:1; padding:13px; border:none; border-radius:8px;
            font-size:14px; font-weight:bold; cursor:pointer;
            text-decoration:none; text-align:center; transition:all 0.3s;
        }
        .btn-primary {
            background:linear-gradient(135deg,#667eea,#764ba2); color:white;
        }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 5px 15px rgba(102,126,234,0.4); }
        .btn-secondary { background:#f0f0f0; color:#333; }
        .btn-secondary:hover { background:#e0e0e0; }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container">
    <div class="success-icon">✅</div>

    <div class="success-card">
        <div class="success-header">
            <h1>Booking Confirmed!</h1>
            <p>Your payment was successful and your car is booked.</p>
        </div>

        <div class="success-body">
            <div class="detail-row">
                <span>🚗 Vehicle</span>
                <span><?php echo htmlentities($success['vehicle']); ?></span>
            </div>
            <div class="detail-row">
                <span>📅 Pickup Date</span>
                <span><?php echo date('M j, Y', strtotime($success['pickup_date'])); ?></span>
            </div>
            <div class="detail-row">
                <span>📅 Return Date</span>
                <span><?php echo date('M j, Y', strtotime($success['return_date'])); ?></span>
            </div>
            <div class="detail-row">
                <span>⏱️ Duration</span>
                <span><?php echo $success['days']; ?> day<?php echo $success['days'] > 1 ? 's' : ''; ?></span>
            </div>

            <div class="total-highlight">
                <span class="label">Amount Paid</span>
                <span class="amount">$<?php echo number_format($success['amount'], 2); ?></span>
            </div>

            <div class="txn-box">
                <strong>Transaction ID</strong>
                <?php echo htmlentities($success['transaction_id']); ?>
            </div>

            <div class="btn-group">
                <a href="my_booking.php" class="btn btn-primary">📅 View My Bookings</a>
                <a href="index.php" class="btn btn-secondary">🏠 Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>