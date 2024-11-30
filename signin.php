<?php
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', 'password', 'your_database');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Check if the user already exists based on the Google ID
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $google_id); // Assuming 'id' in your table refers to Google ID
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // If the user is new, insert them into the users table
    $current_date = date('Y-m-d H:i:s'); // Current date for `booked_date`
    $user_type = 'user'; // Default user type (you can adjust this)

    $insert_sql = "INSERT INTO users (id, username, email, booked_date, user_type) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssss", $google_id, $name, $email, $current_date, $user_type);
    $insert_stmt->execute();
    $insert_stmt->close();
}

$stmt->close();
$conn->close();

// Redirect to the index.php page after successful login
header('Location: index.php');
exit();

?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $servername = "localhost";
    $username = "root";
    $dbpassword = ""; // Update with your database password
    $dbname = "securecart"; // Update with your database name

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, password, username, usertype FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password, $name, $usertype);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $name;
        $_SESSION['usertype'] = $usertype;

        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Signed in successfully!',
                    confirmButtonText: 'OK'
                }).then(function() {";

        if ($usertype === 'admin') {
            echo "window.location = 'adminPage.php?id=$user_id';";
        } else {
            echo "window.location = 'index.php?id=$user_id';";
        }

        echo "});
            };
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Incorrect email or password!',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/loginStyles.css">
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-blue-50 dark:bg-gray-900">
    <section class="bg-blue-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="index.php" class="flex items-center mb-6 text-2xl font-semibold text-blue-700 dark:text-white" style="color:black;">
                <img class="w-8 h-8 mr-2" src="images/logo2.png" alt="logo" style="width:auto;">
            </a>
            <div class="w-full bg-white rounded-lg shadow dark:border sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-700 md:text-2xl dark:text-white">
                        Sign in to your account
                    </h1>
                    <form class="space-y-4 md:space-y-6" action="signin.php" method="POST">
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@mail.com" required>
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="remember" name="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
                                </div>
                            </div>
                            <a href="#" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">Forgot password?</a>
                        </div>
                        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Sign in</button>
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Don’t have an account yet? <a href="signup.php" class="font-medium text-blue-600 hover:underline dark:text-blue-500">Sign up</a>
                        </p>
                    </form>
                    <div class="flex flex-col items-center mt-4 space-y-4">
                        <a href="<?php echo filter_var($auth_url, FILTER_SANITIZE_URL); ?>" class="flex items-center justify-center w-full px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            <img src="https://banner2.cleanpng.com/20180423/gkw/kisspng-google-logo-logo-logo-5ade7dc753b015.9317679115245306313428.jpg" alt="Google Logo" class="w-6 h-6">
                            <span class="ml-2">Sign in with Google</span>
                        </a>
                        <a href="#" class="flex items-center justify-center w-full px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-300">
                            <img src="images/fb.png" alt="Facebook" class="w-5 h-5 mr-2">
                            Sign in with Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('logout') && urlParams.get('logout') === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Logged out',
                    text: 'You have been logged out successfully!',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</body>

</html>