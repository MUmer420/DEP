<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "task";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create customers table if it doesn't exist
$createCustomersTable = "CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL
)";

// Create orders table if it doesn't exist
$createOrdersTable = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
)";

if ($conn->query($createCustomersTable) !== TRUE) {
    die("Error creating customers table: " . $conn->error);
}

if ($conn->query($createOrdersTable) !== TRUE) {
    die("Error creating orders table: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customerName']) && isset($_POST['customerEmail']) && isset($_POST['customerAddress']) && isset($_POST['cartData'])) {
    $customerName = $conn->real_escape_string($_POST['customerName']);
    $customerEmail = $conn->real_escape_string($_POST['customerEmail']);
    $customerAddress = $conn->real_escape_string($_POST['customerAddress']);
    $cartData = json_decode($_POST['cartData'], true);

    $sql = "INSERT INTO customers (name, email, address) VALUES ('$customerName', '$customerEmail', '$customerAddress')";
    if ($conn->query($sql) === TRUE) {
        $customerId = $conn->insert_id;

        foreach ($cartData as $item) {
            $product = $conn->real_escape_string($item['product']);
            $price = $conn->real_escape_string($item['price']);
            $sql = "INSERT INTO orders (customer_id, product, price) VALUES ('$customerId', '$product', '$price')";
            $conn->query($sql);
        }
        echo "Order placed successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boys Fitness E-commerce</title>
    <style>
        body {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('/images/webbacground.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
        }

        ul {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: larger;
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #f3f3f3;
            border-bottom: 1px solid #e7e7e7;
        }

        li {
            float: left;
        }

        li a {
            display: block;
            color: blue;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        li a:hover:not(.active) {
            background-color: grey;
        }

        li a.active {
            color: white;
            background-color: #04AA6D;
        }

        header {
            background-color: #339;
            color: white;
            padding-top: 20px;
            font-family: Georgia, 'Times New Roman', Times, serif;
            text-align: center;
        }

        main > section {
            display: none;
        }

        .active-page {
            display: block;
        }

        section {
            margin-bottom: 2em;
        }

        .home {
            font-family: Georgia, 'Times New Roman', Times, serif;
            border-radius: 6px;
            background-image: radial-gradient(gray, black, blue);
            text-align: center;
            margin-top: 12px;
            color: white;
        }

        .product {
            background-image: repeating-radial-gradient(cadetblue, grey, blueviolet);
            border: 1px solid #ccc;
            padding: 1em;
            margin-bottom: 1em;
            text-align: center;
        }

        #contact {
            background-color: blueviolet;
            padding: 40px;
            color:white;
            border-radius: 20px;  
        }

        #cart {
            padding: 30px;
            border-radius: 16px;
            background-color: grey;
        }
        #customerDetails{
            padding: 30px;
            border-radius: 16px;
            background-color: grey;
        }

        #cart h2 {
            font-family: Georgia, 'Times New Roman', Times, serif;
            border-radius: 6px;
            background-image: radial-gradient(gray, black, blue);
            text-align: center;
            color: white;
            padding: 12px;
        }

        #cart p {
            margin: 0.5em 0;
        }

        #cart ul {
            list-style-type: none;
            padding: 0;
        }

        #cart ul li {
            padding: 0.5em 0;
            border-bottom: 1px solid #ccc;
        }

        #cart button {
            background-color: red;
            color: white;
            border: none;
            padding: 8px;
            cursor: pointer;
        }

        #cart button:hover {
            background-color: #555;
        }
        #customerForm{
