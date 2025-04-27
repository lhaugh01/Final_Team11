<?php
require_once 'connection.php';
session_start();

$signup_error = '';
$signup_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $user_id = $conn->real_escape_string($_POST['userId']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    $subscription_level = $conn->real_escape_string($_POST['subscription_level']);

    $subscription_level = ($_POST['subscription_level'] === 'golden') ? 'golden' : 'free';
    
    $credit_card = $conn->real_escape_string($_POST['credit_card_number'] ?? '');
    $secure_number = $conn->real_escape_string($_POST['secure_number'] ?? '');
    $expire_date = $conn->real_escape_string($_POST['expire_date'] ?? '');
    $address = $conn->real_escape_string($_POST['billing_address'] ?? '');

    if ($password !== $confirm_password) {
        $signup_error = "Passwords do not match.";
    } elseif ($subscription_level === 'premium') {
        $credit_card_stripped = preg_replace('/\D/', '', $credit_card);
        $secure_number_stripped = preg_replace('/\D/', '', $secure_number);
        
        if (strlen($credit_card_stripped) !== 16) {
            $signup_error = "Credit card number must be 16 digits.";
        } elseif (strlen($secure_number_stripped) !== 3) {
            $signup_error = "Security code (CVV) must be 3 digits.";
        } elseif (!preg_match('/^\d{2}\/\d{2}$/', $expire_date)) {
            $signup_error = "Expiration date must be in MM/YY format.";
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
                
                $insert_sql = "INSERT INTO users (user_id, password, subscription_level, credit_card, secure_numbaer, expire_date, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("sssssss", $user_id, $hashed_password, $subscription_level, $credit_card, $secure_number, $expire_date, $address);

                if ($stmt->execute()) {
                    $signup_success = "Registration successful! You can now log in.";
                } else {
                    $signup_error = "Registration failed: " . $conn->error;
                }
            }
        }
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
            
            $insert_sql = "INSERT INTO users (user_id, password, subscription_level, credit_card, secure_numbaer, expire_date, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssssss", $user_id, $hashed_password, $subscription_level, $credit_card, $secure_number, $expire_date, $address);

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
  <style>
    .credit-card-fields {
      display: none;
      border: 1px solid #ddd;
      padding: 15px;
      margin-top: 10px;
      border-radius: 5px;
      background-color: #f9f9f9;
    }
    
    .credit-card-fields h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 16px;
      color: #333;
    }
  </style>
</head>
<body>

<header class="site-header">
  <div style="padding-left: 20px;">
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
  <form method="POST" action="signup.php" class="signup-form" id="signupForm">
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

    <div class="form-group">
      <label for="subscription_level">Subscription Level</label>
      <select id="subscription_level" name="subscription_level" required>
        <option value="free">Free</option>
        <option value="golden">Golden</option>
      </select>
    </div>

    <div id="creditCardFields" class="credit-card-fields">
      <h3>Payment Information</h3>
      <div class="form-group">
        <label for="credit_card_number">Credit Card Number (16 digits)</label>
        <input type="text" id="credit_card_number" name="credit_card_number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" pattern="\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4}" title="Please enter a valid 16-digit credit card number">
      </div>

      <div class="form-group">
        <label for="secure_number">Secure Number (CVV, 3 digits)</label>
        <input type="text" id="secure_number" name="secure_number" placeholder="XXX" maxlength="3" pattern="\d{3}" title="Security code must be 3 digits">
      </div>

      <div class="form-group">
        <label for="expire_date">Expire Date (MM/YY)</label>
        <input type="text" id="expire_date" name="expire_date" placeholder="MM/YY" maxlength="5" pattern="\d{2}/\d{2}" title="Date format: MM/YY">
      </div>
      
      <div class="form-group">
        <label for="billing_address">Billing Address</label>
        <input type="text" id="billing_address" name="billing_address" placeholder="Enter your billing address">
      </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const subscriptionSelect = document.getElementById('subscription_level');
  const creditCardFields = document.getElementById('creditCardFields');
  const creditCardNumber = document.getElementById('credit_card_number');
  const secureNumber = document.getElementById('secure_number');
  const expireDate = document.getElementById('expire_date');
  const billingAddress = document.getElementById('billing_address');
  const signupForm = document.getElementById('signupForm');
  
  function toggleCreditCardFields() {
    if (subscriptionSelect.value === 'golden') {
      creditCardFields.style.display = 'block';
      creditCardNumber.setAttribute('required', 'true');
      secureNumber.setAttribute('required', 'true');
      expireDate.setAttribute('required', 'true');
      billingAddress.setAttribute('required', 'true');
    } else {
      creditCardFields.style.display = 'none';
      creditCardNumber.removeAttribute('required');
      secureNumber.removeAttribute('required');
      expireDate.removeAttribute('required');
      billingAddress.removeAttribute('required');
    }
  }
  
  toggleCreditCardFields();
  
  subscriptionSelect.addEventListener('change', toggleCreditCardFields);
  
  creditCardNumber.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 16) {
      value = value.slice(0, 16);
    }
    let formattedValue = '';
    for (let i = 0; i < value.length; i++) {
      if (i > 0 && i % 4 === 0) {
        formattedValue += ' ';
      }
      formattedValue += value[i];
    }
    this.value = formattedValue;
  });
  
  secureNumber.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').substring(0, 3);
  });
  
  expireDate.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 4) {
      value = value.slice(0, 4);
    }
    if (value.length > 2) {
      this.value = value.slice(0, 2) + '/' + value.slice(2);
    } else {
      this.value = value;
    }
  });
  
  signupForm.addEventListener('submit', function(e) {
    if (subscriptionSelect.value === 'free') {
      subscriptionSelect.value = 'free';
      creditCardNumber.value = '';
      secureNumber.value = '';
      expireDate.value = '';
      billingAddress.value = '';
    } else {
      subscriptionSelect.value = 'golden';
      
      const cardNumber = creditCardNumber.value.replace(/\D/g, '');
      const cvv = secureNumber.value.replace(/\D/g, '');
      const expPattern = /^\d{2}\/\d{2}$/;
      
      let isValid = true;
      let errorMessage = '';
      
      if (cardNumber.length !== 16) {
        isValid = false;
        errorMessage = 'Credit card must be 16 digits';
        e.preventDefault();
      } else if (cvv.length !== 3) {
        isValid = false;
        errorMessage = 'Security code must be 3 digits';
        e.preventDefault();
      } else if (!expPattern.test(expireDate.value)) {
        isValid = false;
        errorMessage = 'Expiration date must be in MM/YY format';
        e.preventDefault();
      }
      
      if (!isValid) {
        alert(errorMessage);
      }
    }
  });
});
</script>

</body>
</html>