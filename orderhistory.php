<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_name']) || !isset($_SESSION['email'])) {
    header('Location: userlogin.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$email = $_SESSION['email'];

// Get user's order history - Using prepared statement for security
$stmt = $conn->prepare("SELECT o.*, m.name as menu_name, m.image_path FROM orders o 
                        JOIN menu_items m ON o.menu_item_id = m.id 
                        WHERE o.user_name = ? 
                        ORDER BY o.delivery_date DESC");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

// Filter orders by status if requested
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Handle search functionality
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Online Catering</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: black;
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 15px 15px 15px rgba(0, 0, 0, 0.4);
            background-color: rgba(0, 0, 0, 0.7);
        }
        .header {
            background-color: black;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

        }
        .nav {
            display: flex;
            background-color: #444;
            overflow: hidden;
        }
        .nav a {
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            flex-grow: 1;
        }
        .nav a:hover {
            background-color: #555;
        }
        .nav .active {
            background-color: #4CAF50;
        }
        h1, h2 {
            text-align: center;
            color: white;
        }
        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
        }
        .filter-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #555;
            color: white;
            transition: background-color 0.3s;
        }
        .filter-btn.active {
            background-color: #4CAF50;
        }
        .filter-btn:hover {
            background-color: #666;
        }
        .search-form {
            display: flex;
            gap: 10px;
        }
        .search-form input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-form button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #45a049;
        }
        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        .order-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            color: #333;
        }
        .order-card:hover {
            transform: translateY(-5px);
        }
        .order-card-header {
            position: relative;
            height: 150px;
            overflow: hidden;
        }
        .order-card-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .order-card-id {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .order-card-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }
        .status-pending {
            background-color: #f39c12;
        }
        .status-confirmed {
            background-color: #3498db;
        }
        .status-delivered {
            background-color: #2ecc71;
        }
        .status-cancelled {
            background-color: #e74c3c;
        }
        .order-card-content {
            padding: 15px;
        }
        .order-card-content h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .order-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .order-detail-label {
            font-weight: bold;
            color: #555;
        }
        .order-card-actions {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background-color: #f5f5f5;
        }
        .order-card-actions a {
            padding: 8px 16px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .view-btn {
            background-color: #3498db;
        }
        .view-btn:hover {
            background-color: #2980b9;
        }
        .cancel-btn {
            background-color: #e74c3c;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }
        .feedback-btn {
            background-color: #f39c12;
        }
        .feedback-btn:hover {
            background-color: #d35400;
        }
        .no-orders {
            text-align: center;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            color: #666;
            grid-column: 1 / -1;
        }
        .page-navigation {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .page-navigation button {
            padding: 8px 16px;
            margin: 0 5px;
            background-color: #555;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .page-navigation button.active {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CatersHub</h1>
    </div>
    
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="lunch-dinner.php">Lunch/Dinner</a>
        <a href="breakfast.php">Breakfast</a>
        <a href="snacks.php">Snacks</a>
        <a href="orderhistory.php" class="active">Order History</a>
        <a href="user-login.php">Logout</a>
    </div>
    
    <div class="container">
        <h2>Your Order History</h2>
        
        <div class="filters">
            <div class="filter-buttons">
                <a href="orderhistory.php" class="filter-btn <?php echo $status_filter == 'all' ? 'active' : ''; ?>">All Orders</a>
                <a href="orderhistory.php?status=Pending" class="filter-btn <?php echo $status_filter == 'Pending' ? 'active' : ''; ?>">Pending</a>
                <a href="orderhistory.php?status=Confirmed" class="filter-btn <?php echo $status_filter == 'Confirmed' ? 'active' : ''; ?>">Confirmed</a>
                <a href="orderhistory.php?status=Delivered" class="filter-btn <?php echo $status_filter == 'Delivered' ? 'active' : ''; ?>">Delivered</a>
                <a href="orderhistory.php?status=Cancelled" class="filter-btn <?php echo $status_filter == 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
            </div>
            
            <form action="orderhistory.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search orders..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <div class="orders-grid">
            <?php 
            $found_orders = false;
            if ($orders->num_rows > 0): 
                while($order = $orders->fetch_assoc()): 
                    // Apply filters
                    if (($status_filter != 'all' && $order['status'] != $status_filter) ||
                        (!empty($search_term) && 
                         stripos($order['menu_name'], $search_term) === false && 
                         stripos($order['id'], $search_term) === false &&
                         stripos($order['location'], $search_term) === false)) {
                        continue;
                    }
                    
                    $found_orders = true;
            ?>
            <div class="order-card">
                <div class="order-card-header">
                    <?php if(isset($order['image_path']) && !empty($order['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($order['image_path']); ?>" alt="<?php echo htmlspecialchars($order['menu_name']); ?>">
                    <?php else: ?>
                        <img src="placeholder-food.jpg" alt="Food Placeholder">
                    <?php endif; ?>
                    <div class="order-card-id">Order #<?php echo htmlspecialchars($order['id']); ?></div>
                    <div class="order-card-status status-<?php echo strtolower(htmlspecialchars($order['status'])); ?>">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </div>
                </div>
                <div class="order-card-content">
                    <h3><?php echo htmlspecialchars($order['menu_name']); ?></h3>
                    <div class="order-detail">
                        <span class="order-detail-label">Delivery Date:</span>
                        <span><?php echo date('d M Y', strtotime($order['delivery_date'])); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="order-detail-label">Plates:</span>
                        <span><?php echo htmlspecialchars($order['num_plates']); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="order-detail-label">Total:</span>
                        <span>₹<?php echo htmlspecialchars($order['final_price']); ?></span>
                    </div>
                    <div class="order-detail">
                        <span class="order-detail-label">Location:</span>
                        <span><?php echo htmlspecialchars($order['location']); ?></span>
                    </div>
                </div>
                <div class="order-card-actions">
                    <a href="view-order.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="view-btn">View Details</a>
                    
                    <?php 
                    // Show Cancel button if order is Pending or Confirmed and delivery date is more than 10 days away
                    $delivery_date = new DateTime($order['delivery_date']);
                    $today = new DateTime();
                    $diff = $today->diff($delivery_date);
                    $days_left = $diff->days;
                    
                    if (($order['status'] == 'Pending' || $order['status'] == 'Confirmed') && $days_left > 10): 
                    ?>
                        <a href="cancelorder.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="cancel-btn">Cancel</a>
                    <?php elseif ($order['status'] == 'Delivered'): ?>
                        <a href="feedback.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="feedback-btn">Feedback</a>
                    <?php else: ?>
                        <span></span> <!-- Empty span to maintain flex layout when no button -->
                    <?php endif; ?>
                </div>
            </div>
            <?php 
                endwhile; 
                
                if (!$found_orders):
            ?>
                <div class="no-orders">
                    <h3>No orders found</h3>
                    <p>No orders match your current filters. Try changing your search criteria or view all orders.</p>
                </div>
            <?php 
                endif;
            else: 
            ?>
                <div class="no-orders">
                    <h3>You haven't placed any orders yet</h3>
                    <p>Explore our menu and place your first order today!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Simple pagination - can be enhanced with actual pagination logic -->
        <!-- <div class="page-navigation">
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>Next &raquo;</button>
        </div> -->
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for order cards
            const orderCards = document.querySelectorAll('.order-card');
            
            orderCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Handle cancel button confirmation
            const cancelButtons = document.querySelectorAll('.cancel-btn');
            
            cancelButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to cancel this order?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>