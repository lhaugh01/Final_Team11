<?php
$host = '35.212.125.126';
$port = '3306';
$dbname = 'dbs5nqdmdcgi92';
$user = 'uxj34ztsgesvj';
$pass = 'tufts12345#';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);

    // Set error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch all users
    $stmt = $pdo->query("SELECT * FROM users");

    echo "<h1>All Users</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>";
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $col = $stmt->getColumnMeta($i);
        echo "<th>" . htmlspecialchars($col['name']) . "</th>";
    }
    echo "</tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>" . htmlspecialchars($val) . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>