<?php
include 'navbar.php';
include 'connection.php'; // Include your database connection file
// Retrieve product_id from the URL and sanitize it
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$query = "SELECT * FROM products WHERE product_id = $product_id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

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
<?php
$isLoggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="./styles/product_details.css">
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <!-- Include FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/kTc2tGP3Xn6KQdJoAwTniZJ6l8V8s7xUZBtp5p5ycM6+fBqaUO9vpNugzzjE/r1TcXK5UQOdqI3Pg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .fa-star,
        .fa-star-half-alt,
        .far.fa-star {
            color: orange;
        }

        .product-price {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
        }
    </style>
</head>

<body>
    <div class="product-details-container">
        <div class="image-gallery">
            <div class="thumbnail-container">
                <?php
                for ($i = 1; $i <= 7; $i++) {
                    $image_field = "product_image$i";
                    if (!empty($product[$image_field])) { ?>
                        <img src="<?php echo $product[$image_field]; ?>" class="thumbnail" onclick="changeImage('<?php echo $product[$image_field]; ?>')">
                <?php }
                } ?>
            </div>
            <div class="main-image-container">
                <img id="main-image" src="<?php echo $product['product_image1']; ?>" class="main-image">
            </div>
        </div>
        <div class="product-info">
            <h1 class="product-title"><a href="product_details.php?product_id=<?php echo $product['product_id']; ?>"><?php echo $product['product_name']; ?></a></h1>
            <div class="product-rating">
                <?php
                $rating = $product['average_rating'];
                $full_stars = floor($rating);
                $half_star = $rating - $full_stars >= 0.5 ? 1 : 0;
                $empty_stars = 5 - $full_stars - $half_star;
                for ($i = 0; $i < $full_stars; $i++) {
                    echo '<i class="fas fa-star"></i>';
                }
                if ($half_star) {
                    echo '<i class="fas fa-star-half-alt"></i>';
                }
                for ($i = 0; $i < $empty_stars; $i++) {
                    echo '<i class="far fa-star"></i>';
                }
                ?>
                <span class="rating-number"><?php echo $rating; ?></span>
                <span class="rating-count">(<?php echo $product['number_of_ratings']; ?> ratings)</span>
                <span class="rating-count"> | <?php echo $product['number_of_buyers']; ?>+ bought in past month</span>
            </div>
            <hr>
            <div class="product-price">
                <span class="new-price">₹<?php echo formatIndianNumber(intval($product['new_price'])); ?></span>
                <span class="old-price">₹<?php echo formatIndianNumber(intval($product['old_price'])); ?></span>
                <span class="discount">(<?php echo round((($product['old_price'] - $product['new_price']) / $product['old_price']) * 100); ?>% off)</span>
            </div>
            <div class="action-buttons">
                <?php
                $product_id = $product["product_id"];
                $productLink = $isLoggedIn ? 'add_to_cart.php?product_id=' . $product_id : 'signin.php';
                echo '
                <a href="' . $productLink . '" class="add-to-cart" data-product-id="' . $product_id . '" style="text-align:center; margin-top:20px;">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </a>';
                ?>
                <button class="buy-now" onclick="redirectToCheckout(<?php echo $product['product_id']; ?>)">
                    <i class="fas fa-bolt"></i> Buy Now
                </button>
            </div>

            <p style="margin: 10px 0;color: #666;">Inclusive of all taxes</p>
            <div class="service-icons">
                <div class="info-item">
                    <img src="https://m.media-amazon.com/images/G/31/A2I-Convert/mobile/IconFarm/icon-returns._CB484059092_.png" alt="7-Day Service Icon">
                    <span>7-Day Service</span>
                </div>
                <div class="info-item">
                    <img src="https://m.media-amazon.com/images/G/31/A2I-Convert/mobile/IconFarm/trust_icon_free_shipping_81px._CB630870460_.png" alt="Free Delivery Icon">
                    <span>FREE Delivery</span>
                </div>
                <div class="info-item">
                    <img src="https://m.media-amazon.com/images/G/31/A2I-Convert/mobile/IconFarm/icon-warranty._CB485935626_.png" alt="1 Year Warranty Icon">
                    <span>1 Year Warranty</span>
                </div>
                <div class="info-item">
                    <img src="https://m.media-amazon.com/images/G/31/A2I-Convert/mobile/IconFarm/icon-top-brand._CB617044271_.png" alt="Top Brands Icon">
                    <span>Top Brands</span>
                </div>
                <div class="info-item">
                    <img src="https://m.media-amazon.com/images/G/31/A2I-Convert/mobile/IconFarm/icon-amazon-delivered._CB485933725_.png" alt="Secure Delivery Icon">
                    <span>Secure Delivery</span>
                </div>
            </div>
            <hr>
            <div class="additional-details">
                <h2 style="padding-bottom:20px;">Product Details</h2>
                <table>
                    <tr>
                        <td style="font-weight:bold;">Brand</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['brand']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Operating System</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['operating_system']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Color</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['color']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">RAM</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['ram']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Memory Storage Capacity</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['memory_storage_capacity']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Processor</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['processor']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Screen Size</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['screen_size']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Resolution</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['resolution']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Network</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['network']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Front Camera</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['frontCamera']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Rear Camera</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['rearCamera']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Battery Capacity</td>
                        <td style="padding-left:20px;"><?php echo htmlspecialchars($product['battery_capacity']); ?></td>
                    </tr>
                </table>
            </div>
            <hr>
            <div class="product-description">
                <h2 style="padding-bottom:10px;">Description</h2>
                <ul>
                    <?php
                    // Assuming `product_description` contains the description in a format suitable for splitting into points
                    $description_points = explode("\n", $product['product_description']);
                    foreach ($description_points as $point) {
                        if (!empty(trim($point))) {
                            echo "<li>" . htmlspecialchars($point) . "</li><br>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function changeImage(imageSrc) {
            document.getElementById('main-image').src = imageSrc;
        }
        function redirectToCheckout(productId) {
            // Redirect to checkout.php with the product_id as a query parameter
            window.location.href = 'checkout.php?product_id=' + productId;
        }
    </script>
</body>

</html>

<?php include 'footer.php'; ?>