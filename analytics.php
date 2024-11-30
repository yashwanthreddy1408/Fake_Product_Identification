<?php include 'connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="styles/adminStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
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

        .chart-container {
            position: relative;
            width: 1000px;
            height: 400px;
            margin: 20px auto;
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
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="analytics.php">
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
                    <h1 class="h2">Analytics Dashboard</h1>
                </div>
            </header>

            <div class="container my-4">
                <?php
                // Fetch total users count
                $userQuery = $conn->query("SELECT COUNT(*) AS totalUsers FROM users");
                $userCount = $userQuery ? $userQuery->fetch_assoc()['totalUsers'] : 0;

                // Fetch total orders count
                $orderQuery = $conn->query("SELECT COUNT(*) AS totalOrders FROM orders");
                $orderCount = $orderQuery ? $orderQuery->fetch_assoc()['totalOrders'] : 0;

                // Fetch user types for distribution
                $userTypeQuery = $conn->query("SELECT usertype, COUNT(*) AS count FROM users GROUP BY usertype");
                $userTypes = [];
                while ($row = $userTypeQuery->fetch_assoc()) {
                    $userTypes[] = $row;
                }

                // Fetch orders per month based on booked_date
                $ordersPerMonthQuery = $conn->query("SELECT MONTH(booked_date) AS month, COUNT(*) AS count FROM orders WHERE YEAR(booked_date) = YEAR(CURDATE()) GROUP BY MONTH(booked_date)");
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

                <!-- User Types Distribution -->
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

                <!-- Orders Per Month -->
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

                <!-- Products by Brand -->
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

                <!-- Footer -->
                <footer class="footer mt-auto py-3">
                    <div class="container">
                        <span class="text-muted">© 2024 SecureCart. All rights reserved.</span>
                    </div>
                </footer>
            </div>
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
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
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
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    borderColor: '#4e73df',
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
                    backgroundColor: ['#1cc88a', '#36b9cc', '#e74a3b', '#f6c23e'],
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
</body>

</html>
