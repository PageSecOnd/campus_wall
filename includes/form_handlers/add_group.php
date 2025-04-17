<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 验证必填字段
        $required = ['title', 'category'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("必填字段 {$field} 未填写");
            }
        }

        // 文件验证
        if (empty($_FILES['qr_code']['name'])) {
            throw new Exception("必须上传二维码图片");
        }

        $file = $_FILES['qr_code'];
        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg'
        ];

        if (!array_key_exists($file['type'], $allowedTypes)) {
            throw new Exception("只支持 JPG/PNG 格式图片");
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception("文件大小超过 2MB 限制");
        }

        // 生成文件名
        $filename = 'qr_' . bin2hex(random_bytes(8)) . '.' . $allowedTypes[$file['type']];
        $uploadPath = __DIR__ . '/../../uploads/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception("文件保存失败");
        }

        // 数据清洗
        $data = [
            'title' => htmlspecialchars(trim($_POST['title'])),
            'description' => htmlspecialchars(trim($_POST['description'] ?? '')),
            'category' => $_POST['category'],
            'qr_code' => $filename,
            'group_number' => isset($_POST['group_number']) 
                            ? preg_replace('/\D/', '', $_POST['group_number'])
                            : null,
            'expire_date' => !empty($_POST['expire_date']) 
                           ? $_POST['expire_date'] 
                           : null
        ];

        // 数据库操作
        $stmt = $pdo->prepare("
            INSERT INTO groups 
            (title, description, category, qr_code, group_number, expire_date)
            VALUES (:title, :description, :category, :qr_code, :group_number, :expire_date)
        ");
        
        if (!$stmt->execute($data)) {
            throw new Exception("数据库写入失败");
        }

        $_SESSION['success'] = '校园墙添加成功！';
        header("Location: add.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}