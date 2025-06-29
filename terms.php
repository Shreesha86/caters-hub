<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - CatersHub</title>
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
        
        .terms-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 0;
        }
        
        .terms-section h2 {
            color: black;
            margin-bottom: 20px;
            font-size: 32px;
            text-align: center;
        }
        
        .terms-section p.last-updated {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-style: italic;
        }
        
        .terms-content h3 {
            color: black;
            margin-top: 25px;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .terms-content p {
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .terms-content ul, .terms-content ol {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        .terms-content li {
            margin-bottom: 8px;
        }
        
        .highlight-box {
            background-color: #fff9e6;
            border-left: 4px solid #FFD700;
            padding: 15px;
            margin: 20px 0;
        }
        
        .highlight-box p {
            margin-bottom: 0;
            font-weight: 500;
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
        <section class="terms-section">
            <h2>Terms and Conditions</h2>
            <p class="last-updated">Last Updated: May 09, 2025</p>
            
            <div class="terms-content">
                <p>Welcome to CatersHub. By accessing or using our website and services, you agree to be bound by these Terms and Conditions. Please read them carefully before using our platform.</p>
                
                <h3>1. Acceptance of Terms</h3>
                <p>By creating an account, accessing, or using our services, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, you may not use our services.</p>
                
                <h3>2. User Registration</h3>
                <p>To use certain features of our website, you must register for an account. You agree to provide accurate, current, and complete information during the registration process and to update such information to keep it accurate, current, and complete.</p>
                
                <div class="highlight-box">
                    <p>You are responsible for safeguarding your password and for all activities that occur under your account. You agree not to disclose your password to any third party.</p>
                </div>
                
                <h3>3. Ordering Process</h3>
                <p>When placing an order through our platform:</p>
                <ul>
                    <li>You are responsible for providing accurate information regarding your order details, including delivery date, time, location, and contact information.</li>
                    <li>All orders are subject to availability and confirmation of the order price.</li>
                    <li>We reserve the right to refuse or cancel any orders at our discretion.</li>
                </ul>
                
                <h3>4. Payment and Pricing</h3>
                <p>All prices displayed on our website are in Indian Rupees (₹) and are inclusive of applicable taxes unless stated otherwise. Payment methods accepted will be displayed at the time of checkout.</p>
                
                <h3>5. Cancellation Policy</h3>
                <p>Orders can be cancelled only up to 10 days before the delivery date. For cancellations made:</p>
                <ul>
                    <li>More than 10 days before delivery date: Full refund</li>
                    <li>Less than 10 days before delivery date: No refund will be provided</li>
                </ul>
                <p>Cancellation requests must be submitted through your user account or by contacting our customer support.</p>
                
                <h3>6. Discount System</h3>
                <p>Our discount system operates as follows:</p>
                <ul>
                    <li>10% discount for orders of 100+ plates</li>
                    <li>15% discount for orders of 200+ plates</li>
                    <li>20% discount for orders of 500+ plates</li>
                </ul>
                <p>Discounts are automatically applied during the checkout process and cannot be combined with other promotional offers unless explicitly stated.</p>
                
                <h3>7. Delivery</h3>
                <p>We strive to ensure timely delivery of all orders. However, we cannot guarantee exact delivery times as they may be affected by factors beyond our control. In case of any anticipated delays, we will notify you in advance.</p>
                
                <h3>8. Feedback and Rating</h3>
                <p>After the delivery of your order, you will have the opportunity to provide feedback and rate our service. We appreciate your honest feedback as it helps us improve our services.</p>
                
                <h3>9. User Conduct</h3>
                <p>You agree not to use our website for any illegal or unauthorized purpose. You must not attempt to gain unauthorized access to our systems or interfere with other users' use of the website.</p>
                
                <h3>10. Changes to Terms</h3>
                <p>We reserve the right to modify these Terms and Conditions at any time. Changes will be effective immediately upon posting on our website. Your continued use of our services after any such changes constitutes your acceptance of the new Terms and Conditions.</p>
                
                <h3>11. Special Dietary Requirements</h3>
                <p>While we make every effort to accommodate special dietary requirements and allergies:</p>
                <ul>
                    <li>You must clearly specify any allergies or dietary restrictions when placing your order</li>
                    <li>We cannot guarantee that our food is completely free from allergens as our kitchen handles various ingredients</li>
                    <li>CatersHub is not liable for allergic reactions if proper notification was not provided at the time of order</li>
                </ul>
                
                <h3>12. Intellectual Property</h3>
                <p>All content, including but not limited to logos, images, text, and software used on this website, is the property of CatersHub and is protected by applicable copyright and trademark laws. You may not use, reproduce, distribute, or create derivative works from this content without express written consent from CatersHub.</p>
                
                <h3>13. Limitation of Liability</h3>
                <p>CatersHub shall not be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use or inability to use our services. In no event shall our liability exceed the amount paid by you for the specific order in question.</p>
                
                <div class="highlight-box">
                    <p>We are not liable for damages caused by delays, failure in performance, or interruption of service due to events beyond our reasonable control.</p>
                </div>
                
                <h3>14. Privacy Policy</h3>
                <p>Your use of our website is also governed by our Privacy Policy, which is incorporated by reference into these Terms and Conditions. Please review our Privacy Policy to understand our practices regarding your personal information.</p>
                
                <h3>15. Customer Responsibilities</h3>
                <p>As a customer, you are responsible for:</p>
                <ul>
                    <li>Ensuring the venue is accessible for our delivery staff</li>
                    <li>Providing accurate address and contact information</li>
                    <li>Being available to receive the delivery during the agreed time window</li>
                    <li>Promptly reporting any issues with the order upon delivery</li>
                </ul>
                
                <h3>16. Service Availability</h3>
                <p>CatersHub currently provides services only in select cities across India. Service availability is subject to change without prior notice. We reserve the right to refuse service to any location at our discretion, particularly in areas that fall outside our standard delivery zones.</p>
                
                <h3>17. Force Majeure</h3>
                <p>CatersHub shall not be liable for any failure or delay in performance due to circumstances beyond our reasonable control, including but not limited to acts of God, natural disasters, pandemic situations, governmental actions, or civil unrest.</p>
                
                <h3>18. Dispute Resolution</h3>
                <p>Any dispute arising from these Terms and Conditions shall be resolved through:</p>
                <ol>
                    <li>Amicable negotiation between parties</li>
                    <li>If unresolved, through mediation by a mutually agreed third party</li>
                    <li>If still unresolved, through arbitration in accordance with the laws of India</li>
                </ol>
                
                <h3>19. Severability</h3>
                <p>If any provision of these Terms and Conditions is found to be unenforceable or invalid, that provision shall be limited or eliminated to the minimum extent necessary so that the Terms and Conditions shall otherwise remain in full force and effect and enforceable.</p>
                
                <h3>20. Governing Law</h3>
                <p>These Terms and Conditions shall be governed by and construed in accordance with the laws of India, without regard to its conflict of law provisions. You agree to submit to the personal and exclusive jurisdiction of the courts located in Mumbai, Maharashtra for any disputes arising out of these terms.</p>
                
                <h3>21. Contact Information</h3>
                <p>If you have any questions about these Terms and Conditions, please contact us through our Help page or send an email to support@CatersHub.com.</p>
                
                <div class="highlight-box">
                    <p>By using CatersHub, you acknowledge that you have read, understood, and agree to these Terms and Conditions.</p>
                </div>
            </div>
        </section>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 CatersHub. All Rights Reserved.</p>
            <p><a href="terms.php">Terms & Conditions</a> |  | <a href="help.php">Help</a></p>
            <p>Address: 123 Catering Way, Mumbai, Maharashtra - 400001</p>
            <p>Contact: +91 9876543210 | Email: support@CatersHub.com</p>
        </div>
    </footer>
</body>
</html>