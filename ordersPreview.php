<?php
session_start(); // Start the session

// Check if user is signed in
if (!isset($_SESSION['id'])) {
    header("Location: signin.php"); // Redirect to signin.php if not logged in
    exit(); // Stop further execution
}

$host = 'localhost'; // your database host
$db = 'securecart'; // your database name
$user = 'root'; // your database username
$pass = ''; // your database password

// Create a MySQLi connection
$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Assume user_id is obtained from session after user logs in
$user_id = $_SESSION['id']; // Fetch the logged-in user's ID

// Function to format Indian number
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

// Fetch orders for the logged-in user
$sql = "SELECT o.order_id, o.first_name, o.last_name, o.username, o.email, o.address, o.address2, o.country, o.state, o.zip, o.payment_method, o.price, o.booked_date, o.quantity, p.product_image1 
        FROM orders o 
        JOIN products p ON o.product_id = p.product_id 
        WHERE o.user_id = ? 
        ORDER BY o.booked_date DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id); // Bind the user_id parameter
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom styles */
        body {
            background-color: #f8fafc;
            /* Light gray background */
        }

        .container {
            max-width: 1200px;
            /* Limit max width for better spacing */
        }

        .status-delivered {
            color: #34D399;
            /* Green for Delivered */
        }

        .status-in-transit {
            color: #FFB02E;
            /* Yellow for In Transit */
        }

        .status-canceled {
            color: #EF4444;
            /* Red for Canceled */
        }

        .order-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .button {
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #e5e7eb;
            /* Gray for hover effect */
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Your Orders</h1>
        <div class="mt-6 flow-root">
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <div class="order-card flex flex-wrap items-center gap-y-4">
                            <div class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                <img src="<?= htmlspecialchars($order['product_image1']) ?>" alt="Product Image" class="w-24 h-24 object-cover rounded-md">
                            </div>
                            <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Order ID:</dt>
                                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-black">
                                    <a href="#" style="color:black;" class="hover:underline">#<?= htmlspecialchars($order['order_id']) ?></a>
                                </dd>
                            </dl>

                            <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Customer:</dt>
                                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white" style="padding-right:5px;">
                                    <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>
                                </dd>
                            </dl>

                            <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Date Booked:</dt>
                                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars(date('d.m.Y', strtotime($order['booked_date']))) ?></dd>
                            </dl>

                            <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Price:</dt>
                                <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">₹ <?= formatIndianNumber($order['price']) ?></dd>
                            </dl>

                            <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Status:</dt>
                                <dd class="me-2 mt-1.5 inline-flex items-center rounded px-2.5 py-0.5 text-xs font-medium dark:bg-primary-900 dark:text-primary-300">
                                    <?php
                                    // Calculate the status based on the booked date
                                    $bookedDate = strtotime($order['booked_date']);
                                    $fourDaysLater = strtotime("+4 days", $bookedDate);
                                    $currentDate = time();

                                    // Set status based on date comparison
                                    $status = ($currentDate > $fourDaysLater) ? "Delivered" : "In Transit";
                                    $statusClass = ($status == "Delivered") ? "status-delivered" : "status-in-transit";
                                    $statusIcon = ($status == "Delivered") ? "fas fa-check-circle" : "fas fa-truck";
                                    ?>
                                    <i class="<?= $statusIcon ?> <?= $statusClass ?> mr-1"></i>
                                    <?= htmlspecialchars($status) ?>
                                </dd>
                            </dl>

                            <div class="w-full grid sm:grid-cols-2 lg:flex lg:w-64 lg:items-center lg:justify-end gap-4">
                                <?php if ($status === "Delivered"): ?>
                                    <a href="orderAgain.php?order_id=<?= $order['order_id'] ?>" class="button w-full rounded-lg border border-green-600 bg-white px-3 py-2 text-center text-sm font-medium text-green-600 hover:bg-green-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-green-300 dark:border-green-500 dark:text-green-500 dark:hover:bg-green-600 dark:hover:text-white dark:focus:ring-green-900 lg:w-auto">Order Again</a>
                                    <a href="orderSummary.php?order_id=<?= $order['order_id'] ?>" class="button w-full rounded-lg border border-green-600 bg-white px-3 py-2 text-center text-sm font-medium text-green-600 hover:bg-green-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-green-300 dark:border-green-500 dark:text-green-500 dark:hover:bg-green-600 dark:hover:text-white dark:focus:ring-green-900 lg:w-auto">Order Details</a>

                                <?php else: ?>
                                    <a href="track_order.php?order_id=<?= $order['order_id'] ?>" class="button w-full rounded-lg border border-blue-600 bg-white px-3 py-2 text-center text-sm font-medium text-blue-700 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-4 focus:ring-red-300 dark:border-red-500 dark:text-red-500 dark:hover:bg-blue-600 dark:hover:text-white dark:focus:ring-blue-900 lg:w-auto">Track Order</a>
                                    <a href="orderSummary.php?order_id=<?= $order['order_id'] ?>" class="button w-full rounded-lg border border-green-600 bg-white px-3 py-2 text-center text-sm font-medium text-green-700 hover:bg-green-700 hover:text-white focus:outline-none focus:ring-4 focus:ring-red-300 dark:border-red-500 dark:text-red-500 dark:hover:bg-green-600 dark:hover:text-white dark:focus:ring-green-900 lg:w-auto">Order Details</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500">You have no orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>