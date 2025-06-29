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

// Process form submission for adding new caterer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_caterer'])) {
    // Sanitize and validate inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Validate inputs
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate phone (10 digits)
    if (empty($phone)) {
        $errors[] = "Phone is required";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $errors[] = "Phone must be 10 digits";
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        // Create the SQL query to insert caterer data
        $sql = "INSERT INTO caterers (name, email, phone, description) 
                VALUES ('$name', '$email', '$phone', '$description')";
        
        // Execute the query
        if ($conn->query($sql) === TRUE) {
            // Success message
            $_SESSION['success_message'] = "New caterer added successfully!";
        } else {
            // Error message
            $_SESSION['error_message'] = "Error: " . $conn->error;
        }
    } else {
        // Store errors in session
        $_SESSION['error_message'] = implode("<br>", $errors);
    }
}

// Get count of pending orders
$pending_sql = "SELECT COUNT(*) as pending_count FROM orders WHERE status = 'Pending'";
$pending_result = $conn->query($pending_sql);
$pending_row = $pending_result->fetch_assoc();
$pending_count = $pending_row['pending_count'];

// Get count of confirmed orders
$confirmed_sql = "SELECT COUNT(*) as confirmed_count FROM orders WHERE status = 'Confirmed'";
$confirmed_result = $conn->query($confirmed_sql);
$confirmed_row = $confirmed_result->fetch_assoc();
$confirmed_count = $confirmed_row['confirmed_count'];

// Get count of delivered orders
$delivered_sql = "SELECT COUNT(*) as delivered_count FROM orders WHERE status = 'Delivered'";
$delivered_result = $conn->query($delivered_sql);
$delivered_row = $delivered_result->fetch_assoc();
$delivered_count = $delivered_row['delivered_count'];

// Get count of cancelled orders
$cancelled_sql = "SELECT COUNT(*) as cancelled_count FROM orders WHERE status = 'Cancelled'";
$cancelled_result = $conn->query($cancelled_sql);
$cancelled_row = $cancelled_result->fetch_assoc();
$cancelled_count = $cancelled_row['cancelled_count'];

// Get total revenue
$revenue_sql = "SELECT SUM(final_price) as total_revenue FROM orders WHERE status IN ('Confirmed', 'Delivered')";
$revenue_result = $conn->query($revenue_sql);
$revenue_row = $revenue_result->fetch_assoc();
$total_revenue = $revenue_row['total_revenue'] ?: 0;

// Get total registered users
$users_sql = "SELECT COUNT(*) as user_count FROM users";
$users_result = $conn->query($users_sql);
$users_row = $users_result->fetch_assoc();
$user_count = $users_row['user_count'];

// Get recent orders
$recent_orders_sql = "SELECT o.*, u.name AS user_name FROM orders o 
                     LEFT JOIN users u ON o.user_name = u.name 
                     ORDER BY o.created_at DESC LIMIT 5";
$recent_orders_result = $conn->query($recent_orders_sql);

