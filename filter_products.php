<?php
session_start();
include 'connection.php';

$filters = [];
$params = [];

// Handling brand filter
if (isset($_GET['brand']) && is_array($_GET['brand'])) {
    $brandPlaceholders = [];
    foreach ($_GET['brand'] as $brand) {
        $brandPlaceholders[] = '?';
        $params[] = $brand; // Store the unescaped brand for binding
    }
    $filters[] = 'brand IN (' . implode(',', $brandPlaceholders) . ')';
}

// Handling screen size filter
if (isset($_GET['screen_size']) && is_array($_GET['screen_size'])) {
    $screenSizePlaceholders = [];
    foreach ($_GET['screen_size'] as $screenSize) {
        $screenSizePlaceholders[] = '?';
        $params[] = $screenSize; // Store the unescaped screen size for binding
    }
    $filters[] = 'screen_size IN (' . implode(',', $screenSizePlaceholders) . ')';
}

// More filters can be added here...

$whereClause = '';
if (!empty($filters)) {
    $whereClause = 'WHERE ' . implode(' AND ', $filters);
}

// Prepare the SQL statement
$sql = "SELECT * FROM products $whereClause";
$stmt = $conn->prepare($sql);

// Check for SQL preparation errors
if (!$stmt) {
    die("SQL preparation failed: " . $conn->error);
}

// Determine the types of the parameters for binding
$types = str_repeat('s', count($params));

// Bind parameters
if ($params) {
    $stmt->bind_param($types, ...$params);
}

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
    <div class="product-list" style="width:70%; margin: 0 auto;">
        <?php while ($row = $result->fetch_assoc()): ?>
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
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p style="text-align: center; font-size: 1.2rem; color: #666;">No results found for your search.</p>
<?php endif;

// Close the statement and connection
$stmt->close();
$conn->close();
?>
