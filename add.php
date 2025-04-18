<?php
require_once 'config.php';
require_once 'includes/auth.php';
checkAdmin();

$group = [
    'title' => '',
    'description' => '',
    'category' => '学校',
    'qr_code' => '',
    'group_number' => ''
];
$isEdit = false;

// 编辑模式
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM c_groups WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $group = $stmt->fetch();
    $isEdit = true;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'category' => $_POST['category'],
        'group_number' => preg_replace('/\D/', '', $_POST['group_number'])
    ];

    // 文件上传处理
    if (!empty($_FILES['qr_code']['name'])) {
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $file = $_FILES['qr_code'];

        if (!array_key_exists($file['type'], $allowedTypes)) {
            die("只支持 JPG/PNG 格式");
        }

        $filename = uniqid('qr_') . '.' . $allowedTypes[$file['type']];
        $uploadPath = __DIR__ . '/uploads/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            die("文件上传失败");
        }
        $data['qr_code'] = $filename;
    } elseif ($isEdit) {
        $data['qr_code'] = $group['qr_code'];
    }

    try {
        if ($isEdit) {
            $stmt = $pdo->prepare("
                UPDATE c_groups SET
                title = :title,
                description = :description,
                category = :category,
                qr_code = :qr_code,
                group_number = :group_number
                WHERE id = :id
            ");
            $data['id'] = $_POST['id'];
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO c_groups 
                (title, description, category, qr_code, group_number)
                VALUES (:title, :description, :category, :qr_code, :group_number)
            ");
        }

        $stmt->execute($data);
        $_SESSION['success'] = $isEdit ? '更新成功' : '添加成功';
        header("Location: admin.php");
        exit;

    } catch (PDOException $e) {
        die("数据库操作失败: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="add.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title><?= $isEdit ? '编辑表白墙' : '新增表白墙' ?></title>
    <header class="c-main-header">
        <div class="c-header-container">
            <h1 class="c-site-title">
                <i class="material-icons">groups</i>
                添加校园墙
            </h1>
            <a href="index.php" class="c-admin-portal">
                <i class="material-icons">admin_panel_settings</i>
                <span>主页</span>
            </a>
            <a href="admin.php" class="c-admin-portal">
                <i class="material-icons">admin_panel_settings</i>
                <span>后台</span>
            </a>
        </div>
    </header>
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
        <div class="c-form-container">
            <h2><?= $isEdit ? '编辑表白墙信息' : '添加新表白墙' ?></h2>

            <form method="POST" enctype="multipart/form-data">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= $group['id'] ?>">
                <?php endif; ?>

                <div class="c-form-group">
                    <label>群名称 *</label>
                    <input type="text" name="title" 
                           value="<?= htmlspecialchars($group['title']) ?>" 
                           required>
                </div>

                <div class="c-form-group">
                    <label>分类 *</label>
                    <select name="category" required>
                        <option value="学校" <?= $group['category'] == '学校' ? 'selected' : '' ?>>学校</option>
                        <option value="社团" <?= $group['category'] == '社团' ? 'selected' : '' ?>>社团</option>
                        <option value="班级" <?= $group['category'] == '班级' ? 'selected' : '' ?>>班级</option>
                        <option value="其他" <?= $group['category'] == '其他' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>

                <div class="c-form-group">
                    <label>群号（纯数字）</label>
                    <input type="text" name="group_number" 
                           pattern="\d{5,20}"
                           value="<?= htmlspecialchars($group['group_number'] ?? '') ?>">
                </div>

                <div class="c-form-group">
                    <label>描述</label>
                    <textarea name="description" rows="4"><?= 
                        htmlspecialchars($group['description']) 
                    ?></textarea>
                </div>

                <div class="c-form-group">
                    <label>二维码图片 *</label>
                    <div class="c-file-upload">
                        <?php if (!empty($group['qr_code'])): ?>
                            <img src="uploads/<?= $group['qr_code'] ?>" 
                                 class="c-current-qr" 
                                 alt="当前二维码">
                        <?php endif; ?>
                        <input type="file" name="qr_code" 
                               <?= $isEdit ? '' : 'required' ?> 
                               accept="image/png, image/jpeg">
                    </div>
                </div>

                <div class="c-form-actions">
                    <button type="submit" class="c-primary-button">
                        <?= $isEdit ? '更新信息' : '添加表白墙' ?>
                    </button>
                    <a href="admin.php" class="c-cancel-button">取消</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>