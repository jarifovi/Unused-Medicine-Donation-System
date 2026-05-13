<?php
header('Content-Type: application/json');
require_once 'db_config.php';

$action = $_GET['action'] ?? '';

if ($action === 'stats') {
    // Simulated rewards stats for the demo
    echo json_encode([
        'success' => true,
        'points' => 1250,
        'badges' => ['Seedling', 'Leaf', 'Heart'],
        'milestones' => [
            ['title' => 'Life Saver Lvl 1', 'progress' => 80],
            ['title' => 'Logistics Master', 'progress' => 45],
            ['title' => 'NGO Trusted', 'progress' => 100]
        ]
    ]);
}
?>
