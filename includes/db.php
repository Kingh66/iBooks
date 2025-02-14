<?php
$host = 'mysql-ibooks.alwaysdata.net';  
$db   = 'ibooks_databases';             
$user = 'ibooks';                        
$pass = 'Sizwe@21';                      
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=3306"; 
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Server Error</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, sans-serif;
            }
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f8d7da;
                text-align: center;
                padding: 20px;
            }
            .error-container {
                background-color: #ff4d4d;
                color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
                max-width: 90%;
                width: 400px;
                animation: shake 0.8s infinite alternate;
            }
            .error-container h1 {
                font-size: 22px;
                margin-bottom: 10px;
            }
            .error-container p {
                font-size: 16px;
            }
            @keyframes shake {
                0% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                50% { transform: translateX(5px); }
                75% { transform: translateX(-5px); }
                100% { transform: translateX(5px); }
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>🔴 Server Down</h1>
            <p>The server is currently unavailable. Please check back later.</p>
        </div>
    </body>
    </html>';
    exit;
}
?>
