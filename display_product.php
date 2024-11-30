<?php
// Retrieve the QR code text from the URL
$qrcode_text = $_GET['qrcode_text'] ?? '';

// Generate the QR code URL
$qr_code_url = "https://quickchart.io/qr?text=" . urlencode($qrcode_text) . "&size=300";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product QR Code</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap");

        body {
            font-family: "Open Sans", sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #ffffff;
        }

        .container {
            background: #fff;
            border-radius: 15px;
            padding: 50px;
            max-width: 700px;
            width: 90%;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
            letter-spacing: 1.5px;
            font-family: 'Trebuchet MS', sans-serif;
        }

        .description {
            font-size: 18px;
            color: #555;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .qr-code img {
            border: 6px solid #f0f0f0;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .qr-code img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .qr-code {
            margin-top: 20px;
        }

        .cta-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: #764ba2;
            color: #fff;
            text-transform: uppercase;
            font-size: 16px;
            letter-spacing: 1.2px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .cta-button:hover {
            background-color: #5c3c91;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 28px;
            }

            .description {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Scan Your Product QR Code</h1>
        <p class="description">
            This unique QR code is generated for this product to ensure its authenticity and security. It will be provided to the buyer for verification and easy access to product details.
        </p>
        <div class="qr-code">
            <img src="<?php echo $qr_code_url; ?>" alt="QR Code">
        </div>
        <button class="cta-button" onclick="window.location.href='index.php'">Go Home</button>

    </div>

</body>

</html>