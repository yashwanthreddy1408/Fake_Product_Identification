<?php
// Include the connection.php file for the database connection
include 'connection.php';

// Start the session
session_start();

// Retrieve the cart_id or product_id from the form submission
$cart_id = $_POST['cart_id'] ?? null;
$product_id = $_POST['product_id'] ?? null; // Product ID for direct booking
$user_id = $_SESSION['id'];

// Validate if either cart_id or product_id is set
if (!$cart_id && !$product_id) {
    die("Error: No cart or product selected for checkout.");
}

// Retrieve the necessary details from the form submission
$first_name = $_POST['firstName'] ?? '';
$last_name = $_POST['lastName'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? null;  // Set to null if not provided
$address = $_POST['address'] ?? '';
$address2 = $_POST['address2'] ?? null; // Set to null if not provided
$country = $_POST['country'] ?? '';
$state = $_POST['state'] ?? '';
$zip = $_POST['zip'] ?? '';
$payment_method = $_POST['paymentMethod'] ?? '';

// Set the booking date to the current date
$booked_date = date("Y-m-d H:i:s");

// Initialize variables
$cart_items = [];
$total_price = 0;

if ($cart_id) {
    // Fetch cart items and product information based on cart_id
    $query = "SELECT p.product_id, p.product_name, c.quantity, p.new_price, (c.quantity * p.new_price) as total_item_price
              FROM cart c
              JOIN products p ON c.product_id = p.product_id
              WHERE c.cart_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the cart has items
    if ($result->num_rows == 0) {
        die("Error: Cart is empty (No items found for this cart ID).");
    }

    // Fetch each item from the result and calculate total price
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['total_item_price'];
    }
} elseif ($product_id) {
    // Fetch product details for direct booking using product_id
    $query = "SELECT product_id, product_name, new_price FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Assuming a direct purchase has a quantity of 1
        $row['quantity'] = 1;
        $row['total_item_price'] = $row['new_price']; // Total price for one item

        $cart_items[] = $row;
        $total_price = $row['total_item_price'];
    } else {
        die("Error: Product not found.");
    }
}

// Begin transaction for placing the order
$conn->begin_transaction();

try {
    // Generate a custom order ID
    $random_number = rand(1000, 9999);
    $custom_order_id = "OIDSC" . $random_number . "FPI";

    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        $sql = "INSERT INTO orders (order_id, user_id, first_name, last_name, username, email, address, address2, country, state, zip, payment_method, price, booked_date, product_id, quantity) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sissssssssssdsii",
            $custom_order_id,
            $user_id,
            $first_name,
            $last_name,
            $username,
            $email,
            $address,
            $address2,
            $country,
            $state,
            $zip,
            $payment_method,
            $total_price,
            $booked_date,
            $product_id,
            $quantity
        );

        // Execute the insert
        if (!$stmt->execute()) {
            throw new Exception("Error placing order: " . $stmt->error);
        }

        // Decrease the product quantity in the products table
        $update_quantity_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE product_id = ?");
        $update_quantity_stmt->bind_param("ii", $quantity, $product_id);
        if (!$update_quantity_stmt->execute() || $update_quantity_stmt->affected_rows === 0) {
            throw new Exception("Error updating product quantity for product ID: $product_id");
        }
    }

    // If coming from cart, remove all items from the cart
    if ($cart_id) {
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
        $delete_stmt->bind_param("i", $cart_id);
        $delete_stmt->execute();
    }

    // Commit transaction
    $conn->commit();
    $_SESSION['order_placed'] = true;
    $_SESSION['order_id'] = $custom_order_id; // Store custom order ID
    header("Location: orderSummary.php");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    error_log("Transaction failed: " . $e->getMessage());
    echo "Transaction failed: " . $e->getMessage();
}

// Close the statements and connection
$stmt->close();
$conn->close();
?>