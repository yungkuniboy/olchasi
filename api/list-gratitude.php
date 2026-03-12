<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['items' => []]);
    exit;
}

try {
    require_once __DIR__ . '/db.php';
    $pdo = getDb();
    $stmt = $pdo->query('SELECT id, message, author, created_at FROM gratitude ORDER BY created_at DESC LIMIT 100');
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as &$row) {
        $row['id'] = (int) $row['id'];
        $row['author_name'] = $row['author'] === 'olya' ? 'Оля' : 'Сергей';
    }
    echo json_encode(['items' => $items]);
} catch (PDOException $e) {
    error_log('gratitude list error: ' . $e->getMessage());
    echo json_encode(['items' => []]);
}
