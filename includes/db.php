<?php
$host = 'sql5.freesqldatabase.com'; // Use FreeSQLDatabase host
$db   = 'sql5761485';               // Your database name
$user = 'sql5761485';               // Your database username
$pass = 'Jv2hT7TgC1';               // Your database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=3306"; // Explicitly set the port
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo ""; // Debugging message (remove in production)
} catch (\PDOException $e) {
    die("" . $e->getMessage());
}
?>
