<?php
// Start session for admin authentication
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caters_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update for Confirmation
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = "Confirmed";
    
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        $status_message = "Order #" . $order_id . " status updated to Confirmed";
    } else {
        $status_message = "Error updating order status: " . $conn->error;
    }
    $stmt->close();
}

// Handle status update for Delivery
if (isset($_POST['mark_delivered'])) {
    $order_id = $_POST['order_id'];
    $new_status = "Delivered";
    
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        $status_message = "Order #" . $order_id . " status updated to Delivered";
    } else {
        $status_message = "Error updating order status: " . $conn->error;
    }
    $stmt->close();
}

// Fetch current orders (Pending and Confirmed)
$sql = "SELECT o.*, u.name AS user_name FROM orders o 
        LEFT JOIN users u ON o.user_name = u.name 
        WHERE o.status IN ('Pending', 'Confirmed') 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

// Get current date and time for comparison
$current_datetime = new DateTime();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Orders - Admin Panel</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .nav {
            display: flex;
            gap: 20px;
        }
        .nav a {
            color: white;
            text-decoration: none;
        }
        .status-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .order-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .order-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-5px);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .order-id {
            font-weight: bold;
            font-size: 18px;
        }
        .order-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .order-details {
            margin-bottom: 15px;
        }
        .order-row {
            display: flex;
            margin-bottom: 8px;
        }
        .order-label {
            font-weight: bold;
            width: 40%;
        }
        .order-value {
            width: 60%;
        }
        .order-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 5px;
        }
        .btn-confirm {
            background-color: #28a745;
            color: white;
        }
        .btn-confirm:hover {
            background-color: #218838;
        }
        .btn-deliver {
            background-color: #17a2b8;
            color: white;
        }
        .btn-deliver:hover {
            background-color: #138496;
        }
        .no-orders {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 5px;
        }
        .badge-due {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel - Current Orders</h1>
        <div class="nav">
            <a href="contactlist.php">Contacts</a>
            <a href="current-orders.php">Current Orders</a>
            <a href="order-history.php">Order History</a>
            <a href="adminlogout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h2>Current Orders</h2>
        
        <?php if(isset($status_message)): ?>
            <div class="status-message">
                <?php echo $status_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if($result->num_rows > 0): ?>
            <div class="order-container">
                <?php while($row = $result->fetch_assoc()): 
                    // Check if delivery date and time has passed
                    $delivery_datetime = new DateTime($row['delivery_date'] . ' ' . $row['delivery_time']);
                    $is_delivery_due = $delivery_datetime <= $current_datetime;
                ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">
                                Order #<?php echo $row['id']; ?>
                                <?php if($is_delivery_due && $row['status'] == 'Confirmed'): ?>
                                    <span class="status-badge badge-due">Delivery Due</span>
                                <?php endif; ?>
                            </div>
                            <div class="order-status status-<?php echo strtolower($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </div>
                        </div>
                        <div class="order-details">
                            <div class="order-row">
                                <div class="order-label">Customer:</div>
                                <div class="order-value"><?php echo $row['user_name']; ?></div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Menu Item:</div>
                                <div class="order-value">
                                    <?php 
                                    // Fetch menu item details
                                    $menu_sql = "SELECT name FROM menu_items WHERE id = ?";
                                    $menu_stmt = $conn->prepare($menu_sql);
                                    $menu_stmt->bind_param("i", $row['menu_item_id']);
                                    $menu_stmt->execute();
                                    $menu_result = $menu_stmt->get_result();
                                    if ($menu_row = $menu_result->fetch_assoc()) {
                                        echo $menu_row['name'];
                                    } else {
                                        echo "Unknown Item";
                                    }
                                    $menu_stmt->close();
                                    ?>
                                </div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Plates:</div>
                                <div class="order-value"><?php echo $row['num_plates']; ?></div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Order Date:</div>
                                <div class="order-value"><?php echo date('d-m-Y', strtotime($row['order_date'])); ?></div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Delivery:</div>
                                <div class="order-value">
                                    <?php echo date('d-m-Y', strtotime($row['delivery_date'])); ?> at 
                                    <?php echo date('h:i A', strtotime($row['delivery_time'])); ?>
                                </div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Location:</div>
                                <div class="order-value"><?php echo $row['location']; ?></div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Contact:</div>
                                <div class="order-value"><?php echo $row['contact_number']; ?></div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Total Price:</div>
                                <div class="order-value">₹<?php echo number_format($row['total_price'], 2); ?></div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Discount:</div>
                                <div class="order-value">₹<?php echo number_format($row['discount_applied'], 2); ?></div>
                            </div>
                            <div class="order-row">
                                <div class="order-label">Final Price:</div>
                                <div class="order-value">₹<?php echo number_format($row['final_price'], 2); ?></div>
                            </div>
                        </div>
                        
                        <div class="order-footer">
                            <?php if($row['status'] == 'Pending'): ?>
                                <form method="post" action="" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="update_status" class="btn btn-confirm">
                                        Confirm Order
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if($is_delivery_due && $row['status'] == 'Confirmed'): ?>
                                <form method="post" action="" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="mark_delivered" class="btn btn-deliver">
                                        Mark as Delivered
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <h3>No current orders found</h3>
                <p>There are no pending or confirmed orders at this time.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-hide status message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const statusMessage = document.querySelector('.status-message');
            if (statusMessage) {
                setTimeout(function() {
                    statusMessage.style.opacity = '0';
                    statusMessage.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        statusMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>