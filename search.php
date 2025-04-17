<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['q']) ? trim($_GET['q']) : '';
    $results = [];

    if (!empty($search)) {
        // 使用 Redis 缓存搜索结果
        $cacheKey = "search:".md5($search);
        if ($redis->exists($cacheKey)) {
            $results = json_decode($redis->get($cacheKey), true);
        } else {
            $stmt = $pdo->prepare("
                SELECT * FROM groups 
                WHERE title LIKE :search 
                   OR description LIKE :search
                ORDER BY view_count DESC 
                LIMIT 12
            ");
            $stmt->execute([':search' => "%$search%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 缓存结果 5 分钟
            $redis->setex($cacheKey, 300, json_encode($results));
        }
    }

    echo json_encode([
        'success' => true,
        'html' => generateResultsHtml($results),
        'count' => count($results)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// 生成 HTML 的函数
function generateResultsHtml($groups) {
    ob_start();
    foreach ($groups as $group): ?>
    <div class="col s12 m6 l4">
        <div class="card hoverable">
            <div class="card-image waves-effect waves-block waves-light">
                <img class="lazy" 
                     data-src="uploads/<?= $group['qr_code'] ?>" 
                     alt="<?= htmlspecialchars($group['title']) ?>">
                <span class="card-title"><?= htmlspecialchars($group['title']) ?></span>
            </div>
            <div class="card-content">
                <p><?= mb_substr(htmlspecialchars($group['description']), 0, 50) ?>...</p>
                <div class="chip"><?= $group['category'] ?></div>
                <div class="secondary-content">
                    <i class="material-icons tiny">visibility</i> <?= $group['view_count'] ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;
    return ob_get_clean();
}