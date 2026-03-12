<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешён']);
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный id']);
    exit;
}

try {
    require_once __DIR__ . '/db.php';
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM gratitude WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['success' => $stmt->rowCount() >= 1]);
} catch (PDOException $e) {
    error_log('gratitude delete error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка удаления']);
}