color:white;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 16px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Boys' Fitness Gear</h1>
        <p>Everyday is a new beginning</p>
        <ul>
            <li><a class="active" href="#home" onclick="showPage('home')">HOME</a></li>
            <li><a href="#products" onclick="showPage('products')">PRODUCTS</a></li>
            <li><a href="#contact" onclick="showPage('contact')">CONTACT US</a></li>
            <li><a href="#cart" onclick="showPage('cart')">CART</a></li>
        </ul>
    </header>
    <main>
        <section id="home" class="active-page">
            <div class="home">
                <h2>Welcome to Boys' Fitness Gear</h2>
                <p>Discover the best fitness gear designed specifically for boys.</p><br>
            </div>
            <h2 style="text-align:center; padding:12px;  font-family: Georgia, 'Times New Roman', Times, serif; color:white; border-radius:10px; background-color:red; ">OUR POPULAR FEATURED PRODUCTS</h2>
            <div class="product" id="product">
                
            <img src="/images/jump.jpg" alt="Jump Rope">
            
                <p style="color: white;">QuickFit Jump Rope - $20.00</p>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="addToCart('Jump Rope', 20)">Add to Cart</button>
            </div>
            <div class="product" id="product">
                <img src="/images/shoes.jpg" alt="Sports Shoes" width="200" height="200">
                <p style="color: white;">SpeedRun Sports Shoes - $25.00</p>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="addToCart('Sports Shoes', 25)">Add to Cart</button>
            </div>
        </section>
        
        <section id="products">
            <h2 style="text-align: center; padding:12px; color:white; border-radius:10px; background-color:red; ">OUR PRODUCTS</h2>
            <div class="product" id="product1">
                <img src="/images/band.jpg" alt="Band" width="200" height="200">
                <p style="color: white;">Band - $10.00</p>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="addToCart('Band', 10)">Add to Cart</button>
            </div>
            <div class="product" id="product2">
                <img src="/images/fitproduct.jpg" alt="Dumbell" width="200" height="200">
                <p style="color: white;">Dumbell - $15.00</p>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="addToCart('Dumbell', 15)">Add to Cart</button>
            </div>
            <div class="product" id="product3">
                <img src="/images/jump.jpg" alt="Jump Rope" width="200" height="200">
                <p style="color: white;">QuickFit Jump Rope - $20.00</p>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="addToCart('Jump Rope', 20)">Add to Cart</button>
            </div>
            <div class="product" id="product4">
                <img src="/images/shoes.jpg" alt="Sports Shoes" width="200" height="200">
                <p style="color: white;">SpeedRun Sports Shoes - $25.00</p>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="addToCart('Sports Shoes', 25)">Add to Cart</button>
            </div>
            <div class="product" id="product5">
                <img src="/images/gloves.jpg" alt="Training Gloves" width="200" height="200">
                <p style="color: white;">ImpactGuard Training Gloves - $30.00</p>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="addToCart('Training Gloves', 30)">Add to Cart</button>
            </div>
        </section><br><br><br><br><br><br>

        <section id="contact">
            <h2 style="            font-family: Georgia, 'Times New Roman', Times, serif;
            border-radius: 6px;
            background-image: radial-gradient(gray, black, blue);
            text-align: center;
            color: white;
            padding: 12px;">FEEL FREE TO REACH US</h2>
            <form action="contact.php" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea><br><br><br>
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;"  type="submit">Send</button>
            </form>
            <br><br>
        </section>

        <section id="cart">
            <h2>Your Cart</h2>
            <p>Your selected items are listed below. Review your cart and proceed to place your order.</p>
            <ul id="cartItems"></ul>
            <p>Total: $<span id="totalPrice">0.00</span></p>
            <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" onclick="showPage('customerDetails')">Proceed to Checkout</button>
        </section><br><br><br><br><br><br><br><br><br>
        <br><br><br><br>

        <section id="customerDetails">
            <h2 style="            font-family: Georgia, 'Times New Roman', Times, serif;
            border-radius: 6px;
            background-image: radial-gradient(gray, black, blue);
            text-align: center;
            color: white;
            padding: 12px;">Customer Details</h2>
            <form id="customerForm" action="save_order.php" method="post" onsubmit="return submitOrder()">
                <label  for="customerName">Name:</label>
                <input type="text" id="customerName" name="customerName" required><br><br>
                <label for="customerEmail">Email:</label>
                <input type="email" id="customerEmail" name="customerEmail" required><br><br>
                <label for="customerAddress">Address:</label>
                <textarea id="customerAddress" name="customerAddress" required></textarea><br><br>
                <input type="hidden" id="cartData" name="cartData">
                <button style="background-color: red; color: white; padding: 10px; border-radius: 8px;" type="submit">Place Order</button>
            </form>
        </section><br><br><br><br><br><br><br><br><br>
        <br><br><br><br>

    </main>
    <footer>
        <p>M. Usman &copy; 2024 All rights reserved</p>
    </footer>

    <script>
        let cart = [];

        function showPage(pageId) {
            document.querySelectorAll('main > section').forEach(section => {
                section.classList.remove('active-page');
            });
            document.getElementById(pageId).classList.add('active-page');

            document.querySelectorAll('ul li a').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`ul li a[href='#${pageId}']`).classList.add('active');
        }

        function addToCart(product, price) {
            cart.push({ product, price });
            updateCart();
        }

        function updateCart() {
            let cartItems = document.getElementById('cartItems');
            cartItems.innerHTML = '';
            let totalPrice = 0;
            cart.forEach(item => {
                let li = document.createElement('li');
                li.textContent = `${item.product} - $${item.price.toFixed(2)}`;
                cartItems.appendChild(li);
                totalPrice += item.price;
            });
            document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);
        }

        function placeOrder() {
            if (cart.length === 0) {
                alert('Your cart is empty. Add items before placing an order.');
                return;
            }
            showPage('customerDetails');
        }

        function submitOrder() {
            if (cart.length === 0) {
                alert('Your cart is empty. Add items before placing an order.');
                return false;
            }
            const cartDataInput = document.getElementById('cartData');
            cartDataInput.value = JSON.stringify(cart);
            return true;
        }
    </script>
</body>
</html>
