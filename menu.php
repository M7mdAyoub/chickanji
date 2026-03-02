<?php
session_start();
require 'includes/connectdb.php';

// Add to cart logic
if(isset($_POST['add-to-cart'])) {
    if(!isset($_SESSION['user_id'])) {
        header('Location: pleaselogin.php');
        exit;
    }

    $menu_item_id = $_POST['menu_item_id'];
    $user_id = $_SESSION['user_id'];

    // Check if it already exists in cart, if so, increment quantity
    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = :user_id AND menu_item_id = :menu_item_id");
    $check->execute([':user_id' => $user_id, ':menu_item_id' => $menu_item_id]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $update = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = :id");
        $update->execute([':id' => $existing['id']]);
    } else {
        $insert = $pdo->prepare("INSERT INTO cart (user_id, menu_item_id, quantity) VALUES (:user_id, :menu_item_id, 1)");
        $insert->execute([':user_id' => $user_id, ':menu_item_id' => $menu_item_id]);
    }
    
    // Optional: display a success message flag
    $added_to_cart = true;
}

// Fetch active menu items
$menu_stmt = $pdo->query("SELECT * FROM menu_items WHERE is_active = 1 ORDER BY id ASC");
$all_menu_items = $menu_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="assets/js/script.js" defer></script>
    <title>Menu | Chickanji</title>
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/menustyle.css?v=<?php echo time(); ?>">
    <link rel="icon" href="assets/images/logo.png">
</head>
<body>
    <nav class="navbar">
        <a href="index.php"><img src="assets/images/logo.png" class="brand-title" width="50" height="50"></a>
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
    <main class="main-content">
      <div class="menu-container">
      <div class="menu-items">
          <?php if(isset($added_to_cart)): ?>
              <p style="color: green; text-align: center; width: 100%;">Item added to cart!</p>
          <?php endif; ?>
          
          <?php foreach($all_menu_items as $item): ?>
          <div class="menu-item">
              <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-img">
              <h2><?php echo htmlspecialchars($item['name']); ?></h2>
              <p style="font-size: 14px; text-align: center; padding: 0 10px;"><?php echo htmlspecialchars($item['description']); ?></p>
              <div class="menu-item-options">
                  <div class="menu-item-price">$<?php echo htmlspecialchars($item['price']); ?></div>
                  <div class="menu-item-add-btn">
                      <form method="post" action="menu.php">
                          <input type="hidden" name="menu_item_id" value="<?php echo $item['id']; ?>">
                          <button type="submit" name="add-to-cart" class="add-btn">+</button>
                      </form>
                  </div>
              </div>
          </div>
          <?php endforeach; ?>
      </div>
      </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>





