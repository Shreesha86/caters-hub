<?php
session_start();
require_once 'db_connect.php';

// This version is for users who are not logged in yet
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Online Catering</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        

        .nav {
            display: flex;
            background-color: #444;
            overflow: visible; /* Changed from hidden to visible */
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
        /* Dropdown styles */
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
        .welcome-banner {
            background-color: white;
            padding: 30px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .welcome-banner h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .welcome-banner p {
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1em;
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #45a049;
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
        /* Footer style */
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
            /* Mobile-friendly dropdown */
            .nav {
                flex-direction: column;
            }
            .dropdown-content {
                position: static;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
                <h1>CatersHub</h1>
    </div>
    
    <div class="nav">
        <a href="dashboard_guest.php" class="active">Dashboard</a>
        <a href="lunch-dinner2.php">Lunch/Dinner</a>
        <a href="breakfast2.php">Breakfast</a>
        <a href="snacks2.php">Snacks</a>
        <div class="dropdown">
            <button class="dropdown-btn">More</button>
            <div class="dropdown-content">
                <a href="aboutus.php">About Us</a>
                <a href="services.php">Services</a>
                <a href="terms.php">Terms & Conditions</a>
                <a href="help.php">Help</a>
                <a href="user-login.php">Login</a>
                <a href="admin-login.php">Admin</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-banner">
            <h2>Welcome to Caters Hub</h2>
            <p>Discover our extensive menu of delicious catering options for all your events and occasions. From breakfast to dinner, we offer premium quality food prepared by expert chefs.</p>
            <p>Sign up now to place orders and track your deliveries!</p>
            <a href="user-login.php" class="cta-button">Login / Register</a>
        </div>
        
        <h2>Meal Selection</h2>
        <div class="meal-selection">
            <div class="meal-card">
                <img src="breakfast.jpg" alt="Breakfast">
                <div class="meal-card-content">
                    <h3>Breakfast</h3>
                    <p>Start your day with our delicious breakfast options</p>
                    <a href="breakfast2.php" class="button">Explore</a>
                </div>
            </div>
            
            <div class="meal-card">
                <img src="lunch.jpg" alt="Lunch/Dinner">
                <div class="meal-card-content">
                    <h3>Lunch/Dinner</h3>
                    <p>Explore our extensive lunch and dinner menu</p>
                    <a href="lunch-dinner2.php" class="button">Explore</a>
                </div>
            </div>
            
            <div class="meal-card">
                <img src="snacks.jpg" alt="Snacks">
                <div class="meal-card-content">
                    <h3>Snacks</h3>
                    <p>Perfect bites for your events and gatherings</p>
                    <a href="snacks2.php" class="button">Explore</a>
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
                        <p><i class="fas fa-map-marker-alt"></i> MGM college gate udupi karnataka India</p>
                        <p><i class="fas fa-phone"></i> +91 9876543210</p>
                        <p><i class="fas fa-envelope"></i> info@catershub.com</p>
                        <p><i class="fas fa-clock"></i> Mon-Sat: 9:00 AM - 8:00 PM</p>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="aboutus.php">About Us</a></p>
                    <p><a href="services.php">Services</a></p>
                    <p><a href="terms.php">Terms & Conditions</a></p>
                    <p><a href="help.php">Help</a></p>
                </div>
                
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <p>Follow us on social media for updates, special offers, and culinary inspiration.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
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
                <p class="developers">Developed by <a href="#">Shreesha</a>, <a href="#">Shashank</a>, <a href="#">Akshay</a></p>
            </div>
        </div>
    </div>
    
    <script>
        // JavaScript to enhance dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get all dropdown buttons
            var dropdowns = document.querySelectorAll('.dropdown-btn');
            
            // Add click event for mobile devices
            dropdowns.forEach(function(dropdown) {
                dropdown.addEventListener('click', function() {
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            });
        });
    </script>
</body>
</html>