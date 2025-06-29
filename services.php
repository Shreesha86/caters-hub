<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - CatersHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 85%;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: black;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo h1 {
            font-size: 28px;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 20px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .nav-links a:hover {
            color: #FFD700;
        }
        
        .services-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 0;
        }
        
        .services-section h2 {
            color: black;
            margin-bottom: 20px;
            font-size: 32px;
            text-align: center;
        }
        
        .services-intro {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .services-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .service-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease;
            border-top: 5px solid black;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
        }
        
        .service-icon {
            font-size: 50px;
            color: black;
            margin-bottom: 15px;
        }
        
        .service-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 22px;
        }
        
        .service-card p {
            color: #666;
            font-size: 15px;
            margin-bottom: 15px;
        }
        
        .meal-options {
            margin-top: 50px;
        }
        
        .meal-options h3 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        
        .meal-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .meal-category {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        
        .meal-category h4 {
            color: black;
            margin-bottom: 10px;
            font-size: 20px;
        }
        
        .meal-category ul {
            list-style-type: none;
            padding: 0;
        }
        
        .meal-category li {
            padding: 5px 0;
            border-bottom: 1px dashed #eee;
        }
        
        .meal-category li:last-child {
            border-bottom: none;
        }
        
        .price-ranges {
            margin-top: 50px;
        }
        
        .price-ranges h3 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .price-table th, .price-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .price-table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: 600;
        }
        
        .price-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .cta-section {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        
        .cta-section h3 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .cta-button {
            display: inline-block;
            background-color: black;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }
        
        .cta-button:hover {
            background-color: #e55a2b;
        }
        
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }
        
        footer p {
            margin: 5px 0;
        }
        
        footer a {
            color: #FFD700;
            text-decoration: none;
        }
        
        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            
            .services-container {
                grid-template-columns: 1fr;
            }
            
            .meal-categories {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>CatersHub</h1>
            </div>
            <ul class="nav-links">
                <li><a href="dashboardlogoff.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="aboutus.php">About Us</a></li>
                <?php if(isset($_SESSION['user_email'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="dashboardlogoff.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="user-login.php">Login</a></li>
                    <li><a href="user-registration.php">Register</a></li>
                <?php endif; ?>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <section class="services-section">
            <h2>Our Catering Services</h2>
            
            <div class="services-intro">
                <p>CatersHub offers comprehensive catering solutions for all your events, big or small. Our platform connects you with quality catering services tailored to your specific needs and budget.</p>
            </div>
            
            <div class="services-container">
                <div class="service-card">
                    <div class="service-icon">🍽️</div>
                    <h3>Diverse Menu Options</h3>
                    <p>Choose from a wide variety of cuisine options including South Indian, North Indian, and international delicacies. Our menu caters to all dietary preferences with vegetarian and non-vegetarian options available.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">💼</div>
                    <h3>Corporate Events</h3>
                    <p>Perfect for business meetings, conferences, and office celebrations. We provide professional catering services that leave a lasting impression on your clients and colleagues.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">💍</div>
                    <h3>Weddings & Celebrations</h3>
                    <p>Make your special day memorable with our exquisite catering services. From intimate gatherings to grand celebrations, we have menu options to suit every occasion.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🏠</div>
                    <h3>Home Parties</h3>
                    <p>Enjoy your own party without the stress of cooking. Our catering services for home events ensure you spend quality time with your guests while we take care of the food.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🎓</div>
                    <h3>Academic Events</h3>
                    <p>From college functions to graduation ceremonies, our catering services are designed to accommodate large groups with efficient service and quality food.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">⏱️</div>
                    <h3>Timely Delivery</h3>
                    <p>We understand the importance of punctuality. Our services ensure that your food is delivered and set up at the specified time, ready to serve your guests.</p>
                </div>
            </div>
            
            <div class="meal-options">
                <h3>Our Meal Categories</h3>
                
                <div class="meal-categories">
                    <div class="meal-category">
                        <h4>Breakfast</h4>
                        <ul>
                            <li>South Indian Breakfast Platters</li>
                            <li>North Indian Breakfast Options</li>
                            <li>Continental Breakfast Sets</li>
                            <li>Healthy Start Options</li>
                            <li>Traditional Breakfast Combos</li>
                        </ul>
                    </div>
                    
                    <div class="meal-category">
                        <h4>Lunch/Dinner (Vegetarian)</h4>
                        <ul>
                            <li>South Indian Vegetarian Thali</li>
                            <li>North Indian Vegetarian Feast</li>
                            <li>Fusion Vegetarian Platters</li>
                            <li>Traditional Festival Meals</li>
                            <li>International Vegetarian Options</li>
                        </ul>
                    </div>
                    
                    <div class="meal-category">
                        <h4>Lunch/Dinner (Non-Vegetarian)</h4>
                        <ul>
                            <li>South Indian Non-Veg Specialties</li>
                            <li>North Indian Non-Veg Delights</li>
                            <li>Coastal Cuisine Platters</li>
                            <li>Barbeque and Grill Options</li>
                            <li>International Non-Veg Selections</li>
                        </ul>
                    </div>
                    
                    <div class="meal-category">
                        <h4>Snacks</h4>
                        <ul>
                            <li>Traditional Indian Snacks</li>
                            <li>Finger Food Assortments</li>
                            <li>Evening Tea/Coffee with Snacks</li>
                            <li>Healthy Snack Options</li>
                            <li>Party Starter Platters</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="price-ranges">
                <h3>Our Price Ranges</h3>
                
                <table class="price-table">
                    <tr>
                        <th>Price Range</th>
                        <th>Description</th>
                        <th>Suitable For</th>
                    </tr>
                    <tr>
                        <td>₹100 - ₹150</td>
                        <td>Basic meal options with essential items</td>
                        <td>Small gatherings, budget events</td>
                    </tr>
                    <tr>
                        <td>₹150 - ₹250</td>
                        <td>Standard meal platters with more variety</td>
                        <td>Medium-sized events, office meetings</td>
                    </tr>
                    <tr>
                        <td>₹250 - ₹350</td>
                        <td>Premium meal options with special items</td>
                        <td>Special occasions, family celebrations</td>
                    </tr>
                    <tr>
                        <td>₹350 - ₹450</td>
                        <td>Deluxe meals with premium ingredients</td>
                        <td>Weddings, corporate events</td>
                    </tr>
                    <tr>
                        <td>₹500+</td>
                        <td>Exclusive gourmet platters with signature dishes</td>
                        <td>Luxury events, VIP gatherings</td>
                    </tr>
                </table>
            </div>
            
            <div class="cta-section">
                <h3>Ready to Order?</h3>
                <p>Experience hassle-free catering services for your next event.</p>
                <?php if(isset($_SESSION['user_email'])): ?>
                    <a href="dashboardlogoff.php" class="cta-button">Go to Dashboard</a>
                <?php else: ?>
                    <a href="user-registration.php" class="cta-button">Register Now</a>
                <?php endif; ?>
            </div>
        </section>
    </div>
    
    <footer>
        <p>&copy; 2025 CatersHub. All Rights Reserved.</p>
        <p><a href="terms.php">Terms & Conditions</a> | <a href="help.php">Help</a></p>
    </footer>
</body>
</html>