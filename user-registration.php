<?php
session_start();
require_once 'db_connect.php';

$registration_success = false;
$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = sanitize_input($_POST['name']);
    $phone = sanitize_input($_POST['phone']);
    $email = sanitize_input($_POST['email']);
    $address = sanitize_input($_POST['address']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);
    
    // Validate form data
    if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($password)) {
        $error_message = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match";
    } else {
        // Check if name already exists
        $check_query = "SELECT * FROM users WHERE name = '$name'";
        $result = $conn->query($check_query);
        
        if ($result->num_rows > 0) {
            $error_message = "Account already exists. Login or Please choose a different name.";
        } else {
            // Hash the password
            $hashed_password = hash_password($password);
            
            // Insert user data into database
            $sql = "INSERT INTO users (name, phone, email, address, password) 
                    VALUES ('$name', '$phone', '$email', '$address', '$hashed_password')";
            
            if ($conn->query($sql) === TRUE) {
                $registration_success = true;
            } else {
                $error_message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - Caters Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #121212;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        .background-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        .circle1 {
            top: -150px;
            left: -150px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #39d353, transparent 70%);
            animation: pulse 15s infinite alternate;
        }

        .circle2 {
            bottom: -200px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #39d353, transparent 70%);
            animation: pulse 12s infinite alternate-reverse;
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background-color: rgba(30, 30, 30, 0.8);
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(57, 211, 83, 0.1);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s forwards 0.3s;
        }

        h1 {
            font-size: 2.5rem;
            text-align: center;
            color: #ffffff;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 15px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #39d353, #2ea043);
            animation: growLine 1.5s forwards 0.8s;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.6s; }
        .form-group:nth-child(2) { animation-delay: 0.7s; }
        .form-group:nth-child(3) { animation-delay: 0.8s; }
        .form-group:nth-child(4) { animation-delay: 0.9s; }
        .form-group:nth-child(5) { animation-delay: 1.0s; }
        .form-group:nth-child(6) { animation-delay: 1.1s; }
        .form-group:nth-child(7) { animation-delay: 1.2s; }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #9e9e9e;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        input:focus,
        textarea:focus {
            border-color: #39d353;
            box-shadow: 0 0 10px rgba(57, 211, 83, 0.2);
        }

        .button {
            width: 100%;
            padding: 14px;
            background-color: transparent;
            color: #39d353;
            border: 2px solid #39d353;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            outline: none;
            opacity: 0;
            animation: fadeIn 1.5s forwards 1.3s;
        }

        .button:hover {
            background-color: #39d353;
            color: #121212;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(57, 211, 83, 0.3);
        }

        .button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .button:hover::before {
            left: 100%;
        }

        .error {
            color: #ff5757;
            margin-bottom: 20px;
            padding: 10px;
            border-left: 3px solid #ff5757;
            background-color: rgba(255, 87, 87, 0.1);
            border-radius: 3px;
            animation: shake 0.5s ease-in-out;
        }

        .success {
            color: #39d353;
            margin-bottom: 20px;
            padding: 15px;
            border-left: 3px solid #39d353;
            background-color: rgba(57, 211, 83, 0.1);
            border-radius: 3px;
            opacity: 0;
            animation: fadeIn 1s forwards;
        }

        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #9e9e9e;
            font-size: 0.9rem;
            opacity: 0;
            animation: fadeIn 1.5s forwards 1.5s;
        }

        .login-link a {
            color: #39d353;
            text-decoration: none;
            font-weight: 500;
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background-color: #39d353;
            transition: width 0.3s ease;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(57, 211, 83, 0.2);
            pointer-events: none;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes growLine {
            from {
                width: 0;
            }
            to {
                width: 120px;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.15;
            }
            100% {
                transform: scale(1);
                opacity: 0.1;
            }
        }

        @keyframes shake {
            0%, 100% {transform: translateX(0);}
            10%, 30%, 50%, 70%, 90% {transform: translateX(-5px);}
            20%, 40%, 60%, 80% {transform: translateX(5px);}
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="background-circle circle1"></div>
    <div class="background-circle circle2"></div>
    
    <div class="container">
        <h1>Join Caters Hub</h1>
        
        <?php if ($registration_success): ?>
            <div class="success">
                Registration successful! You can now <a href="user-login.php">login</a> to your account.
            </div>
        <?php else: ?>
            <?php if (!empty($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="button">Create Account</button>
                </div>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="user-login.php">Login here</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Create animated particles
        function createParticles() {
            const body = document.querySelector('body');
            const particleCount = 25;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random properties
                const size = Math.random() * 15 + 5;
                const posX = Math.random() * window.innerWidth;
                const posY = Math.random() * window.innerHeight;
                const duration = Math.random() * 30 + 10;
                const delay = Math.random() * 5;
                
                // Apply styles
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}px`;
                particle.style.top = `${posY}px`;
                particle.style.opacity = Math.random() * 0.2;
                particle.style.animation = `float ${duration}s ease-in-out ${delay}s infinite`;
                
                body.appendChild(particle);
            }
        }

        // Initialize particles and form validation
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            
            // Client-side validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const phoneRegex = /^\d{10}$/;
                const phone = document.getElementById('phone').value;
                
                if (!phoneRegex.test(phone)) {
                    alert('Please enter a valid 10-digit phone number');
                    e.preventDefault();
                    return;
                }
                
                if (password !== confirmPassword) {
                    alert('Passwords do not match');
                    e.preventDefault();
                }
            });
            
            // Input focus effects
            const inputs = document.querySelectorAll('input, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.previousElementSibling.style.color = '#39d353';
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.previousElementSibling.style.color = '#9e9e9e';
                    }
                });
            });
        });
    </script>
</body>
</html>