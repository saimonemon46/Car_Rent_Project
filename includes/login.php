<?php
session_start();
include('config.php');

$error = '';
$success = '';

// Handle login
if (isset($_POST['login'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        // Check in users table
        $sql = "SELECT * FROM tblusers WHERE EmailId = :email";
        $query = $conn->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($user && md5($password) == $user['Password']) {
            $_SESSION['login'] = $email;
            $success = 'Login successful! Redirecting...';
            header("refresh:2;url=../index.php");
        } else {
            // Check in admin table
            $sql = "SELECT * FROM admin WHERE UserName = :email";
            $query = $conn->prepare($sql);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $admin = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && md5($password) == $admin['Password']) {
                $_SESSION['login'] = $email;
                $_SESSION['admin'] = true;
                $success = 'Admin login successful! Redirecting...';
                header("refresh:2;url=../admin/index.php");
            } else {
                $error = 'Invalid email or password';
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
    <title>Login - Car Rental</title>
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
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
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
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .login-button {
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
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .remember-forgot label {
            display: flex;
            align-items: center;
            margin: 0;
            font-weight: normal;
            color: #666;
        }
        
        .remember-forgot input[type="checkbox"] {
            margin-right: 6px;
            cursor: pointer;
        }
        
        .remember-forgot a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .remember-forgot a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
        
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        
        .signup-link a:hover {
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
        
        .divider {
            position: relative;
            margin: 25px 0;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            position: relative;
            background: white;
            padding: 0 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h1>🚗 Welcome Back</h1>
        <p>Sign in to your Car Rental account</p>
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
            <label for="email">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="Enter your email" 
                required
                autocomplete="email"
            />
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter your password" 
                required
                autocomplete="current-password"
            />
        </div>
        
        <div class="remember-forgot">
            <label>
                <input type="checkbox" name="remember" />
                Remember me
            </label>
            <a href="#">Forgot password?</a>
        </div>
        
        <button type="submit" name="login" class="login-button">Sign In</button>
    </form>
    
    <div class="divider">
        <span>OR</span>
    </div>
    
    <div class="signup-link">
        Don't have an account? <a href="register.php">Create one now</a>
    </div>
</div>

</body>
</html>