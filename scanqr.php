<?php
include 'connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner</title>
    <link rel="stylesheet" href="style_qr.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script src="qrjavascript.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .result {
            background-color: green;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
        }

        .row {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .col {
            padding: 30px;
            text-align: center;
        }

        #reader {
            width: 500px;
        }

        h4 {
            margin-bottom: 10px;
        }

        #checkres {
            margin-top: 20px;
        }

        #checkres input {
            width: calc(70% - 10px);
            padding: 10px;
            box-sizing: border-box;
            margin-top: 10px;
            font-size: 16px;
        }

        #checkres button {
            width: 30%;
            padding: 10px;
            box-sizing: border-box;
            margin-top: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .contact-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .contact-btn:hover {
            background-color: #0056b3;
        }

        a {
            text-decoration: none;
            color: inherit;
            cursor: default;
        }
    </style>

    <div class="row">
        <div class="col">
            <div id="reader"></div>
        </div>
        <div class="col">
            <h4>SCAN RESULT</h4>
            <div id="result"></div>

            <form action="" method="post">
                <div id="checkres">
                    <input type="text" id="resultInput" name="checkresult" hidden>
                    <button type="submit" id="resultButton" name="check" hidden></button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (isset($_POST['check'])) {
        $check_result = $_POST['checkresult'];

        // Use prepared statements to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM products WHERE qrcode_text = ?");
        $stmt->bind_param("s", $check_result);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If product is found, redirect to successful.php with the QR code text as a parameter
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Product Found!',
                text: 'The product associated with this QR code exists in our database.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'successful.php?qrcode=" . urlencode($check_result) . "'; // Pass QR code text as parameter
                }
            });
        </script>";
        } else {
            // Display error message with contact option
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Product Not Found!',
                text: 'We couldn’t find a product associated with this QR code. Please contact us for assistance.',
                showCancelButton: true,
                confirmButtonText: 'Contact Us',
                cancelButtonText: 'Try Again'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'contact_us.php'; // Replace with your contact page
                } else {
                    window.location.reload(); // Reload page to scan again
                }
            });
        </script>";
        }
    }

    ?>

    <script type="text/javascript">
        function onScanSuccess(qrCodeMessage) {
            document.getElementById('resultInput').value = qrCodeMessage; // Set scanned QR code value
            document.getElementById('resultButton').click(); // Submit the form
        }

        function onScanError(errorMessage) {
            console.error("Scan error: ", errorMessage);
            // Optional: Display a message to the user about the scan error
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: 250
            });
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    </script>
</body>

</html>