<?php
// Start session
session_start();

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Define admin credentials
$admin_username = "admin";
$admin_password = "admin123"; // In a real application, use hashed passwords

// Process login form
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple validation
    if ($username == $admin_username && $password == $admin_password) {
        // Set session variables
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_username'] = $username;
        
        // Redirect to admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Caters Hub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #121212;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        .login-container {
            background-color: rgba(30, 30, 30, 0.8);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 380px;
            z-index: 10;
            transform: translateY(-20px);
            opacity: 0;
            animation: fadeInUp 1s forwards 0.3s;
            border: 1px solid rgba(57, 211, 83, 0.2);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .login-logo h1 {
            color: #ffffff;
            font-size: 28px;
            margin: 0;
            position: relative;
            display: inline-block;
        }

        .login-logo h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #39d353, #2ea043);
            animation: growLine 1.5s forwards 0.8s;
        }

        .login-form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #9e9e9e;
            font-size: 14px;
            letter-spacing: 0.5px;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background-color: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            font-size: 15px;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #39d353;
            outline: none;
            box-shadow: 0 0 0 3px rgba(57, 211, 83, 0.15);
        }

        .submit-btn {
            background-color: transparent;
            color: #39d353;
            border: 2px solid #39d353;
            border-radius: 50px;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            background-color: #39d353;
            color: #121212;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(57, 211, 83, 0.3);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .error-message {
            color: #ff5252;
            background-color: rgba(255, 82, 82, 0.1);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 3px solid #ff5252;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #9e9e9e;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
        }

        .back-link:hover {
            color: #39d353;
            transform: translateY(-2px);
        }

        .back-link::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 1px;
            background-color: #39d353;
            transition: width 0.3s ease;
        }

        .back-link:hover::after {
            width: 80%;
        }

        .background-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
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

        .particle {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(57, 211, 83, 0.2);
            pointer-events: none;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
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
                width: 60%;
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

        @media (max-width: 768px) {
            .login-container {
                width: 90%;
                padding: 30px;
            }
            
            .login-logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="background-circle circle1"></div>
    <div class="background-circle circle2"></div>
    
    <div class="login-container">
        <div class="login-logo">
            <h1>Admin Panel - Caters Hub</h1>
        </div>
        
        <?php if(!empty($error)) { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        
        <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="submit-btn">Login</button>
        </form>
        
        <a href="dashboardlogoff.php" class="back-link">Back to Home</a>
    </div>

    <script>
        // Create animated particles
        function createParticles() {
            const body = document.querySelector('body');
            const particleCount = 40;
            
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

        // Execute on page load
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            
            const submitBtn = document.querySelector('.submit-btn');
            
            submitBtn.addEventListener('mouseover', function() {
                this.style.transition = 'all 0.3s ease';
            });
        });
    </script>
</body>
</html>