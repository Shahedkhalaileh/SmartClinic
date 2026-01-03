<?php
session_start();
include("../connection.php");

header('Content-Type: application/json');

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'd') {
    echo json_encode(['has_unread' => false, 'unread_count' => 0]);
    exit();
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

if ($user_id <= 0 || $patient_id <= 0) {
    echo json_encode(['has_unread' => false, 'unread_count' => 0]);
    exit();
}

try {
    // Count unread messages from this specific patient to this doctor
    $query = "SELECT COUNT(*) as total 
              FROM messages 
              WHERE sender_id = ? 
              AND receiver_id = ? 
              AND sender_type = 'patient' 
              AND receiver_type = 'doctor' 
              AND is_read = 0";
    
    $stmt = $database->prepare($query);
    $stmt->bind_param("ii", $patient_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    $count = (int)$row['total'];
    
    echo json_encode([
        'has_unread' => $count > 0, 
        'unread_count' => $count,  // Changed from 'count' to 'unread_count'
        'success' => true
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'has_unread' => false, 
        'unread_count' => 0,  // Changed from 'count' to 'unread_count'
        'error' => $e->getMessage()
    ]);
}
?>