<?php
require_once 'config.php';
require_once 'includes/auth.php';
checkAdmin();

// 删除操作
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM c_groups WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $_SESSION['success'] = '记录已删除';
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        die("删除失败: " . $e->getMessage());
    }
}

// 获取所有群组
$groups = $pdo->query("SELECT * FROM c_groups ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <header class="c-main-header">
        <div class="c-header-container">
            <h1 class="c-site-title">
                <i class="material-icons">groups</i>
                校园墙管理后台
            </h1>
            <a href="index.php" class="c-admin-portal">
                <i class="material-icons">admin_panel_settings</i>
                <span>主页</span>
            </a>
        </div>
    </header>
    <title>后台管理</title>
    <style>
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
    </style>
</head>
<body>
    <div class="c-main-container">
        <div class="c-admin-header">
            <h2>表白墙管理</h2>
            <a href="add.php" class="c-add-button">
                <i class="material-icons">add</i> 新增表白墙
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="c-alert c-alert-success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <table class="c-admin-table">
            <thead>
                <tr>
                    <th>名称</th>
                    <th>分类</th>
                    <th>群号</th>
                    <th>二维码</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $group): ?>
                <tr>
                    <td><?= htmlspecialchars($group['title']) ?></td>
                    <td>
                        <span class="c-tag c-category">
                            <?= htmlspecialchars($group['category']) ?>
                        </span>
                    </td>
                    <td><?= $group['group_number'] ?? '--' ?></td>
                    <td>
                        <img src="uploads/<?= $group['qr_code'] ?>" 
                             class="c-qr-preview"
                             alt="二维码预览">
                    </td>
                    <td>
                        <a href="add.php?edit=<?= $group['id'] ?>" class="c-icon-button">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="admin.php?delete=<?= $group['id'] ?>" 
                           class="c-icon-button c-delete-button"
                           onclick="return confirm('确定删除该记录？')">
                            <i class="material-icons">delete</i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>