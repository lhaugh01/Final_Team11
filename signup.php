<?php
require_once 'connection.php';
session_start();

$signup_error = '';
$signup_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $user_id = $conn->real_escape_string($_POST['userId']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];

    if ($password !== $confirm_password) {
        $signup_error = "Passwords do not match.";
    } else {
        $check_sql = "SELECT user_id FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $signup_error = "This UserID already exists. Please choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (user_id, password, subscription_level) VALUES (?, ?, 'free')";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ss", $user_id, $hashed_password);

            if ($stmt->execute()) {
                $signup_success = "Registration successful! You can now log in.";
            } else {
                $signup_error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up - NetView</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
  <div style="padding-left: 20px;"> <!-- shifted logo right slightly -->
    <a href="index.html" class="logo">
      <img src="logo.png" alt="NetView Logo" class="logo-img">
    </a>
  </div>

  
</header>

<div class="container">

  <?php if (!empty($signup_error)): ?>
    <div class="message error"><?php echo htmlspecialchars($signup_error); ?></div>
  <?php endif; ?>

  <?php if (!empty($signup_success)): ?>
    <div class="message success"><?php echo htmlspecialchars($signup_success); ?></div>
    <div style="text-align: center; margin-top: 20px;">
      <a href="login.php" class="login-btn">Go to Login</a>
    </div>
  <?php endif; ?>

  <?php if (empty($signup_success)) : ?>
  <form method="POST" action="signup.php" class="signup-form">
    <h2 style="text-align: center; margin-bottom: 20px;">Sign Up</h2>

    <div class="form-group">
      <label for="userId">User ID</label>
      <input type="text" id="userId" name="userId" required>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
      <label for="confirmPassword">Confirm Password</label>
      <input type="password" id="confirmPassword" name="confirmPassword" required>
    </div>

    <button type="submit" name="signup" class="signup-btn">Sign Up</button>

    <div class="form-footer">
      Already have an account? <a href="login.php">Log In</a>
    </div>
  </form>
  <?php endif; ?>

</div>

<footer class="site-footer">
  <p>&copy; 2025 NetView | Powered by TMDB API</p>
</footer>

</body>
</html>