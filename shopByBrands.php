<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./images/favicon.png">
    <title>Shop by Brands</title>
    <style>
        body {
            font-family: "Amazon Ember", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .brands-section {
            text-align: center;
            padding: 10px 20px;
        }

        .brands-section h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #333;
        }

        .brands-container {
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            gap: 20px;
            overflow-x: auto;
            padding: 20px 0;
        }

        .brand {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            width: 138px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            flex: 0 0 auto;
        }

        .brand img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .brand p {
            margin: 15px 0 0;
            font-size: 1.2em;
            color: #555;
        }

        .brand:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="brands-section">
        <h1>Shop by Brands</h1>
        <div class="brands-container">
            <div class="brand">
                <a href="search.php?query=Apple">
                    <img src="https://suprememobiles.in/cdn/shop/files/Apple_6eed614b-bb3f-4381-9245-a9451182b314.jpg?v=1666018131" alt="Apple">
                    <p>Apple</p>
                </a>
            </div>
            <div class="brand">
                <a href="search.php?query=Samsung">
                    <img src="https://suprememobiles.in/cdn/shop/files/Samsung_2bc2196e-f658-404f-b316-d609379fbc3a.jpg?v=1666018150" alt="Samsung">
                    <p>Samsung</p>
                </a>
            </div>
            <div class="brand">
                <a href="search.php?query=Oppo">
                    <img src="https://suprememobiles.in/cdn/shop/files/Oppo_fcceb162-89c6-4cb2-85bb-33b0a75ecb49.jpg?v=1666018150" alt="Oppo">
                    <p>Oppo</p>
                </a>
            </div>
            <div class="brand">
                <a href="search.php?query=Vivo">
                    <img src="https://suprememobiles.in/cdn/shop/files/Vivo_d2a18001-0099-415e-adfd-9f55ee78fa53.jpg?v=1666018159" alt="Vivo">
                    <p>Vivo</p>
                </a>
            </div>
            <div class="brand">
                <a href="search.php?query=Redmi">
                    <img src="https://suprememobiles.in/cdn/shop/files/Xiaomi_bb695e87-1c33-4d7d-add1-4509580eb742.jpg?v=1666018159" alt="Redmi">
                    <p>Redmi</p>
                </a>
            </div>
            <div class="brand">
                <a href="search.php?query=Realme">
                    <img src="https://suprememobiles.in/cdn/shop/files/Realme_b45d11ee-5ccd-498e-b222-087d030f47a0.jpg?v=1666018150" alt="Realme">
                    <p>Realme</p>
                </a>
            </div>
            <div class="brand">
                <a href="search.php?query=OnePlus">
                    <img src="https://suprememobiles.in/cdn/shop/files/Oneplus_0be39f98-b091-41e5-9177-fb8bd18e9f0d.jpg?v=1666018149" alt="OnePlus">
                    <p>OnePlus</p>
                </a>
            </div>
        </div>
    </div>

</body>

</html>