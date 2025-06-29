<?php
// Database connection
include('db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_name']) || !isset($_SESSION['email'])) {
    header("Location: userlogin.php");
    exit();
}

// Get user information
$user_name = $_SESSION['user_name'];
$email = $_SESSION['email'];

// First, get the user_id from the users table
$user_query = "SELECT * FROM users WHERE email = ?";
$user_stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($user_stmt, "s", $email);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);

if (mysqli_num_rows($user_result) == 0) {
    // User not found - this should not happen if properly logged in
    header("Location: userlogin.php");
    exit();
}

$user_row = mysqli_fetch_assoc($user_result);
//$user_id = $user_row['id'];

// Check if order ID is provided (accept both 'id' and 'order_id' parameters)
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
} elseif (isset($_GET['id'])) {
    $order_id = $_GET['id'];
} else {
    header("Location: dashboard.php");
    exit();
}

// DEBUG: Print variables to check values
// echo "Order ID: " . $order_id . "<br>";
// echo "User ID: " . $user_id . "<br>";
// echo "User Name: " . $user_name . "<br>";

// First try with user_id
$query = "SELECT * FROM orders WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    $error = "Order not found.";
} else {
    $order = mysqli_fetch_assoc($result);
    
    // Check if the order belongs to the current user
    // This depends on your database schema - adjust field names as needed
    $order_user_field = null;
    
    // Add proper checks before accessing array keys to avoid undefined array key errors
    $orderBelongsToUser = true; // Default to true unless we find it doesn't
    
    // Check possible user identification fields
    if (isset($order['user_id'])) {
        if ($order['user_id'] != $user_id) {
            $orderBelongsToUser = false;
        }
    } elseif (isset($order['user_name'])) {
        if ($order['user_name'] != $user_name) {
            $orderBelongsToUser = false;
        }
    }
    
    if (!$orderBelongsToUser) {
        $error = "This order doesn't belong to your account.";
    } else {
        // Check if order can be cancelled (10 days rule)
        $delivery_date = isset($order['delivery_date']) ? new DateTime($order['delivery_date']) : new DateTime();
        $today = new DateTime();
        $interval = $today->diff($delivery_date);
        $days_difference = $interval->days;
        
        if (isset($order['status']) && $order['status'] == 'Cancelled') {
            $error = "This order has already been cancelled.";
        } elseif (isset($order['status']) && $order['status'] == 'Completed') {
            $error = "Completed orders cannot be cancelled.";
        } elseif ($days_difference < 10) {
            $error = "Orders cannot be cancelled less than 10 days before delivery date.";
        } else {
            // Process form submission for confirmation
            if (isset($_POST['confirm_cancel']) && $_POST['confirm_cancel'] == 'yes') {
                // Update order status
                $update_query = "UPDATE orders SET status = 'Cancelled' WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "i", $order_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Order #" . $order_id . " has been successfully cancelled.";
                } else {
                    $error = "Error cancelling order: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Adjust field names for display based on your database schema
// This block adapts to different possible column names in your orders table
function getValueOrDefault($array, $keys, $default = "") {
    foreach ($keys as $key) {
        if (isset($array[$key])) {
            return $array[$key];
        }
    }
    return $default;
}

// Only set these variables if we have an order
if (isset($order)) {
    $menuType = getValueOrDefault($order, ['menu_type', 'menu_item_id', 'platter_type'], "");
    $platterName = getValueOrDefault($order, ['platter_name', 'name', 'menu_name'], "");
    $plates = getValueOrDefault($order, ['plates', 'num_plates', 'quantity'], "");
    $totalAmount = getValueOrDefault($order, ['total_amount', 'final_price', 'total_price'], "0");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Order - Online Catering</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 700px;
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
        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
        }
        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .cancel-form {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #ffeeba;
        }
        .warning {
            font-size: 16px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
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
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .debug-info {
            background-color: #f1f1f1;
            padding: 10px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cancel Order</h1>
            <div class="subtitle">Review and confirm order cancellation</div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($order) && empty($message) && empty($error)): ?>
            <div class="order-details">
                <div class="detail-row">
                    <div class="detail-label">Order ID:</div>
                    <div>#<?php echo isset($order['id']) ? $order['id'] : $order_id; ?></div>
                </div>
                
                <!-- Check which fields exist and display accordingly -->
                <?php 
                if (!empty($menuType) && !empty($platterName)): 
                ?>
                <div class="detail-row">
                    <div class="detail-label">Menu:</div>
                    <div><?php echo $menuType; ?> - <?php echo $platterName; ?></div>
                </div>
                <?php 
                endif; 
                ?>
                
                <?php 
                if (!empty($plates)): 
                ?>
                <div class="detail-row">
                    <div class="detail-label">Number of Plates:</div>
                    <div><?php echo $plates; ?></div>
                </div>
                <?php 
                endif; 
                ?>
                
                <div class="detail-row">
                    <div class="detail-label">Order Date:</div>
                    <div><?php echo isset($order['order_date']) ? date('d-m-Y', strtotime($order['order_date'])) : 'N/A'; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Delivery Date:</div>
                    <div><?php echo isset($order['delivery_date']) ? date('d-m-Y h:i A', strtotime($order['delivery_date'])) : 'N/A'; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Total Amount:</div>
                    <div>₹<?php echo $totalAmount; ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div><?php echo isset($order['status']) ? $order['status'] : 'N/A'; ?></div>
                </div>
            </div>
            
            <div class="cancel-form">
                <div class="warning">Are you sure you want to cancel this order?</div>
                <p>Once cancelled, this action cannot be undone. Your order will be permanently removed from the schedule.</p>
                
                <form method="post" action="">
                    <div class="form-group">
                        <input type="hidden" name="confirm_cancel" value="yes">
                    </div>
                    <div class="buttons">
                        <a href="dashboard.php" class="btn btn-secondary">Go Back</a>
                        <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
            
        <?php else: ?>
            <div class="buttons">
                <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            </div>
        <?php endif; ?>
        
        <!-- Uncomment this for debugging
        <div class="debug-info">
            <p>ORDER ID: <?php echo isset($_GET['order_id']) ? $_GET['order_id'] : (isset($_GET['id']) ? $_GET['id'] : 'Not set'); ?></p>
            <p>USER ID: <?php echo isset($user_id) ? $user_id : 'Not set'; ?></p>
            <p>USER NAME: <?php echo isset($user_name) ? $user_name : 'Not set'; ?></p>
            <p>ORDER DATA: <?php echo isset($order) ? print_r($order, true) : 'No order found'; ?></p>
        </div>
        -->
    </div>
</body>
</html>