<?php
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_GET['userId'])) {
    echo json_encode(['error' => 'Missing userId']);
    exit;
}

$userId = (int) $_GET['userId'];

$stmt = $conn->prepare("SELECT subscription_level FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    echo json_encode(['subscription_level' => $row['subscription_level']]);
} else {
    echo json_encode(['error' => 'User not found']);
}
?>