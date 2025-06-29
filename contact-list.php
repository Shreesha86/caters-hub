<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    redirect('adminlogin.php');
}

// Process form submission for adding new caterer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

// Fetch caterer details
$sql = "SELECT * FROM caterers ORDER BY name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caterer Contact List - Admin Panel</title>
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
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .admin-nav {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            padding: 10px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
        }
        .admin-nav a:hover {
            background-color: #555;
        }
        .admin-nav .logout {
            background-color: #f44336;
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
        .add-form {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-nav">
        <div>
            <a href="contactlist.php">Caterer Contacts</a>
            <a href="current-orders.php">Current Orders</a>
            <a href="order-history.php">Order History</a>
        </div>
        <a href="admin_logout.php" class="logout">Logout</a>
    </div>

    <div class="container">
        <h1>Caterer Contact List</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success">
                <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']); 
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error">
                <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']); 
                ?>
            </div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No caterers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="add-form">
            <h2>Add New Caterer</h2>
            <form action="contact-list.php" method="post">
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
                    <button type="submit" class="button">Add Caterer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const phoneRegex = /^\d{10}$/;
            const phone = document.getElementById('phone').value;
            
            if (!phoneRegex.test(phone)) {
                alert('Please enter a valid 10-digit phone number');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>