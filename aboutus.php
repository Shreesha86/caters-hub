<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CatersHub</title>
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
        
        .about-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 0;
        }
        
        .about-section h2 {
            color: black;
            margin-bottom: 20px;
            font-size: 32px;
            text-align: center;
        }
        
        .about-content {
            margin-bottom: 30px;
        }
        
        .about-content p {
            margin-bottom: 20px;
            text-align: justify;
            font-size: 16px;
        }
        
        .team-section h3 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        
        .team-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }
        
        .team-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
        }
        
        .team-card img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 5px solid black;
        }
        
        .team-card h4 {
            color: black;
            margin-bottom: 5px;
            font-size: 20px;
        }
        
        .team-card p {
            color: #666;
            margin-bottom: 5px;
            font-size: 14px;
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
            
            .team-container {
                flex-direction: column;
                align-items: center;
            }
            
            .team-card {
                width: 100%;
                max-width: 300px;
                margin-bottom: 20px;
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
                
                <li><a href="services.php">Services</a></li>
                <li><a href="aboutus.php">About Us</a></li>
                <?php if(isset($_SESSION['user_email'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="dashboardlogoff.php">Logout</a></li>
                    <li><a href="dashboard.php">Home</a></li>
                <?php endif; ?>

                <?php if(!isset($_SESSION['user_email'])): ?>
                    <li><a href="user-login.php">Login</a></li>
                    <li><a href="user-registration.php">Register</a></li>
                    <li><a href="dashboardlogoff.php">Home</a></li>
                <?php endif; ?>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <section class="about-section">
            <h2>About CatersHub</h2>
            
            <div class="about-content">
                <p>Welcome to CatersHub, your premier destination for hassle-free catering services in Udupi and beyond. Founded in 2025, we have quickly established ourselves as a trusted platform connecting customers with quality catering services for all occasions. Our mission is to simplify the catering booking process while ensuring exceptional dining experiences for every event.</p>
                
                <p>At CatersHub, we believe that good food brings people together. That's why we've created an intuitive platform that allows you to browse through various meal options, compare prices, and book your catering needs with just a few clicks. From intimate gatherings to large corporate events, our diverse range of menu options ensures that there's something for everyone's taste and budget.</p>
                
                <p>What sets us apart is our commitment to quality and customer satisfaction. We carefully vet all our catering partners to ensure they meet our high standards of food quality, presentation, and service. Our platform provides transparent pricing, detailed menu descriptions, and a seamless booking process, making catering arrangements stress-free for our users.</p>
                
                <p>CatersHub is more than just a booking platform; we're your partner in creating memorable dining experiences. Our dedicated support team is always available to assist you with any questions or special requirements you might have. Whether you're planning a birthday celebration, wedding reception, corporate meeting, or any other event, CatersHub is here to make your catering experience exceptional.</p>
            </div>
            
            <div class="team-section">
                <h3>Meet Our Development Team</h3>
                <div class="team-container">
                    <div class="team-card">
                       
                        <h4>Shreesha</h4>
                        <p>Student</p>
                        <p>3rd BCA A</p>
                        <p>S0058</p>
                        <p>MGM COLLEGE, UDUPI</p>
                    </div>
                    
                    <div class="team-card">
                      
                        <h4>Shashank</h4>
                        <p>Student</p>
                        <p>3rd BCA A</p>
                        <p>S0060</p>
                        <p>MGM COLLEGE, UDUPI</p>
                    </div>
                    
                    <div class="team-card">
                      
                        <h4>Akshay</h4>
                        <p>Student</p>
                        <p>3rd BCA A</p>
                        <p>S0063</p>
                        <p>MGM COLLEGE, UDUPI</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <footer>
        <p>&copy; 2025 CatersHub. All Rights Reserved.</p>
        <p><a href="terms.php">Terms & Conditions</a> | <a href="help.php">Help</a></p>
    </footer>
</body>
</html>