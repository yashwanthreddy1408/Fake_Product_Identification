<?php
// Include the database connection
include 'connection.php';
session_start();
$user_id = $_SESSION['id'];

// Retrieve form inputs from GET request
$firstName = $_GET['firstName'] ?? '';
$lastName = $_GET['lastName'] ?? '';
$email = $_GET['email'] ?? '';
$username = $_GET['username'] ?? '';
$address = $_GET['address'] ?? '';
$address2 = $_GET['address2'] ?? '';
$country = $_GET['country'] ?? '';
$state = $_GET['state'] ?? '';
$zip = $_GET['zip'] ?? '';
$paymentMethod = $_GET['paymentMethod'] ?? '';

// Set the booking date to the current date
$bookedDate = date("Y-m-d H:i:s"); // Format as needed

// Variables for cart or product booking
$cart_id = isset($_GET['cart_id']) ? intval($_GET['cart_id']) : 0; // Ensure cart_id is an integer
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0; // For direct product booking

// Initialize variables for cart/product details
$cartDetails = [];
$originalPrice = 0;
$totalItemPrice = 0;
$savings = 0;

if ($cart_id > 0) {
    // Booking from the cart
    $query = "SELECT 
                p.product_name, 
                c.quantity, 
                p.old_price, 
                p.new_price, 
                (c.quantity * p.new_price) AS total_item_price,
                (p.old_price - p.new_price) AS savings
              FROM 
                cart c
              JOIN 
                products p ON c.product_id = p.product_id
              WHERE 
                c.cart_id = ?";

    // Use a prepared statement for cart details
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cartDetails = $result->fetch_all(MYSQLI_ASSOC);

        // Calculate original price, savings, and total price
        foreach ($cartDetails as $item) {
            $originalPrice += $item['old_price'] * $item['quantity'];
            $totalItemPrice += $item['new_price'] * $item['quantity'];
            $savings += ($item['old_price'] - $item['new_price']) * $item['quantity'];
        }
        $stmt->close();
    } else {
        echo "Error preparing cart query: " . $conn->error;
    }
} elseif ($product_id > 0) {
    // Direct booking using product_id
    $query = "SELECT 
                product_name, 
                old_price, 
                new_price, 
                1 AS quantity,  -- Direct booking assumes quantity 1
                (new_price) AS total_item_price,
                (old_price - new_price) AS savings
              FROM 
                products 
              WHERE 
                product_id = ?";

    // Use a prepared statement for product details
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $productDetails = $result->fetch_assoc();

        if ($productDetails) {
            $cartDetails[] = $productDetails; // Treat as single-item cart
            $originalPrice += $productDetails['old_price'];
            $totalItemPrice += $productDetails['new_price'];
            $savings += $productDetails['old_price'] - $productDetails['new_price'];
        } else {
            echo "No product found for the given product ID.";
        }
        $stmt->close();
    } else {
        echo "Error preparing product query: " . $conn->error;
    }
} else {
    echo "No cart ID or product ID provided.";
}

