<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start(); // Start the session if not already started
}

// Initialize variables for cart item count and total quantity with default values
$cartItemCount = 0;
$totalQuantity = 0;

// Check if the 'id' session variable is set before using it
if (isset($_SESSION['id'])) {
  // Database connection
  $conn = new mysqli('localhost', 'root', '', 'securecart');
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $userId = $_SESSION['id']; // Get the user ID from the session

  $sql = "SELECT COUNT(cart_id) AS cart_count, SUM(quantity) AS total_quantity FROM cart WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }
  $stmt->bind_param('i', $userId);
  $stmt->execute();
  $stmt->bind_result($cartItemCount, $totalQuantity);
  $stmt->fetch();
  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SecureCart</title>
  <link rel="stylesheet" href="./styles/navbarStyles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/kTc2tGP3Xn6KQdJoAwTniZJ6l8V8s7xUZBtp5p5ycM6+fBqaUO9vpNugzzjE/r1TcXK5UQOdqI3Pg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="icon" type="image/png" href="./images/favicon.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
  <style>
    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
    }

    .sign-in {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      background-color: white;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      padding: 20px;
      z-index: 1;
      width: 450px;
    }

    .dropdown-content a {
      color: black;
      text-decoration: none;
      display: block;
      padding: 5px 0;
    }

    .sign-in:hover .dropdown-content {
      display: block;
    }
  </style>
</head>

<body>
  <header>
    <nav class="navbar">
      <div class="nav-logo">
        <a href="index.php"><img src="./images/logo.png" alt="logo"></a>
      </div>
      <div class="address">
        <?php if (isset($_SESSION['username'])): ?>
          <a href="#" class="deliver">Deliver to <?php echo $_SESSION['username']; ?></a>
        <?php else: ?>
          <a href="#" class="deliver">Deliver</a>
        <?php endif; ?>
        <div class="map-icon">
          <span class="material-symbols-outlined">location_on</span>
          <a href="#" class="location">India</a>
        </div>
      </div>

      <form action="search.php" method="GET" class="nav-search-form">
        <div class="nav-search">
          <select class="select-search" name="category">
            <option value="mobiles">Mobiles</option>
            <!-- You can add more options here if needed -->
          </select>
          <input type="text" placeholder="Search SecureCart" class="search-input" name="query" required>
          <button type="submit" class="search-icon">
            <span class="material-symbols-outlined">search</span>
          </button>
        </div>
      </form>

      <div class="sign-in">
    <?php if (!empty($_SESSION['access_token']) || isset($_SESSION['username'])): ?>
        <a href="#" class="account-link">
            <p class="greeting">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <span class="account-lists">Account &amp; Lists</span>
        </a>
        <div class="dropdown-content">
            <div class="column">
                <h3 style="color:black;">Your Lists</h3>
                <a href="#">Create a Wish List</a>
                <a href="#">Wish from Any Website</a>
                <a href="#">Baby Wishlist</a>
                <a href="#">Discover Your Style</a>
                <a href="#">Explore Showroom</a>
            </div>
            <div class="column">
                <h3 style="color:black;">Your Account</h3>
                <a href="#">Your Orders</a>
                <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'admin'): ?>
                    <a href="adminPage.php">Your Dashboard</a>
                <?php endif; ?>
                <a href="scanqr.php">Check Your Product</a>
                <a href="#">Your Wish List</a>
                <a href="#">Keep shopping for</a>
                <a href="#">Your Recommendations</a>
                <a href="#">Recalls and Product Safety Alerts</a>
                <a href="#">Your Subscribe & Save Items</a>
                <a href="#">Memberships & Subscriptions</a>
                <a href="#">Your Seller Account</a>
                <a href="#">Content Library</a>
                <a href="#">Devices</a>
                <a href="#">Your Free SecureCart Business Account</a>
                <a href="#">Switch Accounts</a>
                <a href="logout.php">Sign Out</a>
            </div>
        </div>
    <?php else: ?>
        <a href="signin.php" class="account-link">
            <p class="greeting">Hello, Sign in</p>
            <span class="account-lists">Account &amp; Lists</span>
        </a>
    <?php endif; ?>
</div>
      <div class="returns">
        <a href="ordersPreview.php">
          <p>Returns</p>
          <span>&amp; Orders</span>
        </a>
      </div>

      <div class="cart">
        <a href="cart.php" class="cart-link">
          <img src="./images/cartIcon.png" alt="Cart Icon" class="cart-icon" style="width: 40px;margin-right: 5px;">
          <span class="cart-text">Cart</span>
          <span class="badge"><?php echo $cartItemCount; ?></span>
        </a>
      </div>

      </div>
    </nav>

    <div class="banner">
      <div class="banner-content">
        <div class="panel">
          <span class="material-symbols-outlined">menu</span>
          <a href="#">All</a>
        </div>

        <ul class="links">
          <li><a href="#">Today's Deals</a></li>
          <li><a href="#">Customer Service</a></li>
          <li><a href="#">Registry</a></li>
          <li><a href="#">Gift Cards</a></li>
          <li><a href="#">Sell</a></li>
        </ul>
        <div class="deals">
          <a href="#">Shop deals in Electronics</a>
        </div>
      </div>
    </div>
  </header>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const isLoggedIn = <?php echo json_encode(isset($_SESSION['id'])); ?>;
      const redirectUrl = isLoggedIn ? 'cart.php' : 'signin.php';

      document.querySelector('.cart a').addEventListener('click', function(event) {
        event.preventDefault();
        window.location.href = redirectUrl;
      });
    });
  </script>
</body>

</html>