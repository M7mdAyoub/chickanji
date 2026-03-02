<?php
session_start();
require 'includes/connectdb.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: pleaselogin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="assets/js/script.js" defer></script>
    <title>Cart | Chickanji</title>
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="assets/css/cartstyle.css?v=<?php echo time(); ?>">
    <link rel="icon" href="assets/images/logo.png">
</head>
  <body>
    <header>
      <nav class="navbar">
        <a href="index.php">
          <img src="assets/images/logo.png" class="brand-title" width="50" height="50">
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
    <div class="cart-container">
      <h1>Shopping Cart</h1>
      <table class="cart-table">
        <thead>
          <tr>
            <th class="text-left">Item</th>
            <th>Price</th>
            <th>Quantity</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
try {
  $stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, m.name, m.price, m.image_path 
                         FROM cart c 
                         JOIN menu_items m ON c.menu_item_id = m.id 
                         WHERE c.user_id = :user_id");
  $stmt->execute([':user_id' => $user_id]);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

if (!empty($results)) {
  foreach ($results as $row) {
      $item_total = $row['price'] * $row['quantity'];
      $cart_total += $item_total;
      
      echo "<tr>
              <td>
                <div class='cart-item-info'>
                   <img src='" . htmlspecialchars($row['image_path']) . "' alt='" . htmlspecialchars($row['name']) . "'>
                   <span>" . htmlspecialchars($row['name']) . "</span>
                </div>
              </td>
              <td class='price-cell'>$" . number_format($row['price'], 2) . "</td>
              <td>
                <div class='quantity-controls'>
                  <form action='includes/updatecart.php' method='post'>
                      <input type='hidden' name='id' value='" . $row['cart_id'] . "'>
                      <input type='hidden' name='action' value='decrease'>
                      <button type='submit' class='qty-btn'>-</button>
                  </form>
                  <span class='qty-val'>" . $row['quantity'] . "</span>
                  <form action='includes/updatecart.php' method='post'>
                      <input type='hidden' name='id' value='" . $row['cart_id'] . "'>
                      <input type='hidden' name='action' value='increase'>
                      <button type='submit' class='qty-btn'>+</button>
                  </form>
                </div>
              </td>
              <td class='text-right'>
                <form action='includes/removeitem.php' method='post' onsubmit='return confirmRemove();'>
                  <input type='hidden' name='id' value='" . $row['cart_id'] . "'>
                  <button type='submit' class='remove-btn'>Remove</button>
                </form>
              </td>
            </tr>";
  }
  echo "</tbody>
        <tfoot>
          <tr>
            <td colspan='2' class='text-right cart-total-label'>Total:</td>
            <td colspan='2' class='text-right cart-total-value'>$" . number_format($cart_total, 2) . "</td>
          </tr>
        </tfoot>";
} else {
  echo "<tr><td colspan='4' class='empty-cart-msg'>Your cart is currently empty.</td></tr></tbody>";
}
          ?>
      </table>

      
        

      </div>
      <div class="checkout-container">
        <a href="checkout.php" class="checkout-btn">Checkout</a>
       </div>
    </div>
    <script>
function confirmRemove() {
  return confirm('Are you sure you want to remove this item?');
}
</script>

    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
  </body>
</html>