// Format prices using the Indian format (optional)
function formatIndianNumber($number) {
    $number = (int)$number;
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
$_SESSION['cart_id'] = $cart_id;
$_SESSION['product_id'] = $product_id;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .text-sm {
            font-size: 17px;
            line-height: 1.25rem;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16 w-full">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mx-auto max-w-5xl bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl text-center">Payment Details</h2>

                <div class="mt-8 lg:flex lg:items-start lg:gap-16 justify-center">
                    <!-- Payment Form -->
                    <form id="paymentForm" data-cart-id="<?php echo $cart_id; ?>" class="w-full rounded-lg p-6 shadow-md lg:max-w-xl" action="processing_order.php" method="POST">
                        <input type="hidden" name="firstName" value="<?= htmlspecialchars($firstName); ?>">
                        <input type="hidden" name="lastName" value="<?= htmlspecialchars($lastName); ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username); ?>">
                        <input type="hidden" name="address" value="<?= htmlspecialchars($address); ?>">
                        <input type="hidden" name="address2" value="<?= htmlspecialchars($address2); ?>">
                        <input type="hidden" name="country" value="<?= htmlspecialchars($country); ?>">
                        <input type="hidden" name="state" value="<?= htmlspecialchars($state); ?>">
                        <input type="hidden" name="zip" value="<?= htmlspecialchars($zip); ?>">
                        <input type="hidden" name="paymentMethod" value="<?= htmlspecialchars($paymentMethod); ?>">
                        <input type="hidden" name="cart_id" value="<?= intval($cart_id); ?>">
                        <input type="hidden" name="user_id" value="<?= intval($user_id); ?>">
                        <input type="hidden" name="product_id" value="<?= intval($product_id); ?>">

                        <div class="grid grid-cols-2 gap-6">
    <div class="col-span-2 sm:col-span-1">
        <label for="full_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Full Name on Card*</label>
        <input type="text" id="full_name" name="full_name" class="block w-full rounded-lg p-3 text-base focus:ring-blue-500" placeholder="John Doe" required />
    </div>

    <div class="col-span-2 sm:col-span-1">
    <label for="card-number-input" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Card Number* </label>
    <input type="text" id="card-number-input" name="card_number" class="block w-full rounded-lg p-3 text-base focus:ring-blue-500" placeholder="xxxx-xxxx-xxxx-xxxx" maxlength="19" required 
           oninput="this.value = this.value.replace(/\D/g, '').replace(/(.{4})/g, '$1-').trim().slice(0, 19);" />
</div>


    <div class="col-span-1">
        <label for="card-expiration-input" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Expiration Date (MM/YYYY)* </label>
        <input type="text" id="card-expiration-input" name="card_expiration" class="block w-full rounded-lg p-3 text-base focus:ring-blue-500" placeholder="MM/YYYY" required maxlength="7" pattern="(0[1-9]|1[0-2])\/\d{4}" />
    </div>

    <div class="col-span-1">
        <label for="cvv-input" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> CVV* </label>
        <input type="text" id="cvv-input" name="cvv" class="block w-full rounded-lg p-3 text-base focus:ring-blue-500" placeholder="•••" maxlength="3" pattern="\d{3}" required oninput="this.value = this.value.replace(/\D/g, '');" />
    </div>
</div>

<button type="submit" class="mt-6 w-full rounded-lg bg-blue-600 px-5 py-3 text-lg font-semibold text-white hover:bg-blue-700 focus:outline-none">Complete Payment</button>

                    </form>

                    <!-- Payment Summary -->
                    <div class="mt-10 lg:mt-0 lg:ml-12">
                        <div class="rounded-lg bg-gray-50 p-6 shadow-md dark:bg-gray-800">
                            <dl class="space-y-4 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600 dark:text-gray-400">Original Price</dt>
                                    <dd class="text-gray-900 dark:text-white">₹<?php echo htmlspecialchars($originalPrice); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600 dark:text-gray-400">Savings</dt>
                                    <dd class="text-green-600">₹<?php echo htmlspecialchars($savings); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600 dark:text-gray-400">Store Pickup</dt>
                                    <dd class="text-gray-900 dark:text-white">₹0</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600 dark:text-gray-400">Tax</dt>
                                    <dd class="text-gray-900 dark:text-white">₹0</dd>
                                </div>
                            </dl>

                            <div class="mt-6 border-t pt-4 dark:border-gray-700">
                                <dl class="flex justify-between text-lg font-semibold">
                                    <dt class="text-gray-900 dark:text-white">Total</dt>
                                    <dd class="text-gray-900 dark:text-white">₹<?php echo htmlspecialchars($totalItemPrice); ?></dd>
                                </dl>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-center space-x-8">
                            <img src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/brand-logos/paypal.svg" class="h-8 w-auto" alt="PayPal" />
                            <img src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/brand-logos/visa.svg" class="h-8 w-auto" alt="Visa" />
                            <img src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/brand-logos/mastercard.svg" class="h-8 w-auto" alt="Mastercard" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
// Initialize Flatpickr for the Expiration Date
flatpickr("#card-expiration-input", {
    enableTime: false,
    dateFormat: "m/Y",
    altInput: true,
    altFormat: "m/Y",
    onChange: function(selectedDates, dateStr, instance) {
        // Custom formatting can be applied here
        if (dateStr) {
            const [month, year] = dateStr.split('/');
            document.getElementById("card-expiration-input").value = `${month}/${year}`;
        }
    },
    // Prevent past dates
    minDate: new Date(),
    allowInput: true,
});
</script>
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting immediately

            // Validate the form (assuming validateForm() is your validation function)
            if (!validateForm()) {
                return; // Stop the process if validation fails
            }

            // Use SweetAlert for confirmation
            Swal.fire({
                title: 'Are you sure you want to proceed with this purchase?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, buy it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show success message
                    Swal.fire({
                        title: 'Transaction Successful!',
                        text: 'Thank you for your purchase.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        const form = document.getElementById('paymentForm');

                        // Function to serialize form data
                        function getFormData(form) {
                            const formData = new FormData(form);
                            const data = {};
                            formData.forEach((value, key) => {
                                data[key] = value;
                            });
                            return data;
                        }
                        const formData = getFormData(form);
                        const queryParams = new URLSearchParams(formData).toString();
                        form.action = 'processing_order.php?' + queryParams;

                        // Use data attribute for cart_id if you prefer cleaner JavaScript
                        const cartId = document.getElementById('paymentForm').getAttribute('data-cart-id');

                        // Append cart_id as hidden input
                        const cartIdInput = document.createElement('input');
                        cartIdInput.type = 'hidden';
                        cartIdInput.name = 'cart_id';
                        cartIdInput.value = cartId;
                        form.appendChild(cartIdInput);

                        // Append all other form inputs
                        Array.from(document.querySelectorAll('#paymentForm input')).forEach((input) => {
                            if (input.name && input.name !== 'cart_id') { // Avoid adding cart_id again
                                const inputClone = document.createElement('input');
                                inputClone.type = 'hidden';
                                inputClone.name = input.name;
                                inputClone.value = input.value;
                                form.appendChild(inputClone);
                            }
                        });

                        // Finally, append and submit the form
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
            });
        });

        function validateForm() {
            // Check for empty required fields
            const fullName = document.getElementById('full_name').value;
            const cardNumber = document.getElementById('card-number-input').value;
            const expirationDate = document.getElementById('card-expiration-input').value;
            const cvv = document.getElementById('cvv-input').value;

            if (!fullName || !cardNumber || !expirationDate || !cvv) {
                Swal.fire('Please fill in all required fields.');
                return false; // Form is not valid
            }
            return true; // Form is valid
        }
    </script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            "50": "#eff6ff",
                            "100": "#dbeafe",
                            "200": "#bfdbfe",
                            "300": "#93c5fd",
                            "400": "#60a5fa",
                            "500": "#3b82f6",
                            "600": "#2563eb",
                            "700": "#1d4ed8",
                            "800": "#1e40af",
                            "900": "#1e3a8a",
                            "950": "#172554"
                        }
                    }
                },
                fontFamily: {
                    'body': [
                        'Inter',
                        'ui-sans-serif',
                        'system-ui',
                        '-apple-system',
                        'system-ui',
                        'Segoe UI',
                        'Roboto',
                        'Helvetica Neue',
                        'Arial',
                        'Noto Sans',
                        'sans-serif',
                        'Apple Color Emoji',
                        'Segoe UI Emoji',
                        'Segoe UI Symbol',
                        'Noto Color Emoji'
                    ],
                    'sans': [
                        'Inter',
                        'ui-sans-serif',
                        'system-ui',
                        '-apple-system',
                        'system-ui',
                        'Segoe UI',
                        'Roboto',
                        'Helvetica Neue',
                        'Arial',
                        'Noto Sans',
                        'sans-serif',
                        'Apple Color Emoji',
                        'Segoe UI Emoji',
                        'Segoe UI Symbol',
                        'Noto Color Emoji'
                    ]
                }
            }
        }
    </script>
</body>

</html>