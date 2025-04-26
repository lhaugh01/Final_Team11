<?php
require_once 'connection.php';
session_start();

// Check if already logged in
if (isset($_SESSION['userId'])) {
    header('Location: redirect_after_login.php');
    exit();
}

$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user_id = $conn->real_escape_string($_POST['loginUserId']);
    $password = $_POST['loginPassword'];

    $sql = "SELECT id, user_id, password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['userId'] = $user['id']; // Save user id to PHP session
            header('Location: redirect_after_login.php');
            exit;
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "User does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - NetView</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
<h1><a href="index.html" style="text-decoration: none; color: inherit;">🎬 NetView</a></h1>
  <p>Welcome back — sign in to explore movies</p>
</header>

<div class="container">

  <?php if (!empty($login_error)): ?>
    <div class="message error"><?php echo htmlspecialchars($login_error); ?></div>
  <?php endif; ?>

  <form method="POST" action="login.php" class="login-form">
    <h2 style="text-align: center; margin-bottom: 20px;">Log In</h2>

    <div class="form-group">
      <label for="loginUserId">User ID</label>
      <input type="text" id="loginUserId" name="loginUserId" required>
    </div>

    <div class="form-group">
      <label for="loginPassword">Password</label>
      <input type="password" id="loginPassword" name="loginPassword" required>
    </div>

    <button type="submit" name="login" class="login-btn">Log In</button>

    <div class="form-footer">
      No account yet? <a href="signup.php">Sign Up</a>
    </div>
  </form>

</div>

</body>
</html>