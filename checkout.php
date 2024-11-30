<?php
// Assuming you have a database connection file called 'db_connection.php'
include 'connection.php';
session_start(); // Start the session

// Retrieve user ID from the session (ensure the user is logged in)
$user_id = $_SESSION['id']; // Get user ID from session
if (!$user_id) {
    die('User not logged in'); // Handle the error if user is not logged in
}

// Check if the user exists
$user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
$user_check->bind_param("i", $user_id);
$user_check->execute();
$user_check->store_result();

if ($user_check->num_rows === 0) {
    die('User not found'); // Handle the case where the user does not exist
}

$cart_id = isset($_GET['cart_id']) ? intval($_GET['cart_id']) : 0;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$cart_items = [];
$total_price = 0;

if ($cart_id) {
    // Cart checkout logic
    $stmt = $conn->prepare("
        SELECT p.product_name, c.quantity, p.new_price, (c.quantity * p.new_price) as total_item_price
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.cart_id = ?
    ");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['total_item_price'];
    }
    $stmt->close();
} elseif ($product_id) {
    // Single product checkout logic
    $stmt = $conn->prepare("
        SELECT product_name, new_price 
        FROM products 
        WHERE product_id = ?
    ");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Assuming quantity is 1 since it's a direct purchase
        $row['quantity'] = 1;
        $row['total_item_price'] = $row['new_price']; // total price for one item

        $cart_items[] = $row;
        $total_price = $row['total_item_price'];
    }
    $stmt->close();
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

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>SecureCart - Checkout</title>
    <style>
        .container {
            max-width: 960px;
        }

        .buy-now {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-family: 'Arial', sans-serif;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .buy-now:hover {
            background-color: #0b5ed7;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }

        .buy-now:active {
            background-color: #0a58ca;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.15);
            transform: scale(0.98);
        }

        .buy-now:focus {
            outline: none;
            box-shadow: 0px 0px 0px 3px rgba(13, 110, 253, 0.4);
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container" style="margin-bottom:50px;">
        <main>
            <div class="py-5 text-center">
                <h2><i class="fas fa-shopping-cart" style="color:black;"></i> Checkout Form</h2>
            </div>
            <div class="row g-5">
                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">Your cart</span>
                        <span class="badge bg-primary rounded-pill"><?php echo count($cart_items); ?></span>
                    </h4>
                    <ul class="list-group mb-3">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                    <small class="text-muted">Quantity: <?php echo htmlspecialchars($item['quantity']); ?></small>
                                </div>
                                <span class="text-muted">₹<?php echo formatIndianNumber($item['total_item_price']); ?></span>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total</span>
                            <strong>₹<?php echo formatIndianNumber($total_price); ?></strong>
                        </li>
                    </ul>

                    <form class="card p-2" id="promoForm">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Promo code" id="promoCode">
                            <button type="button" class="btn btn-secondary" onclick="applyPromo()">Redeem</button>
                        </div>
                        <div class="invalid-feedback">
                            This Promo code doesn't exist or is unavailable.
                        </div>
                    </form>
                </div>
                <div class="col-md-7 col-lg-8">
                    <h4 class="mb-3">Billing Address</h4>
                    <form class="needs-validation" id="checkoutForm" method="POST" action="process_order.php">
                        <div class="row g-3">
                            <!-- First name, Last name, Username, Email, Address fields here -->
                            <div class="col-sm-6">
                                <input type="hidden" name="cart_id" value="<?php echo $cart_id; ?>">
                                <label for="firstName" class="form-label">First name</label>
                                <input type="text" class="form-control" name="firstName" id="firstName" placeholder="John" value="" required>
                                <div class="invalid-feedback">Valid first name is required.</div>
                            </div>

                            <div class="col-sm-6">
                                <label for="lastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Smith" value="" required>
                                <div class="invalid-feedback">Valid last name is required.</div>
                            </div>

                            <div class="col-12">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                                    <div class="invalid-feedback">Your username is required.</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com">
                                <div class="invalid-feedback">Please enter a valid email address for shipping updates.</div>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" id="address" placeholder="1234 Main St" required>
                                <div class="invalid-feedback">Please enter your shipping address.</div>
                            </div>

                            <div class="col-md-5">
    <label for="country" class="form-label">Country</label>
    <select class="form-select" name="country" id="country" required>
        <option value="">Choose...</option>
        <option value="India">India</option>
        <option value="USA">United States</option>
        <option value="Canada">Canada</option>
        <option value="Australia">Australia</option>
        <option value="UK">United Kingdom</option>
        <option value="Germany">Germany</option>
        <option value="France">France</option>
        <option value="Japan">Japan</option>
        <option value="China">China</option>
        <option value="Brazil">Brazil</option>
    </select>
    <div class="invalid-feedback">Please select a valid country.</div>
</div>

<div class="col-md-4">
    <label for="state" class="form-label">State</label>
    <select class="form-select" name="state" id="state" required>
        <option value="">Choose...</option>
        <!-- States for India -->
        <option data-country="India">Andhra Pradesh</option>
        <option data-country="India">Arunachal Pradesh</option>
        <option data-country="India">Assam</option>
        <option data-country="India">Bihar</option>
        <option data-country="India">Chhattisgarh</option>
        <option data-country="India">Goa</option>
        <option data-country="India">Gujarat</option>
        <option data-country="India">Haryana</option>
        <option data-country="India">Himachal Pradesh</option>
        <option data-country="India">Jharkhand</option>
        <option data-country="India">Karnataka</option>
        <option data-country="India">Kerala</option>
        <option data-country="India">Madhya Pradesh</option>
        <option data-country="India">Maharashtra</option>
        <option data-country="India">Manipur</option>
        <option data-country="India">Meghalaya</option>
        <option data-country="India">Mizoram</option>
        <option data-country="India">Nagaland</option>
        <option data-country="India">Odisha</option>
        <option data-country="India">Punjab</option>
        <option data-country="India">Rajasthan</option>
        <option data-country="India">Sikkim</option>
        <option data-country="India">Tamil Nadu</option>
        <option data-country="India">Telangana</option>
        <option data-country="India">Tripura</option>
        <option data-country="India">Uttar Pradesh</option>
        <option data-country="India">Uttarakhand</option>
        <option data-country="India">West Bengal</option>
        <option data-country="India">Delhi</option>
        <option data-country="India">Puducherry</option>
        <option data-country="India">Chandigarh</option>
        <option data-country="India">Lakshadweep</option>
        <option data-country="India">Dadra and Nagar Haveli and Daman and Diu</option>
        
        <!-- States for USA -->
        <option data-country="USA">California</option>
        <option data-country="USA">Texas</option>
        <option data-country="USA">New York</option>
        <option data-country="USA">Florida</option>
        
        <!-- States for Canada -->
        <option data-country="Canada">Ontario</option>
        <option data-country="Canada">Quebec</option>
        <option data-country="Canada">British Columbia</option>
        
        <!-- States for Australia -->
        <option data-country="Australia">New South Wales</option>
        <option data-country="Australia">Victoria</option>
        <option data-country="Australia">Queensland</option>
        
        <!-- States for UK -->
        <option data-country="UK">England</option>
        <option data-country="UK">Scotland</option>
        <option data-country="UK">Wales</option>
    </select>
    <div class="invalid-feedback">Please provide a valid state.</div>
</div>


                            <div class="col-md-3">
                                <label for="zip" class="form-label">Zip</label>
                                <input type="text" class="form-control" name="zip" id="zip" placeholder="" required>
                                <div class="invalid-feedback">Zip code required.</div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h4 class="mb-3">Payment</h4>
                        <div class="my-3">
                            <div class="form-check">
                                <input id="cod" name="paymentMethod" type="radio" class="form-check-input" value="cod" required>
                                <label class="form-check-label" for="cod">Cash on Delivery</label>
                            </div>
                            <div class="form-check">
                                <input id="card" name="paymentMethod" type="radio" class="form-check-input" value="card" required>
                                <label class="form-check-label" for="card">Online Payment</label>
                            </div>
                        </div>
                        <input type="hidden" name="product_id" value="<?= intval($product_id); ?>">

                        <hr class="my-4">
                        <button class="btn btn-primary" type="submit" id="buyNowButton">Buy Now</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('buyNowButton').addEventListener('click', function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Get selected payment method
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

        // Get the form
        const form = document.getElementById('checkoutForm');

        // Function to serialize form data
        function getFormData(form) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            return data;
        }

        // Set form action based on payment method
        if (paymentMethod === 'cod') {
            // Show SweetAlert confirmation for COD
            Swal.fire({
                title: 'Are you sure you want to proceed with Cash on Delivery?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, set the form action and submit
                    form.action = 'process_order.php'; // Cash on Delivery
                    form.submit(); // Submit the form
                }
            });
        } else if (paymentMethod === 'card') {
            // For online payment, serialize the form data and pass as query parameters
            const formData = getFormData(form);
            const queryParams = new URLSearchParams(formData).toString();
            form.action = 'paymentpage.php?' + queryParams; // Online Payment with serialized form data
            form.submit(); // Submit the form
        }
    });
</script>


    <script>
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    const allStates = Array.from(stateSelect.options);

    countrySelect.addEventListener('change', function () {
        const selectedCountry = this.value;

        // Clear the state dropdown
        stateSelect.innerHTML = '<option value="">Choose...</option>';

        // Filter states based on selected country
        allStates.forEach(option => {
            if (option.dataset.country === selectedCountry) {
                stateSelect.appendChild(option);
            }
        });

        // If no country is selected, show the default option
        if (!selectedCountry) {
            stateSelect.innerHTML = '<option value="">Choose...</option>';
        }
    });
</script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>

</html>