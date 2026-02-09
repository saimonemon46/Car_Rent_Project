<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">

<div class="login-container">
    <div class="login-box">
        <h2>Admin Portal</h2>
        <p>Log in to manage your car inventory.</p>

        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($data['error']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?url=auth/login" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" 
                       value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" 
                       required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-footer">
                <button type="submit" name="submit" class="btn-login">Sign In</button>
            </div>
        </form>
        
        <div class="login-help">
            <a href="index.php?url=auth/forgot_password">Forgot Password?</a>
        </div>
    </div>
</div>

</body>
</html>