<?php
require_once 'connection.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['userId'];

// Fetch user data
$stmt = $conn->prepare("SELECT user_id, subscription_level, search_history_name FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Split search history names
$searchHistory = [];
if (!empty($user['search_history_name'])) {
    $searchHistory = explode(',', $user['search_history_name']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Account - NetView</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
<h1><a href="index.html" style="text-decoration: none; color: inherit;">🎬 NetView</a></h1>
  <p>My Account</p>
</header>

<div class="container">

  <h2 style="text-align:center; margin-bottom: 20px;">Welcome, <?php echo htmlspecialchars($user['user_id']); ?>!</h2>

  <div style="text-align:center; margin-bottom: 30px;">
    <p><strong>Subscription:</strong> <?php echo htmlspecialchars(ucfirst($user['subscription_level'])); ?></p>
    <button onclick="logout()" class="login-btn" style="margin-top:20px;">Log Out</button>
  </div>

  <div style="margin-top: 40px;">
    <h3>🎬 Your Search History:</h3>
    <?php if (!empty($searchHistory)): ?>
      <ul style="list-style: none; padding-left: 0; margin-top: 10px;">
        <?php foreach ($searchHistory as $movie): ?>
          <li style="background: #fff; margin-bottom: 8px; padding: 10px; border-radius: 6px; box-shadow: 0 1px 5px rgba(0,0,0,0.1);">
            <?php echo htmlspecialchars($movie); ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p style="margin-top:10px;">You haven't searched any movies yet.</p>
    <?php endif; ?>
  </div>

</div>

<script>
function logout() {
    // Clear user info
    localStorage.removeItem('userId');
    window.location.href = 'logout.php';
}
</script>

</body>
</html>