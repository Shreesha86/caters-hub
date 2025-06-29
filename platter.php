<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    redirect('userlogin.php');
}

// Get platter id
$platter_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($platter_id <= 0) {
    redirect('lunchdinner.php');
}

// Get platter details
$sql = "SELECT m.*, c.name as category_name, p.range_name 
        FROM menu_items m
        JOIN menu_categories c ON m.category_id = c.id
        JOIN price_ranges p ON m.price_range_id = p.id
        WHERE m.id = $platter_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    redirect('lunchdinner.php');
}

$platter = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $platter['name']; ?> - Online Catering</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
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
        .platter-details {
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
        }
        .platter-image {
            flex: 0 0 40%;
            margin-right: 20px;
        }
        .platter-image img {
            width: 100%;
            border-radius: 5px;
        }
        .platter-info {
            flex: 1;
        }
        .platter-info h1 {
            margin-top: 0;
            color: #333;
        }
        .platter-info .category {
            color: #666;
            margin-bottom: 15px;
        }
        .platter-info .price {
            font-size: 1.5em;
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .platter-info .description {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        .button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
        }
        .button-primary {
            background-color: #4CAF50;
            color: white;
        }
        .button-primary:hover {
            background-color: #45a049;
        }
        .button-secondary {
            background-color: #f1f1f1;
            color: #333;
        }
        .button-secondary:hover {
            background-color: #e1e1e1;
        }
        @media (max-width: 768px) {
            .platter-details {
                flex-direction: column;
            }
            .platter-image {
                flex: 0 0 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Platter Details</h1>
    </div>
    
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="lunch-dinner.php" class="active">Lunch/Dinner</a>
        <a href="breakfast.php">Breakfast</a>
        <a href="snacks.php">Snacks</a>
        <a href="orderhistory.php">Order History</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <div class="container">
        <div class="platter-details">
            <div class="platter-image">
                <img src="<?php echo $platter['image_path']; ?>" alt="<?php echo $platter['name']; ?>">
            </div>
            
            <div class="platter-info">
                <h1><?php echo $platter['name']; ?></h1>
                <div class="category">
                    Category: <?php echo $platter['category_name']; ?> | 
                    Price Range: <?php echo $platter['range_name']; ?>
                </div>
                <div class="price">₹<?php echo $platter['price']; ?> per plate</div>
                <div class="description">
                    <?php echo $platter['description']; ?>
                </div>
                <div class="buttons">
                    <a href="order-details.php" class="button button-primary">Proceed to Order</a>
                    <a href="javascript:history.back()" class="button button-secondary">Go Back</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
