<?php
include 'connection.php'; // Include your database connection file
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}
$user_id = $_SESSION['id']; // Assuming user ID is stored in session

// Function to format numbers according to the Indian numbering system
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

// Fetch cart items and product details
$sql = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.new_price, p.product_image1, p.quantity AS stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $cart_items = [];
}
$conn->close();

// Calculate totals
$totalItems = count($cart_items);
$totalAmount = array_reduce($cart_items, function ($carry, $item) {
    return $carry + ($item['new_price'] * $item['quantity']);
}, 0);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMfsFB9XdLuoP+CZ/L3sW7cgRmr7YKk9e1IdY+M" crossorigin="anonymous">

    <link rel="stylesheet" href="./styles/cartStyles.css"> <!-- Link to your CSS file -->
</head>

<body>
    <?php include 'navbar.php'; // Include the navigation bar 
    ?>
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        <a href="#" id="select-all" class="select-all">Select all items</a> <!-- Anchor tag for select/deselect all -->

        <!-- Line divider -->
        <hr class="divider">

        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="product" id="product-container-<?php echo $item['cart_id']; ?>" data-stock-quantity="<?php echo $item['stock_quantity']; ?>">
                    <input type="checkbox" id="product-<?php echo $item['cart_id']; ?>" class="product-checkbox">
                    <label for="product-<?php echo $item['cart_id']; ?>" class="product-checkbox-label"></label>
                    <img src="<?php echo htmlspecialchars($item['product_image1']); ?>" alt="Product Image" class="product-image">
                    <div class="product-details">
                        <a href="product_details.php?product_id=<?php echo $item['product_id']; ?>" class="product-title">
                            <?php echo htmlspecialchars($item['product_name']); ?>
                        </a>
                        <p class="product-price">Price:
                            <span id="price-<?php echo $item['cart_id']; ?>" data-price-per-unit="<?php echo htmlspecialchars($item['new_price']); ?>" style="font-weight:700;">
                                ₹<?php echo formatIndianNumber($item['new_price'] * $item['quantity']); ?>
                            </span>
                        </p>
                        <p class="product-stock <?php echo $item['stock_quantity'] > 10 ? 'in-stock' : ($item['stock_quantity'] > 0 ? 'low-stock' : 'out-of-stock'); ?>">
                            <?php
                            if ($item['stock_quantity'] > 10) {
                                echo 'In Stock';
                            } elseif ($item['stock_quantity'] > 0) {
                                echo 'Only ' . $item['stock_quantity'] . ' left in stock';
                            } else {
                                echo 'Out of Stock';
                            }
                            ?>
                        </p>
                        <div class="quantity-controls">
                            <button class="quantity-button" onclick="changeQuantity(<?php echo $item['cart_id']; ?>, -1)" style="margin-left:0px;">-</button>
                            <input type="text" id="quantity-<?php echo $item['cart_id']; ?>" value="<?php echo $item['quantity']; ?>" class="quantity-input" readonly>
                            <button class="quantity-button" onclick="changeQuantity(<?php echo $item['cart_id']; ?>, 1)">+</button>
                        </div>
                        <div class="options">
                            <button class="option-button" onclick="proceedToCheckout(<?php echo $item['cart_id']; ?>)" style="padding-left:0px;">Proceed to Checkout</button>
                            <span class="divider"></span>
                            <button class="option-button" onclick="deleteItem(<?php echo $item['cart_id']; ?>)">Delete</button>
                            <span class="divider"></span>
                            <button class="option-button">Save for Later</button>
                            <span class="divider"></span>
                            <button class="option-button">See More Like This</button>
                            <span class="divider"></span>
                            <button class="option-button">Share</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemCountElement = document.getElementById('item-count');
            const totalAmountElement = document.getElementById('total-amount-value');
            const checkoutButton = document.getElementById('checkout-button');

            // Function to format Indian numbers
            function formatIndianNumber(number) {
                number = number.toString();
                const lastThree = number.substring(number.length - 3);
                const otherNumbers = number.substring(0, number.length - 3);
                if (otherNumbers !== '') {
                    lastThree = ',' + lastThree;
                }
                return otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
            }
        });
    </script>
    <script>
        function updateStockStatus(cart_id, quantity) {
            var stockElement = document.querySelector('#product-container-' + cart_id + ' .product-stock');
            var stockQuantity = parseInt(stockElement.dataset.stockQuantity);

            if (stockQuantity > 10) {
                stockElement.textContent = 'In Stock';
                stockElement.className = 'product-stock in-stock';
            } else if (stockQuantity > 0) {
                stockElement.textContent = 'Only ' + stockQuantity + ' left in stock';
                stockElement.className = 'product-stock low-stock';
            } else {
                stockElement.textContent = 'Out of Stock';
                stockElement.className = 'product-stock out-of-stock';
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllLink = document.getElementById('select-all');
            let allSelected = false;

            selectAllLink.addEventListener('click', function(e) {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('.product-checkbox');
                allSelected = !allSelected;

                checkboxes.forEach(checkbox => {
                    checkbox.checked = allSelected;
                });

                selectAllLink.textContent = allSelected ? 'Deselect all items' : 'Select all items';
            });
        });

        function changeQuantity(cart_id, amount) {
            var quantityInput = document.getElementById('quantity-' + cart_id);
            if (!quantityInput) {
                console.error('Quantity input not found for cart_id: ' + cart_id);
                return;
            }

            var currentQuantity = parseInt(quantityInput.value);
            var productContainer = document.getElementById('product-container-' + cart_id);
            if (!productContainer) {
                console.error('Product container not found for cart_id: ' + cart_id);
                return;
            }

            var stockQuantity = parseInt(productContainer.getAttribute('data-stock-quantity'));
            if (isNaN(stockQuantity)) {
                console.error('Stock quantity is not a valid number for cart_id: ' + cart_id);
                return;
            }

            var newQuantity = currentQuantity + amount;

            // Ensure newQuantity is between 1 and stockQuantity
            if (newQuantity < 1) {
                newQuantity = 1;
            } else if (newQuantity > stockQuantity) {
                newQuantity = stockQuantity;
            }

            // Update quantity in the input field
            quantityInput.value = newQuantity;

            // Get the price per unit from the price element's data attribute
            var priceElement = document.getElementById('price-' + cart_id);
            if (!priceElement) {
                console.error('Price element not found for cart_id: ' + cart_id);
                return;
            }

            var pricePerUnit = parseFloat(priceElement.getAttribute('data-price-per-unit'));
            if (isNaN(pricePerUnit)) {
                console.error('Price per unit is not a valid number for cart_id: ' + cart_id);
                return;
            }

            var newPrice = pricePerUnit * newQuantity;

            // Format the new price and update the text content
            priceElement.textContent = '₹' + formatIndianNumber(newPrice);

            // Send AJAX request to update the quantity in the database
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_quantity.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                var messageElement = document.getElementById('message-' + cart_id);
                if (!messageElement) {
                    console.error('Message element not found for cart_id: ' + cart_id);
                    return;
                }

                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Successfully updated the quantity
                        console.log('Quantity updated successfully');
                        messageElement.textContent = 'Quantity updated successfully';
                        messageElement.style.color = 'green';
                    } else {
                        // Display error message
                        console.error('Error updating quantity: ' + response.error);
                        messageElement.textContent = 'Error updating quantity: ' + response.error;
                        messageElement.style.color = 'red';
                    }
                } else {
                    console.error('Error updating quantity');
                    messageElement.textContent = 'Error updating quantity';
                    messageElement.style.color = 'red';
                }
            };
            xhr.onerror = function() {
                var messageElement = document.getElementById('message-' + cart_id);
                if (!messageElement) {
                    console.error('Message element not found for cart_id: ' + cart_id);
                    return;
                }
                console.error('Request failed');
                messageElement.textContent = 'Request failed';
                messageElement.style.color = 'red';
            };
            xhr.send('cart_id=' + encodeURIComponent(cart_id) + '&quantity=' + encodeURIComponent(newQuantity));
        }

        function formatIndianNumber(number) {
            number = number.toString();
            const lastThree = number.substring(number.length - 3);
            const otherNumbers = number.substring(0, number.length - 3);
            if (otherNumbers !== '') {
                return otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + ',' + lastThree;
            }
            return lastThree;
        }



        function deleteItem(cart_id) {
            var productContainer = document.getElementById('product-container-' + cart_id);

            // Make an AJAX request to delete_item.php
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_item.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Successfully deleted the item
                        productContainer.remove(); // Remove item from DOM
                    } else {
                        // Display error message
                        alert('Error: ' + response.error);
                    }
                } else {
                    alert('Request failed. Returned status of ' + xhr.status);
                }
            };
            xhr.send('cart_id=' + encodeURIComponent(cart_id));
        }

        function proceedToCheckout(cart_id) {
            // Redirect to the checkout page or handle checkout process
            window.location.href = 'checkout.php?cart_id=' + cart_id;
        }
    </script>
</body>

</html>