<?php
session_start();
include("../connection.php");

header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';

if ($user_id <= 0) {
    echo json_encode(['count' => 0, 'error' => 'Invalid user_id']);
    exit;
}

try {
    if ($user_type === 'doctor') {
        // Count ALL unread messages from ALL patients to this doctor
        $query = "SELECT COUNT(*) as total 
                  FROM messages 
                  WHERE receiver_id = ? 
                  AND receiver_type = 'doctor' 
                  AND is_read = 0";
    } else if ($user_type === 'patient') {
        // Count ALL unread messages from doctor to this patient
        $query = "SELECT COUNT(*) as total 
                  FROM messages 
                  WHERE receiver_id = ? 
                  AND receiver_type = 'patient' 
                  AND is_read = 0";
    } else {
        echo json_encode(['count' => 0, 'error' => 'Invalid user_type']);
        exit;
    }
    
    $stmt = $database->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $database->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    $count = (int)$row['total'];
    
    echo json_encode([
        'count' => $count, 
        'success' => true,
        'debug' => [
            'user_id' => $user_id,
            'user_type' => $user_type,
            'query_executed' => true
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'count' => 0, 
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>