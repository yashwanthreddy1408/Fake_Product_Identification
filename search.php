<?php
// Include your database connection
include 'connection.php';

// Start the session
session_start(); // Make sure to start the session to access session variables

// Get the search query from the GET request
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

// Initialize an empty array to hold search results
$results = [];

// Prepare the SQL statement
if (!empty($searchQuery)) {
    // Create a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM products WHERE brand LIKE ? OR product_name LIKE ?");
    $searchTerm = "%" . $searchQuery . "%"; // Adding wildcards for LIKE query
    $stmt->bind_param("ss", $searchTerm, $searchTerm); // Bind parameters
    $stmt->execute(); // Execute the statement
    $result = $stmt->get_result(); // Get the result set

    // Fetch results
    while ($row = $result->fetch_assoc()) {
        $results[] = $row; // Store each product in the results array
    }
    $stmt->close(); // Close the statement
}

// Close the database connection
$conn->close();

$isLoggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <link rel="stylesheet" href="./styles/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php include 'navbar.php'; ?> <!-- Include the navbar -->

    <div style="text-align: center; margin: 20px 0;">
        <h1 style="font-size: 2.5rem; color: #333; font-weight: bold; margin-bottom: 10px;">
            Search Results
        </h1>
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 20px;">
            Showing results for "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
        </p>
    </div>

    <?php if (!empty($results)): ?>
        <div class="product-list" style="width:70%; margin: 0 auto;">
            <?php
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
            <?php foreach ($results as $row): ?>
                <?php
                $fullStars = floor($row["average_rating"]);
                $halfStar = ($row["average_rating"] - $fullStars) >= 0.5 ? 1 : 0;
                $emptyStars = 5 - $fullStars - $halfStar;

                $oldPrice = (int)$row["old_price"];
                $newPrice = (int)$row["new_price"];
                $discountPercentage = round((($oldPrice - $newPrice) / $oldPrice) * 100);
                ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($row["product_image1"]); ?>" alt="<?php echo htmlspecialchars($row["product_name"]); ?>" class="product-image">
                    <div class="product-details">
                        <h2 class="product-name">
                            <a style="text-decoration:none;color:black;font-weight:500;"
                                onmouseover="this.style.color='orange'"
                                onmouseout="this.style.color='black'"
                                href="product_details.php?product_id=<?php echo $row["product_id"]; ?>">
                                <?php echo htmlspecialchars($row["product_name"]); ?>
                            </a>
                        </h2>
                        <div class="price-container">
                            <span class="new-price">₹<?php echo formatIndianNumber($newPrice); ?></span>
                            <span class="old-price">M.R.P: ₹<?php echo formatIndianNumber($oldPrice); ?></span>
                            <span class="discount">(<?php echo $discountPercentage; ?>% off)</span>
                        </div>
                        <div class="product-ratings">
                            <?php for ($i = 0; $i < $fullStars; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                            <?php if ($halfStar): ?>
                                <i class="fas fa-star-half-alt"></i>
                            <?php endif; ?>
                            <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                                <i class="far fa-star"></i>
                            <?php endfor; ?>
                            <span class="number-of-ratings">(<?php echo formatIndianNumber($row["number_of_ratings"]); ?> ratings)</span>
                        </div>
                        <p class="number-of-buyers" style="margin:0;padding:0px;font-weight:600;">
                            <?php echo formatIndianNumber($row["number_of_buyers"]); ?> + bought in past month
                        </p>
                        <p class="save-extra" style="margin:0;padding:5px 0px;">Save extra with No Cost EMI</p>
                        <p class="free-delivery" style="margin:0;padding:0px;">FREE Delivery <strong>Tomorrow 8 am - 12 pm</strong></p>
                        <?php $productLink = $isLoggedIn ? 'add_to_cart.php?product_id=' . $row["product_id"] : 'signin.php'; ?>
                        <a href="<?php echo $productLink; ?>" class="add-to-cart" data-product-id="<?php echo $row["product_id"]; ?>" style="text-align:center; margin-top:20px;">Add to Cart</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; font-size: 1.2rem; color: #666;">No results found for your search.</p>
    <?php endif; ?>

    <?php include 'footer.php'; ?> <!-- Include the footer -->
</body>

</html>