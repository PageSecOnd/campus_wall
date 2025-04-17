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
    <?php 
    $page_title = "管理员登录";
    require_once 'includes/header.php'; 
    ?>
    <style>
        .login-container {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
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
                    <label for="username">用户名</label>
                </div>

                <div class="input-field">
                    <i class="material-icons prefix">lock</i>
                    <input type="password" id="password" name="password" required>
                    <label for="password">密码</label>
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

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>