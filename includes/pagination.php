<?php
function getPagination($pdo, $search) {
    $totalQuery = "SELECT SQL_CALC_FOUND_ROWS * FROM groups";
    if (!empty($search)) {
        $totalQuery .= " WHERE title LIKE '%$search%' OR description LIKE '%$search%'";
    }
    $pdo->query($totalQuery);
    $total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

    $perPage = 12;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $totalPages = ceil($total / $perPage);

    return [
        'total' => $total,
        'current' => $currentPage,
        'perPage' => $perPage,
        'totalPages' => $totalPages
    ];
}