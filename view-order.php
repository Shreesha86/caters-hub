<?php
// Database connection
include('db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_name']) || !isset($_SESSION['email'])) {
    header("Location: userlogin.php");
    exit();
}

// FIXED: Changed from order_id to id to match the parameter from dashboard.php
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_name = $_SESSION['user_name'];
$email = $_SESSION['email'];

// Fetch order details from database
$query = "SELECT o.*, m.name AS platter_name, m.image_path AS image, m.description 
          FROM orders o
          LEFT JOIN menu_items m ON o.menu_item_id = m.id
          WHERE o.id = ? AND o.user_name = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "is", $order_id, $user_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Order not found or does not belong to you.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

$order = mysqli_fetch_assoc($result);

// Fetch user details using email
$query = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - Online Catering</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
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
        .order-status {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .order-section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .platter-info {
            display: flex;
            margin-bottom: 20px;
        }
        .platter-image {
            flex: 0 0 150px;
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
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .platter-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .price-summary {
            background-color: #eaf7ff;
            padding: 15px;
            border-radius: 8px;
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
        .buttons {
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
            text-decoration: none;
            text-align: center;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .btn-warning {
            background-color: #f39c12;
            color: white;
        }
        .btn-warning:hover {
            background-color: #d35400;
        }
        .order-id {
            font-weight: bold;
            color: #3498db;
        }
        @media print {
            .buttons {
                display: none;
            }
            body {
                background-color: #fff;
            }
            .container {
                box-shadow: none;
                margin: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Details</h1>
            <div class="subtitle">Order ID: <span class="order-id">#<?php echo $order_id; ?></span></div>
        </div>
        
        <div class="order-status status-<?php echo strtolower($order['status']); ?>">
            Order Status: <strong><?php echo $order['status']; ?></strong>
        </div>
        
        <div class="order-details">
            <div class="order-section">
                <div class="section-title">Menu Details</div>
                <div class="platter-info">
                    <?php if (isset($order['image']) && !empty($order['image'])): ?>
                    <div class="platter-image">
                        <img src="<?php echo $order['image']; ?>" alt="<?php echo $order['platter_name']; ?>">
                    </div>
                    <?php endif; ?>
                    <div class="platter-details">
                        <div class="platter-name"><?php echo $order['platter_name']; ?></div>
                        <div class="platter-description"><?php echo isset($order['description']) ? $order['description'] : ''; ?></div>
                        <div class="detail-row">
                            <div class="detail-label">Number of Plates:</div>
                            <div><?php echo $order['num_plates']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-section">
                <div class="section-title">Delivery Information</div>
                <div class="detail-row">
                    <div class="detail-label">Delivery Date:</div>
                    <div><?php echo date('d-m-Y', strtotime($order['delivery_date'])); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Delivery Time:</div>
                    <div><?php echo date('h:i A', strtotime($order['delivery_date'])); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Delivery Location:</div>
                    <div><?php echo $order['location']; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Contact Number:</div>
                    <div><?php echo $order['contact_number']; ?></div>
                </div>
            </div>
            
            <div class="order-section">
                <div class="section-title">Price Details</div>
                <div class="price-summary">
                    <div class="price-row">
                        <div>Base Price (₹<?php echo number_format($order['total_price'] / $order['num_plates'], 2); ?> × <?php echo $order['num_plates']; ?> plates):</div>
                        <div>₹<?php echo $order['total_price']; ?></div>
                    </div>
                    <div class="price-row">
                        <div>Discount:</div>
                        <div>₹<?php echo $order['discount_applied']; ?></div>
                    </div>
                    <div class="total-row">
                        <div>Total Amount:</div>
                        <div>₹<?php echo $order['final_price']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="buttons">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            
            <?php 
            // Add Cancel button if order is Pending or Confirmed and delivery date is more than 10 days away
            $delivery_date = new DateTime($order['delivery_date']);
            $today = new DateTime();
            $diff = $today->diff($delivery_date);
            $days_left = $diff->days;
            
            if (($order['status'] == 'Pending' || $order['status'] == 'Confirmed') && $days_left > 10): 
            ?>
                <a href="cancelorder.php?id=<?php echo $order_id; ?>" class="btn btn-danger">Cancel Order</a>
            <?php endif; ?>
            
            <?php if ($order['status'] == 'Delivered'): ?>
                <a href="feedback.php?id=<?php echo $order_id; ?>" class="btn btn-warning">Leave Feedback</a>
            <?php endif; ?>
            
            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        </div>
    </div>
</body>
</html>