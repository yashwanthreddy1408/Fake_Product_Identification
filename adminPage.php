<?php include 'connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles/adminStyles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom styles for dashboard */
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: #ffffff;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .card-header {
            background-color: #007bff; /* Primary color */
            color: #ffffff;
            font-weight: bold;
        }

        .footer {
            background-color: #ffffff;
            color: black;
        }

        /* Chart Styles */
        .chart-container {
            position: relative;
            width: 1000px;
            height: 400px;
            margin: 20px auto; /* Center align with margin */
        }

        /* Adjust the main content margin to avoid overlap */
        main {
            margin-left: 20px; /* Prevent overlap with the sidebar */
        }
    </style>
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
                        <a class="nav-link active" href="adminPage.php">
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
                        <a class="nav-link" href="orders.php">
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
                    <h1 class="h2">Admin Dashboard</h1>
                </div>
            </header>

            <?php
            // Random placeholder values
            $earnings = 25000; // Example earnings
            $dailyViews = rand(100, 400); // Random daily views between 100 and 400
            $comments = rand(20, 100); // Random comments

            // Fetch total users count
            $userQuery = $conn->query("SELECT COUNT(*) AS totalUsers FROM users");
            $userCount = $userQuery ? $userQuery->fetch_assoc()['totalUsers'] : 0;

            // Fetch total products count
            $productQuery = $conn->query("SELECT COUNT(*) AS totalProducts FROM products");
            $productCount = $productQuery ? $productQuery->fetch_assoc()['totalProducts'] : 0;

            // Fetch total orders count
            $orderQuery = $conn->query("SELECT COUNT(*) AS totalOrders FROM orders");
            $orderCount = $orderQuery ? $orderQuery->fetch_assoc()['totalOrders'] : 0;

            // Fetch additional data for analytics
            $userTypeQuery = $conn->query("SELECT usertype, COUNT(*) AS count FROM users GROUP BY usertype");
            $userTypes = [];
            while ($row = $userTypeQuery->fetch_assoc()) {
                $userTypes[] = $row;
            }

            // Fetch orders per month based on booked_date
            $ordersPerMonthQuery = $conn->query("
                SELECT MONTH(booked_date) AS month, COUNT(*) AS count 
                FROM orders 
                WHERE YEAR(booked_date) = YEAR(CURDATE()) 
                GROUP BY MONTH(booked_date)
            ");
            $ordersPerMonth = [];
            while ($row = $ordersPerMonthQuery->fetch_assoc()) {
                $ordersPerMonth[$row['month']] = $row['count'];
            }

            // Fetch products by brand
            $productCategoryQuery = $conn->query("SELECT brand, COUNT(*) AS count FROM products GROUP BY brand");
            $productCategories = [];
            while ($row = $productCategoryQuery->fetch_assoc()) {
                $productCategories[] = $row;
            }
            ?>
            <?php
                // Fetch total earnings from orders
                $earningsQuery = $conn->query("SELECT SUM(price) AS totalEarnings FROM orders");
                $earningsData = $earningsQuery ? $earningsQuery->fetch_assoc() : ['totalEarnings' => 0, 'totalOrders' => 0];

                $earnings = $earningsData['totalEarnings'];
                // Generate random values for daily views and comments
                $dailyViews = rand(100, 400);
                $comments = rand(100, 500); // Random comments between 10 and 50
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
            ?>
            <div class="row my-4">
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="text-muted"><i class="fas fa-money-bill-wave"></i> Earnings</h5>
                            <h2 class="display-6">₹<?php echo formatIndianNumber($earnings); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="text-muted"><i class="fas fa-shopping-cart"></i> Total Orders</h5>
                            <h2 class="display-6"><?php echo $orderCount; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="text-muted"><i class="fas fa-eye"></i> Daily Views</h5>
                            <h2 class="display-6"><?php echo $dailyViews; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="text-muted"><i class="fas fa-comments"></i> Comments</h5>
                            <h2 class="display-6"><?php echo $comments; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
<!-- Recent Orders Section -->
<div class="row">
    <!-- Recent Orders Section -->
    <div class="col-md-8">
        <div class="card mb-4 shadow">
            <div class="card-header bg-gradient">
                <h5 class="card-title text-white">Recent Orders</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php
                    // Fetch recent orders from the database
                    $orders = $conn->query("SELECT order_id, product_id, price, status, booked_date FROM orders ORDER BY booked_date DESC LIMIT 6");
                    while ($order = $orders->fetch_assoc()) {
                        // Fetch product name based on product_id
                        $product = $conn->query("SELECT product_name FROM products WHERE product_id = {$order['product_id']}")->fetch_assoc();
                        $productName = $product ? $product['product_name'] : 'Unknown Product';
                        $formattedDate = date('d M Y', strtotime($order['booked_date']));
                        
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center border-0 rounded-3 mb-2 bg-light shadow-sm hover-effect'>
                                <div class='d-flex align-items-center'>
                                    <div class='icon-circle'>
                                        <i class='fas fa-box'></i>
                                    </div>
                                    <div class='ms-3'>
                                        <strong>{$productName}</strong><br>
                                        <small class='text-muted'>Status: {$order['status']} | Date: {$formattedDate}</small>
                                    </div>
                                </div>
                                <span class='badge bg-success rounded-pill'>₹{$order['price']}</span>
                              </li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Users Section -->
    <div class="col-md-4">
        <div class="card mb-4 shadow">
            <div class="card-header bg-gradient">
                <h5 class="card-title text-white">Recent Users</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php
                    // Fetch recent users from the database
                    $users = $conn->query("SELECT username, email, usertype, created_at FROM users ORDER BY created_at DESC LIMIT 6");
                    while ($user = $users->fetch_assoc()) {
                        $initial = strtoupper(substr($user['username'], 0, 1));
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center border-0 rounded-3 mb-2 bg-light shadow-sm hover-effect'>
                                <div class='d-flex align-items-center'>
                                    <div class='icon-circle'>
                                        <strong>{$initial}</strong>
                                    </div>
                                    <div class='ms-3'>
                                        <strong>{$user['username']}</strong><br>
                                        <small class='text-muted'>Role: {$user['usertype']} | Email: {$user['email']}</small>
                                    </div>
                                </div>
                                <span class='text-muted'>{$user['created_at']}</span>
                              </li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>


            <!-- Analytics Section -->
            <div class="row mb-4">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header">User Types Distribution</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="chart-container">
                                        <canvas id="userTypesChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mt-4">User Type Insights</h5>
                                    <p class="text-muted">Understanding the distribution of different user types helps in tailoring services and promotions effectively.</p>
                                    <p class="text-muted">This analysis can guide your marketing strategies and user engagement efforts.</p>
                                    
                                    <h6 class="mt-4">User Types Overview</h6>
                                    <ul class="list-group mt-3">
                                        <?php foreach ($userTypes as $userType): ?>
                                            <?php if ($userType['usertype'] !== 'Vendor'): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <i class="bi bi-person-circle me-2"></i> <!-- Using Bootstrap Icons -->
                                                    <?php echo htmlspecialchars($userType['usertype']); ?>
                                                    <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($userType['count']); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>

                                    <div class="mt-3">
                                        <h6 class="text-success">Total Users: 
                                            <span class="badge bg-success"><?php echo array_sum(array_column($userTypes, 'count')); ?></span>
                                        </h6>
                                        <p class="text-muted">Use the insights gained here to optimize your outreach and improve user satisfaction.</p>
                                        <p class="text-muted">By focusing on user engagement, you can increase retention rates and drive more conversions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header">Orders Per Month (Current Year)</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="ordersPerMonthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header">Products by Brand</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="productCategoriesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mt-4">
    <div class="row">
        <!-- User Registrations Over Time -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">User Registrations Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="registrationsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Orders by Status -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Orders by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="ordersStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Customer Demographics -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Customer Age Demographics</h5>
                </div>
                <div class="card-body">
                    <canvas id="demographicsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Mobile Phone Sales Distribution -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Mobile Phone Sales Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Additional Insights -->
 <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Additional Insights</h5>
                </div>
                <div class="card-body">
                    <p>As an admin, it's essential to monitor user engagement and order flow to ensure smooth operations. Here are some insights:</p>
                    <ul>
                        <li><strong>Growth Rate:</strong> Your user base has grown by 15% this month compared to last month.</li>
                        <li><strong>Customer Retention:</strong> 60% of users return for repeat purchases.</li>
                        <li><strong>Popular Brands:</strong> Samsung and Apple account for 70% of total mobile phone sales.</li>
                        <li><strong>Average Order Value:</strong> The average order value has increased to ₹1,500.</li>
                    </ul>
                </div>
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

    <!-- Chart.js JavaScript for displaying charts -->
    <script>
        // User Types Chart
        var ctx1 = document.getElementById('userTypesChart').getContext('2d');
        var userTypesChart = new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($userTypes, 'usertype')); ?>,
                datasets: [{
                    label: 'User Types',
                    data: <?php echo json_encode(array_column($userTypes, 'count')); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'], // Different colors
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'User Types Distribution'
                    }
                }
            }
        });

        // Orders Per Month Chart
        var ctx2 = document.getElementById('ordersPerMonthChart').getContext('2d');
        var ordersPerMonthChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Orders',
                    data: [
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            echo (isset($ordersPerMonth[$i]) ? $ordersPerMonth[$i] : 0) . ",";
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(78, 115, 223, 0.2)', // Light blue fill
                    borderColor: '#4e73df', // Blue border
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Orders Per Month (Current Year)'
                    }
                }
            }
        });

        // Product Categories Chart
        var ctx3 = document.getElementById('productCategoriesChart').getContext('2d');
        var productCategoriesChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($productCategories, 'brand')); ?>,
                datasets: [{
                    label: 'Products',
                    data: <?php echo json_encode(array_column($productCategories, 'count')); ?>,
                    backgroundColor: ['#1cc88a', '#36b9cc', '#e74a3b', '#f6c23e'], // Different colors for bars
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Products by Brand'
                    }
                }
            }
        });
    </script>
    <script>
    // User Registrations Over Time
    const registrationsCtx = document.getElementById('registrationsChart').getContext('2d');
    const registrationsChart = new Chart(registrationsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'User Registrations',
                data: [30, 50, 40, 60, 70, 100],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { mode: 'index', intersect: false },
            },
            scales: {
                x: { title: { display: true, text: 'Months' } },
                y: { title: { display: true, text: 'Registrations' } },
            }
        }
    });

    // Orders by Status
    const ordersStatusCtx = document.getElementById('ordersStatusChart').getContext('2d');
    const ordersStatusChart = new Chart(ordersStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Completed', 'Cancelled'],
            datasets: [{
                label: 'Number of Orders',
                data: [15, 40, 5],
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { mode: 'index', intersect: false },
            },
            scales: {
                x: { title: { display: true, text: 'Order Status' } },
                y: { title: { display: true, text: 'Number of Orders' } },
            }
        }
    });

    // Customer Age Demographics
    const demographicsCtx = document.getElementById('demographicsChart').getContext('2d');
    const demographicsChart = new Chart(demographicsCtx, {
        type: 'pie',
        data: {
            labels: ['18-24', '25-34', '35-44', '45+'],
            datasets: [{
                label: 'Age Groups',
                data: [25, 35, 20, 20],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(255, 206, 86, 0.5)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { mode: 'index', intersect: false },
            },
        }
    });

    // Mobile Phone Sales Distribution
    const salesDistributionCtx = document.getElementById('salesDistributionChart').getContext('2d');
    const salesDistributionChart = new Chart(salesDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Samsung', 'Apple', 'Xiaomi', 'OnePlus', 'Others'],
            datasets: [{
                label: 'Mobile Phone Sales',
                data: [40, 30, 15, 10, 5],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { mode: 'index', intersect: false },
            },
        }
    });
</script>


</body>

</html>
