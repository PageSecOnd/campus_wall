<?php
// 初始化配置
require_once 'config.php';

/**
 * 执行群组搜索
 * @param PDO $pdo 数据库实例
 * @param string $keyword 搜索关键词
 * @return array|bool 搜索结果数组或false表示失败
 */
function performGroupSearch($pdo, $keyword) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, description, category, qr_code, group_number, created_at
            FROM c_groups
            WHERE title LIKE :keyword 
               OR description LIKE :keyword
            ORDER BY created_at DESC
            LIMIT 12
        ");
        $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("[SEARCH_ERROR] " . $e->getMessage());
        return false;
    }
}

// 处理搜索请求
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchResults = [];
$errorMessage = '';

if (!empty($searchTerm)) {
    $searchResults = performGroupSearch($pdo, $searchTerm);
    
    if ($searchResults === false) {
        $errorMessage = "系统暂时无法处理您的搜索请求";
    }
}

// 页面元数据
$pageTitle = $searchTerm ? "搜索：{$searchTerm}" : "校园墙查询";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="c-main-header">
        <div class="c-header-container">
            <h1 class="c-site-title">
                <i class="material-icons">groups</i>
                校园墙查询系统
            </h1>
            <a href="admin.php" class="c-admin-portal">
                <i class="material-icons">admin_panel_settings</i>
                <span>管理后台</span>
            </a>
        </div>
    </header>
    <main class="c-main-container">
        <!-- 搜索模块 -->
        <section class="c-search-section <?= empty($searchResults) ? 'c-centered' : '' ?>">
            <form class="c-search-form" method="GET" action="">
                <div class="c-input-group">
                    <input type="search" 
                         class="c-search-input"
                         name="q"
                         value="<?= htmlspecialchars($searchTerm) ?>"
                         placeholder="输入群名称或描述..."
                         required
                         aria-label="校园墙搜索">
                    <button type="submit" class="c-search-button">
                        <i class="material-icons">search</i>
                    </button>
                </div>
            </form>
        </section>

        <!-- 错误提示 -->
        <?php if ($errorMessage): ?>
        <div class="c-alert c-alert-error">
            <i class="material-icons c-alert-icon">error_outline</i>
            <span class="c-alert-text"><?= $errorMessage ?></span>
        </div>
        <?php endif; ?>

        <!-- 搜索结果 -->
        <?php if (!empty($searchResults)): ?>
        <div class="c-results-grid">
            <?php foreach ($searchResults as $group): ?>
            <article class="c-card">
                <figure class="c-card-media">
                    <img src="uploads/<?= htmlspecialchars($group['qr_code']) ?>" 
                         alt="<?= htmlspecialchars($group['title']) ?>二维码" 
                         class="c-card-image"
                         loading="lazy">
                </figure>
                <div class="c-card-body">
                    <h3 class="c-card-title"><?= htmlspecialchars($group['title']) ?></h3>
                    <p class="c-card-description">
                        <?= mb_strimwidth(htmlspecialchars($group['description']), 0, 80, '...') ?>
                    </p>
                    <div class="c-card-meta">
                        <span class="c-badge c-category"><?= htmlspecialchars($group['category']) ?></span>
                        <?php if ($group['group_number']): ?>
                        <span class="c-group-id">
                            <i class="material-icons c-id-icon">tag</i>
                            <?= htmlspecialchars($group['group_number']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php elseif ($searchTerm): ?>
        <div class="c-empty-state">
            <i class="material-icons c-empty-icon">search_off</i>
            <p class="c-empty-text">未找到与 <strong>"<?= htmlspecialchars($searchTerm) ?>"</strong> 相关的结果</p>
        </div>
        <?php endif; ?>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
<style>
    /* styles.css 新增/修改样式 */
/* 绿色渐变色页眉 */
.c-main-header {
    background: linear-gradient(135deg, #4CAF50 0%, #43A047 100%);
    border-radius: 8px;
    margin: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.c-header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* 网站标题样式 */
.c-site-title {
    color: white;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 1.8rem;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.c-site-title i {
    font-size: 2.2rem;
    color: #C8E6C9;
}

/* 后台入口按钮 */
.c-admin-portal {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.8rem 1.5rem;
    background: rgba(255,255,255,0.9);
    border-radius: 25px;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.3);
}

.c-admin-portal:hover {
    background: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.c-admin-portal i {
    color: #2E7D32;
    font-size: 1.8rem;
}

.c-admin-portal span {
    color: #1B5E20;
    font-weight: 500;
    font-size: 1.1rem;
}

/* 移动端适配 */
@media (max-width: 768px) {
    .c-header-container {
        padding: 0.8rem 1rem;
        border-radius: 6px;
    }
    
    .c-site-title {
        font-size: 1.4rem;
        gap: 0.5rem;
    }
    
    .c-site-title i {
        font-size: 1.8rem;
    }
    
    .c-admin-portal {
        padding: 0.6rem 1rem;
    }
    
    .c-admin-portal span {
        display: none;
    }
}
</style>
</html>