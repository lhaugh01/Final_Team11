<?php
require_once 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$userId = $data['userId'] ?? null;
$movieTitle = $data['movieTitle'] ?? null;
$movieId = $data['movieId'] ?? null;

if (!$userId || !$movieTitle || !$movieId) {
    http_response_code(400);
    echo json_encode(["error" => "Missing userId, movieTitle, or movieId"]);
    exit;
}

//fetch current histories
$stmt = $conn->prepare("SELECT search_history_name, search_history_id FROM users WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Database prepare failed"]);
    exit;
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    exit;
}

$historyName = $user['search_history_name'] ?? '';
$historyId = $user['search_history_id'] ?? '';

$updatedHistoryName = $historyName ? "$historyName,$movieTitle" : $movieTitle;
$updatedHistoryId = $historyId ? "$historyId,$movieId" : $movieId;

//update histories
$updateStmt = $conn->prepare("UPDATE users SET search_history_name = ?, search_history_id = ? WHERE id = ?");
if (!$updateStmt) {
    http_response_code(500);
    echo json_encode(["error" => "Database prepare failed"]);
    exit;
}
$updateStmt->bind_param("ssi", $updatedHistoryName, $updatedHistoryId, $userId);

if ($updateStmt->execute()) {
    echo json_encode(["message" => "Search history updated successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update history"]);
}

$conn->close();
?>