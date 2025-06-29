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

// Get user details - Using prepared statement for security
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get user's order history - Using prepared statement for security
$stmt = $conn->prepare("SELECT o.*, m.name as menu_name FROM orders o 
                        JOIN menu_items m ON o.menu_item_id = m.id 
                        WHERE o.user_name = ? 
                        ORDER BY o.delivery_date DESC");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Online Catering</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: black;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 15px 15px 15px rgba(0, 0, 0, 0.4);
            text-align: center;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .header {
            background-color: black;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

        }
        .dropdown {
            position: relative;
            display: inline-block;
            flex-grow: 1;
        }
        .dropdown-btn {
            color: white;
            padding: 14px 16px;
            background-color: #444;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            font-size: 16px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #444;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            width: 100%;
            left: 0; /* Ensure dropdown is aligned with parent */
        }
        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        .dropdown-content a:hover {
            background-color: #555;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown:hover .dropdown-btn {
            background-color: #555;
        }

        .nav {
            display: flex;
            background-color: #444;
            overflow: visible;
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
        .profile {
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile h2 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .profile-details {
            margin-top: 20px;
        }
        .profile-details p {
            margin: 10px 0;
        }
        .orders {
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .orders h2 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .status {
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-size: 0.8em;
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
        .meal-selection {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
            justify-content: space-between;
        }
        .meal-card {
            flex: 0 0 30%;
            background-color: white;
            margin-bottom: 20px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .meal-card:hover {
            transform: translateY(-5px);
        }
        .meal-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .meal-card-content {
            padding: 15px;
        }
        .meal-card h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .meal-card p {
            color: #666;
            margin-bottom: 15px;
        }
        .meal-card .button {
            display: block;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .meal-card .button:hover {
            background-color: #45a049;
        }
        .empty-orders {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .action-buttons a {
            display: inline-block;
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 3px;
            text-decoration: none;
            color: white;
            font-size: 0.8em;
        }
        .view-button {
            background-color: #3498db;
        }
        .cancel-button {
            background-color: #e74c3c;
        }
        .feedback-button {
            background-color: #f39c12;
        }
/* Footer style */
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            font-size: 0.9em;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
.footer {
        background-color: #222;
        color: #ccc;
        padding: 40px 0 20px;
        margin-top: 50px;
        font-size: 0.95em;
        line-height: 1.6;
    }
    .footer-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .footer-section {
        flex: 1;
        min-width: 250px;
        margin-bottom: 30px;
        padding: 0 15px;
    }
    .footer-section h3 {
        color: #ffffff;
        font-size: 1.3em;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }
    .footer-section h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: #4CAF50;
    }
    .footer-section p {
        margin-bottom: 15px;
    }
    .social-links {
        display: flex;
        margin-top: 20px;
    }
    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background-color: #333;
        border-radius: 50%;
        margin-right: 10px;
        transition: all 0.3s ease;
    }
    .social-links a:hover {
        background-color: #4CAF50;
        transform: translateY(-3px);
    }
    .social-links i {
        color: white;
        font-size: 18px;
    }
    .contact-info {
        margin-top: 20px;
    }
    .contact-info p {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .contact-info i {
        margin-right: 10px;
        color: #4CAF50;
    }
    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        margin-top: 20px;
        border-top: 1px solid #333;
    }
    .footer-bottom p {
        margin: 5px 0;
    }
    .developers {
        margin-top: 10px;
        font-style: italic;
    }
    .developers a {
        color: #4CAF50;
        text-decoration: none;
        transition: color 0.3s;
    }
    .developers a:hover {
        color: #ffffff;
        text-decoration: underline;
    }
    @media (max-width: 768px) {
        .footer-section {
            flex: 0 0 100%;
        }
    }

    </style>
</head>
<body>
    <div class="header">
        <h1>CatersHub</h1>
    </div>
    
    <div class="nav">
        <a href="dashboardlogoff.php" class="active">Dashboard</a>
        <a href="lunch-dinner.php">Lunch/Dinner</a>
        <a href="breakfast.php">Breakfast</a>
        <a href="snacks.php">Snacks</a>
        <a href="orderhistory.php">Order History</a>
        <div class="dropdown">
            <button class="dropdown-btn">More</button>
            <div class="dropdown-content">
                <a href="aboutus1.php">About Us</a>
                <a href="services1.php">Services</a>
                <a href="terms1.php">Terms & Conditions</a>
                <a href="help1.php">Help</a>
                <a href="user-login.php">logout</a>
                <a href="admin-login.php">Admin</a>
            </div>
        </div>

    </div>
    
    <div class="container">
        <div class="profile">
            <h2>User Profile</h2>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            </div>
        </div>
        
        <div class="orders">
            <h2>Your Orders</h2>
            <?php if ($orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Menu Item</th>
                            <th>Delivery Date</th>
                            <th>Plates</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['menu_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($order['delivery_date'])); ?></td>
                                <td><?php echo htmlspecialchars($order['num_plates']); ?></td>
                                <td>₹<?php echo htmlspecialchars($order['final_price']); ?></td>
                                <td>
                                    <span class="status status-<?php echo strtolower(htmlspecialchars($order['status'])); ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="view-order.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="view-button">View</a>
                                    <?php if ($order['status'] == 'Pending' || $order['status'] == 'Confirmed'): ?>
                                        <?php 
                                            $delivery_date = new DateTime($order['delivery_date']);
                                            $today = new DateTime();
                                            $diff = $today->diff($delivery_date);
                                            $days_left = $diff->days;
                                            
                                            if ($days_left > 10): 
                                        ?>
                                            <a href="cancelorder.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="cancel-button">Cancel</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($order['status'] == 'Delivered'): ?>
                                        <a href="feedback.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="feedback-button">Feedback</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-orders">
                    <p>You have no orders yet.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <h2>Meal Selection</h2>
        <div class="meal-selection">
            <div class="meal-card">
                <img src="breakfast.jpg" alt="Breakfast">
                <div class="meal-card-content">
                    <h3>Breakfast</h3>
                    <p>Start your day with our delicious breakfast options</p>
                    <a href="breakfast.php" class="button">Explore</a>
                </div>
            </div>
            
            <div class="meal-card">
                <img src="lunch.jpg" alt="Lunch/Dinner">
                <div class="meal-card-content">
                    <h3>Lunch/Dinner</h3>
                    <p>Explore our extensive lunch and dinner menu</p>
                    <a href="lunch-dinner.php" class="button">Explore</a>
                </div>
            </div>
            
            <div class="meal-card">
                <img src="snacks.jpg" alt="Snacks">
                <div class="meal-card-content">
                    <h3>Snacks</h3>
                    <p>Perfect bites for your events and gatherings</p>
                    <a href="snacks.php" class="button">Explore</a>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="footer-container">
        <div class="footer-section">
            <h3>About Caters Hub</h3>
            <p>Caters Hub is a premier online catering marketplace connecting food enthusiasts with professional caterers. We serve as a trusted mediator, ensuring high-quality culinary experiences for all your events and gatherings.</p>
            <p>Our platform brings together diverse culinary talents, offering everything from traditional flavors to contemporary fusion, all delivered to your doorstep with ease and reliability.</p>
        </div>
        
        <div class="footer-section">
            <h3>Contact Us</h3>
            <div class="contact-info">
                <p><i class="fas fa-map-marker-alt"></i>MGM college gate udupi karnataka India</p>
                <p><i class="fas fa-phone"></i> +91 9876543210</p>
                <p><i class="fas fa-envelope"></i> info@catershub.com</p>
                <p><i class="fas fa-clock"></i> Mon-Sat: 9:00 AM - 8:00 PM</p>
            </div>
        </div>
        
        <div class="footer-section">
            <h3>Quick Links</h3>
            <p><a href="aboutus1.php">About Us</a></p>
            <p><a href="services1.php">Services</a></p>
            <p><a href="terms1.php">Terms & Conditions</a></p>
            <p><a href="help1.php">help</a></p>
            
            
        </div>
        
       <div class="footer-section">
            <h3>Connect With Us</h3>
            <p>Follow us on social media for updates, special offers, and culinary inspiration.</p>
            <div class="social-links">
               
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <p style="margin-top: 20px;">Subscribe to our newsletter:</p>
            <form action="#" method="post" style="display: flex; margin-top: 10px;">
                <input type="email" placeholder="Your email" style="flex: 1; padding: 8px; border: none; border-radius: 4px 0 0 4px;">
                <button type="submit" style="background: #4CAF50; color: white; border: none; padding: 8px 15px; border-radius: 0 4px 4px 0; cursor: pointer;">Subscribe</button>
            </form>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>© 2025 Caters Hub. All rights reserved.</p>
        <p class="developers">Developed  by <a href="#">Shreesha</a>, <a href="#">Shashank</a>, <a href="#">Akshay</a></p>
    </div>
        </div>
    </div>
    
    <script>
        // JavaScript for any interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Handle logout
            const logoutLink = document.querySelector('.nav a[href="userlogin.php"]');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    // You can create a separate logout script or handle it here
                    if (confirm("Are you sure you want to logout?")) {
                        // Send to logout script that destroys session
                        window.location.href = "logout.php";
                    }
                });
            }
        });
    </script>
</body>
</html>