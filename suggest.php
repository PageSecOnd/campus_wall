<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $term = isset($_GET['term']) ? trim($_GET['term']) : '';
    
    if (strlen($term) < 2) {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT title 
        FROM groups 
        WHERE title LIKE :term
        GROUP BY title
        LIMIT 5
    ");
    $stmt->execute([':term' => "%$term%"]);
    
    $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($suggestions);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}