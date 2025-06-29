<?php
// Start session for admin authentication
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
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

// Fetch order history (Delivered and Cancelled)
$sql = "SELECT o.*, u.name AS user_name FROM orders o 
        LEFT JOIN users u ON o.user_name = u.name 
        WHERE o.status IN ('Delivered', 'Cancelled') 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Admin Panel</title>
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
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .filter-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            background-color: #f8f9fa;
        }
        .filter-btn.active {
            background-color: #007bff;
            color: white;
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
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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
        .feedback-container {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .feedback-header {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .rating {
            color: #ffc107;
            margin-bottom: 5px;
        }
        .no-orders {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel - Order History</h1>
        <div class="nav">
            <a href="admin_dashboard.php">Home</a>
            <a href="contactlist.php">Contacts</a>
            <a href="current-orders.php">Current Orders</a>
            <a href="order-history.php">Order History</a>
            <a href="adminlogout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h2>Order History</h2>
        
        <div class="filters">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="delivered">Delivered</button>
            <button class="filter-btn" data-filter="cancelled">Cancelled</button>
        </div>
        
        <?php if($result->num_rows > 0): ?>
            <div class="order-container">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="order-card" data-status="<?php echo strtolower($row['status']); ?>">
                        <div class="order-header">
                            <div class="order-id">Order #<?php echo $row['id']; ?></div>
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
                        
                        <?php 
                        // Check if there's feedback for this order
                        if($row['status'] == 'Delivered') {
                            $feedback_sql = "SELECT rating, comments FROM feedback WHERE order_id = ?";
                            $feedback_stmt = $conn->prepare($feedback_sql);
                            $feedback_stmt->bind_param("i", $row['id']);
                            $feedback_stmt->execute();
                            $feedback_result = $feedback_stmt->get_result();
                            
                            if($feedback_result->num_rows > 0) {
                                $feedback = $feedback_result->fetch_assoc();
                                echo '<div class="feedback-container">';
                                echo '<div class="feedback-header">Customer Feedback</div>';
                                echo '<div class="rating">';
                                // Display stars based on rating
                                for($i = 1; $i <= 5; $i++) {
                                    if($i <= $feedback['rating']) {
                                        echo '★';
                                    } else {
                                        echo '☆';
                                    }
                                }
                                echo ' ' . $feedback['rating'] . '/5';
                                echo '</div>';
                                if(!empty($feedback['comments'])) {
                                    echo '<div class="feedback-comments">"' . $feedback['comments'] . '"</div>';
                                }
                                echo '</div>';
                            }
                            $feedback_stmt->close();
                        }
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <h3>No order history found</h3>
                <p>There are no delivered or cancelled orders in the system.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const filterButtons = document.querySelectorAll('.filter-btn');
            const orderCards = document.querySelectorAll('.order-card');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    // Show/hide order cards based on filter
                    orderCards.forEach(card => {
                        if (filter === 'all' || card.getAttribute('data-status') === filter) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>