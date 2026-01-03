<?php
header('Content-Type: application/json');
include("../connection.php");

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

if ($pid > 0) {
    $stmt = $database->prepare("SELECT COUNT(*) as cnt FROM admin_messages 
                                WHERE receiver_type = 'patient' 
                                AND receiver_id = ? 
                                AND is_read = 0");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode(['status' => 'ok', 'admin_unread' => $row['cnt']]);
} else {
    echo json_encode(['status' => 'error', 'admin_unread' => 0]);
}
?>