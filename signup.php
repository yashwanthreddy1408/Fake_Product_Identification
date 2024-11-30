<?php
ob_start(); // Start output buffering

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $usertype = $_POST['usertype'];

    // Database connection
    $servername = "localhost";
    $username = "root";
    $db_password = "";
    $dbname = "securecart";

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (username, email, password, usertype) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $usertype);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Account created successfully!',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location = 'signin.php';
                });
            };
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to create account!',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    }

    $stmt->close();
    $conn->close();
}
ob_end_flush(); // Send output
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .info-bg {
            background-color: #f9fafb;
            /* Light background for contrast */
        }

        .section-title {
            font-size: 1.75rem;
            /* Increased font size */
            color: #4B5563;
            /* Dark gray color */
            font-weight: 700;
            /* Bold text */
        }

        .section-text {
            font-size: 1rem;
            /* Standard font size */
            color: #6B7280;
            /* Medium gray color */
            line-height: 1.5;
            /* Better line spacing */
        }

        .highlight {
            color: #2563EB;
            /* Blue color for emphasis */
            font-weight: 600;
            /* Semi-bold */
        }
    </style>
</head>

<body>
    <section class="bg-blue-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen">
            <a href="index.php" class="flex items-center mb-6 text-2xl font-semibold text-blue-700 dark:text-white">
                <img class="w-8 h-8 mr-2" src="images/logo2.png" alt="logo" style="width:auto" ;>
            </a>
            <div class="flex w-full max-w-4xl bg-white rounded-lg shadow dark:bg-gray-800">
                <!-- Sign Up Form -->
                <div class="w-1/2 p-6 space-y-4 md:space-y-6">
                    <h1 class="text-xl font-bold text-gray-700 dark:text-white text-center">
                        Create your account
                    </h1>
                    <form action="signup.php" method="POST" class="space-y-4 md:space-y-6">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
                            <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Your name" required>
                        </div>
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="name@mail.com" required>
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="••••••••" required>
                        </div>
                        <div>
                            <label for="usertype" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User Type</label>
                            <select name="usertype" id="usertype" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <option value="" disabled selected>Select user type</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 rounded-lg p-2.5">Sign Up</button>
                    </form>
                </div>
                <!-- Info Section -->
                <div class="w-1/2 p-6 flex flex-col items-center justify-center space-y-4 info-bg">
                    <h2 class="section-title text-center">Why Join Us?</h2>
                    <p class="section-text text-center">
                        At <span class="highlight">SecureCart</span>, we believe in providing a seamless and secure shopping experience. By signing up, you gain access to exclusive deals and ensure a safe environment for all your purchases.
                    </p>
                    <h2 class="section-title mt-4 text-center">Or sign up with</h2>
                    <!-- Google Button -->
                    <a href="#" class="flex items-center justify-center w-full px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-300">
                        <img src="images/google.png" alt="Google" class="w-5 h-5 mr-2">
                        Sign up with Google
                    </a>
                    <!-- Facebook Button -->
                    <a href="#" class="flex items-center justify-center w-full px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-300">
                        <img src="images/fb.png" alt="Facebook" class="w-5 h-5 mr-2">
                        Sign up with Facebook
                    </a>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Already have an account? <a href="signin.php" class="text-blue-600 hover:underline dark:text-blue-500">Sign in here</a></p>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>