<?php
//get_movie.php

require_once 'connection_movies.php';

$movieId = $_GET['movie_id'] ?? null;

if ($movieId) {
    $sql = "SELECT * FROM movies WHERE movie_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Movie not found"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing movie_id"]);
}

$conn->close();
?>