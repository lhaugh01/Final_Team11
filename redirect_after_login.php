<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Redirecting...</title>
  <script>
    // Save userId into localStorage
    localStorage.setItem('userId', <?php echo json_encode($_SESSION['userId']); ?>);
    window.location.href = 'index.html'; // Redirect after setting
  </script>
</head>
<body>
Logging in... Please wait.
</body>
</html>