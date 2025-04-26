<?php
// display_users.php

require_once 'connection.php'; // This already creates $conn (mysqli)

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
      font-family: 'Courier New', Courier, monospace;
      background-color: #f4f4f4;
      color: #333;
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
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #141414;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
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
    // Output table header
    while ($fieldinfo = $result->fetch_field()) {
        echo "<th>" . htmlspecialchars($fieldinfo->name) . "</th>";
    }
    ?>
  </tr>

  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <?php foreach ($row as $val): ?>
        <td><?php echo htmlspecialchars($val); ?></td>
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