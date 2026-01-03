<?php
header('Content-Type: application/json');
include("../connection.php");

$pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;

if ($pid > 0) {
    $stmt = $database->prepare("UPDATE admin_messages 
                                SET is_read = 1 
                                WHERE receiver_type = 'patient' 
                                AND receiver_id = ? 
                                AND is_read = 0");
    $stmt->bind_param("i", $pid);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error']);
}
?>