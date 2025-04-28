<?php
require_once 'connection.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['userId'])) {
    echo '<script>alert("You must be logged in to comment.")</script>';
    header('Location: login.php');
    exit();
}
    
// Get the data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

//at some point user_id was assigned to username and id was assigned to userid
$movieid = $data['movieid'];
$comment = $data['comment'];
$user_id = $_SESSION['userId'];
$username = $_SESSION['username'];

//insert comment data into the database
$sql = "INSERT INTO Comments (movieid, username, user_id, comment) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $movieid, $username, $user_id, $comment);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Comment inserted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to insert comment']);
}

$stmt->close();
$conn->close();
?>