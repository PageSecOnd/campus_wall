<?php
require_once 'config.php';
require_once 'includes/auth.php';
checkAdmin();

$group = [
    'title' => '',
    'description' => '',
    'category' => '社团',
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
    <?php include 'includes/header.php'; ?>
    <title><?= $isEdit ? '编辑表白墙' : '新增表白墙' ?></title>
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
                        <option value="社团" <?= $group['category'] == '社团' ? 'selected' : '' ?>>社团</option>
                        <option value="学院" <?= $group['category'] == '学院' ? 'selected' : '' ?>>学院</option>
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>