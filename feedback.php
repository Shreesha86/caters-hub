<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in - using the same approach as dashboard.php
if (!isset($_SESSION['user_name']) || !isset($_SESSION['email'])) {
    header('Location: userlogin.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$email = $_SESSION['email'];

$message = '';
$error = '';
$existing_feedback = null;

// Check if order ID is provided
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    
    // Fetch order details - Using prepared statement for security
    $stmt = $conn->prepare("SELECT o.*, m.name as menu_name FROM orders o 
                           JOIN menu_items m ON o.menu_item_id = m.id 
                           WHERE o.id = ? AND o.user_name = ?");
    $stmt->bind_param("is", $order_id, $user_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $error = "Order not found or you don't have permission to provide feedback for this order.";
    } else {
        $order = $result->fetch_assoc();
        
        // Check if feedback already exists for this order
        $check_stmt = $conn->prepare("SELECT * FROM feedback WHERE order_id = ?");
        $check_stmt->bind_param("i", $order_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Feedback already exists - store it to display to the user
            $existing_feedback = $check_result->fetch_assoc();
            $message = "Thank you! You have already provided feedback for this order.";
        } else {
            // Check if order is delivered (only delivered orders can receive feedback)
            if ($order['status'] !== 'Delivered') {
                $error = "Feedback can only be provided for delivered orders.";
            } else {
                // Process form submission
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])) {
                    $rating = $_POST['rating'];
                    $feedback_text = $_POST['feedback_text'];
                    
                    // Insert feedback into the feedback table
                    $insert_stmt = $conn->prepare("INSERT INTO feedback (order_id, rating, comments, created_at) VALUES (?, ?, ?, NOW())");
                    $insert_stmt->bind_param("iis", $order_id, $rating, $feedback_text);
                    
                    if ($insert_stmt->execute()) {
                        $message = "Thank you! Your feedback has been submitted successfully.";
                    } else {
                        $error = "Error submitting feedback: " . $conn->error;
                    }
                    $insert_stmt->close();
                }
            }
        }
        if (isset($check_stmt)) {
            $check_stmt->close();
        }
    }
    $stmt->close();
} else {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Online Catering</title>
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
        .feedback-form {
            background-color: #eaf7ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .previous-feedback {
            background-color: #f0f7f0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .star-display {
            color: #ffb300;
            font-size: 24px;
            margin: 10px 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            min-height: 120px;
            resize: vertical;
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            cursor: pointer;
            font-size: 30px;
            color: #ddd;
            margin-right: 5px;
        }
        .star-rating :checked ~ label {
            color: #ffb300;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffb300;
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
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .nav {
            display: flex;
            background-color: #444;
            overflow: hidden;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Rate Your Experience</h1>
            <div class="subtitle">Tell us about your catering experience</div>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($order)): ?>
            <div class="order-details">
                <div class="detail-row">
                    <div class="detail-label">Order ID:</div>
                    <div>#<?php echo $order['id']; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Menu Item:</div>
                    <div><?php echo $order['menu_name']; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Number of Plates:</div>
                    <div><?php echo $order['num_plates']; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Delivery Date:</div>
                    <div><?php echo date('d M Y', strtotime($order['delivery_date'])); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Total Amount:</div>
                    <div>₹<?php echo $order['final_price']; ?></div>
                </div>
            </div>
            
            <?php if ($existing_feedback): ?>
                <div class="message"><?php echo $message; ?></div>
                
                <div class="previous-feedback">
                    <h3>Your Submitted Feedback</h3>
                    <div class="detail-row">
                        <div class="detail-label">Rating:</div>
                        <div class="star-display">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php echo ($i <= $existing_feedback['rating']) ? '★' : '☆'; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($existing_feedback['comments'])): ?>
                    <div class="detail-row">
                        <div class="detail-label">Your Comments:</div>
                        <div><?php echo nl2br(htmlspecialchars($existing_feedback['comments'])); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-row">
                        <div class="detail-label">Submitted On:</div>
                        <div><?php echo date('d M Y, h:i A', strtotime($existing_feedback['created_at'])); ?></div>
                    </div>
<div class="buttons">
                <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            </div>

                </div>
            <?php elseif (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php else: ?>
                <form class="feedback-form" method="post" action="">
                    <div class="form-group">
                        <label>How would you rate your experience?</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required />
                            <label for="star5" title="5 stars">★</label>
                            <input type="radio" id="star4" name="rating" value="4" />
                            <label for="star4" title="4 stars">★</label>
                            <input type="radio" id="star3" name="rating" value="3" />
                            <label for="star3" title="3 stars">★</label>
                            <input type="radio" id="star2" name="rating" value="2" />
                            <label for="star2" title="2 stars">★</label>
                            <input type="radio" id="star1" name="rating" value="1" />
                            <label for="star1" title="1 star">★</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="feedback_text">Additional Comments:</label>
                        <textarea id="feedback_text" name="feedback_text" placeholder="Share your experience, suggestions, or any feedback about the food quality, service, etc."></textarea>
                    </div>
                    
                    <div class="buttons">
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    </div>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <div class="buttons">
                <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Optional JavaScript to enhance the user experience
        document.addEventListener('DOMContentLoaded', function() {
            // Highlighting star rating on selection
            const stars = document.querySelectorAll('.star-rating input');
            stars.forEach(star => {
                star.addEventListener('change', function() {
                    // Could add additional functionality here if needed
                });
            });
        });
    </script>
</body>
</html>