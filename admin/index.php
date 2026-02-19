<?php
session_start();
include('includes/config.php');

$error = '';

// Redirect if already logged in
if (isset($_SESSION['admin'])) {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $sql = "SELECT UserName, Password FROM admin WHERE UserName = :username";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() === 1) {
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['Password'])) {
                $_SESSION['admin'] = $row['UserName'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #2c3e50; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { background: white; padding: 30px; border-radius: 8px; width: 350px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        h2 { margin-bottom: 5px; color: #2c3e50; }
        .subtitle { color: #888; font-size: 13px; margin-bottom: 20px; }
        label { font-size: 14px; color: #555; }
        input { width: 100%; padding: 10px; margin: 6px 0 14px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        input:focus { outline: none; border-color: #27ae60; box-shadow: 0 0 0 2px rgba(39,174,96,0.2); }
        button { width: 100%; padding: 11px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; transition: background 0.2s; }
        button:hover { background: #219a52; }
        .error { color: red; font-size: 13px; background: #fff0f0; border: 1px solid #fcc; padding: 8px 10px; border-radius: 4px; margin-bottom: 12px; }
        .footer-link { margin-top: 18px; text-align: center; font-size: 13px; color: #888; }
        .footer-link a { color: #27ae60; text-decoration: none; }
        .footer-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <p class="subtitle">Sign in to access the dashboard</p>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form method="post" autocomplete="off">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required autofocus>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Login</button>
        </form>

        <!-- <p class="footer-link">Don't have an account? <a href="register.php">Create Admin</a></p> -->
    </div>
</body>
</html>