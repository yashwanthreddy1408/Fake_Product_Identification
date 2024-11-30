<?php
include 'connection.php'; // Include your database connection file

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['id']; // Assuming user ID is stored in session

// Function to format numbers according to the Indian numbering system
function formatIndianNumber($number)
{
    $number = (int)$number; // Remove decimals
    $number = (string)$number;
    $result = "";
    $length = strlen($number);

    if ($length > 3) {
        $last3Digits = substr($number, -3);
        $remainingDigits = substr($number, 0, $length - 3);
        $result = "," . $last3Digits;

        while (strlen($remainingDigits) > 2) {
            $result = "," . substr($remainingDigits, -2) . $result;
            $remainingDigits = substr($remainingDigits, 0, strlen($remainingDigits) - 2);
        }
        $result = $remainingDigits . $result;
    } else {
        $result = $number;
    }

    return $result;
}

if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = (int)$_POST['cart_id']; // Ensure cart_id is an integer
    $quantity = (int)$_POST['quantity']; // Ensure quantity is an integer

    // Validate quantity to ensure it's a positive integer
    if ($quantity <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid quantity']);
        exit;
    }

    // Prepare and execute the update statement
    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('iii', $quantity, $cart_id, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Fetch the new price from the products table
            $sql = "SELECT p.new_price FROM products p JOIN cart c ON p.product_id = c.product_id WHERE c.cart_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
                exit;
            }
            $stmt->bind_param('i', $cart_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $new_price = $product['new_price'] * $quantity;

                // Validate the new_price to ensure it's numeric
                if (!is_numeric($new_price)) {
                    echo json_encode(['success' => false, 'error' => 'Invalid price data']);
                    exit;
                }

                $formatted_price = formatIndianNumber($new_price);
                echo json_encode([
                    'success' => true,
                    'new_price' => '₹' . $formatted_price, // Add currency symbol here
                    'new_quantity' => $quantity // Return the updated quantity
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Product not found']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No rows affected']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update quantity: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
