<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'redirect' => 'login.php'
    ]);
    exit;
}

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
$bookId = isset($data['book_id']) ? (int)$data['book_id'] : 0;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

// Validate input
if ($bookId < 1 || $quantity < 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request parameters'
    ]);
    exit;
}

try {
    $userId = $_SESSION['user']['user_id'];

    // 1. Check book stock
    $stmt = $pdo->prepare("SELECT stock FROM books WHERE book_id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();

    if (!$book) {
        echo json_encode([
            'success' => false,
            'message' => 'Book not found'
        ]);
        exit;
    }

    // 2. Get current cart quantity
    $stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$userId, $bookId]);
    $currentQuantity = $stmt->fetchColumn() ?? 0;

    // 3. Validate stock availability
    $proposedQuantity = $currentQuantity + $quantity;
    if ($proposedQuantity > $book['stock']) {
        echo json_encode([
            'success' => false,
            'message' => "Only {$book['stock']} items available in stock"
        ]);
        exit;
    }

    // 4. Update cart with quantity
    $stmt = $pdo->prepare("
        INSERT INTO cart_items (user_id, book_id, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + ?
    ");
    $stmt->execute([$userId, $bookId, $quantity, $quantity]);

    // 5. Get updated cart total items
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalItems = $stmt->fetchColumn() ?? 0;

    echo json_encode([
        'success' => true,
        'new_total_items' => (int)$totalItems,
        'stock_remaining' => $book['stock'] - $proposedQuantity
    ]);

} catch (PDOException $e) {
    error_log("Cart error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating cart. Please try again.'
    ]);
}