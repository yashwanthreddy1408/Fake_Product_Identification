<?php
// Include the database connection and session start
include 'navbar.php'; // Assuming the navbar file is included for header navigation
include 'connection.php'; // Your database connection

// Check if order_id is available in the URL or session
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
}

// Fetch the order details from the database
$query = "SELECT o.orderid, o.total_price, o.created_at, o.payment_method, o.shipping_address, u.name AS customer_name
          FROM orderst o
          JOIN users u ON o.user_id = u.user_id
          WHERE o.orderid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "<p>Invalid order ID.</p>";
    exit();
}

// Fetch the products in the order
$query_items = "SELECT od.quantity, od.price, p.product_name 
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
$stmt_items = $conn->prepare($query_items);
$stmt_items->bind_param('i', $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your custom CSS here -->
</head>

<body>

    <div class="order-confirmation">
        <h1>Order Confirmation</h1>
        <p>Thank you for your purchase, <?php echo $order['customer_name']; ?>!</p>

        <h2>Order Summary</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <td><?php echo $order['order_id']; ?></td>
            </tr>
            <tr>
                <th>Total Price</th>
                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
            </tr>
            <tr>
                <th>Order Date</th>
                <td><?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?></td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td><?php echo $order['payment_method']; ?></td>
            </tr>
            <tr>
                <th>Shipping Address</th>
                <td><?php echo $order['shipping_address']; ?></td>
            </tr>
        </table>

        <h2>Items Purchased</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Total: $<?php echo number_format($order['total_price'], 2); ?></h3>

        <p>Your order will be shipped to: <?php echo $order['shipping_address']; ?></p>
        <p>If you have any questions about your order, please contact us.</p>

        <a href="index.php" class="btn">Continue Shopping</a>
    </div>

    <?php include 'footer.php'; // Include the footer file for consistent design 
    ?>
</body>

</html>

<?php
$stmt->close();
$stmt_items->close();
$conn->close();
?>