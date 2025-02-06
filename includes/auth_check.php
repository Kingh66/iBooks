<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect unauthenticated users
if (!isset($_SESSION['user'])) {
    // Store current URL for redirection after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}
?>