<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Summary - Thank You!</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>

<body class="bg-gray-100 dark:bg-gray-900">

    <?php
    // Include the database connection
    include 'connection.php';

    // Start the session to fetch session data
    session_start();

    $order_id = $_SESSION['order_id'] ?? $_GET['order_id'];

    $phone = ""; // Initialize phone 

    if ($order_id) {
        // Fetch order details from the `orders` table
        $order_query = "SELECT * FROM orders WHERE order_id = ?";
        if ($stmt = $conn->prepare($order_query)) {
            $stmt->bind_param("s", $order_id); // Changed to "s" since order_id is varchar
            $stmt->execute();
            $result = $stmt->get_result();
            if ($order = $result->fetch_assoc()) {
                // Extract order details
                $order_number = $order['order_id'];
                $booked_date = date("F d, Y", strtotime($order['booked_date']));
                $payment_method = $order['payment_method'];
                $name = $order['first_name'] . " " . $order['last_name'];
                $address = $order['address'] . ", " . $order['state'] . ", " . $order['zip'];
                $email = $order['email'];
                $quantity=$order['quantity'];
                $price = $order['price'] / $quantity;
                $product_id = $order['product_id'];
                $payment_method_display = ($payment_method === 'cod') ? 'Cash on Delivery' : 'Online Payment';
                // Fetch product name from the `products` table
                $pnamequery = "SELECT product_name,old_price,new_price FROM products WHERE product_id = ?";
                if ($pstmt = $conn->prepare($pnamequery)) {
                    $pstmt->bind_param("i", $product_id);
                    $pstmt->execute();
                    $presult = $pstmt->get_result();
                    if ($product = $presult->fetch_assoc()) {
                        $phone = $product['product_name']; // Store product name in $phone
                    }
                    $pstmt->close();
                }
            }
            $stmt->close();
        }
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

    <?php include 'navbar.php'; ?>

    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-3xl px-4 2xl:px-0">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl mb-2">Thanks for your order!</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6 md:mb-8">
                Your order
                <a href="#" class="font-medium text-gray-900 dark:text-white hover:underline">#<?php echo $order_number; ?></a>
                will be processed within 24 hours during working days. We will notify you by email once your order has been shipped.
            </p>

            <div class="space-y-6 sm:space-y-4 rounded-lg border border-gray-100 bg-gray-50 p-8 min-h-[250px] dark:border-gray-700 dark:bg-gray-800 mb-6 md:mb-8">
                <dl class="flex justify-between gap-4">
                    <dt class="font-normal text-gray-500 dark:text-gray-400 text-right">Date</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right w-full sm:w-2/3"><?php echo $booked_date; ?></dd>
                </dl>
                <dl class="flex justify-between gap-4">
                    <dt class="font-normal text-gray-500 dark:text-gray-400 text-right">Payment Method</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right w-full sm:w-2/3"><?php echo $payment_method_display; ?></dd>
                </dl>
                <dl class="flex justify-between gap-4">
                    <dt class="font-normal text-gray-500 dark:text-gray-400 text-right">Name</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right w-full sm:w-2/3"><?php echo $name; ?></dd>
                </dl>
                <dl class="flex justify-between gap-4">
                    <dt class="font-normal text-gray-500 dark:text-gray-400 text-right">Address</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right w-full sm:w-2/3"><?php echo $address; ?></dd>
                </dl>
                <dl class="flex justify-between gap-4">
                    <dt class="font-normal text-gray-500 dark:text-gray-400 text-right">Product Name</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right w-full sm:w-2/3"><?php echo $phone; ?></dd>
                </dl>
                <dl class="flex justify-between gap-4">
                    <dt class="font-normal text-gray-500 dark:text-gray-400 text-right">Price</dt>
                    <dd class="font-medium text-gray-900 dark:text-white text-right w-full sm:w-2/3"><?php echo $quantity; ?> x <?php echo formatIndianNumber($price) ;?> = <?php echo formatIndianNumber($price*$quantity) ;?></dd>
                </dl>
            </div>

            <div class="flex flex-col items-center space-y-4">
                <!-- Track Order Button -->
                <a href="track_order.php?order_id=<?php echo $order_id; ?>" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">
                    Track your order
                </a>

                <!-- Return to Shopping Button -->
                <a href="index.php" class="py-3 px-5 text-sm font-medium text-blue-600 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Return to shopping
                </a>
            </div>
        </div>
    </section>


    <!-- Footer (optional) -->
    <?php include 'footer.php'; ?>

    <!-- JavaScript links -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>