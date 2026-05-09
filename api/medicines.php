<?php
header('Content-Type: application/json');
require_once 'db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

if ($action === 'donate') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'];
    $desc = $data['description'];
    $expiry = $data['expiry_date'];
    $qty = $data['quantity'];

    try {
        $stmt = $pdo->prepare("INSERT INTO medicines (name, description, expiry_date, quantity, donor_id, status) VALUES (?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$name, $desc, $expiry, $qty, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Medicine donated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Donation failed: ' . $e->getMessage()]);
    }
}

if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE status = 'Available'");
    $stmt->execute();
    $medicines = $stmt->fetchAll();
    echo json_encode(['success' => true, 'medicines' => $medicines]);
}

if ($action === 'my_donations') {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE donor_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true, 'medicines' => $stmt->fetchAll()]);
}
?>
