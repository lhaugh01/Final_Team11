<?php
require_once 'connection.php';
session_start();
    
    if (!isset($_GET['movieid'])) {
        echo json_encode(['success' => false, 'message' => 'Movie ID is required']);
        exit();
    }
    $movieid = $_GET['movieid'];

    // SQL query to get comments for a movie
    $sql = "SELECT * FROM Comments WHERE movieid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $movieid);
    $stmt->execute();

    // Get all comments for the movie
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    // Respond with the comments data
    echo json_encode(['success' => true, 'comments' => $comments]);

    $stmt->close();
    $conn->close();
?>