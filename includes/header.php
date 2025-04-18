<?php
$is_admin_page = strpos($_SERVER['REQUEST_URI'], 'admin.php') !== false;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="/assets/materialize.min.css">
    
    <!-- 自定义样式 -->
    <style>
        <?php include 'header.css'; ?>
        
        <?php if (!$is_admin_page): ?>
        .nav-wrapper {
            padding-left: 20px;
            padding-right: 20px;
        }
        .brand-logo {
            font-weight: 500;
            letter-spacing: 1px;
        }
        <?php endif; ?>
    </style>
</head>
<body>

<?php if (!$is_admin_page): ?>
<!-- 前台导航 -->
<nav class="teal lighten-1">
    <div class="nav-wrapper">
        <a href="/" class="brand-logo">校园墙</a>
        <a href="#" data-target="mobile-nav" class="sidenav-trigger">
            <i class="material-icons">menu</i>
        </a>
        <ul class="right hide-on-med-and-down">
            <li><a href="#search-modal"><i class="material-icons left">search</i>搜索</a></li>
            <li><a href="/admin.php"><i class="material-icons left">lock</i>管理入口</a></li>
        </ul>
    </div>
</nav>

<!-- 移动端导航 -->
<ul class="sidenav" id="mobile-nav">
    <li><a href="#search-modal"><i class="material-icons">search</i>搜索</a></li>
    <li><a href="/admin.php"><i class="material-icons">lock</i>管理入口</a></li>
</ul>

<!-- 搜索模态框 -->
<div id="search-modal" class="modal">
    <div class="modal-content">
        <form action="/search.php" method="GET">
            <div class="input-field">
                <input id="search" type="search" name="q" required>
                <label class="label-icon" for="search">
                    <i class="material-icons">search</i>
                </label>
                <i class="material-icons modal-close">close</i>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>