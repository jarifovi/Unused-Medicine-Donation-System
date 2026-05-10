<?php
require_once 'db_config.php';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, type, is_verified) VALUES ('System Admin', 'admin@meddonate.com', ?, 'Admin', 1) ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute([$hash, $hash]);
    echo "<h1>Admin Account Ready!</h1><p>Email: admin@meddonate.com<br>Password: admin123</p><a href='../index.html'>Go to Login</a>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
