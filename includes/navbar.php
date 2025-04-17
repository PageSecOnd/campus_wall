<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="teal lighten-1" role="navigation">
    <div class="nav-wrapper container">
        <!-- 网站Logo -->
        <a id="logo-container" href="index.php" class="brand-logo">
            <i class="material-icons">groups</i>校园墙
        </a>

        <!-- 桌面端导航链接 -->
        <ul class="right hide-on-med-and-down">
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
                <li class="<?= $current_page === 'admin.php' ? 'active' : '' ?>">
                    <a href="admin.php"><i class="material-icons left">dashboard</i>管理后台</a>
                </li>
                <li class="<?= $current_page === 'add.php' ? 'active' : '' ?>">
                    <a href="add.php"><i class="material-icons left">add_circle</i>添加新群</a>
                </li>
                <li>
                    <a href="logout.php"><i class="material-icons left">exit_to_app</i>注销</a>
                </li>
            <?php else: ?>
                <li class="<?= $current_page === 'login.php' ? 'active' : '' ?>">
                    <a href="login.php"><i class="material-icons left">lock</i>管理员登录</a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- 移动端汉堡菜单 -->
        <a href="#" data-target="mobile-nav" class="sidenav-trigger">
            <i class="material-icons">menu</i>
        </a>

        <!-- 移动端侧边导航 -->
        <ul id="mobile-nav" class="sidenav">
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
                <li class="<?= $current_page === 'admin.php' ? 'active' : '' ?>">
                    <a href="admin.php"><i class="material-icons">dashboard</i>管理后台</a>
                </li>
                <li class="<?= $current_page === 'add.php' ? 'active' : '' ?>">
                    <a href="add.php"><i class="material-icons">add_circle</i>添加新群</a>
                </li>
                <li>
                    <a href="logout.php"><i class="material-icons">exit_to_app</i>注销</a>
                </li>
            <?php else: ?>
                <li class="<?= $current_page === 'login.php' ? 'active' : '' ?>">
                    <a href="login.php"><i class="material-icons">lock</i>管理员登录</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
// 初始化移动端侧边导航
document.addEventListener('DOMContentLoaded', function() {
    const sidenavs = document.querySelectorAll('.sidenav');
    M.Sidenav.init(sidenavs);
});
</script>

<style>
/* 自定义导航栏样式 */
nav .brand-logo {
    padding-left: 10px;
    font-weight: 500;
    letter-spacing: 1px;
}

nav .sidenav-trigger {
    margin: 0 10px;
}

.sidenav li.active > a {
    background-color: rgba(0,0,0,0.05);
}

.sidenav li > a > i.material-icons {
    margin-right: 15px;
}
</style>