<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - CatersHub</title>
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
        
        .help-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 0;
        }
        
        .help-section h2 {
            color: black;
            margin-bottom: 20px;
            font-size: 32px;
            text-align: center;
        }
        
        .help-intro {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .faq-container {
            margin-top: 30px;
        }
        
        .faq-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        
        .faq-question {
            font-weight: 600;
            color: #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }
        
        .faq-question:after {
            content: '+';
            font-size: 22px;
            color: black;
        }
        
        .faq-answer {
            display: none;
            padding: 10px 0;
            color: #666;
        }
        
        .faq-item.active .faq-question:after {
            content: '-';
        }
        
        .faq-item.active .faq-answer {
            display: block;
        }
        
        .contact-section {
            margin-top: 50px;
        }
        
        .contact-section h3 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        
        .contact-methods {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .contact-method {
            text-align: center;
            width: 30%;
            margin-bottom: 30px;
        }
        
        .contact-icon {
            font-size: 40px;
            color: black;
            margin-bottom: 15px;
        }
        
        .contact-method h4 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .contact-method p {
            color: #666;
        }
        
        .contact-form {
            margin-top: 50px;
        }
        
        .contact-form h3 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .submit-btn:hover {
            background-color: #e55a2b;
        }
        
        .guide-section {
            margin-top: 50px;
        }
        
        .guide-section h3 {
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        
        .guide-steps {
            margin-top: 30px;
        }
        
        .step {
            display: flex;
            margin-bottom: 30px;
            align-items: flex-start;
        }
        
        .step-number {
            background-color: black;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 20px;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .step-content h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .step-content p {
            color: #666;
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
            
            .contact-methods {
                flex-direction: column;
                align-items: center;
            }
            
            .contact-method {
                width: 100%;
                max-width: 300px;
            }
            
            .step {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .step-number {
                margin-right: 0;
                margin-bottom: 15px;
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

                <?php if(!isset($_SESSION['user_email'])):?>
                    <li><a href="user-login.php">Login</a></li>
                    <li><a href="user-registration.php">Register</a></li>
                    <li><a href="dashboardlogoff.php">Home</a></li>
                <?php endif; ?>
                <li><a href="help.php">Help</a></li>

            </ul>
        </nav>
    </header>
    
    <div class="container">
        <section class="help-section">
            <h2>Help & Support</h2>
            
            <div class="help-intro">
                <p>We're here to assist you with any questions or issues you might have. Find answers to frequently asked questions or reach out to our support team.</p>
            </div>
            
            <div class="faq-container">
                <h3>Frequently Asked Questions</h3>
                
                <div class="faq-item">
                    <div class="faq-question">How do I create an account?</div>
                    <div class="faq-answer">
                        <p>Creating an account is simple! Click on the "Register" button in the navigation menu and fill out the required information including your name, phone number, email address, and password. Once you submit the form, your account will be created and you can immediately log in to start exploring our catering options.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">How does the ordering process work?</div>
                    <div class="faq-answer">
                        <p>The ordering process involves several easy steps:</p>
                        <ol>
                            <li>Log in to your account</li>
                            <li>Browse through our meal categories (Breakfast, Lunch/Dinner, Snacks)</li>
                            <li>Select your preferred menu items and platters</li>
                            <li>Enter details like delivery date, time, location, and number of plates</li>
                            <li>Review your order summary including any applicable discounts</li>
                            <li>Confirm your order</li>
                        </ol>
                        <p>Once confirmed, your order will appear in your dashboard and our team will start processing it.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">What is your cancellation policy?</div>
                    <div class="faq-answer">
                        <p>Orders can be cancelled up to 10 days before the scheduled delivery date for a full refund. Cancellations made less than 10 days before the delivery date are not eligible for refunds. To cancel an order, log in to your account, navigate to your orders, and select the cancellation option for the specific order you wish to cancel.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">How does the discount system work?</div>
                    <div class="faq-answer">
                        <p>We offer volume-based discounts as follows:</p>
                        <ul>
                            <li>10% discount for orders of 100+ plates</li>
                            <li>15% discount for orders of 200+ plates</li>
                            <li>20% discount for orders of 500+ plates</li>
                        </ul>
                        <p>Discounts are automatically applied during the checkout process based on the number of plates you order.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">What payment methods do you accept?</div>
                    <div class="faq-answer">
                        <p>We accept various payment methods including credit/debit cards, net banking, UPI, and wallet payments. All transactions are secure and processed through trusted payment gateways. For large orders, we may also offer options for partial advance payment.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">Can I modify my order after placing it?</div>
                    <div class="faq-answer">
                        <p>Yes, you can modify your order up to 10 days before the delivery date. To make changes, log in to your account, go to your active orders, and select the modification option. Please note that significant changes to your order may affect pricing and availability.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">How can I provide feedback on my order?</div>
                    <div class="faq-answer">
                        <p>We value your feedback! After your order has been delivered, you'll receive a notification to rate and review our service. You can provide feedback directly through your user dashboard by navigating to your order history and selecting the "Provide Feedback" option for the completed order.</p>
                    </div>
                </div>
            </div>
            
            <div class="guide-section">
                <h3>How to Use CatersHub</h3>
                
                <div class="guide-steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Create an Account</h4>
                            <p>Register with your name, email, phone number, and password to create your CatersHub account.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Browse Meal Options</h4>
                            <p>Explore our various meal categories including Breakfast, Lunch/Dinner (Veg & Non-Veg), and Snacks.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Select Your Platters</h4>
                            <p>Choose from a variety of platters based on cuisine type and price range that suits your preferences.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h4>Enter Order Details</h4>
                            <p>Provide delivery date, time, location, contact number, and number of plates required.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <h4>Review & Confirm</h4>
                            <p>Review your order summary including any applicable discounts and confirm your booking.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">6</div>
                        <div class="step-content">
                            <h4>Track Your Order</h4>
                            <p>Monitor your order status through your user dashboard and receive updates on your booking.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="contact-section">
                <h3>Contact Us</h3>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">📞</div>
                        <h4>Phone Support</h4>
                        <p>+91 9876543210</p>
                        <p>Mon-Sat: 9AM - 6PM</p>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">✉️</div>
                        <h4>Email Support</h4>
                        <p>support@CatersHub.com</p>
                        <p>Response within 24 hours</p>
                    </div>
              </div>     
  </section>
    </div>
    
    <footer>
        <p>&copy; 2025 CatersHub. All Rights Reserved.</p>
        <p><a href="terms.php">Terms & Conditions</a> | <a href="help.php">Help</a></p>
    </footer>
    
    <script>
        // FAQ Toggle Script
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const faqItem = question.parentElement;
                    faqItem.classList.toggle('active');
                });
            });
        });
    </script>
</body>
</html>