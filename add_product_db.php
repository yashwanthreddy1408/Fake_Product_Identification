<?php
// Database configuration
$host = 'localhost';
$db = 'securecart';
$user = 'root';
$pass = '';

// Create a PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Collect data from the form
$data = [
    'product_name' => $_POST['product_name'],
    'brand' => $_POST['brand'],
    'product_description' => $_POST['product_description'],
    'old_price' => $_POST['old_price'],
    'new_price' => $_POST['new_price'],
    'average_rating' => $_POST['average_rating'],
    'number_of_ratings' => $_POST['number_of_ratings'],
    'number_of_buyers' => $_POST['number_of_buyers'],
    'operating_system' => $_POST['operating_system'],
    'memory_storage_capacity' => $_POST['memory_storage_capacity'],
    'screen_size' => $_POST['screen_size'],
    'resolution' => $_POST['resolution'],
    'color' => $_POST['color'],
    'quantity' => $_POST['quantity'],
    'ram' => $_POST['ram'],
    'processor_type' => $_POST['processor_type'],
    'processor' => $_POST['processor'],
    'network' => $_POST['network'],
    'rearCamera' => $_POST['rearCamera'],
    'frontCamera' => $_POST['frontCamera'],
    'battery_capacity' => $_POST['battery_capacity'],
];

// Process image URLs
$imageURLs = isset($_POST['image_urls']) ? array_map('trim', explode(',', $_POST['image_urls'])) : [];
$errors = [];

// Check if required fields are filled
foreach (['product_name', 'old_price', 'new_price', 'average_rating', 'number_of_ratings', 'number_of_buyers'] as $field) {
    if (empty($data[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
    }
}

// Validate image URLs (basic validation)
foreach ($imageURLs as $url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid URL format: $url";
    }
}

// If there are errors, display them
if (!empty($errors)) {
    echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
    exit;
}

// Function to generate a unique random string
function generateUniqueRandomString($pdo, $length = 100)
{
    do {
        $randomString = bin2hex(random_bytes($length / 2)); // Generate a random string
        // Check if the string already exists in the database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE qrcode_text = :qrcode_text");
        $stmt->execute(['qrcode_text' => $randomString]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists); // Repeat until a unique string is found
    return $randomString;
}

// Check if the product already exists by name or some other unique identifier (e.g., ID, SKU)
$product_name = $data['product_name'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_name = :product_name");
$stmt->execute(['product_name' => $product_name]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // If product exists and qrcode_text is empty or null, generate a new qrcode_text
    if (empty($product['qrcode_text'])) {
        $qrcode_text = generateUniqueRandomString($pdo);
        // Update the existing product with the new qrcode_text
        $updateSql = "UPDATE products SET qrcode_text = :qrcode_text WHERE product_name = :product_name";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'qrcode_text' => $qrcode_text,
            'product_name' => $product_name,
        ]);
    } else {
        // Use existing qrcode_text
        $qrcode_text = $product['qrcode_text'];
    }
} else {
    // If product does not exist, generate a new qrcode_text
    $qrcode_text = generateUniqueRandomString($pdo);
}

// Prepare and execute the SQL insert query for a new product
$sql = "INSERT INTO products (
    product_name, brand, product_description, old_price, new_price, average_rating, number_of_ratings, number_of_buyers,
    operating_system, memory_storage_capacity, screen_size, resolution, color, quantity, ram, processor_type, processor, 
    network, rearCamera, frontCamera, battery_capacity, qrcode_text, product_image1, product_image2, product_image3, product_image4, product_image5, product_image6, product_image7
) VALUES (
    :product_name, :brand, :product_description, :old_price, :new_price, :average_rating, :number_of_ratings, :number_of_buyers,
    :operating_system, :memory_storage_capacity, :screen_size, :resolution, :color, :quantity, :ram, :processor_type, :processor,
    :network, :rearCamera, :frontCamera, :battery_capacity, :qrcode_text, :image1, :image2, :image3, :image4, :image5, :image6, :image7
)";

$stmt = $pdo->prepare($sql);

// Bind parameters for insert
$params = array_merge($data, [
    'qrcode_text' => $qrcode_text,
    'image1' => $imageURLs[0] ?? null,
    'image2' => $imageURLs[1] ?? null,
    'image3' => $imageURLs[2] ?? null,
    'image4' => $imageURLs[3] ?? null,
    'image5' => $imageURLs[4] ?? null,
    'image6' => $imageURLs[5] ?? null,
    'image7' => $imageURLs[6] ?? null,
]);

$stmt->execute($params);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Show SweetAlert on page load
            Swal.fire({
                title: 'Success!',
                text: 'Product has been added successfully.',
                icon: 'success',
                confirmButtonText: 'View Product'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to display_product.php with the QR code text
                    window.location.href = "display_product.php?qrcode_text=<?php echo urlencode($qrcode_text); ?>";
                }
            });
        });
    </script>
</head>

<body>
</body>

</html>