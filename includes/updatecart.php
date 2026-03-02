<?php
session_start();
require 'connectdb.php';

if(!isset($_SESSION['user_id']) || !isset($_POST['id']) || !isset($_POST['action'])) {
    header('Location: ../pleaselogin.php');
    exit;
}

$id = $_POST['id'];
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

try {
    if ($action === 'increase') {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $user_id]);
    } else if ($action === 'decrease') {
        // First check current quantity
        $check = $pdo->prepare("SELECT quantity FROM cart WHERE id = :id AND user_id = :user_id");
        $check->execute([':id' => $id, ':user_id' => $user_id]);
        $row = $check->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['quantity'] > 1) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        } else if ($row && $row['quantity'] == 1) {
            // Remove if it reaches 0
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        }
    }
} catch(PDOException $e) {
    // Silently fail
}

header('Location: ../cart.php');
exit;
?>


