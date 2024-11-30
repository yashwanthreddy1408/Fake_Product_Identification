<?php
include 'connection.php'; // Include your database connection file
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}
$user_id = $_SESSION['id']; // Assuming user ID is stored in session

if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    // Prepare and execute the delete statement
    $sql = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
        exit;
    }

    $stmt->bind_param('ii', $cart_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete item: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
