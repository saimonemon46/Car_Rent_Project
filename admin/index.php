<?php
session_start();
var_dump($_SESSION); // Temporary debug line

include('includes/config.php');

// Redirect if already logged in
if(isset($_SESSION['admin'])) {
    header("Location: dashboard.php"); // Updated to dashboard since they are already auth'd
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } else {
        // 1. Fetch user by username ONLY
        // Note: Column names must match your DB (UserName, Password)
        $sql  = "SELECT id, UserName, Password FROM admin WHERE UserName = :uname LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uname', $username, PDO::PARAM_STR);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Verify password using bcrypt
        if($admin && password_verify($password, $admin['Password'])) {
            $_SESSION['admin']   = $admin['UserName'];
            $_SESSION['adminid'] = $admin['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Car Rental</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:         #0b0c0f;
            --surface:    #13151a;
            --border:     #1f2230;
            --accent:     #e8b84b;
            --accent2:    #c9971a;
            --text:       #e8eaf0;
            --muted:      #6b7080;
            --danger:     #f05454;
            --input-bg:   #0e1016;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            background-color: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(232,184,75,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(232,184,75,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            animation: gridPan 30s linear infinite;
            pointer-events: none;
        }

        @keyframes gridPan {
            0%   { background-position: 0 0; }
            100% { background-position: 48px 48px; }
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .18;
            pointer-events: none;
        }
        .orb-1 {
            width: 520px; height: 520px;
            background: radial-gradient(circle, #e8b84b, transparent 70%);
            top: -140px; left: -140px;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #667eea, transparent 70%);
            bottom: -100px; right: -100px;
            animation: drift 16s ease-in-out infinite alternate-reverse;
        }
        @keyframes drift {
            from { transform: translate(0, 0); }
            to   { transform: translate(40px, 30px); }
        }

        .login-wrap {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: slideUp .55s cubic-bezier(.22,.68,0,1.2) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 44px 40px 40px;
            box-shadow: 0 0 0 1px rgba(255,255,255,.03), 0 24px 60px rgba(0,0,0,.55);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .brand-icon {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            box-shadow: 0 4px 20px rgba(232,184,75,.3);
        }

        .brand-text .title {
            font-family: 'Bebas Neue', cursive;
            font-size: 26px;
            letter-spacing: 1.5px;
            color: var(--text);
            line-height: 1;
        }

        .brand-text .sub {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .login-heading { margin-bottom: 28px; }

        .login-heading h1 {
            font-family: 'Bebas Neue', cursive;
            font-size: 34px;
            letter-spacing: 2px;
            color: var(--text);
            line-height: 1;
        }

        .login-heading p {
            font-size: 13px;
            color: var(--muted);
            margin-top: 6px;
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin-bottom: 28px;
        }

        .field { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: .5;
            pointer-events: none;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 13px 14px 13px 42px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232,184,75,.12);
        }

        .pw-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 16px;
            padding: 2px;
            transition: color .2s;
        }
        .pw-toggle:hover { color: var(--accent); }

        .error-msg {
            background: rgba(240,84,84,.1);
            border: 1px solid rgba(240,84,84,.3);
            border-radius: 8px;
            padding: 11px 14px;
            color: var(--danger);
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: shake .35s cubic-bezier(.36,.07,.19,.97);
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%);
            color: #0b0c0f;
            border: none;
            border-radius: 8px;
            font-family: 'Bebas Neue', cursive;
            font-size: 18px;
            letter-spacing: 2px;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s, filter .2s;
            margin-top: 6px;
            box-shadow: 0 4px 20px rgba(232,184,75,.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(232,184,75,.4);
            filter: brightness(1.08);
        }

        .btn-login:active { transform: translateY(0); }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--muted);
        }

        .login-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .login-card { padding: 36px 26px 32px; }
        }
    </style>
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="login-wrap">
    <div class="login-card">

        <div class="brand">
            <div class="brand-icon">🚘</div>
            <div class="brand-text">
                <div class="title">CarRental</div>
                <div class="sub">Administration</div>
            </div>
        </div>

        <div class="login-heading">
            <h1>Admin Login</h1>
            <p>Restricted area — authorised personnel only.</p>
        </div>

        <div class="divider"></div>

        <?php if($error !== ''): ?>
        <div class="error-msg">
            <span>⚠</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">

            <div class="field">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter username"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required
                        autofocus>
                    <span class="input-icon">👤</span>
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        required>
                    <span class="input-icon">🔑</span>
                    <button type="button" class="pw-toggle" id="pwToggle">👁</button>
                </div>
            </div>

            <button type="submit" class="btn-login">Sign In →</button>
        </form>

        <div class="login-footer">
            <a href="../../index.php">← Return to public site</a>
        </div>

    </div>
</div>

<script>
    const pwToggle = document.getElementById('pwToggle');
    const pwInput  = document.getElementById('password');
    pwToggle.addEventListener('click', () => {
        const show = pwInput.type === 'password';
        pwInput.type = show ? 'text' : 'password';
        pwToggle.textContent = show ? '🙈' : '👁';
    });
</script>

</body>
</html>