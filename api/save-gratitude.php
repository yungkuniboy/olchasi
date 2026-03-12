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

$message = isset($_POST['message']) ? trim((string) $_POST['message']) : '';
$author = isset($_POST['author']) ? trim((string) $_POST['author']) : '';
$project = isset($_POST['project']) ? trim((string) $_POST['project']) : 'love';
$project = preg_replace('/[^a-z0-9\-_]/', '', $project) ?: 'love';

$allowedAuthors = ['sergey', 'olya'];
if (!in_array($author, $allowedAuthors, true)) {
    echo json_encode(['success' => false, 'message' => 'Укажите: Сергей или Оля']);
    exit;
}

if ($message === '' || mb_strlen($message) > 2000) {
    echo json_encode(['success' => false, 'message' => 'Текст благодарности от 1 до 2000 символов']);
    exit;
}

try {
    require_once __DIR__ . '/db.php';
    $pdo = getDb();

    $stmt = $pdo->prepare('INSERT INTO gratitude (project, message, author) VALUES (?, ?, ?)');
    $stmt->execute([$project, $message, $author]);

    $success = $stmt->rowCount() >= 1;
    $id = (int) $pdo->lastInsertId();
    echo json_encode(['success' => $success, 'id' => $id]);
} catch (PDOException $e) {
    error_log('gratitude save error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сохранения. Проверьте настройки БД.']);
}
