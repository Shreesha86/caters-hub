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

// Fetch all users (caterers)
$sql = "SELECT * FROM users ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact List - Admin Panel</title>
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
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .search-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-btn {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .contact-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
        }
        .contact-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .contact-avatar {
            width: 50px;
            height: 50px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: #6c757d;
            margin-right: 15px;
        }
        .contact-name {
            font-size: 18px;
            font-weight: bold;
        }
        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .contact-item {
            display: flex;
            align-items: flex-start;
        }
        .contact-label {
            min-width: 80px;
            font-weight: bold;
            color: #6c757d;
        }
        .contact-value {
            word-break: break-word;
        }
        .contact-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
        .contact-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-email {
            background-color: #28a745;
            color: white;
        }
        .btn-call {
            background-color: #17a2b8;
            color: white;
        }
        .no-contacts {
            grid-column: 1 / -1;
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
        <h1>Admin Panel - Contact List</h1>
        <div class="nav">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="contactlist.php">Contacts</a>
            <a href="current-orders.php">Current Orders</a>
            <a href="order-history.php">Order History</a>
            <a href="admin-login.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h2>Customer Contacts</h2>
        
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Search by name, email or phone...">
            <button class="search-btn" id="searchBtn">Search</button>
        </div>
        
        <?php if($result->num_rows > 0): ?>
            <div class="contact-container" id="contactContainer">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="contact-card">
                        <div class="contact-header">
                            <div class="contact-avatar">
                                <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                            </div>
                            <div class="contact-name"><?php echo $row['name']; ?></div>
                        </div>
                        <div class="contact-details">
                            <div class="contact-item">
                                <div class="contact-label">Email:</div>
                                <div class="contact-value"><?php echo $row['email']; ?></div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-label">Phone:</div>
                                <div class="contact-value"><?php echo $row['phone']; ?></div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-label">Address:</div>
                                <div class="contact-value"><?php echo $row['address']; ?></div>
                            </div>
                        </div>
                        <div class="contact-actions">
                            <a href="mailto:<?php echo $row['email']; ?>" class="contact-btn btn-email">
                                Email
                            </a>
                            <a href="tel:<?php echo $row['phone']; ?>" class="contact-btn btn-call">
                                Call
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-contacts">
                <h3>No contacts found</h3>
                <p>There are no registered users in the system.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const contactCards = document.querySelectorAll('.contact-card');
            
            // Search functionality
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase();
                let hasResults = false;
                
                contactCards.forEach(card => {
                    const name = card.querySelector('.contact-name').textContent.toLowerCase();
                    const email = card.querySelector('.contact-value').textContent.toLowerCase();
                    const phone = card.querySelectorAll('.contact-value')[1].textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm)) {
                        card.style.display = 'block';
                        hasResults = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Show no results message if no matches found
                const contactContainer = document.getElementById('contactContainer');
                let noResultsMsg = document.querySelector('.no-results');
                
                if (!hasResults) {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement('div');
                        noResultsMsg.className = 'no-contacts no-results';
                        noResultsMsg.innerHTML = '<h3>No matching contacts</h3><p>Try a different search term.</p>';
                        contactContainer.appendChild(noResultsMsg);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
            
            // Search on button click
            searchBtn.addEventListener('click', performSearch);
            
            // Search on enter key
            searchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    performSearch();
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>