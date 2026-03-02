<?php
require '../includes/connectdb.php';

$email = 'admin@chickanji.com';
$password = 'Admin@123';
$hashed = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "User found: {$user['username']} | Role: {$user['role']}\n";
        if ($user['role'] !== 'admin') {
            $update = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
            $update->execute([':id' => $user['id']]);
            echo "Updated role to 'admin'.\n";
        }
    } else {
        echo "User not found. Creating 'admin' user...\n";
        $insert = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES ('admin', :email, :password, 'admin')");
        $insert->execute([':email' => $email, ':password' => $hashed]);
        echo "Created 'admin' user with role 'admin' and password 'Admin@123'.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
