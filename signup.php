<?php
    session_start();

require 'includes/connectdb.php';

if(isset($_POST['insert'])){
  $Username = trim($_POST['Username']);
  $Email = trim($_POST['Email']);
  $Password = $_POST['Password'];

  // Check if email already exists
  $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
  $check_stmt->execute([':email' => $Email]);
  if ($check_stmt->fetch()) {
      $error_msg = "Email already in use!";
  } else {
      $sql = "INSERT INTO users(username, email, password) values (:Username, :Email, :Password)";
      $statement = $pdo->prepare($sql);
      
      $hashed_password = password_hash($Password, PASSWORD_DEFAULT);
      
      $statement->bindParam(":Username", $Username, PDO::PARAM_STR);
      $statement->bindParam(":Email", $Email, PDO::PARAM_STR);
      $statement->bindParam(":Password", $hashed_password, PDO::PARAM_STR);

      if ($statement->execute()) {
          $_SESSION['user_id'] = $pdo->lastInsertId();
          $_SESSION['user_role'] = 'customer';
          $_SESSION['username'] = $Username;
          header('Location: index.php');
          exit;
      } else {
          $error_msg = "An error occurred during registration.";
      }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="assets/js/script.js" defer></script>
    <title>Sign Up | Chickanji</title>
    <link rel="icon" href="assets/images/logo.png">
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/signupstyle.css?v=<?php echo time(); ?>">
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
        <!-- Already on signup, show Login button -->
        <a href="login.php" class="login-btn">Login</a>
    </div>
  </nav>
<main class="main-content">
  <section>
    <div class="signup-container">
      <h1>Sign Up</h1>
      <?php if(!empty($error_msg)): ?>
          <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error_msg); ?></p>
      <?php endif; ?>
      <form method ="post">
        <input type="text" placeholder="Username" name="Username" required >
        <input type="email" placeholder="Email" name="Email" required>
        <input type="password" placeholder="Password" name="Password" required>
        <button type="submit" name="insert">Sign Up</button>
        <p class="login-message" >Already have an account? <a href="login.php">Login in</a></p>
      </form>
    </div>
  </section>
</main>
    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>




