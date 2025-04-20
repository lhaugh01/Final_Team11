<?php
require_once 'connection.php';

$signup_error = '';
$signup_success = '';
$login_error = '';
$login_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $user_id = $conn->real_escape_string($_POST['userId']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    
    if ($password !== $confirm_password) {
        $signup_error = "Passwords do not match";
    } else {
        $check_sql = "SELECT user_id FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $signup_error = "This UserID already exists, please choose another one";
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user_id = $conn->real_escape_string($_POST['loginUserId']);
    $password = $_POST['loginPassword'];
    
    $sql = "SELECT user_id, password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $login_success = "Login successful! Welcome back, " . $user_id;
        } else {
            $login_error = "Incorrect password";
        }
    } else {
        $login_error = "User does not exist";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Database</title>
    <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Courier New', Courier, monospace;
      background-color: #f4f4f4;
      color: #333;
      line-height: 1.6;
    }

    .site-header {
      background-color: #141414;
      color: #fff;
      padding: 20px 0;
      text-align: center;
      border-bottom: 4px solid #e50914;
    }

    .site-header h1 {
      font-size: 2.5em;
      margin-bottom: 0.2em;
    }

    .site-header p {
      font-size: 1.1em;
      color: #ccc;
    }

    .site-footer {
      background-color: #141414;
      color: #ccc;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
      font-size: 0.9em;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 20px;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0; top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.7);
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      width: 80%;
      max-width: 600px;
      border-radius: 8px;
      position: relative;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .close-button {
      color: #aaa;
      font-size: 28px;
      font-weight: bold;
      position: absolute;
      right: 20px;
      top: 10px;
      cursor: pointer;
    }

    .close-button:hover {
      color: #e50914;
    }

    .login-form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .login-form h2 {
      margin-bottom: 20px;
      color: #141414;
    }

    .login-form input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    .login-form button {
      padding: 10px 20px;
      background-color: #e50914;
      color: white;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
    }

    .login-form button:hover {
      background-color: #c40611;
    }

    .login-btn {
      padding: 8px 16px;
      background-color: #e50914;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }

    .login-btn:hover {
      background-color: #c40611;
    }

    .auth-buttons {
      text-align: center;
      margin: 20px 0;
    }

    .auth-buttons .login-btn {
      margin: 0 10px;
    }

    .fixed-login-btn {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
    }

    .signup-container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .signup-form {
      display: flex;
      flex-direction: column;
      width: 100%;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1em;
    }

    .signup-btn {
      padding: 12px 20px;
      background-color: #e50914;
      color: white;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      font-size: 1em;
      margin-top: 10px;
    }

    .signup-btn:hover {
      background-color: #c40611;
    }

    .form-footer {
      margin-top: 20px;
      text-align: center;
      font-size: 0.9em;
    }

    .form-footer a {
      color: #e50914;
      text-decoration: none;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }
    
    .message {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
      text-align: center;
    }
    
    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    </style>
</head>
<body>
    <header class="site-header">
        <h1>Test</h1>
        <p>Discover movies you like</p>
    </header>

    <button class="login-btn fixed-login-btn" id="loginBtnFixed">Login</button>

    <div class="container">
        <div class="signup-container">
            <?php if (!empty($signup_error)): ?>
                <div class="message error"><?php echo $signup_error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($signup_success)): ?>
                <div class="message success"><?php echo $signup_success; ?></div>
            <?php endif; ?>
            
            <form class="signup-form" id="signupForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h2 style="text-align: center; margin-bottom: 25px;">Sign up</h2>
                
                <div class="form-group">
                    <label for="userId">UserID</label>
                    <input type="text" id="userId" name="userId" placeholder="Please Enter UserID" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Please enter Password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Please enter Password again" required>
                </div>
                
                <button type="submit" name="signup" class="signup-btn">Sign up</button>
                
                <div class="form-footer">
                    Got an account？<a href="#" id="showLoginModal">Login</a>
                </div>
            </form>
        </div>
    </div>

    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeLoginModal">&times;</span>
            
            <?php if (!empty($login_error)): ?>
                <div class="message error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($login_success)): ?>
                <div class="message success"><?php echo $login_success; ?></div>
            <?php endif; ?>
            
            <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h2>Login</h2>
                <input type="text" name="loginUserId" placeholder="UserID" required>
                <input type="password" name="loginPassword" placeholder="Password" required>
                <button type="submit" name="login">Log in</button>
                <p style="margin-top: 15px;">No account？<a href="#" id="switchToSignup" style="color: #e50914; text-decoration: none;">Sign Up</a></p>
            </form>
        </div>
    </div>

    <footer class="site-footer">
        <p>&copy; 2025</p>
    </footer>

    <script>
        const loginModal = document.getElementById('loginModal');
        const loginBtnFixed = document.getElementById('loginBtnFixed');
        const closeLoginModal = document.getElementById('closeLoginModal');
        const showLoginModal = document.getElementById('showLoginModal');
        const switchToSignup = document.getElementById('switchToSignup');
        const signupForm = document.getElementById('signupForm');

        loginBtnFixed.onclick = function() {
            loginModal.style.display = 'block';
        }

        showLoginModal.onclick = function(e) {
            e.preventDefault();
            loginModal.style.display = 'block';
        }

        closeLoginModal.onclick = function() {
            loginModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == loginModal) {
                loginModal.style.display = 'none';
            }
        }

        switchToSignup.onclick = function(e) {
            e.preventDefault();
            loginModal.style.display = 'none';
        }

        signupForm.onsubmit = function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match! Please try again.');
                e.preventDefault();
                return false;
            }
        }
        
        <?php if (!empty($login_error) || !empty($login_success)): ?>
            loginModal.style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>