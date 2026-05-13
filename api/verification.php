<?php
header('Content-Type: application/json');
require_once 'db_config.php';

$action = $_GET['action'] ?? '';

if ($action === 'verify_license') {
    // Simulated AI Document Analysis
    // In a real scenario, we would use Tesseract or an external AI API here
    sleep(2); // Simulate processing time
    
    echo json_encode([
        'success' => true,
        'message' => 'AI Document Analysis Complete.',
        'data' => [
            'license_no' => 'NGO-'.rand(1000, 9999),
            'expiry_date' => '2028-12-31',
            'authenticity' => '98.7%',
            'status' => 'Verified'
        ]
    ]);
}
?>
