<?php
session_start();
require_once 'db_connect.php';
// Check if user is logged in
if (!is_user_logged_in()) {
    redirect('user-login.php');
}
// Get price range parameter
$price_range = isset($_GET['price_range']) ? (int)$_GET['price_range'] : 0;

// Get price ranges
$sql = "SELECT * FROM price_ranges ORDER BY min_price";
$price_ranges = $conn->query($sql);

// Get breakfast menu items based on filters
// Get breakfast menu items based on filters
$items_query = "SELECT m.* FROM menu_items m 
                JOIN menu_categories c ON m.category_id = c.id
                WHERE c.name = 'Breakfast'";

if ($price_range > 0) {
    $items_query .= " AND m.price_range_id = $price_range";
}
$menu_items = $conn->query($items_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breakfast Selection - Online Catering</title>
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
            overflow:visible;
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
        .filters {
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .filter-section {
            margin-bottom: 15px;
        }
        .filter-section h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }
        .filter-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .filter-option {
            padding: 8px 15px;
            background-color: #f1f1f1;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .filter-option:hover {
            background-color: #e1e1e1;
        }
        .filter-option.active {
            background-color: #4CAF50;
            color: white;
        }
        .menu-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .menu-item {
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .menu-item-info {
            padding: 15px;
        }
        .menu-item-info h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }
        .menu-item-info p {
            color: #666;
            margin-bottom: 15px;
        }
        .menu-item-info .price {
            font-weight: bold;
            color: #4CAF50;
            font-size: 1.2em;
        }
        .menu-item-info .button {
            display: block;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .menu-item-info .button:hover {
            background-color: #45a049;
        }
        .no-items {
            grid-column: 1 / -1;
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

    </style>
</head>
<body>
    <div class="header">
        <h1>CatersHub</h1>
    </div>
    
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="lunch-dinner.php">Lunch/Dinner</a>
        <a href="breakfast.php" class="active">Breakfast</a>
        <a href="snacks.php">Snacks</a>
        <a href="orderhistory.php">Order History</a>
        <div class="dropdown">
            <button class="dropdown-btn">More</button>
            <div class="dropdown-content">
                <a href="aboutus.php">About Us</a>
                <a href="services.php">Services</a>
                <a href="terms.php">Terms & Conditions</a>
                <a href="help.php">Help</a>
                <a href="user-login.php">logout</a>
                <a href="admin-login.php">Admin</a>
            </div>
        </div>

    </div>
    
    <div class="container">
        <div class="filters">
            <div class="filter-section">
                <h3>Price Range</h3>
                <div class="filter-options">
                    <a href="breakfast.php" 
                       class="filter-option <?php echo $price_range === 0 ? 'active' : ''; ?>">
                        All
                    </a>
                    <?php if ($price_ranges->num_rows > 0): ?>
                        <?php while($range = $price_ranges->fetch_assoc()): ?>
                            <a href="breakfast.php?price_range=<?php echo $range['id']; ?>" 
                               class="filter-option <?php echo $price_range === (int)$range['id'] ? 'active' : ''; ?>">
                                <?php echo $range['range_name']; ?>
                            </a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="menu-items">
            <?php if ($menu_items && $menu_items->num_rows > 0): ?>
                <?php while($item = $menu_items->fetch_assoc()): ?>
                    <div class="menu-item">
                        <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['name']; ?>">
                        <div class="menu-item-info">
                            <h3><?php echo $item['name']; ?></h3>
                            <p><?php echo $item['description']; ?></p>
                            <div class="price">₹<?php echo $item['price']; ?></div>
                            <a href="order-details.php?id=<?php echo $item['id']; ?>" class="button">Proceed to Order</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items">
                    <p>No breakfast items found matching your criteria. Please try different filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // JavaScript for any interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add any client-side interactivity here
        });
    </script>
</body>
</html>
