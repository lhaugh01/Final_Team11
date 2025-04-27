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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <title>My Account - NetView</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
  <div class="nav-bar">
    <div class="logo">
      <a href="index.html">
        <img src="logo.png" alt="NetView Logo" class="logo-img">
      </a>
    </div>
    <div class="nav-links">
      <a href="index.html" class="nav-btn">Home</a>
      <a href="account.php" class="nav-btn">My Account</a>
      <a href="display_users.php" class="nav-btn">Admin</a>
    </div>
  </div>
</header>

<main class="container" style="text-align: center; margin-top: 30px;">

  <h2>Welcome Back <?php echo htmlspecialchars($user['user_id']); ?>!</h2>

  <div style="margin: 20px 0;">
    <p><strong>Subscription Tier:</strong> <?php echo htmlspecialchars(ucfirst($user['subscription_level'])); ?></p>
    <button onclick="logout()" class="login-btn" style="margin-top:20px;">Log Out</button>
  </div>

  <section style="margin-top: 40px;">
    <h3>🎬 Your Search History:</h3>
    <?php if (!empty($searchHistory)): ?>
      <table class="account-table" style="margin-top:20px; margin-left:auto; margin-right:auto;">

        <thead>
          <tr>
            <th>Movie Title</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($searchHistory as $movie): ?>
            <tr>
              <td><?php echo htmlspecialchars($movie); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="margin-top:10px;">You haven't searched any movies yet.</p>
    <?php endif; ?>
  </section>

</main>

<footer class="site-footer">
  <p>&copy; 2025 NetView | Powered by TMDB API</p>
</footer>

<script>
function logout() {
    localStorage.removeItem('userId');
    window.location.href = 'logout.php';
}
</script>

</body>
</html>
