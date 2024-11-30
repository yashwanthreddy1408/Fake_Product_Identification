<?php
// Include the connection.php file for the database connection
include 'connection.php';

// Start the session
session_start();
$first_name = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$last_name = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$address2 = isset($_POST['address2']) ? trim($_POST['address2']) : '';
$country = isset($_POST['country']) ? trim($_POST['country']) : '';
$state = isset($_POST['state']) ? trim($_POST['state']) : '';
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : '';
$payment_method = isset($_POST['paymentMethod']) ? trim($_POST['paymentMethod']) : '';
$cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
$product_id_direct = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
// Set the booking date to the current date
$booked_date = date("Y-m-d H:i:s");

$cart_items = [];
$total_price = 0;

if ($cart_id > 0) {
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
        $total_price += $row['total_item_price']; // Sum total item prices
    }

} elseif ($product_id_direct > 0) {
    // Fetch product details directly based on product_id
    $query = "SELECT product_id, product_name, new_price FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id_direct);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the product exists
    if ($result->num_rows == 0) {
        die("Error: Product not found.");
    }

    // Fetch product details and calculate total price for a single item
    $row = $result->fetch_assoc();
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default quantity to 1
    $total_item_price = $row['new_price'] * $quantity;
    
    $cart_items[] = [
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'],
        'quantity' => $quantity,
        'new_price' => $row['new_price'],
        'total_item_price' => $total_item_price
    ];

    $total_price += $total_item_price;
} else {
    die("Error: No valid cart_id or product_id provided.");
}

// Begin transaction for placing the order
$conn->begin_transaction();

try {
    // Generate a custom order ID
    $random_number = rand(1000, 9999); // Generate a random number (adjust range as needed)
    $custom_order_id = "OIDSC" . $random_number . "FPI"; // Create custom order ID

    // Loop through each item in the cart to process the order
    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        // Prepare the SQL statement
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

    // Remove all items from the cart if cart_id is provided
    if ($cart_id > 0) {
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
        $delete_stmt->bind_param("i", $cart_id);
        $delete_stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Store the custom order_id in the session
    $_SESSION['order_placed'] = true;
    $_SESSION['order_id'] = $custom_order_id; // Save custom order ID to session

    // Redirect to the order placed confirmation page
    header("Location: orderSummary.php");
    exit();
} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();
    echo "Transaction failed: " . $e->getMessage();
}

// Close the statements and connection
$stmt->close();
$conn->close();

?>
