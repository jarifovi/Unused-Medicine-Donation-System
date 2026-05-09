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
        $med_id = $pdo->lastInsertId();

        // Check Wishlist
        $stmt = $pdo->prepare("SELECT ngo_id FROM wishlist WHERE medicine_name LIKE ?");
        $stmt->execute(['%' . $data['name'] . '%']);
        while ($row = $stmt->fetch()) {
            notify($pdo, $row['ngo_id'], "A medicine on your wishlist is now available: " . $data['name']);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) { $pdo->rollBack(); echo json_encode(['success' => false]); }
}

if ($action === 'approve_request' && $user_type === 'Admin') {
    $data = json_decode(file_get_contents('php://input'), true);
    $code = substr(str_shuffle("0123456789ABCDEF"), 0, 6); // Collection Code
    try {
        $stmt = $pdo->prepare("UPDATE requests SET status = 'Approved', collection_code = ? WHERE id = ?");
        $stmt->execute([$code, $data['request_id']]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) { echo json_encode(['success' => false]); }
}

if ($action === 'stats') {
    $total_donations = $pdo->query("SELECT COUNT(*) FROM medicines")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $completed = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'Collected'")->fetchColumn();
    
    // Stats by category
    $cats = $pdo->query("SELECT category, COUNT(*) as count FROM medicines GROUP BY category")->fetchAll();
    
    echo json_encode(['success' => true, 'stats' => [
        'total' => $total_donations,
        'users' => $total_users,
        'completed' => $completed,
        'categories' => $cats
    ]]);
}

if ($action === 'add_wishlist') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO wishlist (ngo_id, medicine_name) VALUES (?, ?)");
    $stmt->execute([$user_id, $data['name']]);
    echo json_encode(['success' => true]);
}

if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT m.*, u.name as donor_name FROM medicines m JOIN users u ON m.donor_id = u.id WHERE m.status = 'Available'");
    $stmt->execute();
    echo json_encode(['success' => true, 'medicines' => $stmt->fetchAll()]);
}

if ($action === 'notifications') {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true, 'notifications' => $stmt->fetchAll()]);
}
?>
