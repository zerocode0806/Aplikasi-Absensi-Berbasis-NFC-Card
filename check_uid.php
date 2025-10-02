<?php
header('Content-Type: application/json');
require 'koneksi.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get UID from POST data
$uid = trim($_POST['uid'] ?? '');

if (empty($uid)) {
    echo json_encode([
        'exists' => false,
        'error' => 'UID is required'
    ]);
    exit;
}

try {
    // Prepare statement to check if UID exists
    $stmt = $koneksi->prepare("SELECT id, nama FROM peserta_main WHERE uid = ? LIMIT 1");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // UID exists
        $row = $result->fetch_assoc();
        echo json_encode([
            'exists' => true,
            'id' => $row['id'],
            'nama' => $row['nama'],
            'uid' => $uid
        ]);
    } else {
        // UID does not exist
        echo json_encode([
            'exists' => false,
            'uid' => $uid
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    // Handle database errors
    error_log("Database error in check_uid.php: " . $e->getMessage());
    echo json_encode([
        'exists' => false,
        'error' => 'Database error occurred'
    ]);
}

$koneksi->close();
?>