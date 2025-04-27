<?php
// display_users.php
session_start();

// Hardcoded admin credentials
$adminUser = 'admin1';
$adminPass = 'admin1234';

// If admin not logged in, check form
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputUser = $_POST['username'] ?? '';
        $inputPass = $_POST['password'] ?? '';

        if ($inputUser === $adminUser && $inputPass === $adminPass) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: display_users.php');
            exit();
        } else {
            $error = "Invalid admin credentials.";
        }
    } else {
        $error = "";
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Admin Login - NetView</title>
      <style>
        body {
          font-family: 'Poppins', sans-serif;
          background-color: #181818;
          color: #eee;
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100vh;
          margin: 0;
        }
        .login-box {
          background: #222;
          padding: 30px;
          border-radius: 12px;
          box-shadow: 0 5px 15px rgba(0,0,0,0.5);
          text-align: center;
          width: 300px;
        }
        input[type="text"], input[type="password"] {
          width: 100%;
          padding: 10px;
          margin: 10px 0;
          border-radius: 6px;
          border: none;
        }
        button {
          width: 100%;
          padding: 10px;
          margin-top: 10px;
          background-color: #e50914;
          color: white;
          border: none;
          font-weight: bold;
          border-radius: 6px;
          cursor: pointer;
        }
        button:hover {
          background-color: #c40611;
        }
        .error {
          color: #ff6666;
          margin-bottom: 10px;
        }
        .back-home-button {
          display: inline-block;
          margin-top: 20px;
          padding: 10px 20px;
          background-color: #555;
          color: white;
          text-decoration: none;
          border-radius: 5px;
          transition: background-color 0.3s ease;
        }
      </style>
    </head>
    <body>
      <div class="login-box">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
          <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post">
          <input type="text" name="username" placeholder="Admin Username" required><br>
          <input type="password" name="password" placeholder="Password" required><br>
          <button type="submit">Login</button>
          <a href="index.html" class="back-home-button">← Back to Home Page</a>
        </form>
      </div>
    </body>
    </html>
    <?php
    exit();
}

// Admin was logged in — immediately destroy session to force logout on reload
unset($_SESSION['admin_logged_in']);

require_once 'connection.php';
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Users</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #181818;
      color: #eee;
      padding: 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 0 auto;
      background: #222;
      box-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }
    th, td {
      padding: 12px 15px;
      text-align: center;
      vertical-align: top;
      border-bottom: 1px solid #333;
    }
    th {
      background-color: #e50914;
      color: white;
    }
    tr:hover {
      background-color: #2a2a2a;
    }
    .back-home {
      display: block;
      margin: 30px auto;
      text-align: center;
      font-size: 1.2em;
      text-decoration: none;
      color: #e50914;
      font-weight: bold;
    }
    .back-home:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<h1>All Registered Users</h1>

<?php if ($result && $result->num_rows > 0): ?>
<table>
  <tr>
    <?php
    $fields = [];
    while ($fieldinfo = $result->fetch_field()) {
      if ($fieldinfo->name !== 'password') { // Hide password column
          $fields[] = $fieldinfo->name;
          echo "<th>" . htmlspecialchars($fieldinfo->name) . "</th>";
      }
    }
    ?>
  </tr>

  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <?php foreach ($fields as $fieldName): ?>
        <td>
          <?php
            if (!empty($row[$fieldName])) {
              if ($fieldName === 'search_history_name' || $fieldName === 'search_history_id') {
                $items = explode(',', $row[$fieldName]);
                foreach ($items as $item) {
                  echo htmlspecialchars(trim($item)) . "<br>";
                }
              } else {
                echo htmlspecialchars($row[$fieldName]);
              }
            } else {
              echo "N/A";
            }
          ?>
        </td>
      <?php endforeach; ?>
    </tr>
  <?php endwhile; ?>
</table>
<?php else: ?>
  <p style="text-align: center;">No users found.</p>
<?php endif; ?>

<a href="index.html" class="back-home">← Back to Home</a>

</body>
</html>