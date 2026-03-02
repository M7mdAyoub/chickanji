<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="assets/js/script.js" defer></script>
    <title>Contact Us | Chickanji</title>
    <link rel="icon" href="assets/images/logo.png">

    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .contact-container {
            max-width: 600px;
            margin: 150px auto 100px;
            padding: 3rem;
            background-color: var(--bg-glass);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }
        .contact-container h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        .contact-container p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--text-light);
        }
        .contact-container a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .contact-container a:hover {
            text-decoration: underline;
        }
        .insta {
            margin-top: 1rem;
            transition: transform 0.3s ease;
        }
        .insta:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php"><img src="assets/images/logo.png" class="brand-title" width="50" height="50" alt="Logo"></a>
        <a href="#" class="toggle-button">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </a>
        <div class="navbar-links">
            <ul>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="aboutus.php">About us</a></li>
                <li><a href="contactus.php">Contact us</a></li>
                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="admin/index.php" style="color:var(--primary);">Admin Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="login-signup-btns">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="login-btn">Login</a>
                <a href="signup.php" class="signup-btn">Sign Up</a>
            <?php else: ?>
                <span style="color:white; margin-right: 15px; font-weight: 500;">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="logout-btn">Log out</a>
            <?php endif; ?>
        </div>
        <div class="cart-icon-container">
            <a href="cart.php" class="cart-icon">
                <img src="assets/images/cart.jpg" width="30" height="30" alt="cart">
            </a>
        </div>
    </nav>
    <div class="contact-container">
        <h1>Contact Us</h1>
        <p>Email us at: <a href="mailto:21110247@htu.edu.jo">info@chickanji.com</a></p>
        <p class="location">Dawood Complex, Amman</p>
        <p class="hours">Open Saturday-Friday 12pm-12am</p>
        <a href="https://www.instagram.com/chickanji.jo" target="_blank"><img class="insta" src="assets/images/instagram_icon_transparent.png" width="40" height="40" alt="Instagram"></a>
    </div>
    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>


