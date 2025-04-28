<?php
require_once 'connection.php';
session_start();

//redirect if not logged in
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['userId'];

//fetch user data
$stmt = $conn->prepare("SELECT user_id, subscription_level, search_history_name FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

//split search history
$searchHistory = [];
if (!empty($user['search_history_name'])) {
    $searchHistory = explode(',', $user['search_history_name']);
}

//fetch user's comments
$commentStmt = $conn->prepare("SELECT movie_id, comment_text, created_at FROM comments WHERE user_id = ? ORDER BY created_at DESC");
$commentStmt->bind_param("i", $userId);
$commentStmt->execute();
$commentResult = $commentStmt->get_result();
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
          <img src="logo.png" alt="NetView Logo">
        </a>
      </div>
  
      <!--mobile friendly -->
      <div class="hamburger" id="hamburger">
        <div></div>
        <div></div>
        <div></div>
      </div>
  
      <!--navigation links-->
      <div class="nav-links" id="navLinks">
        <a href="index.html" class="nav-btn">Home</a>
        <a href="about.html" class="nav-btn">About Us</a>
        <a href="donate.html" class="nav-btn">Donate</a>
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
      <table class="account-table">
        <thead><tr><th>Movie Title</th></tr></thead>
        <tbody>
          <?php foreach ($searchHistory as $movie): ?>
            <tr><td><?php echo htmlspecialchars($movie); ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>You haven't searched any movies yet.</p>
    <?php endif; ?>
  </section>

  <section style="margin-top: 40px;">
    <h3>💬 Your Comment History:</h3>
    <?php if ($commentResult->num_rows > 0): ?>
      <table class="account-table">
        <thead><tr><th>Movie</th><th>Comment</th><th>Time</th></tr></thead>
        <tbody>
          <?php while ($comment = $commentResult->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($comment['movie_id']); ?></td>
              <td><?php echo htmlspecialchars($comment['comment_text']); ?></td>
              <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($comment['created_at']))); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>You haven't made any comments yet.</p>
    <?php endif; ?>
  </section>

</main>

<footer class="site-footer">
  <p>&copy; 2025 NetView</p>
</footer>
<script src="hamburger.js"></script>
<script>
//logout
function logout() {
    localStorage.removeItem('userId');
    localStorage.removeItem('subscriptionLevel');  //clear subscription too
    window.location.href = 'logout.php';
}

//upgrade
<?php if (isset($_GET['upgraded']) && $_GET['upgraded'] == 1): ?>
localStorage.setItem('subscriptionLevel', 'golden');
alert('🎉 You are now a Golden Member! You can comment on movies.');
<?php endif; ?>
</script>

</body>
</html>