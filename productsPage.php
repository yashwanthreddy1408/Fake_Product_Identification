<?php

$isLoggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="./styles/products.css">
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/kTc2tGP3Xn6KQdJoAwTniZJ6l8V8s7xUZBtp5p5ycM6+fBqaUO9vpNugzzjE/r1TcXK5UQOdqI3Pg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="filter-sidebar">
            <h2 style="padding-left:10px;padding-bottom:20px;">Filters</h2>
            <form id="filter-form">
            <div class="filter-group">
                <button class="dropdown-btn">Brand</button>
                <div class="dropdown-content-filter">
                    <label><input type="checkbox" name="brand" value="Apple"> Apple</label><br>
                    <label><input type="checkbox" name="brand" value="Nokia"> Nokia</label><br>
                    <label><input type="checkbox" name="brand" value="Oneplus"> Oneplus</label><br>
                    <label><input type="checkbox" name="brand" value="Oppo"> Oppo</label><br>
                    <label><input type="checkbox" name="brand" value="Realme"> Realme</label><br>
                    <label><input type="checkbox" name="brand" value="Redmi"> Redmi</label><br>
                    <label><input type="checkbox" name="brand" value="Samsung"> Samsung</label><br>
                    <label><input type="checkbox" name="brand" value="Tecno"> Tecno</label><br>
                    <label><input type="checkbox" name="brand" value="Vivo"> Vivo</label><br>
                    <label><input type="checkbox" name="brand" value="Xiaomi"> Xiaomi</label><br>
                </div>
            </div>
            <div class="filter-group">
                <button class="dropdown-btn">Screen Size</button>
                <div class="dropdown-content-filter">
                    <label><input type="checkbox" name="screen_size" value="6.0 - 6.5 inches"> 6.0 - 6.5 inches</label><br>
                    <label><input type="checkbox" name="screen_size" value="6.5 - 7 inches"> 6.5 - 7 inches</label><br>
                    <label><input type="checkbox" name="screen_size" value="Above 7 inches"> Above 7 inches</label><br>
                </div>
            </div>
            <div class="filter-group">
                <button class="dropdown-btn">Price</button>
                <div class="dropdown-content-filter">
                    <label><input type="checkbox" name="price" value="Below Rs 10000"> Below Rs 10000<toabel><br>
                            <label><input type="checkbox" name="price" value="Rs 10000  Rs 20000"> Rs 10000 to Rs 20000</label><br>
                            <label><input type="checkbox" name="price" value="Rs 20000  Rs 30000"> Rs 20000 to Rs 30000</label><br>
                            <label><input type="checkbox" name="price" value="Rs 30000  Rs 45000"> Rs 30000 to Rs 45000</label><br>
                            <label><input type="checkbox" name="price" value="Rs 45000  Rs 60000"> Rs 45000 to Rs 60000</label><br>
                            <label><input type="checkbox" name="price" value="Rs 60000  Rs 80000"> Rs 60000 to Rs 80000</label><br>
                            <label><input type="checkbox" name="price" value="Rs 80000  Rs 100000"> Rs 800000 to Rs 100000</label><br>
                            <label><input type="checkbox" name="price" value="Above Rs 100000"> Above Rs 100000</label><br>
                </div>
            </div>
            <div class="filter-group">
                <button class="dropdown-btn">Color</button>
                <div class="dropdown-content-filter">
                    <label><input type="checkbox" name="color" value="Blue"> Blue</label><br>
                    <label><input type="checkbox" name="color" value="Red"> Red</label><br>
                    <label><input type="checkbox" name="color" value="White"> White</label><br>
                    <label><input type="checkbox" name="color" value="Black"> Black</label><br>
                    <label><input type="checkbox" name="color" value="Pink"> Pink</label><br>
                    <label><input type="checkbox" name="color" value="Silver"> Silver</label><br>
                    <label><input type="checkbox" name="color" value="Purple"> Purple</label><br>
                    <label><input type="checkbox" name="color" value="Brown"> Brown</label><br>
                    <label><input type="checkbox" name="color" value="Yellow"> Yellow</label><br>
                    <label><input type="checkbox" name="color" value="Grey"> Grey</label><br>
                    <label><input type="checkbox" name="color" value="Green"> Green</label><br>
                    <label><input type="checkbox" name="color" value="Violet"> Violet</label><br>
                    <label><input type="checkbox" name="color" value="Beige"> Beige</label><br>
                    <label><input type="checkbox" name="color" value="Dark Blue"> Dark Blue</label><br>
                    <label><input type="checkbox" name="color" value="Rose Gold"> Rose Gold</label><br>
                    <label><input type="checkbox" name="color" value="Peach"> Peach</label><br>

                </div>
                <div class="filter-group">
                    <button class="dropdown-btn" style="margin-top: 18px;">Battery Capacity</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="battery_capacity" value="4000 - 4500mAh"> 4000 - 4500mAh<br>
                            <label><input type="checkbox" name="battery_capacity" value="4500 - 5000mAh"> 4500 - 5000mAh<br>
                                <label><input type="checkbox" name="battery_capacity" value="Above 5000mAh"> Above 5000mAh<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">Processor</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="processor" value="MediaTek"> MediaTek<br>
                            <label><input type="checkbox" name="processor" value="Snapdragon"> Snapdragon<br>
                                <label><input type="checkbox" name="processor" value="Exynos"> Exynos<br>
                                    <label><input type="checkbox" name="processor" value="Unisoc"> Unisoc<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">Network</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="network" value="2G"> 2G<br>
                            <label><input type="checkbox" name="network" value="3G"> 3G<br>
                                <label><input type="checkbox" name="network" value="4G"> 4G<br>
                                    <label><input type="checkbox" name="network" value="5G"> 5G<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">RAM</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="ram" value="3GB"> 3GB<br>
                            <label><input type="checkbox" name="ram" value="4GB"> 4GB<br>
                                <label><input type="checkbox" name="ram" value="6GB"> 6GB<br>
                                    <label><input type="checkbox" name="ram" value="8GB"> 8GB<br>
                                        <label><input type="checkbox" name="ram" value="12GB"> 12GB<br>
                                            <label><input type="checkbox" name="ram" value="16GB"> 16GB<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">Storage</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="storage" value="64GB"> 64GB<br>
                            <label><input type="checkbox" name="storage" value="128GB"> 128GB<br>
                                <label><input type="checkbox" name="storage" value="256GB"> 256GB<br>
                                    <label><input type="checkbox" name="storage" value="512GB"> 512GB<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">SIM</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="sim" value="Dual Sim"> Dual Sim<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">Camera Configuration</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="camera" value="Single Camera"> Single Camera<br>
                            <label><input type="checkbox" name="camera" value="Dual Camera"> Dual Camera<br>
                                <label><input type="checkbox" name="camera" value="Triple Camera"> Triple Camera<br>
                                    <label><input type="checkbox" name="camera" value="Quad Camera"> Quad Camera<br>
                                        <label><input type="checkbox" name="camera" value="64 MP Triple Rear Camera"> 64 MP Triple Rear Camera<br>
                                            <label><input type="checkbox" name="camera" value="108 MP Triple Rear Camera"> 108 MP Triple Rear Camera<br>
                                                <label><input type="checkbox" name="camera" value="50 MP Triple Rear Camera"> 50 MP Triple Rear Camera<br>
                                                    <label><input type="checkbox" name="camera" value="13 MP Rear Camera"> 13 MP Rear Camera<br>
                                                        <label><input type="checkbox" name="camera" value="50 MP Dual Rear Camera"> 50 MP Dual Rear Camera<br>
                                                            <label><input type="checkbox" name="camera" value="48 MP Triple Rear Camera"> 48 MP Triple Rear Camera<br>
                                                                <label><input type="checkbox" name="camera" value="48 MP Dual Rear Camera"> 48 MP Dual Rear Camera<br>
                                                                    <label><input type="checkbox" name="camera" value="8 MP Dual Rear Camera"> 8 MP Dual Rear Camera<br>
                                                                        <label><input type="checkbox" name="camera" value="13 MP Dual Rear Camera"> 13 MP Dual Rear Camera<br>
                                                                            <label><input type="checkbox" name="camera" value="12 MP Dual Rear Camera"> 12 MP Dual Rear Camera<br>
                                                                                <label><input type="checkbox" name="camera" value="5 MP Front Camera"> 5 MP Front Camera<br>
                                                                                    <label><input type="checkbox" name="camera" value="50 MP Front Camera"> 50 MP Front Camera<br>
                                                                                        <label><input type="checkbox" name="camera" value="16 MP Front Camera"> 16 MP Front Camera<br>
                                                                                            <label><input type="checkbox" name="camera" value="13 MP Front Camera"> 13 MP Front Camera<br>
                                                                                                <label><input type="checkbox" name="camera" value="32 MP Front Camera"> 32 MP Front Camera<br>
                                                                                                    <label><input type="checkbox" name="camera" value="8 MP Front Camera"> 8 MP Front Camera<br>
                                                                                                        <label><input type="checkbox" name="camera" value="10 MP Front Camera"> 10 MP Front Camera<br>
                                                                                                            <label><input type="checkbox" name="camera" value="10 MP Dual Front Camera"> 10 MP Dual Front Camera<br>
                                                                                                                <label><input type="checkbox" name="camera" value="12 MP Front Camera"> 12 MP Front Camera<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">Operating System</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="os" value="Android"> Android<br>
                            <label><input type="checkbox" name="os" value="iOS"> iOS<br>
                                <label><input type="checkbox" name="os" value="Android 12"> Android 12<br>
                                    <label><input type="checkbox" name="os" value="iOS 15"> iOS 15<br>
                    </div>
                </div>
                <div class="filter-group">
                    <button class="dropdown-btn">Stock</button>
                    <div class="dropdown-content-filter">
                        <label><input type="checkbox" name="stock" value="instock"> In Stock<br>
                            <label><input type="checkbox" name="stock" value="outofstock"> Include Out of Stock<br>
                    </div>
                </div>
            </div>
            </form>
            <!-- Repeat the same structure for other filter groups -->
        </div>
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
        include 'connection.php';

        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<div class="product-list">';
            while ($row = $result->fetch_assoc()) {
                $fullStars = floor($row["average_rating"]);
                $halfStar = ($row["average_rating"] - $fullStars) >= 0.5 ? 1 : 0;
                $emptyStars = 5 - $fullStars - $halfStar;

                $oldPrice = (int)$row["old_price"];
                $newPrice = (int)$row["new_price"];
                $discountPercentage = round((($oldPrice - $newPrice) / $oldPrice) * 100);

                echo '<div class="product">';
                echo '<img src="' . $row["product_image1"] . '" alt="' . $row["product_name"] . '" class="product-image">';
                echo '<div class="product-details">';
                echo '<h2 class="product-name"><a style="text-decoration:none;color:black;font-weight:500;" onmouseover="this.style.color=\'orange\'" onmouseout="this.style.color=\'black\'" href="product_details.php?product_id=' . $row["product_id"] . '">' . $row["product_name"] . '</a></h2>';
                echo '<div class="price-container">';
                echo '<span class="new-price">₹' . formatIndianNumber($newPrice) . '</span>';
                echo '<span class="old-price">M.R.P: ₹' . formatIndianNumber($oldPrice) . '</span>';
                echo '<span class="discount">(' . $discountPercentage . '% off)</span>';
                echo '</div>';
                echo '<div class="product-ratings">';
                for ($i = 0; $i < $fullStars; $i++) {
                    echo '<i class="fas fa-star"></i>';
                }
                if ($halfStar) {
                    echo '<i class="fas fa-star-half-alt"></i>';
                }
                for ($i = 0; $i < $emptyStars; $i++) {
                    echo '<i class="far fa-star"></i>';
                }
                echo '<span class="number-of-ratings">(' . formatIndianNumber($row["number_of_ratings"]) . ' ratings)</span>';
                echo '</div>';
                echo '<p class="number-of-buyers" style="margin:0;padding:0px;font-weight:600;">' . formatIndianNumber($row["number_of_buyers"]) . ' + bought in past month</p>';
                echo '<p class="save-extra" style="margin:0;padding:5px 0px;">Save extra with No Cost EMI</p>';
                echo '<p class="free-delivery" style="margin:0;padding:0px;">FREE Delivery <strong>Tomorrow 8 am - 12 pm</strong></p>';
                $product_id = $row["product_id"];
                $productLink = $isLoggedIn ? 'add_to_cart.php?product_id=' . $product_id : 'signin.php';
                echo '
                        <a href="' . $productLink . '" class="add-to-cart" data-product-id="' . $product_id . '" style="text-align:center; margin-top:20px;">Add to Cart</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo "0 results";
        }

        $conn->close();
        ?>

    </div>
    <script>
        document.querySelectorAll('.dropdown-btn').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                button.classList.add('active'); // Ensure active class is applied
                content.style.display = 'block'; // Ensure content is visible
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.add-to-cart').forEach(function(button) {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    <?php if ($isLoggedIn): ?>
                        // Redirect to add_to_cart.php with the product ID as a query parameter
                        window.location.href = 'add_to_cart.php?product_id=' + productId;
                    <?php else: ?>
                        // Redirect to signin page
                        window.location.href = 'signin.php';
                    <?php endif; ?>
                });
            });
        });
        document.getElementById('filter-form').addEventListener('change', function() {
    const formData = new FormData(this);
    const queryString = new URLSearchParams(formData).toString();

    fetch('filter_products.php?' + queryString)
        .then(response => response.text())
        .then(data => {
            document.querySelector('.product-list').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
});

    </script>
</body>

</html>