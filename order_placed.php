<?php
session_start();

// Function to calculate expected delivery date
function getExpectedDeliveryDate($days)
{
    return date("Y-m-d", strtotime("+$days days"));
}

// Set expected delivery date range
$delivery_start = getExpectedDeliveryDate(2);
$delivery_end = getExpectedDeliveryDate(3);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <style>
        body {
            font-family: 'Open sans', sans-serif;
            text-align: center;
            padding: 50px;
        }

        .info {
            margin-top: 20px;
        }

        .thank-you {
            font-size: 20px;
            color: green;
        }

        .tracking-info {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #ccc;
            display: inline-block;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <?php
    if (isset($_SESSION['order_placed'])) {
        // Display SweetAlert notification
        echo "<script>
        swal({
            title: 'Success!',
            text: 'Your order has been placed successfully!',
            type: 'success',
            timer: 3000,
            showConfirmButton: false
        }).then(function() {
            window.location = 'index.php'; // Redirect to the homepage or any other page
        });
    </script>";

        // Unset the session variable after showing the alert
        unset($_SESSION['order_placed']);
    } else {
        // If session variable is not set, redirect to homepage
        header("Location: index.php");
        exit();
    }
    ?>

    <div class="thank-you">Thank you! Your order has been placed.</div>

    <div class="info">
        <p>Please check your email for order confirmation or detailed delivery information.</p>
        <div class="tracking-info">
            <h3>Tracking Information</h3>
            <p>Your expected delivery date is between <strong><?php echo $delivery_start; ?></strong> and <strong><?php echo $delivery_end; ?></strong>.</p>
            <p>You can track your order status through the link sent to your email.</p>
        </div>
    </div>

</body>

</html>