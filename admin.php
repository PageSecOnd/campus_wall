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

// 获取所有表白墙
$groups = $pdo->query("SELECT * FROM c_groups ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'includes/header.php'; ?>
    <title>后台管理</title>
    <style>
        /* 新增管理页样式 */
        .c-admin-table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .c-admin-table th, .c-admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .c-admin-table th {
            background: #f8f9fa;
            color: #666;
            font-weight: 500;
        }
        .c-qr-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

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

    <?php include 'includes/footer.php'; ?>
</body>
</html>