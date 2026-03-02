<?php
  session_start();
  require 'connectdb.php';
  
  if(!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
      header('Location: ../pleaselogin.php');
      exit;
  }

  $id = $_POST['id'];
  $user_id = $_SESSION['user_id'];

  try {
    // Ensure we only delete from this user's cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $id, ':user_id' => $user_id]);
  } catch(PDOException $e) {
    // Silently fail or log error
  }

  header('Location: ../cart.php');
  exit;
?>


