<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Required | Chickanji</title>
    <link rel="icon" href="assets/images/logo.png">
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .pleaselogin-container {
            max-width: 500px;
            margin: 150px auto;
            padding: 3rem;
            background-color: var(--bg-glass);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }
        .pleaselogin-container h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        .login-btn-large {
            display: inline-block;
            background-color: var(--primary);
            color: #000;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .login-btn-large:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 184, 0, 0.2);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php"><img src="assets/images/logo.png" class="brand-title" width="50" height="50" alt="Logo"></a>
        <div class="navbar-links">
            <ul>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="aboutus.php">About us</a></li>
                <li><a href="contactus.php">Contact us</a></li>
            </ul>
        </div>
        <div class="login-signup-btns">
            <a href="login.php" class="login-btn">Login</a>
            <a href="signup.php" class="signup-btn">Sign Up</a>
        </div>
    </nav>
    <div class="pleaselogin-container">
        <h1>You must be logged in to checkout</h1>
        <a href="login.php" class="login-btn-large">Log in</a>
    </div>
    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>


