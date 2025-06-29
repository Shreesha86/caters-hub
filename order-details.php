<?php
// Database connection
session_start();
require('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_name']) || !isset($_SESSION['email'])) {
    header('Location: userlogin.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$email = $_SESSION['email']; // Correctly retrieve email from session

// Get platter id
$platter_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($platter_id <= 0) {
    header('Location: lunch-dinner.php');
    exit();
}

// Get platter details
$sql = "SELECT m.*, c.name as category_name, p.range_name 
        FROM menu_items m
        JOIN menu_categories c ON m.category_id = c.id
        JOIN price_ranges p ON m.price_range_id = p.id
        WHERE m.id = $platter_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: lunch-dinner.php');
    exit();
}

$platter = $result->fetch_assoc();

// Fetch user details - CORRECTED THIS PART
$query = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email); // Changed from "i" to "s" for string
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_date = date('Y-m-d');
    $delivery_date = $_POST['delivery_date'] . ' ' . $_POST['delivery_time'];
    $location = $_POST['location'];
    $contact_number = $_POST['contact_number'];
    $plates = $_POST['plates'];
    
    // Calculate total amount
    $base_price = $platter['price'] * $plates;
    
    // Apply discount based on number of plates
    $discount = 0;
    if ($plates >= 500) {
        $discount = $base_price * 0.20; // 20% off
    } elseif ($plates >= 200) {
        $discount = $base_price * 0.15; // 15% off
    } elseif ($plates >= 100) {
        $discount = $base_price * 0.10; // 10% off
    }
    
    $total_amount = $base_price - $discount;
    
    // Insert order into database
    $query = "INSERT INTO orders (user_name, menu_item_id, num_plates, total_price, discount_applied,
              final_price, order_date, delivery_date, location, contact_number, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "siidddssss", $user_name, $platter_id, $plates, $base_price, 
                          $discount, $total_amount, $order_date, $delivery_date, $location, $contact_number);
    
    if (mysqli_stmt_execute($stmt)) {
        $order_id = mysqli_insert_id($conn);
        header("Location: order-confirm.php?order_id=" . $order_id);
        exit();
    } else {
        $error = "Error placing order: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Online Catering</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #666;
        }
        .platter-info {
            display: flex;
            margin-bottom: 30px;
        }
        .platter-image {
            flex: 0 0 200px;
            margin-right: 20px;
        }
        .platter-image img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .platter-details {
            flex: 1;
        }
        .platter-name {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .platter-description {
            color: #666;
            margin-bottom: 15px;
        }
        .platter-price {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 15px;
        }
        .order-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="number"], input[type="date"], input[type="time"], textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus, input[type="time"]:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        .calculation {
            margin-top: 20px;
            padding: 15px;
            background-color: #eaf7ff;
            border-radius: 6px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
        }
        .discount-info {
            font-size: 14px;
            color: #27ae60;
            margin-top: 10px;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-back {
            background-color: #95a5a6;
            color: white;
        }
        .btn-back:hover {
            background-color: #7f8c8d;
        }
        .btn-proceed {
            background-color: #3498db;
            color: white;
        }
        .btn-proceed:hover {
            background-color: #2980b9;
        }
        .error {
            color: #e74c3c;
            margin-top: 20px;
            padding: 10px;
            background-color: #fadbd8;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Complete Your Order</h1>
            <div class="subtitle">Please provide the delivery details for your selected meal</div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="platter-info">
            <div class="platter-image">
                <img src="<?php echo $platter['image_path']; ?>" alt="<?php echo $platter['name']; ?>">
            </div>
            <div class="platter-details">
                <div class="platter-name"><?php echo $platter['name']; ?></div>
                <div class="platter-description"><?php echo $platter['description']; ?></div>
                <div class="platter-price">₹<?php echo $platter['price']; ?> per plate</div>
            </div>
        </div>
        
        <form class="order-form" method="post" action="" id="orderForm">
            <div class="form-group">
                <label for="delivery_date">Delivery Date:</label>
                <input type="date" id="delivery_date" name="delivery_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
            
            <div class="form-group">
                <label for="delivery_time">Delivery Time:</label>
                <input type="time" id="delivery_time" name="delivery_time" required>
            </div>
            
            <div class="form-group">
                <label for="location">Delivery Location:</label>
                <textarea id="location" name="location" rows="3" required><?php echo $user['address']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" required value="<?php echo $user['phone']; ?>">
            </div>
            
            <div class="form-group">
                <label for="plates">Number of Plates:</label>
                <input type="number" id="plates" name="plates" min="10" required value="10" onchange="calculatePrice()">
                <div class="discount-info">
                    <p>Discount offers:</p>
                    <ul>
                        <li>10% off for 100+ plates</li>
                        <li>15% off for 200+ plates</li>
                        <li>20% off for 500+ plates</li>
                    </ul>
                </div>
            </div>
            
            <div class="calculation">
                <div class="price-row">
                    <span>Base Price:</span>
                    <span id="basePrice">₹0</span>
                </div>
                <div class="price-row">
                    <span>Discount:</span>
                    <span id="discount">₹0</span>
                </div>
                <div class="total-row">
                    <span>Total Amount:</span>
                    <span id="totalAmount">₹0</span>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="javascript:history.back()" class="btn btn-back">Go Back</a>
                <button type="submit" class="btn btn-proceed">Place Order</button>
            </div>
        </form>
    </div>
    
    <script>
        // Set minimum date for delivery (tomorrow)
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('delivery_date').min = tomorrow.toISOString().split('T')[0];
        
        // Calculate price based on number of plates
        function calculatePrice() {
            const platePrice = <?php echo $platter['price']; ?>;
            const plates = parseInt(document.getElementById('plates').value);
            const basePrice = platePrice * plates;
            
            let discount = 0;
            if (plates >= 500) {
                discount = basePrice * 0.20; // 20% off
            } else if (plates >= 200) {
                discount = basePrice * 0.15; // 15% off
            } else if (plates >= 100) {
                discount = basePrice * 0.10; // 10% off
            }
            
            const totalAmount = basePrice - discount;
            
            document.getElementById('basePrice').textContent = '₹' + basePrice.toFixed(2);
            document.getElementById('discount').textContent = '₹' + discount.toFixed(2);
            document.getElementById('totalAmount').textContent = '₹' + totalAmount.toFixed(2);
        }
        
        // Initialize calculation
        calculatePrice();
        
        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const deliveryDate = new Date(document.getElementById('delivery_date').value);
            const today = new Date();
            
            if (deliveryDate <= today) {
                e.preventDefault();
                alert('Delivery date must be in the future.');
            }
        });
    </script>
</body>
</html>