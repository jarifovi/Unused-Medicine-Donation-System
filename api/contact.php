<?php
header('Content-Type: application/json');
require_once 'db_config.php';

$action = $_GET['action'] ?? '';

if ($action === 'send') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'];
    $email = $data['email'];
    $subject = $data['subject'] ?? 'General Inquiry';
    $message = $data['message'];

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to send message: ' . $e->getMessage()]);
    }
}

if ($action === 'list') {
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY created_at DESC");
    $stmt->execute();
    echo json_encode(['success' => true, 'messages' => $stmt->fetchAll()]);
}
?>
