<?php
// 确保session_start在最顶部
session_start();

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'campus_wall');
define('DB_USER', 'campus_wall');
define('DB_PASS', 'BWbzeW3tC3drjbmH');
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);

// 初始化数据库连接（修复4：全局声明）
global $pdo, $redis;

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 初始化Redis连接
try {
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
} catch (RedisException $e) {
    die("Redis连接失败: " . $e->getMessage());
}
?>