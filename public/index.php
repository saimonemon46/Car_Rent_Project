<?php require_once '../app/config/config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | <?= SITENAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Welcome to the <?= SITENAME; ?></h2>

    <a href="<?= URLROOT ?>/auth/login" class="btn">Admin Login</a>
</body>
</html>