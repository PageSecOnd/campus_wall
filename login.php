<?php
require_once 'config.php';
require_once 'includes/auth.php';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php 
    require_once 'includes/header.php'; 
    ?>
    <title>管理员登录</title>
</head>
<body>
    <div class="container">
        <div class="login-container white">
            <h4 class="center-align teal-text">管理员登录</h4>
            
            <?php if (isset($error)): ?>
            <div class="red-text center"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="input-field">
                    <i class="material-icons prefix">account_circle</i>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="input-field">
                    <i class="material-icons prefix">lock</i>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="center">
                    <button type="submit" name="login" class="btn waves-effect teal">
                        登录
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>