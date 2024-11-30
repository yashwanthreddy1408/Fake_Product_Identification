<?php
session_start();
include 'connection.php'; // Include your database connection

// Check if user is signed in
if (!isset($_SESSION['id'])) {
    header("Location: signin.php");
    exit();
}

// Get user_id from session
$user_id = $_SESSION['id'];
$order_id = $_GET['order_id']; // Assume you are passing order_id in the URL

$sql = "SELECT order_id, first_name, last_name, booked_date, price, quantity, product_id FROM orders WHERE user_id = ? AND order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "<div class='text-center mt-10'><h2 class='text-lg font-semibold'>Order not found.</h2></div>";
    exit();
}

// Get product details based on product_id
$product_id = $order['product_id'];
$product_sql = "SELECT product_name, product_image1, average_rating, new_price, product_description FROM products WHERE product_id = ?";
$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product = $product_result->fetch_assoc();

// Calculate the status based on the booked date
$bookedDate = strtotime($order['booked_date']);
$fourDaysLater = strtotime("+4 days", $bookedDate);
$currentDate = strtotime(date('Y-m-d')); // Get current date

// Set status based on date comparison
$status = ($currentDate >= $fourDaysLater) ? "Delivered" : "In Transit";

// Format price in INR
function formatIndianNumber($number) {
    $number = (int)$number; // Remove decimals
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

$formattedPrice = formatIndianNumber($order['price']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Include Tailwind CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome for icons -->
</head>

<body class="bg-gray-100">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center mb-8">Order Details</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="p-4 border rounded-lg bg-gray-50 shadow-md">
                <dl>
                    <dt class="font-medium text-gray-700">Customer:</dt>
                    <dd class="font-semibold text-gray-800"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></dd>
                </dl>
            </div>

            <div class="p-4 border rounded-lg bg-gray-50 shadow-md">
                <dl>
                    <dt class="font-medium text-gray-700">Date Booked:</dt>
                    <dd class="font-semibold text-gray-800"><?= htmlspecialchars(date('d.m.Y', strtotime($order['booked_date']))) ?></dd>
                </dl>
            </div>

            <div class="p-4 border rounded-lg bg-gray-50 shadow-md">
                <dl>
                    <dt class="font-medium text-gray-700">Price:</dt>
                    <dd class="font-semibold text-gray-800">₹<?= $formattedPrice ?></dd>
                </dl>
            </div>

            <div class="p-4 border rounded-lg bg-gray-50 shadow-md">
                <dl>
                    <dt class="font-medium text-gray-700">Status:</dt>
                    <dd class="inline-flex items-center rounded <?= $status === 'Delivered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?> px-2.5 py-1 text-xs font-medium">
                        <i class="<?= $status === 'Delivered' ? 'fas fa-check-circle' : 'fas fa-truck' ?>"></i>
                        <?= htmlspecialchars($status) ?>
                    </dd>
                </dl>
            </div>
        </div>

        <h2 class="text-2xl font-bold mt-6 mb-4">Product Details</h2>
        <div class="mt-4 p-4 border rounded-lg bg-gray-50 shadow-md">
            <img src="<?= htmlspecialchars($product['product_image1']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="w-full h-64 object-cover rounded-lg mb-4" style="width:100px;">
            <h3 class="font-medium text-gray-700 text-xl"><?= htmlspecialchars($product['product_name']) ?></h3>
            <p class="text-gray-600">Price: ₹<?= formatIndianNumber($product['new_price']) ?></p>
            <p class="text-gray-600">Average Rating: <?= htmlspecialchars($product['average_rating']) ?></p>
            <p class="text-gray-600">Quantity: <?= htmlspecialchars($order['quantity']) ?></p>
            <p class="mt-2 text-gray-600"><?= htmlspecialchars($product['product_description']) ?></p>
        </div>

        <div class="mt-8 text-center">
            <a href="orderAgain.php?order_id=<?= htmlspecialchars($order_id) ?>" class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <?= $status === 'Delivered' ? 'Order Again' : 'Cancel Order' ?>
            </a>
        </div>
    </div>

</body>

</html>

<?php
// Close the statement and database connection
$stmt->close();
$product_stmt->close();
$conn->close();
?>
