<?php
function checkAdmin() {
    if (empty($_SESSION['admin_logged_in'])) {
        header("Location: login.php");
        exit;
    }
}

// 登录处理
if (isset($_POST['login'])) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username=?");
    $stmt->execute([$_POST['username']]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($_POST['password'], $admin['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "用户名或密码错误";
    }
}
?>