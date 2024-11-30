<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orders Management</title>
    <link rel="stylesheet" href="styles/adminStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>

    <div class="d-flex flex-column flex-md-row">
        <!-- Sidebar -->
        <nav class="sidebar col-md-2 col-lg-2 d-md-block bg-dark">
            <div class="position-sticky">
                <a href="index.php">
                <img src="./images/logo.png" alt="Site Logo" style="width: 150px; height: auto;">
            </a>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="adminPage.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-line"></i> Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cogs"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            <header class="header p-3">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h1 class="h2">Orders Management</h1>
                </div>
            </header>

            <div class="my-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Orders List</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Payment Method</th>
                                    <th>Price (INR)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Include the database connection
                                include 'connection.php';

                                // Function to format numbers in Indian style
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

                                // Fetch orders from the database
                                $orders = $conn->query("SELECT order_id, first_name, last_name, email, address, country, payment_method, price, status FROM orders");
                                while ($order = $orders->fetch_assoc()) {
                                    $fullName = $order['first_name'] . ' ' . $order['last_name'];
                                    echo "<tr>
                                            <td>{$order['order_id']}</td>
                                            <td>{$fullName}</td>
                                            <td>{$order['email']}</td>
                                            <td>{$order['address']}</td>
                                            <td>{$order['payment_method']}</td>
                                            <td>₹" . formatIndianNumber($order['price']) . "</td>
                                            <td>{$order['status']}</td>
                                            <td>
                                                <form method='POST' action='delete_order.php'>
                                                    <input type='hidden' name='id' value='{$order['order_id']}'>
                                                    <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                                                </form>
                                            </td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer mt-auto py-3">
                <div class="container">
                    <span class="text-muted">© 2024 SecureCart. All rights reserved.</span>
                </div>
            </footer>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>
