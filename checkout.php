<?php
session_start();
require 'includes/connectdb.php';

if(!isset($_SESSION['user_id'])){
    header('Location: pleaselogin.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items to create the order
$stmt = $pdo->prepare("SELECT c.id, c.quantity, m.id as menu_item_id, m.price, m.name 
                       FROM cart c 
                       JOIN menu_items m ON c.menu_item_id = m.id 
                       WHERE c.user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    $message = "Your cart is empty. Please add items to your cart before checking out.";
} else {
    try {
        $pdo->beginTransaction();

        // Calculate total
        $total_amount = 0;
        foreach ($cart_items as $item) {
            $total_amount += ($item['price'] * $item['quantity']);
        }

        // Insert into orders
        $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (:user_id, :total_amount, 'pending')");
        $order_stmt->execute([':user_id' => $user_id, ':total_amount' => $total_amount]);
        $order_id = $pdo->lastInsertId();

        // Insert into order_items
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price_at_purchase) VALUES (:order_id, :menu_item_id, :quantity, :price)");
        foreach ($cart_items as $item) {
            $item_stmt->execute([
                ':order_id' => $order_id,
                ':menu_item_id' => $item['menu_item_id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        // Clear the user's cart
        $clear_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $clear_stmt->execute([':user_id' => $user_id]);

        $pdo->commit();
        $message = "Your order (#" . $order_id . ") for $" . number_format($total_amount, 2) . " has been received successfully!";
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        $message = "An error occurred while processing your order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Status | Chickanji</title>
    <script src="assets/js/script.js" defer></script>
    <link rel="icon" href="assets/images/logo.png">
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .checkout-container {
            max-width: 600px;
            margin: 150px auto 100px;
            padding: 3rem;
            background-color: var(--bg-glass);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }
        .checkout-container h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        .checkout-container p {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            color: var(--text-light);
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            background-color: var(--primary);
            color: #000;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-home:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 184, 0, 0.2);
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
    <div class="checkout-container">
        <h1>Order Confirmation</h1>
        <div class="success-icon" style="font-size: 4rem; color: #4BB543; margin-bottom: 1rem;">✔</div>
        <p><?php echo htmlspecialchars($message); ?></p>
        
        <?php if(isset($order_id) && !empty($cart_items)): ?>
            <div class="order-summary" style="text-align: left; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
                <h3 style="color: var(--primary); margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">Order Details</h3>
                <ul style="list-style: none; padding: 0;">
                    <?php foreach($cart_items as $item): ?>
                        <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-light);">
                            <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 0.5rem; border-top: 2px solid var(--primary); font-weight: bold; font-size: 1.2rem; color: var(--primary);">
                    <span>Total</span>
                    <span>$<?php echo number_format($total_amount, 2); ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <a href="index.php" class="btn-home">Return Home</a>
    </div>
    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>

