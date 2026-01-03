<?php
session_start();
include("../connection.php");

$sender = isset($_GET['sender']) ? intval($_GET['sender']) : 0;
$receiver = isset($_GET['receiver']) ? intval($_GET['receiver']) : 0;

if ($sender > 0 && $receiver > 0) {
    // ✅ فقط علّم الرسائل التي أرسلها الدكتور للمريض
    // ✅ لا تعلّم رسائل المريض للدكتور!
    
    $query = "UPDATE messages 
              SET is_read = 1 
              WHERE sender_id = ? 
              AND receiver_id = ? 
              AND sender_type = 'doctor'
              AND receiver_type = 'patient'
              AND is_read = 0";
    
    $stmt = $database->prepare($query);
    $stmt->bind_param("ii", $sender, $receiver);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>