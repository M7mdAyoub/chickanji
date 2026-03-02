<?php
session_start();
require '../includes/connectdb.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Check if user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$message = "";

// Handle status updates for orders
if (isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $new_status, ':id' => $order_id]);
        $message = "Order #$order_id status updated to $new_status.";
    } catch(PDOException $e) {
        $message = "Error updating order: " . $e->getMessage();
    }
}

// Handle Menu Item Actions
if (isset($_POST['add_menu_item'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    
    // Image Upload
    $target_dir = "../assets/images/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, image_path, is_active) VALUES (:name, :desc, :price, :path, 1)");
            $stmt->execute([':name' => $name, ':desc' => $desc, ':price' => $price, ':path' => $target_file]);
            $message = "New item '$name' added successfully!";
        } catch(PDOException $e) {
            $message = "Error adding item: " . $e->getMessage();
        }
    } else {
        $message = "Error uploading image.";
    }
}

if (isset($_POST['edit_menu_item'])) {
    $id = $_POST['item_id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    
    try {
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "../assets/images/";
            $image_name = basename($_FILES["image"]["name"]);
            $target_file = $target_dir . time() . "_" . $image_name;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            
            $stmt = $pdo->prepare("UPDATE menu_items SET name = :name, description = :desc, price = :price, image_path = :path WHERE id = :id");
            $stmt->execute([':name' => $name, ':desc' => $desc, ':price' => $price, ':path' => $target_file, ':id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE menu_items SET name = :name, description = :desc, price = :price WHERE id = :id");
            $stmt->execute([':name' => $name, ':desc' => $desc, ':price' => $price, ':id' => $id]);
        }
        $message = "Item '$name' updated successfully!";
    } catch(PDOException $e) {
        $message = "Error updating item: " . $e->getMessage();
    }
}

if (isset($_POST['toggle_status'])) {
    $id = $_POST['item_id'];
    $current = $_POST['current_status'];
    $new = $current ? 0 : 1;
    
    try {
        $stmt = $pdo->prepare("UPDATE menu_items SET is_active = :new WHERE id = :id");
        $stmt->execute([':new' => $new, ':id' => $id]);
        $message = "Menu item status updated.";
    } catch(PDOException $e) {
        $message = "Error toggling status.";
    }
}

// Fetch Metrics
try {
    // Total Revenue
    $rev_stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $total_revenue = $rev_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total Orders
    $orders_count_stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
    $total_orders = $orders_count_stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Active Customers (Users who have placed at least one order)
    $cust_stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as count FROM orders");
    $total_customers = $cust_stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

} catch (PDOException $e) {
    die("Error fetching dashboard metrics.");
}

// Fetch all orders with customer details
try {
    $order_stmt = $pdo->query("SELECT o.id, o.total_amount, o.status, o.created_at, u.username, u.email 
                               FROM orders o 
                               JOIN users u ON o.user_id = u.id 
                               ORDER BY o.created_at DESC");
    $orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching orders.");
}

// Fetch all menu items
try {
    $menu_stmt = $pdo->query("SELECT * FROM menu_items ORDER BY id ASC");
    $menu_items = $menu_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching menu.");
}

// Simple Tab Logic
$tab = $_GET['tab'] ?? 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Chickanji</title>
    <link rel="icon" href="../assets/images/logo.png">
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        :root {
            --sidebar-width: 260px;
        }
        
        body {
            background-color: #0b0d11;
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            overflow-x: hidden;
            padding-top: 0;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: #0f1115;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.5rem;
            z-index: 1001;
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 3rem;
            text-decoration: none;
        }
        
        .sidebar-logo h2 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 0;
        }
        
        .nav-menu {
            list-style: none;
            flex-grow: 1;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 184, 0, 0.1);
            color: var(--primary);
        }
        
        .logout-section {
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 2rem 3rem;
            width: calc(100% - var(--sidebar-width));
        }
        
        .header-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background-color: var(--bg-glass);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            backdrop-filter: blur(10px);
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-light);
            font-family: 'Outfit', sans-serif;
        }
        
        .stat-card.revenue .stat-value { color: #4BB543; }
        .stat-card.orders .stat-value { color: var(--primary); }
        .stat-card.customers .stat-value { color: #3498db; }
        
        .section-box {
            background-color: var(--bg-glass);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-header h2 {
            margin-bottom: 0;
            font-size: 1.5rem;
        }
        
        /* Table Styles */
        .dashboard-table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            text-align: left;
            padding: 1rem;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        td {
            padding: 1.2rem 1rem;
            color: var(--text-light);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            font-size: 0.95rem;
        }
        
        tr:last-child td { border-bottom: none; }
        
        /* Status Badges */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .badge-pending {
            background-color: rgba(255, 184, 0, 0.1);
            color: var(--primary);
        }
        
        .badge-completed {
            background-color: rgba(75, 181, 67, 0.1);
            color: #4BB543;
        }
        
        .badge-cancelled {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        
        .status-select {
            padding: 6px 10px;
            background: #1a1d23;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            font-size: 0.85rem;
            outline: none;
        }
        
        .btn-update {
            background: var(--primary);
            color: #000;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .btn-update:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .success-msg {
            background-color: rgba(75, 181, 67, 0.1);
            color: #4BB543;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px solid rgba(75, 181, 67, 0.2);
            text-align: center;
        }

        /* Forms */
        .mgmt-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            background: rgba(255,255,255,0.02);
            padding: 1.5rem;
            border-radius: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .form-group input, .form-group textarea {
            background: #1a1d23;
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            padding: 10px;
            border-radius: 8px;
            outline: none;
        }

        .btn-toggle {
            background: rgba(255,255,255,0.05);
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.75rem;
        }

        .btn-toggle.active { color: #4BB543; border-color: #4BB543; }
        .btn-toggle.inactive { color: #e74c3c; border-color: #e74c3c; }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-container {
            background: #16191d;
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-overlay.active .modal-container { transform: translateY(0); }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.5rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.3s;
        }

        .modal-close:hover { color: var(--primary); }

        .modal-header { margin-bottom: 1.5rem; }
        .modal-header h2 { font-size: 1.5rem; color: var(--primary); margin-bottom: 5px; }
        .modal-header p { font-size: 0.9rem; color: var(--text-muted); }

        .search-container {
            margin-bottom: 2rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            padding: 12px 20px 12px 45px;
            border-radius: 12px;
            outline: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary);
            background: rgba(255,255,255,0.08);
            box-shadow: 0 0 0 3px rgba(255, 184, 0, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .btn-add-main {
            background: var(--primary);
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-add-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 184, 0, 0.2);
        }

        @media (max-width: 992px) {
            :root { --sidebar-width: 80px; }
            .sidebar-logo h2, .nav-link span { display: none; }
            .sidebar { padding: 2rem 1rem; align-items: center; }
            .nav-link { justify-content: center; padding: 12px; }
        }
    </style>
</head>
<body>
    <!-- Dashboard Sidebar -->
    <aside class="sidebar">
        <a href="../index.php" class="sidebar-logo">
            <img src="../assets/images/logo.png" width="40" height="40" alt="Logo">
            <h2>Chickanji</h2>
        </a>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php?tab=overview" class="nav-link <?php if($tab=='overview') echo 'active'; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span>Overview</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?tab=menu" class="nav-link <?php if($tab=='menu') echo 'active'; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
                    <span>Manage Menu</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../menu.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    <span>View Store</span>
                </a>
            </li>
        </ul>
        
        <div class="logout-section">
            <a href="../logout.php" class="nav-link" style="color: #e74c3c;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <header style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Dashboard</h1>
                <p style="color: var(--text-muted);"><?php echo $tab == 'overview' ? 'Overview' : 'Menu Management'; ?> | Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            </div>
            <div style="color: var(--text-muted); font-size: 0.9rem;">
                <?php echo date('l, F jS Y'); ?>
            </div>
        </header>

        <?php if($message): ?>
            <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if($tab == 'overview'): ?>
        <!-- Metric Cards -->
        <div class="header-stats">
            <div class="stat-card revenue">
                <span class="stat-label">Total Revenue</span>
                <span class="stat-value">$<?php echo number_format($total_revenue, 2); ?></span>
            </div>
            <div class="stat-card orders">
                <span class="stat-label">Total Orders</span>
                <span class="stat-value"><?php echo $total_orders; ?></span>
            </div>
            <div class="stat-card customers">
                <span class="stat-label">Active Customers</span>
                <span class="stat-value"><?php echo $total_customers; ?></span>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="section-box">
            <div class="section-header">
                <h2>Recent Orders</h2>
            </div>
            <div class="dashboard-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td><span style="color: var(--primary); font-weight: 600;">#<?php echo $o['id']; ?></span></td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600;"><?php echo htmlspecialchars($o['username']); ?></span>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($o['email']); ?></span>
                                </div>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($o['created_at'])); ?></td>
                            <td><span style="font-weight: 600;">$<?php echo number_format($o['total_amount'], 2); ?></span></td>
                            <td>
                                <span class="badge badge-<?php echo $o['status']; ?>">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" style="display:flex; gap:10px; align-items:center;">
                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                    <select name="new_status" class="status-select">
                                        <option value="pending" <?php if($o['status']=='pending') echo 'selected'; ?>>Pending</option>
                                        <option value="completed" <?php if($o['status']=='completed') echo 'selected'; ?>>Completed</option>
                                        <option value="cancelled" <?php if($o['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_order_status" class="btn-update">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($orders)): ?>
                            <tr><td colspan="6" style="text-align:center; padding: 2rem;">No orders found yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Menu Overview (Quick View) -->
        <div class="section-box">
            <div class="section-header">
                <h2>Menu Items Overview</h2>
            </div>
            <div class="dashboard-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_slice($menu_items, 0, 5) as $m): ?>
                        <tr>
                            <td><?php echo $m['id']; ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <img src="../<?php echo htmlspecialchars($m['image_path']); ?>" width="40" height="40" style="border-radius: 8px; object-fit: cover;">
                                    <span style="font-weight: 600;"><?php echo htmlspecialchars($m['name']); ?></span>
                                </div>
                            </td>
                            <td><span style="color: var(--primary); font-weight: 600;">$<?php echo number_format($m['price'], 2); ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($m['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php elseif($tab == 'menu'): ?>
        
        <div class="section-header" style="margin-bottom: 2rem;">
            <h2>Menu Management</h2>
            <button onclick="openAddModal()" class="btn-add-main">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add New Item
            </button>
        </div>

        <!-- Search Filter -->
        <div class="search-container">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="menuSearch" class="search-input" placeholder="Search by name or description..." onkeyup="filterMenu()">
        </div>

        <!-- Existing Items List -->
        <div class="section-box">
            <div class="dashboard-table-container">
                <table id="menuTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Item Details</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($menu_items as $m): ?>
                        <tr class="menu-row">
                            <td>
                                <img src="../<?php echo htmlspecialchars($m['image_path']); ?>" width="60" height="60" style="border-radius: 10px; object-fit: cover;">
                            </td>
                            <td class="item-info">
                                <div style="display: flex; flex-direction: column;">
                                    <span class="item-name" style="font-weight: 700; font-size: 1.1rem;"><?php echo htmlspecialchars($m['name']); ?></span>
                                    <span class="item-desc" style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($m['description']); ?></span>
                                </div>
                            </td>
                            <td><span style="color: var(--primary); font-weight: 700;">$<?php echo number_format($m['price'], 2); ?></span></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="item_id" value="<?php echo $m['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $m['is_active']; ?>">
                                    <button type="submit" name="toggle_status" class="btn-toggle <?php echo $m['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $m['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <button onclick='openEditModal(<?php echo json_encode($m); ?>)' class="btn-update" style="background: rgba(255,255,255,0.1); color: white;">Edit</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Item Modal -->
        <div id="addModal" class="modal-overlay">
            <div class="modal-container">
                <span class="modal-close" onclick="closeModal('addModal')">&times;</span>
                <div class="modal-header">
                    <h2>Add Menu Item</h2>
                    <p>Create a new product for your menu.</p>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label>Item Name</label>
                        <input type="text" name="name" placeholder="e.g. Spicy Chicken" required>
                    </div>
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" placeholder="9.99" required>
                    </div>
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label>Description</label>
                        <textarea name="description" rows="3" placeholder="Brief description..."></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label>Image</label>
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_menu_item" class="btn-update" style="width:100%; padding:12px;">Add to Menu</button>
                </form>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div id="editModal" class="modal-overlay">
            <div class="modal-container">
                <span class="modal-close" onclick="closeModal('editModal')">&times;</span>
                <div class="modal-header">
                    <h2>Edit Menu Item</h2>
                    <p>Update the details of your menu item.</p>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="item_id" id="edit_item_id">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label>Item Name</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" id="edit_price" required>
                    </div>
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label>New Image (optional)</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <button type="submit" name="edit_menu_item" class="btn-update" style="width:100%; padding:12px;">Save Changes</button>
                </form>
            </div>
        </div>

        <script>
            function filterMenu() {
                var input = document.getElementById('menuSearch');
                var filter = input.value.toLowerCase();
                var table = document.getElementById('menuTable');
                var rows = table.getElementsByClassName('menu-row');

                for (var i = 0; i < rows.length; i++) {
                    var name = rows[i].querySelector('.item-name').innerText.toLowerCase();
                    var desc = rows[i].querySelector('.item-desc').innerText.toLowerCase();
                    if (name.includes(filter) || desc.includes(filter)) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }

            function openAddModal() {
                document.getElementById('addModal').classList.add('active');
            }

            function openEditModal(item) {
                document.getElementById('edit_item_id').value = item.id;
                document.getElementById('edit_name').value = item.name;
                document.getElementById('edit_price').value = item.price;
                document.getElementById('edit_description').value = item.description;
                document.getElementById('editModal').classList.add('active');
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.remove('active');
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target.classList.contains('modal-overlay')) {
                    event.target.classList.remove('active');
                }
            }
        </script>
        <?php endif; ?>
    </main>
</body>
</html>





