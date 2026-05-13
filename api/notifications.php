<?php
header('Content-Type: application/json');
require_once 'db_config.php';

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    // Simulated notifications for the demo
    echo json_encode([
        'success' => true,
        'notifications' => [
            ['id' => 1, 'message' => 'Your donation of Amoxicillin was accepted by City Hope NGO.', 'time' => '2 hours ago', 'type' => 'success'],
            ['id' => 2, 'message' => 'Urgent Request: Dialysis kits needed at General Hospital.', 'time' => '5 hours ago', 'type' => 'urgent'],
            ['id' => 3, 'message' => 'Achievement Unlocked: Zero Waste Champion Badge!', 'time' => '1 day ago', 'type' => 'reward']
        ]
    ]);
}
?>
