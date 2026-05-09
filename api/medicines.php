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
$user_type = $_SESSION['user_type'];

function notify($pdo, $uid, $msg) {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$uid, $msg]);
}

if ($action === 'donate') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO medicines (name, category, description, expiry_date, quantity, donor_id, status) VALUES (?, ?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$data['name'], $data['category'], $data['description'], $data['expiry_date'], $data['quantity'], $user_id]);
        
        $stmt = $pdo->prepare("UPDATE users SET donations_made = donations_made + 1 WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Donation recorded']);
    } catch (Exception $e) { $pdo->rollBack(); echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
}

if ($action === 'request') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO requests (medicine_id, ngo_id, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$data['medicine_id'], $user_id]);
        
        $stmt = $pdo->prepare("UPDATE medicines SET status = 'Requested' WHERE id = ?");
        $stmt->execute([$data['medicine_id']]);
        
        // Notify Donor
        $medStmt = $pdo->prepare("SELECT donor_id, name FROM medicines WHERE id = ?");
        $medStmt->execute([$data['medicine_id']]);
        $med = $medStmt->fetch();
        notify($pdo, $med['donor_id'], "An NGO has requested your donation: " . $med['name']);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) { $pdo->rollBack(); echo json_encode(['success' => false]); }
}

if ($action === 'approve_request' && $user_type === 'Admin') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['request_id']]);

        $reqStmt = $pdo->prepare("SELECT medicine_id, ngo_id FROM requests WHERE id = ?");
        $reqStmt->execute([$data['request_id']]);
        $req = $reqStmt->fetch();

        if ($data['status'] === 'Approved') {
            $stmt = $pdo->prepare("UPDATE medicines SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$req['medicine_id']]);
            $stmt = $pdo->prepare("UPDATE users SET requests_completed = requests_completed + 1 WHERE id = ?");
            $stmt->execute([$req['ngo_id']]);
            notify($pdo, $req['ngo_id'], "Your request has been approved!");
        } else {
            $stmt = $pdo->prepare("UPDATE medicines SET status = 'Available' WHERE id = ?");
            $stmt->execute([$req['medicine_id']]);
            notify($pdo, $req['ngo_id'], "Your request was rejected.");
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) { $pdo->rollBack(); echo json_encode(['success' => false]); }
}

if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT m.*, u.name as donor_name, u.is_verified as donor_verified FROM medicines m JOIN users u ON m.donor_id = u.id WHERE m.status = 'Available'");
    $stmt->execute();
    echo json_encode(['success' => true, 'medicines' => $stmt->fetchAll()]);
}

if ($action === 'notifications') {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true, 'notifications' => $stmt->fetchAll()]);
}

if ($action === 'leaderboard') {
    $donors = $pdo->query("SELECT name, donations_made FROM users WHERE type = 'Individual' ORDER BY donations_made DESC LIMIT 5")->fetchAll();
    $ngos = $pdo->query("SELECT name, requests_completed FROM users WHERE type = 'NGO' ORDER BY requests_completed DESC LIMIT 5")->fetchAll();
    echo json_encode(['success' => true, 'donors' => $donors, 'ngos' => $ngos]);
}
?>
