<?php
session_start();
header('Content-Type: application/json');
require_once 'db_config.php';

$action = $_GET['action'] ?? '';

// Allow public access to stats and leaderboard
$public_actions = ['stats', 'leaderboard'];

if (!in_array($action, $public_actions) && !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? null;

function notify($pdo, $uid, $msg) {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$uid, $msg]);
}

// 1. Donate Medicine
if ($action === 'donate') {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO medicines (name, category, description, expiry_date, quantity, donor_id, status) VALUES (?, ?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([$data['name'], $data['category'], $data['description'], $data['expiry_date'], $data['quantity'], $user_id]);
        
        // Update Impact Points
        $stmt = $pdo->prepare("UPDATE users SET donations_made = donations_made + 1 WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Check Wishlist
        $stmt = $pdo->prepare("SELECT ngo_id FROM wishlist WHERE medicine_name LIKE ?");
        $stmt->execute(['%' . $data['name'] . '%']);
        while ($row = $stmt->fetch()) {
            notify($pdo, $row['ngo_id'], "A medicine on your wishlist is now available: " . $data['name']);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) { 
        $pdo->rollBack(); 
        echo json_encode(['success' => false, 'message' => $e->getMessage()]); 
    }
}

// 2. List Available Medicines
if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT m.*, u.name as donor_name, u.is_verified as donor_verified FROM medicines m JOIN users u ON m.donor_id = u.id WHERE m.status = 'Available'");
    $stmt->execute();
    echo json_encode(['success' => true, 'medicines' => $stmt->fetchAll()]);
}

// 3. Request Medicine (NGO only)
if ($action === 'request') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($user_type !== 'NGO') {
        echo json_encode(['success' => false, 'message' => 'Only NGOs can request']);
        exit;
    }
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO requests (medicine_id, ngo_id, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$data['medicine_id'], $user_id]);
        
        $stmt = $pdo->prepare("UPDATE medicines SET status = 'Requested' WHERE id = ?");
        $stmt->execute([$data['medicine_id']]);
        
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) { 
        $pdo->rollBack(); 
        echo json_encode(['success' => false, 'message' => $e->getMessage()]); 
    }
}

// 4. Admin: List Pending Requests
if ($action === 'admin_requests' && $user_type === 'Admin') {
    $stmt = $pdo->prepare("SELECT r.*, m.name as medicine_name, u.name as ngo_name FROM requests r JOIN medicines m ON r.medicine_id = m.id JOIN users u ON r.ngo_id = u.id WHERE r.status = 'Pending'");
    $stmt->execute();
    echo json_encode(['success' => true, 'requests' => $stmt->fetchAll()]);
}

// 5. Admin: Approve/Reject Request
if ($action === 'approve_request' && $user_type === 'Admin') {
    $data = json_decode(file_get_contents('php://input'), true);
    $status = $data['status']; // Approved or Rejected
    $code = ($status === 'Approved') ? substr(str_shuffle("0123456789ABCDEF"), 0, 6) : null;

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE requests SET status = ?, collection_code = ? WHERE id = ?");
        $stmt->execute([$status, $code, $data['request_id']]);

        $reqStmt = $pdo->prepare("SELECT medicine_id, ngo_id FROM requests WHERE id = ?");
        $reqStmt->execute([$data['request_id']]);
        $req = $reqStmt->fetch();

        $medStatus = ($status === 'Approved') ? 'Approved' : 'Available';
        $stmt = $pdo->prepare("UPDATE medicines SET status = ? WHERE id = ?");
        $stmt->execute([$medStatus, $req['medicine_id']]);

        notify($pdo, $req['ngo_id'], "Your request has been $status. " . ($status === 'Approved' ? "Code: $code" : ""));

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) { $pdo->rollBack(); echo json_encode(['success' => false]); }
}

// 6. Statistics
if ($action === 'stats') {
    $total = $pdo->query("SELECT COUNT(*) FROM medicines")->fetchColumn();
    $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $cats = $pdo->query("SELECT category, COUNT(*) as count FROM medicines GROUP BY category")->fetchAll();
    echo json_encode(['success' => true, 'stats' => ['total' => $total, 'users' => $users, 'categories' => $cats]]);
}

// 7. Notifications
if ($action === 'notifications') {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true, 'notifications' => $stmt->fetchAll()]);
}

// 8. Leaderboard
if ($action === 'leaderboard') {
    $donors = $pdo->query("SELECT name, donations_made FROM users WHERE type = 'Individual' ORDER BY donations_made DESC LIMIT 5")->fetchAll();
    $ngos = $pdo->query("SELECT name, requests_completed FROM users WHERE type = 'NGO' ORDER BY requests_completed DESC LIMIT 5")->fetchAll();
    echo json_encode(['success' => true, 'donors' => $donors, 'ngos' => $ngos]);
}

// 9. Wishlist
if ($action === 'add_wishlist' && $user_type === 'NGO') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO wishlist (ngo_id, medicine_name) VALUES (?, ?)");
    $stmt->execute([$user_id, $data['name']]);
    echo json_encode(['success' => true]);
}
