<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: signin.php');
    exit();
}

// Check if the request method is GET and the product_id is set
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Ensure the product ID is valid
    if ($product_id > 0) {
        // Database connection details
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "securecart";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the product exists in the database
        $sql = "SELECT product_id FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Product exists, proceed with cart operations
            $user_id = $_SESSION['id'];

            // Check if the product is already in the cart
            $sql = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Product is already in the cart, update quantity
                $row = $result->fetch_assoc();
                $new_quantity = $row['quantity'] + 1;

                // Debugging: Print old and new quantities
                error_log("Product ID: " . $product_id . " is already in the cart.");
                error_log("Old Quantity: " . $row['quantity']);
                error_log("New Quantity: " . $new_quantity);

                $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $new_quantity, $row['cart_id']);
                $stmt->execute();
            } else {
                // Product is not in the cart, insert it with quantity 1
                // Debugging: Print action
                error_log("Product ID: " . $product_id . " is not in the cart. Inserting with quantity 1.");

                $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
            }

            // Redirect to the cart page
            header('Location: cart.php');
            exit();
        } else {
            error_log("Invalid product ID: " . $product_id);
            echo "Invalid product ID!";
        }

        $stmt->close();
        $conn->close();
    } else {
        error_log("Invalid product ID: " . $product_id);
        echo "Invalid product ID!";
    }
} else {
    error_log("Invalid request method or missing product_id.");
    echo "Invalid request method!";
}
