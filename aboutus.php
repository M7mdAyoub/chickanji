<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Chickanji</title>
    <script src="assets/js/script.js" defer></script>
    <link rel="icon" href="assets/images/logo.png">
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .about-us-container {
            max-width: 800px;
            margin: 120px auto 50px;
            padding: 3rem;
            background-color: var(--bg-glass);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }
        .about-us-container h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        .about-us-container p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            text-align: justify;
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
    <div class="about-us-container">
        <h1>About Us</h1>
        <p>At Chickanji, we are dedicated to bringing you the best quality chicken dishes from around the world. Our menu features a variety of flavors and styles, all made with fresh ingredients and cooked to perfection. We believe that food is a universal language and our goal is to bring people together through the love of delicious chicken dishes.</p>
        <p>Our team is made up of experienced chefs and passionate foodies who work tirelessly to create new and exciting menu items. We pride ourselves on our attention to detail and commitment to using only the freshest and highest quality ingredients. Whether you're in the mood for a classic dish or something new and exciting, we have something for everyone at Chickanji.</p>
        <p>We are constantly striving to improve and innovate our menu, so be sure to check back often to see what new dishes we have in store. We can't wait to share our love of chicken with you!</p>
    </div>
    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>


