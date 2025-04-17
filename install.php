<?php
require 'config.php';

try {
    // 创建表
    $pdo->exec("CREATE TABLE IF NOT EXISTS groups (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        category ENUM('社团','学院','班级','其他') NOT NULL,
        qr_code VARCHAR(255) NOT NULL,
        expire_date DATE,
        view_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash CHAR(60) NOT NULL,
        last_login DATETIME
    )");
    
    // 创建初始管理员 (密码: admin123)
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->exec("INSERT INTO admins (username, password_hash) 
               VALUES ('admin', '$password')");
    
    echo "安装成功！<br>管理员账号：admin<br>密码：admin123";
} catch (Exception $e) {
    die("安装失败: " . $e->getMessage());
}
?>