<?php
    // Start the session
    session_start();

    // Connect to the database
    require 'includes/connectdb.php';

    // Check if the form has been submitted
    if (isset($_POST['login'])) {
        $sql = "SELECT id, username, password, role FROM users WHERE email = :email";

        $email = trim($_POST['Email']);
        $password = $_POST['Password'];

        $statement = $pdo->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($password, $result['password'])) {
            // Password is correct, start session
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['user_role'] = $result['role'];
            $_SESSION['user'] = $result; // Backwards compatible with existing checks in navbar

            header('Location: index.php');
            exit;
        } else {
            $error_msg = "Invalid email or password.";
        }
    }
?>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Chickanji</title>
    <script src="assets/js/script.js" defer></script>
    <link rel="icon" href="assets/images/logo.png">
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="assets/css/loginstyle.css?v=<?php echo time(); ?>">
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
            <!-- Already on login page, so just show Sign Up -->
            <a href="signup.php" class="signup-btn">Sign Up</a>
        </div>
</nav>
<main class="main-content">
    <div class="login-container">
        <?php if(!empty($error_msg)): ?>
            <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error_msg); ?></p>
        <?php endif; ?>
        <form method="post">
            <h1>Login</h1>
            <input type="text" placeholder="Email" name="Email" required>
            <input type="password" placeholder="Password" name="Password" required>
            <input type="submit" value="Login" name="login">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
    </div>
</main>
    <footer>
        <div class="footer-container">
            <p>Copyright © Chickanji. Made by Mohammad Ayoub. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>





