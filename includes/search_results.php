<?php
require_once __DIR__ . '/../config.php';

if(isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $searchTerm = trim($_GET['q']);
    $search = "%$searchTerm%";
    
    try {
        $stmt = $pdo->prepare("
            SELECT *, 
            MATCH(title, description) AGAINST(:search IN BOOLEAN MODE) AS relevance
            FROM `groups`
            WHERE MATCH(title, description) AGAINST(:search IN BOOLEAN MODE)
            ORDER BY relevance DESC
            LIMIT 12
        ");
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($groups) > 0): ?>
            <div class="row">
                <?php foreach ($groups as $group): ?>
                <div class="col s12 m6 l4">
                    <div class="card hoverable">
                        <div class="card-image waves-effect waves-block waves-light">
                            <img src="uploads/<?= htmlspecialchars($group['qr_code']) ?>" 
                                 alt="<?= htmlspecialchars($group['title']) ?>"
                                 class="responsive-img activator">
                        </div>
                        <div class="card-content">
                            <span class="card-title activator">
                                <?= htmlspecialchars($group['title']) ?>
                                <i class="material-icons right">more_vert</i>
                            </span>
                            <p><?= mb_substr(htmlspecialchars($group['description']), 0, 50) ?>...</p>
                            <div class="chip"><?= htmlspecialchars($group['category']) ?></div>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title">
                                <?= htmlspecialchars($group['title']) ?>
                                <i class="material-icons right">close</i>
                            </span>
                            <p><?= nl2br(htmlspecialchars($group['description'])) ?></p>
                            <?php if($group['group_number']): ?>
                                <p>群号：<?= htmlspecialchars($group['group_number']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="center grey-text">
                <i class="material-icons large">search_off</i>
                <p>未找到匹配结果</p>
            </div>
        <?php endif;
    } catch (PDOException $e) {
        echo '<div class="card-panel red lighten