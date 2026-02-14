<?php
session_start();
include('includes/config.php');

// Check if user is logged in
if (empty($_SESSION['login'])) {
    header("Location: includes/login.php");
    exit();
}

// Check if logged in user is an admin (redirect to admin panel instead)
if (!empty($_SESSION['admin'])) {
    header("Location: admin/index.php");
    exit();
}

$email = $_SESSION['login'];
$error = '';
$success = '';

// Fetch user data from tblusers table
$sql = "SELECT * FROM tblusers WHERE EmailId = :email";
$query = $conn->prepare($sql);
$query->bindParam(':email', $email, PDO::PARAM_STR);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php");
    exit();
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    
    if (empty($fullname)) {
        $error = 'Full Name is required';
    } else {
        $update_sql = "UPDATE tblusers SET FullName = :fullname, ContactNo = :phone, Address = :address WHERE EmailId = :email";
        $update_query = $conn->prepare($update_sql);
        $update_query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $update_query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $update_query->bindParam(':address', $address, PDO::PARAM_STR);
        $update_query->bindParam(':email', $email, PDO::PARAM_STR);
        
        if ($update_query->execute()) {
            $success = 'Profile updated successfully!';
            // Refresh user data
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to update profile';
        }
    }
}

// Get booking count
$booking_sql = "SELECT COUNT(*) as count FROM tblbooking WHERE userEmail = :email";
$booking_query = $conn->prepare($booking_sql);
$booking_query->bindParam(':email', $email, PDO::PARAM_STR);
$booking_query->execute();
$booking_stats = $booking_query->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Car Rental</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .profile-header h1 {
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .profile-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-header .user-name {
            font-size: 20px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .profile-header .user-email {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 600px) {
            .info-row {
                grid-template-columns: 1fr;
            }
        }
        
        .info-group {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #667eea;
        }
        
        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
            word-break: break-word;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background-color: #e0e0e0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background-color: #d0d0d0;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background-color: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container">
    <a href="index.php" class="back-link">← Back to Home</a>
    
    <div class="profile-header">
        <h1>👤 My Profile</h1>
        <div class="user-name"><?php echo htmlentities($user['FullName'] ?? 'User'); ?></div>
        <div class="user-email"><?php echo htmlentities($email); ?></div>
    </div>
    
    <!-- Stats -->
    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo htmlentities($booking_stats['count']); ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">⭐</div>
            <div class="stat-label">Member Since</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">✓</div>
            <div class="stat-label">Verified Account</div>
        </div>
    </div>
    
    <!-- Display Information -->
    <div class="profile-card">
        <div class="card-header">📋 Personal Information</div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlentities($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlentities($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input 
                        type="text" 
                        id="fullname" 
                        name="fullname" 
                        value="<?php echo htmlentities($user['FullName']); ?>"
                        placeholder="Enter your full name"
                    />
                </div>
                
                <div class="info-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            value="<?php echo htmlentities($email); ?>"
                            placeholder="Your email"
                            readonly
                            style="background-color: #f0f0f0; cursor: not-allowed;"
                        />
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="<?php echo htmlentities($user['ContactNo'] ?? ''); ?>"
                            placeholder="Enter your phone number"
                        />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea 
                        id="address" 
                        name="address" 
                        placeholder="Enter your full address"
                    ><?php echo htmlentities($user['Address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Member Since</label>
                    <div class="info-group">
                        <div class="info-value">
                            <?php 
                            if (!empty($user['RegDate'])) {
                                echo date('F j, Y', strtotime($user['RegDate']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="update_profile" class="btn btn-primary">💾 Save Changes</button>
                    <a href="change_password.php" class="btn btn-secondary">🔐 Change Password</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="profile-card">
        <div class="card-header">🔗 Quick Links</div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <a href="my_booking.php" style="padding: 20px; text-align: center; text-decoration: none; border-radius: 8px; background-color: #f0f0f0; transition: 0.3s; color: #333; font-weight: 600;" onmouseover="this.style.backgroundColor='#e8e8e8'" onmouseout="this.style.backgroundColor='#f0f0f0'">
                    📅 My Bookings
                </a>
                <a href="search.php" style="padding: 20px; text-align: center; text-decoration: none; border-radius: 8px; background-color: #f0f0f0; transition: 0.3s; color: #333; font-weight: 600;" onmouseover="this.style.backgroundColor='#e8e8e8'" onmouseout="this.style.backgroundColor='#f0f0f0'">
                    🔍 Search Cars
                </a>
                <a href="contact_us.php" style="padding: 20px; text-align: center; text-decoration: none; border-radius: 8px; background-color: #f0f0f0; transition: 0.3s; color: #333; font-weight: 600;" onmouseover="this.style.backgroundColor='#e8e8e8'" onmouseout="this.style.backgroundColor='#f0f0f0'">
                    📞 Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
