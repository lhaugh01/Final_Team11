<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //assume payment success
    $stmt = $conn->prepare("UPDATE users SET subscription_level = 'golden' WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        header('Location: account.php?upgraded=1');
        exit();
    } else {
        $error = "Failed to upgrade membership. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade to Golden Membership</title>
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
    <div class="nav-links" id="navLinks">
        <a href="index.html" class="nav-btn">Home</a>
        <a href="about.html" class="nav-btn">About Us</a>
        <a href="donate.html" class="nav-btn">Donate</a>
        <a href="account.php" class="nav-btn">My Account</a>
        <a href="display_users.php" class="nav-btn">Admin</a>
      </div>
  </div>
</header>

<main class="container" style="max-width: 500px; margin-top: 50px;">
    <h2 style="text-align: center;">Upgrade to Golden Membership</h2>

    <p style="text-align: center; margin-bottom: 20px; color: #ccc;">
      💳 You will be charged <strong style="color: #e50914;">$15/month</strong> for Golden benefits like commenting and exclusive features.
    </p>

    <?php if (!empty($error)): ?>
      <div style="color:red; margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="upgrade.php" style="background-color: #111; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.7);">
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="cardName" style="font-weight:bold;">Name on Card</label>
            <input type="text" id="cardName" name="cardName" placeholder="John Doe" required>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="cardNumber" style="font-weight:bold;">Card Number</label>
            <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
        </div>

        <div class="form-group" style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex:1;">
                <label for="expiry" style="font-weight:bold;">Expiry</label>
                <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
            </div>

            <div style="flex:1;">
                <label for="cvv" style="font-weight:bold;">CVV</label>
                <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
            </div>
        </div>

        <button type="submit" class="signup-btn" style="width: 100%;">Confirm Upgrade</button>
    </form>

    <p style="text-align: center; margin-top: 20px; font-size: 0.9em; color:#aaa;">* No real charge will occur in this demo.</p>
</main>



<footer class="site-footer">
  <p>&copy; 2025 NetView</p>
</footer>

</body>
</html>