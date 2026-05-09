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
    $category = $data['category'] ?? 'Tablet';
    $desc = $data['description'];
    $expiry = $data['expiry_date'];
    $qty = $data['quantity'];

    try {
        $stmt = $pdo->prepare("INSERT INTO medicines (name, category, description, expiry_date, quantity, donor_id, status) VALUES (?, ?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$name, $category, $desc, $expiry, $qty, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Medicine donated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Donation failed: ' . $e->getMessage()]);
    }
}

if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT m.*, u.name as donor_name FROM medicines m JOIN users u ON m.donor_id = u.id WHERE m.status = 'Available'");
    $stmt->execute();
    $medicines = $stmt->fetchAll();
    echo json_encode(['success' => true, 'medicines' => $medicines]);
}

if ($action === 'request') {
    $data = json_decode(file_get_contents('php://input'), true);
    $medicine_id = $data['medicine_id'];

    if ($_SESSION['user_type'] !== 'NGO') {
        echo json_encode(['success' => false, 'message' => 'Only NGOs can request medicines']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO requests (medicine_id, ngo_id, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$medicine_id, $user_id]);
        
        $stmt = $pdo->prepare("UPDATE medicines SET status = 'Requested' WHERE id = ?");
        $stmt->execute([$medicine_id]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Request submitted successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Request failed: ' . $e->getMessage()]);
    }
}

if ($action === 'my_donations') {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE donor_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true, 'medicines' => $stmt->fetchAll()]);
}

if ($action === 'my_requests') {
    $stmt = $pdo->prepare("SELECT r.*, m.name as medicine_name, m.category FROM requests r JOIN medicines m ON r.medicine_id = m.id WHERE r.ngo_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true, 'requests' => $stmt->fetchAll()]);
}
?>
