# Fake Product Identification System

## Project Overview
This project addresses the growing issue of counterfeit products by developing a QR code-based system to ensure product authenticity. The web application enables consumers to verify and track the authenticity of their purchases from production to delivery, fostering trust and protecting brand integrity.

## Features
- **QR Code Integration**: Each product is assigned a unique QR code for easy identification and tracking.
- **Product Authentication**: Users can scan QR codes to verify the authenticity of products in real-time.
- **E-Commerce Features**: Includes cart, checkout, and order tracking for a seamless authentication experience.
- **Real-Time Product Verification**: The application connects to a secure MySQL database for verifying product authenticity as soon as a QR code is scanned.
- **Product Traceability**: Trace the product journey from production to delivery by scanning the QR code.

## How to Use
1. Open the web application in your browser.
2. Scan the QR code on the product using your mobile device or web camera.
3. The application will display the product’s authenticity status and provide traceability information.
4. You can make a purchase, track your order, and authenticate your product with each scan.

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript, PHP, Tailwind CSS  
- **Backend**: MySQL for secure product verification and traceability  
- **QR Code Generation**: PHP for generating unique QR codes for each product  
- **Real-Time Product Verification**: PHP and MySQL for immediate authentication  
- **E-Commerce Features**: Cart, checkout, and order tracking for user convenience

## Getting Started

To run this project locally, follow these steps:

### Prerequisites
Before you begin, make sure you have the following software installed on your computer:

- **PHP**: Download it from [php.net](https://www.php.net).
- **MySQL**: Ensure MySQL is running for product database management.

### Installation
1. Clone the repository to your local machine using Git:

    ```bash
    git clone https://github.com/yashwanthreddy1408/Fake_Product_Identification.git
    ```

2. Navigate to the project's directory:

    ```bash
    cd Fake_Product_Identification
    ```

3. Set up your MySQL database and import the provided SQL file for product and order data.

4. Configure the database connection in the `config.php` file.

5. Run the application with a local server, e.g., using XAMPP or a similar tool.

### Running the Project
Once everything is set up, start your local server and navigate to the URL where your application is hosted (e.g., `http://localhost:8000`).

### Usage
- You can now use the web application to:
  - Scan QR codes to verify the authenticity of products.
  - Track the journey of your products from production to delivery.
  - Make purchases and authenticate your orders.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.

## Acknowledgements
- **QR Code Generation**: PHP QR Code library for generating unique product identifiers.  
- **MySQL**: Used for real-time product verification and database management.  
- **Tailwind CSS**: A utility-first CSS framework for designing responsive and modern user interfaces.
