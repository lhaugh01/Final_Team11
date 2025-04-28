<?php
require_once 'connection.php';
session_start();

header('Content-Type: application/json');

//check if logged in
if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['userId'];

//verify if golden member
$stmt = $conn->prepare("SELECT subscription_level FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || strtolower($user['subscription_level']) !== 'golden') {
    echo json_encode(['success' => false, 'error' => 'Only Golden members can comment']);
    exit();
}

//handle comment
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['comment']) || !isset($input['movieId'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit();
}

$commentText = trim($input['comment']);
$movieId = intval($input['movieId']);
$parentId = isset($input['parentId']) ? intval($input['parentId']) : null;

if (empty($commentText)) {
    echo json_encode(['success' => false, 'error' => 'Comment cannot be empty']);
    exit();
}

//insert comment
$stmt = $conn->prepare("INSERT INTO comments (user_id, movie_id, comment_text, parent_id, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iisi", $userId, $movieId, $commentText, $parentId);
$stmt->execute();

echo json_encode(['success' => true]);
?>