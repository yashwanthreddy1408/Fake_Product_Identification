<?php
include 'navbar.php'; 
include 'connection.php';

// Assuming you pass the QR code text as a GET parameter
$qrCodeText = isset($_GET['qrcode']) ? $_GET['qrcode'] : '';

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM products WHERE qrcode_text = ?");
$stmt->bind_param("s", $qrCodeText);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the product details
    $product = $result->fetch_assoc();
} else {
    // Redirect or show a message if the product is not found
    header("Location: index.php?error=Product not found");
    exit();
}
$isLoggedIn = isset($_SESSION['username']);

function formatIndianNumber($number) {
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>    
    <link rel="stylesheet" href="./styles/products.css">

</head>

<body>
<?php
// Assuming the $product variable holds the product details after fetching from the database using QR code
if (!empty($product)): ?>
<div style="text-align: center; margin: 20px 0; font-size: 1.5rem; color: #333;">
        <strong>Your product details for the scanned QR code:</strong>
    </div>
    <div class="product-list" style="width:70%; margin: 0 auto;margin-top:50px;">
        <div class="product">
            <img src="<?php echo htmlspecialchars($product["product_image1"]); ?>" alt="<?php echo htmlspecialchars($product["product_name"]); ?>" class="product-image">
            <div class="product-details">
                <h2 class="product-name">
                    <a style="text-decoration:none;color:black;font-weight:500;"
                       onmouseover="this.style.color='orange'"
                       onmouseout="this.style.color='black'"
                       href="product_details.php?product_id=<?php echo $product["product_id"]; ?>">
                        <?php echo htmlspecialchars($product["product_name"]); ?>
                    </a>
                </h2>
                <div class="price-container">
                    <span class="new-price">₹<?php echo formatIndianNumber($product['new_price']); ?></span>
                    <span class="old-price">M.R.P: ₹<?php echo formatIndianNumber($product['old_price']); ?></span>
                    <?php
                    $oldPrice = (int)$product["old_price"];
                    $newPrice = (int)$product["new_price"];
                    $discountPercentage = round((($oldPrice - $newPrice) / $oldPrice) * 100);
                    ?>
                    <span class="discount">(<?php echo $discountPercentage; ?>% off)</span>
                </div>
                
                <!-- Ratings -->
                <?php
                $fullStars = floor($product["average_rating"]);
                $halfStar = ($product["average_rating"] - $fullStars) >= 0.5 ? 1 : 0;
                $emptyStars = 5 - $fullStars - $halfStar;
                ?>
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
                    <span class="number-of-ratings">(<?php echo formatIndianNumber($product["number_of_ratings"]); ?> ratings)</span>
                </div>

                <p class="number-of-buyers" style="margin:0;padding:0px;font-weight:600;">
                    <?php echo formatIndianNumber($product["number_of_buyers"]); ?> + bought in past month
                </p>
                <p class="save-extra" style="margin:0;padding:5px 0px;">Save extra with No Cost EMI</p>
                <p class="free-delivery" style="margin:0;padding:0px;">FREE Delivery <strong>Tomorrow 8 am - 12 pm</strong></p>
                
                <?php $productLink = $isLoggedIn ? 'add_to_cart.php?product_id=' . $product["product_id"] : 'signin.php'; ?>
                <a href="<?php echo $productLink; ?>" class="add-to-cart" data-product-id="<?php echo $product["product_id"]; ?>" style="text-align:center; margin-top:20px;">Add to Cart</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <p style="text-align: center; font-size: 1.2rem; color: #666;">No product found for the scanned QR code.</p>
<?php endif; ?>

    <?php include 'footer.php'; ?>
</body>
</html>
