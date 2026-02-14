<?php
session_start();
include('config.php');

$error = '';
$success = '';

// Handle registration
if (isset($_POST['register'])) {
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    
    // Validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Full Name, Email, and Password are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        // Check if email already exists
        $check_sql = "SELECT * FROM tblusers WHERE EmailId = :email";
        $check_query = $conn->prepare($check_sql);
        $check_query->bindParam(':email', $email, PDO::PARAM_STR);
        $check_query->execute();
        
        if ($check_query->rowCount() > 0) {
            $error = 'Email already registered. Please use a different email or login';
        } else {
            // Hash password
            $hashed_password = md5($password);
            
            // Insert user
            $insert_sql = "INSERT INTO tblusers (FullName, EmailId, Password, ContactNo, Address, RegDate) 
                          VALUES (:fullname, :email, :password, :contact, :address, NOW())";
            $insert_query = $conn->prepare($insert_sql);
            $insert_query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
            $insert_query->bindParam(':email', $email, PDO::PARAM_STR);
            $insert_query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $insert_query->bindParam(':contact', $contact, PDO::PARAM_STR);
            $insert_query->bindParam(':address', $address, PDO::PARAM_STR);
            
            if ($insert_query->execute()) {
                $_SESSION['login'] = $email;
                $success = 'Registration successful! Redirecting to home page...';
                header("refresh:2;url=../index.php");
            } else {
                $error = 'Registration failed. Please try again';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Car Rental</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 520px;
            padding: 40px;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        .register-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        
        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .register-button:active {
            transform: translateY(0);
        }
        
        .terms {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .terms input[type="checkbox"] {
            margin-right: 8px;
            margin-top: 3px;
            cursor: pointer;
        }
        
        .terms label {
            margin: 0;
            font-weight: normal;
            color: #666;
        }
        
        .terms a {
            color: #667eea;
            text-decoration: none;
        }
        
        .terms a:hover {
            text-decoration: underline;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
            color: #764ba2;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        
        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            padding: 8px;
            border-radius: 4px;
            background-color: #f0f0f0;
            color: #666;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-header">
        <h1>🚗 Join Us</h1>
        <p>Create your Car Rental account and start booking</p>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <strong>Error:</strong> <?php echo htmlentities($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>Success:</strong> <?php echo htmlentities($success); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="fullname">Full Name *</label>
            <input 
                type="text" 
                id="fullname" 
                name="fullname" 
                placeholder="Enter your full name" 
                required
            />
        </div>
        
        <div class="form-group">
            <label for="email">Email Address *</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="Enter your email" 
                required
                autocomplete="email"
            />
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="password">Password *</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="At least 6 characters" 
                    required
                    autocomplete="new-password"
                    minlength="6"
                />
                <div class="password-strength">
                    Password must be at least 6 characters long
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Confirm your password" 
                    required
                    autocomplete="new-password"
                    minlength="6"
                />
            </div>
        </div>
        
        <div class="form-group">
            <label for="contact">Phone Number</label>
            <input 
                type="tel" 
                id="contact" 
                name="contact" 
                placeholder="Enter your phone number"
            />
        </div>
        
        <div class="form-group">
            <label for="address">Address</label>
            <textarea 
                id="address" 
                name="address" 
                placeholder="Enter your full address"
            ></textarea>
        </div>
        
        <div class="terms">
            <input type="checkbox" id="terms" name="terms" required />
            <label for="terms">I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></label>
        </div>
        
        <button type="submit" name="register" class="register-button">Create Account</button>
    </form>
    
    <div class="login-link">
        Already have an account? <a href="login.php">Sign in here</a>
    </div>
</div>

<script>
    // Optional: Real-time password match validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    confirmPassword.addEventListener('input', function() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.style.borderColor = '#ff6b6b';
        } else if (confirmPassword.value !== '') {
            confirmPassword.style.borderColor = '#67c23a';
        }
    });
</script>

</body>
</html>
