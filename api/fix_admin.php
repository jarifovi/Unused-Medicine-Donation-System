<?php
require_once 'db_config.php';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@meddonate.com'");
$stmt->execute([$hash]);
echo "Admin password updated with hash: $hash";
?>
