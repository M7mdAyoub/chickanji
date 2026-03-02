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
    <meta name="description" content="Discover the delicious taste of Chickanji's chicken dishes.">
    <title>Chickanji</title>
    <link rel="icon" href="assets/images/logo.png">
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
    <nav class="navbar">
    <a href="index.php"><img src="assets/images/logo.png" class="brand-title" width="50" height="50" ></a>
    
    
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
</header>
<section class="hero-section">
  <div class="video-overlay"></div>
  <div class="video">
    <video loop muted autoplay id="video">
        <source src="assets/images/video.mp4" type="video/mp4">
    </video>
  </div>
  <div class="content">
    <h1 class="animate-title">WELCOME TO <br><span class="highlight">CHICKANJI</span></h1>
    <p class="animate-subtitle">Experience the finest, crispiest chicken crafted to perfection.</p>
    <a href="menu.php" class="cta-button animate-btn">Explore Menu</a>
  </div>
</section>
<footer>
  <div class="footer-container">
      <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
  </div>
</footer>

</body>
</html>




