<?php
header('Content-Type: application/json');
include("../connection.php");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';

if ($user_id && $user_type === 'patient') {
    $query = "SELECT COUNT(*) as count FROM messages 
              WHERE receiver_id = ? AND receiver_type = 'doctor' AND is_read = 0";
    
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode(['status' => 'ok', 'count' => $row['count']]);
} else {
    echo json_encode(['status' => 'error', 'count' => 0]);
}
?>