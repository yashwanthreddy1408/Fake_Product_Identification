<?php
// Include the database connection
include 'connection.php';

// Start the session to fetch session data
session_start();

$order_id = $_SESSION['order_id'] ?? ($_POST['order_id'] ?? null) ?? ($_GET['order_id'] ?? null);

if ($order_id) {
    // Fetch order details from the `orders` table
    $order_query = "SELECT * FROM orders WHERE order_id = ?";
    if ($stmt = $conn->prepare($order_query)) {
        $stmt->bind_param("s", $order_id); // Assuming order_id is a string
        $stmt->execute();
        $result = $stmt->get_result();

        if ($order = $result->fetch_assoc()) {
            // Extract order details
            $order_number = $order['order_id'];
            $quantity = $order['quantity'];
            $booked_date = date("Y-m-d", strtotime($order['booked_date'])); // Store as Y-m-d for date comparison
            $payment_method = $order['payment_method'];
            $name = $order['first_name'] . " " . $order['last_name'];
            $address = $order['address'] . ", " . $order['address2'] . ", " . $order['state'] . ", " . $order['zip'];
            $email = $order['email'];
            $product_id = $order['product_id'];
            $payment_method_display = ($payment_method === 'cod') ? 'Cash on Delivery' : 'Online Payment';

            // Fetch product details from the `products` table
            $pnamequery = "SELECT product_name, old_price, new_price, product_image1 FROM products WHERE product_id = ?";
            if ($pstmt = $conn->prepare($pnamequery)) {
                $pstmt->bind_param("i", $product_id);
                $pstmt->execute();
                $presult = $pstmt->get_result();

                if ($product = $presult->fetch_assoc()) {
                    $product_name = $product['product_name'];
                    $product_image = $product['product_image1'];
                    $original_price = $product['new_price'] * $quantity;
                    $old_price = $product['old_price'] * $quantity;
                    $savings = ($old_price) - ($original_price);
                    $store_pickup = 0;
                    $tax = 0;
                    $total = $original_price + $tax; // Updated total to include tax

                    // Prepare tracking stages (1 day for each stage)
                    $tracking_stages = [
                        'Order Placed' => 0,
                        'Processing' => 1,
                        'Shipped' => 2,
                        'Out for Delivery' => 3,
                        'Delivered' => 4 // Last stage
                    ];

                    // Prepare the stages array dynamically
                    $stages = [];
                    foreach ($tracking_stages as $description => $days) {
                        $stage_date = date("F d, Y", strtotime($booked_date . " +$days days"));
                        $stages[] = [
                            'date' => $stage_date,
                            'description' => $description
                        ];
                    }

                    // Determine current stage index based on today's date
                    $current_stage_index = 0; // Default to the first stage
                    $current_date = date("Y-m-d");
                    foreach ($stages as $index => $stage) {
                        if ($current_date > date("Y-m-d", strtotime($stage['date']))) {
                            $current_stage_index = $index; // Update index to the last completed stage
                        } else {
                            break; // Stop at the first future stage
                        }
                    }

                    // If the current date exceeds the last stage's date, mark as delivered
                    if ($current_date > date("Y-m-d", strtotime($stages[count($stages) - 1]['date']))) {
                        $current_stage_index = count($stages) - 1; // Set to last stage
                    }
                }
                $pstmt->close();
            }
        }
        $stmt->close();
    }
} else {
    echo "Order ID not provided.";
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Tracking</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        .tracking-container {
            position: relative;
            /* To position the tracking items */
            margin-left: 40px;
            /* Space for the vertical line */
        }

        .tracking-item {
            margin-bottom: 2rem;
            position: relative;
            /* To position the line */
            padding-left: 30px;
            /* Add padding for the text */
        }

        .tracking-item::before {
            content: '';
            position: absolute;
            left: 10px;
            /* Position the line to the left of the text */
            top: 0;
            /* Start from the top of the container */
            height: 100%;
            /* Full height of the tracking item */
            width: 2px;
            /* Width of the line */
            background-color: black;
            /* Change the color of the line to black */
            z-index: -1;
            /* Ensure the line is behind the text */
        }

        .tracking-item span {
            position: absolute;
            left: -1.5rem;
            /* Position the circle slightly left */
            top: 0.5rem;
            /* Adjust the vertical position of the circle */
        }

        .tracking-item h4 {
            margin-top: 0.5rem;
        }

        .tracking-item p {
            margin-top: 0.25rem;
        }

        .flex-container {
            display: flex;
            gap: 20px;
        }

        .order-history {
            flex-grow: 1;
        }

        .product-details {
            flex-basis: 60%;
        }
    </style>
</head>
<?php include 'navbar.php'; ?>

<body class="bg-gray-100 dark:bg-gray-900">
    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Track the delivery of order #<?php echo htmlspecialchars($order_id); ?></h2>

            <div class="mt-6 sm:mt-8 flex-container">
                <!-- Product Details Container -->
                <div class="product-details w-full divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200 dark:divide-gray-700 dark:border-gray-700 lg:max-w-xl xl:max-w-2xl">
                    <div class="space-y-4 p-6">
                        <div class="flex items-center gap-6">
                            <a href="product_details.php?product_id=<?php echo htmlspecialchars($product_id); ?>" class="h-14 w-14 shrink-0">
                                <img class="h-full w-full dark:hidden" src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>" />
                            </a>
                            <a href="product_details.php?product_id=<?php echo htmlspecialchars($product_id); ?>" class="min-w-0 flex-1 font-medium text-gray-900 hover:underline dark:text-white">
                                <?php echo htmlspecialchars($product_name); ?>
                            </a>
                        </div>

                        <div class="space-y-4 bg-gray-50 p-6 dark:bg-gray-800">
                            <div class="space-y-2">
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="font-normal text-gray-500 dark:text-gray-400">Original Price</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">₹<?php echo formatIndianNumber($old_price); ?></dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="font-normal text-gray-500 dark:text-gray-400">Savings</dt>
                                    <dd class="text-base font-medium text-green-500">-₹<?php echo formatIndianNumber($savings); ?></dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="font-normal text-gray-500 dark:text-gray-400">Store Pickup</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">₹<?php echo formatIndianNumber($store_pickup); ?></dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="font-normal text-gray-500 dark:text-gray-400">Tax</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">₹<?php echo formatIndianNumber($tax); ?></dd>
                                </dl>
                            </div>
                            <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                <dt class="text-lg font-bold text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-lg font-bold text-gray-900 dark:text-white">₹<?php echo formatIndianNumber($total); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Tracking Information -->
                <!-- Tracking Information -->
        <div class="order-history w-full md:max-w-2xl">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Tracking</h3>
            <div class="tracking-container">
    <?php
    $current_date = date("Y-m-d");
    foreach ($stages as $index => $stage):
        $stage_date = date("Y-m-d", strtotime($stage['date']));
        $isPastStage = $stage_date < $current_date; // Check if the stage date is in the past
        $isTodayStage = $stage_date === $current_date; // Check if the stage date is today
    ?>
        <div class="tracking-item">
            <span class="h-6 w-6 rounded-full border-2 <?php echo $isPastStage ? 'border-green-600 bg-green-600' : ($isTodayStage ? 'border-blue-600 bg-blue-600' : 'border-gray-300 bg-gray-200'); ?> flex items-center justify-center">
                <i class="fas <?php echo $isPastStage ? 'fa-check text-white' : ($isTodayStage ? 'fa-clock text-white' : 'fa-circle text-gray-400'); ?>"></i>
            </span>
            <h4 class="text-md font-semibold <?php echo $isPastStage ? 'text-green-600' : ($isTodayStage ? 'text-blue-600' : 'text-gray-400'); ?>"><?php echo htmlspecialchars($stage['description']); ?></h4>
            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($stage['date']); ?></p>
        </div>
    <?php endforeach; ?>
</div>
        </div>

            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>

</html>
