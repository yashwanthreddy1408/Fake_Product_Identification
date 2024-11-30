<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Settings - Admin Dashboard</title>
    <link rel="stylesheet" href="styles/adminStyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5; /* Light grey background for contrast */
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Increased margin for better spacing */
        }

        .card-header {
            background-color: #007bff;
            color: #ffffff;
            font-weight: bold;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary {
            background-color: #0056b3; /* Darker blue for buttons */
            border-color: #004085; /* Darker border */
        }

        .btn-primary:hover {
            background-color: #004085; /* Darken on hover */
        }

        .footer {
            background-color: #fff; /* Dark footer */
            color: black; /* White text for footer */
        }

        .form-label {
            font-weight: bold; /* Bold labels for better readability */
        }

        .nav-link.active {
            background-color: #0056b3; /* Darker blue for active link */
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
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-line"></i> Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="settings.php">
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
                    <h1 class="h2">Settings</h1>
                </div>
            </header>

            <div class="my-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">General Settings</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label for="siteName" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="siteName" placeholder="Enter site name">
                            </div>
                            <div class="mb-3">
                                <label for="siteDescription" class="form-label">Site Description</label>
                                <textarea class="form-control" id="siteDescription" rows="3" placeholder="Enter site description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="active" value="active">
                                    <label class="form-check-label" for="active">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="inactive" value="inactive">
                                    <label class="form-check-label" for="inactive">Inactive</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>

                <!-- User Management Section -->
                <div class="card my-3">
                    <div class="card-header">
                        <h5 class="card-title">User Management</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">User Roles</label>
                                <select class="form-select">
                                    <option selected>Choose role</option>
                                    <option value="1">Admin</option>
                                    <option value="2">User</option>
                                    <option value="3">Vendor</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </form>
                    </div>
                </div>

                <!-- Notifications Section -->
                <div class="card my-3">
                    <div class="card-header">
                        <h5 class="card-title">Notifications</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Email Notifications</label>
                                <input type="checkbox" class="form-check-input" id="emailNotifications">
                                <label class="form-check-label" for="emailNotifications">Enable</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Notifications</button>
                        </form>
                    </div>
                </div>

                <!-- Password Management Section -->
                <div class="card my-3">
                    <div class="card-header">
                        <h5 class="card-title">Password Management</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password">
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>

                <!-- Theme Preferences Section -->
                <div class="card my-3">
                    <div class="card-header">
                        <h5 class="card-title">Theme Preferences</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Select Theme</label>
                                <select class="form-select">
                                    <option selected>Choose theme</option>
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                    <option value="blue">Blue</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Apply Theme</button>
                        </form>
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
