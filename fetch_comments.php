<?php
require_once 'connection.php';
session_start();

$movieId = $_GET['movieId'] ?? 0;

function fetchReplies($conn, $parentId) {
    $replies = [];
    $stmt = $conn->prepare("SELECT comments.*, users.user_id AS username FROM comments JOIN users ON comments.user_id = users.id WHERE parent_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $parentId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($reply = $result->fetch_assoc()) {
        $reply['replies'] = fetchReplies($conn, $reply['id']);
        $reply['created_at'] = date('c', strtotime($reply['created_at']));
        $replies[] = $reply;
    }
    return $replies;
}

$stmt = $conn->prepare("SELECT comments.*, users.user_id AS username FROM comments JOIN users ON comments.user_id = users.id WHERE movie_id = ? AND parent_id IS NULL ORDER BY created_at ASC");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $row['replies'] = fetchReplies($conn, $row['id']);
    $row['created_at'] = date('c', strtotime($row['created_at']));
    $comments[] = $row;
}

header('Content-Type: application/json');
echo json_encode($comments);
?>