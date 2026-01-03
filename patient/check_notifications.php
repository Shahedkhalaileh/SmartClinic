<?php
session_start();
include("../connection.php");

header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';

if ($user_id <= 0 || empty($user_type)) {
    echo json_encode(['count' => 0]);
    exit();
}

// For patients: count ALL unread messages FROM ALL doctors
if ($user_type === 'patient') {
    $query = "SELECT COUNT(*) as count 
              FROM messages 
              WHERE receiver_id = ? 
              AND receiver_type = 'patient'
              AND sender_type = 'doctor'
              AND is_read = 0";
    
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $user_id);
    
} elseif ($user_type === 'doctor') {
    // For doctors: count ALL unread messages FROM ALL patients
    $query = "SELECT COUNT(*) as count 
              FROM messages 
              WHERE receiver_id = ? 
              AND receiver_type = 'doctor'
              AND sender_type = 'patient'
              AND is_read = 0";
    
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $user_id);
    
} else {
    echo json_encode(['count' => 0]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode([
    'count' => intval($row['count'])
]);
?>