// Fetch caterer details for list
$caterers_sql = "SELECT * FROM caterers ORDER BY name LIMIT 5";
$caterers_result = $conn->query($caterers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Catering System</title>
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
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .welcome-text {
            font-size: 20px;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        .stat-card .stat-title {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-card .stat-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .stat-card.pending {
            border-top: 3px solid #ffc107;
        }
        .stat-card.confirmed {
            border-top: 3px solid #17a2b8;
        }
        .stat-card.delivered {
            border-top: 3px solid #28a745;
        }
        .stat-card.cancelled {
            border-top: 3px solid #dc3545;
        }
        .stat-card.revenue {
            border-top: 3px solid #6f42c1;
        }
        .stat-card.users {
            border-top: 3px solid #fd7e14;
        }
        .recent-orders-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .view-all-btn {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            text-decoration: none;
            font-size: 14px;
        }
        .recent-orders-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .recent-orders-table th,
        .recent-orders-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .recent-orders-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .recent-orders-table tr:last-child td {
            border-bottom: none;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .quick-links {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        .quick-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .quick-link-btn:hover {
            background-color: #e9ecef;
        }
        .dashboard-section {
            margin-bottom: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .add-caterer-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .message {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .caterers-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .caterers-table th, 
        .caterers-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .caterers-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 15px;
            cursor: pointer;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            margin-right: 5px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .tab.active {
            background-color: white;
            border-bottom: 1px solid white;
            margin-bottom: -1px;
            font-weight: bold;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel - Online Catering</h1>
        <div class="nav">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="contactlist.php">Contacts</a>
            <a href="current-orders.php">Current Orders</a>
            <a href="order-history.php">Order History</a>
            <a href="adminlogout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-header">
            <div class="welcome-text">
                Welcome back, Admin!
            </div>
            <div class="date">
                <?php echo date('l, d M Y'); ?>
            </div>
        </div>
        
        <div class="quick-links">
            <a href="current-orders.php" class="quick-link-btn">
                Manage Current Orders
            </a>
            <a href="order-history.php" class="quick-link-btn">
                View Order History
            </a>
            <a href="contactlist.php" class="quick-link-btn">
                Customer Contacts
            </a>
        </div>
        
        <div class="stats-container">
            <div class="stat-card pending">
                <div class="stat-icon">⏳</div>
                <div class="stat-value"><?php echo $pending_count; ?></div>
                <div class="stat-title">Pending Orders</div>
            </div>
            <div class="stat-card confirmed">
                <div class="stat-icon">✓</div>
                <div class="stat-value"><?php echo $confirmed_count; ?></div>
                <div class="stat-title">Confirmed Orders</div>
            </div>
            <div class="stat-card delivered">
                <div class="stat-icon">🚚</div>
                <div class="stat-value"><?php echo $delivered_count; ?></div>
                <div class="stat-title">Delivered Orders</div>
            </div>
            <div class="stat-card cancelled">
                <div class="stat-icon">❌</div>
                <div class="stat-value"><?php echo $cancelled_count; ?></div>
                <div class="stat-title">Cancelled Orders</div>
            </div>
            <div class="stat-card revenue">
                <div class="stat-icon">💰</div>
                <div class="stat-value">₹<?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-title">Total Revenue</div>
            </div>
            <div class="stat-card users">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $user_count; ?></div>
                <div class="stat-title">Registered Users</div>
            </div>
        </div>
        
        <!-- Caterer Management Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Caterer Management</h2>
                
            </div>
            
            <div class="tabs">
                <div class="tab active" onclick="openTab(event, 'caterer-list')">Recent Caterers</div>
                <div class="tab" onclick="openTab(event, 'add-caterer')">Add New Caterer</div>
            </div>
            
            <div id="caterer-list" class="tab-content active">
                <?php if ($caterers_result && $caterers_result->num_rows > 0): ?>
                    <table class="caterers-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($caterer = $caterers_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $caterer['name']; ?></td>
                                    <td><?php echo $caterer['email']; ?></td>
                                    <td><?php echo $caterer['phone']; ?></td>
                                    <td><?php echo $caterer['description']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No caterers found.</p>
                <?php endif; ?>
            </div>
            
            <div id="add-caterer" class="tab-content">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="message success-message">
                        <?php 
                            echo $_SESSION['success_message']; 
                            unset($_SESSION['success_message']); 
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="message error-message">
                        <?php 
                            echo $_SESSION['error_message']; 
                            unset($_SESSION['error_message']); 
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="add-caterer-form">
                    <form action="admin_dashboard.php" method="post" id="catererForm">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="4"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <input type="hidden" name="add_caterer" value="1">
                            <button type="submit" class="submit-btn">Add Caterer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders Section -->
        <div class="dashboard-section">
            <div class="recent-orders-header">
                <h2>Recent Orders</h2>
                <a href="current-orders.php" class="view-all-btn">View All</a>
            </div>
            
            <?php if($recent_orders_result->num_rows > 0): ?>
                <table class="recent-orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Delivery Date</th>
                            <th>Plates</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $recent_orders_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['user_name']; ?></td>
                                <td><?php echo date('d-m-Y', strtotime($order['delivery_date'])); ?></td>
                                <td><?php echo $order['num_plates']; ?></td>
                                <td>₹<?php echo number_format($order['final_price'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-orders">
                    <p>No recent orders found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Tab functionality
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            // Hide all tab content
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            
            // Remove "active" class from all tabs
            tablinks = document.getElementsByClassName("tab");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            
            // Show the selected tab content and add "active" class to the button
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
        
        // Client-side validation for phone number
        document.getElementById('catererForm').addEventListener('submit', function(e) {
            const phoneRegex = /^\d{10}$/;
            const phone = document.getElementById('phone').value;
            
            if (!phoneRegex.test(phone)) {
                alert('Please enter a valid 10-digit phone number');
                e.preventDefault();
            }
        });
        
        // Show "Add Caterer" tab if there was a form submission error
        <?php if (isset($_SESSION['error_message'])): ?>
            document.querySelector('.tab:nth-child(2)').click();
        <?php endif; ?>
        
        // Show "Add Caterer" tab if there was a success message
        <?php if (isset($_SESSION['success_message'])): ?>
            document.querySelector('.tab:nth-child(2)').click();
        <?php endif; ?>
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>