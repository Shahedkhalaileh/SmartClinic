<?php
session_start();
include("../connection.php");

header('Content-Type: application/json');

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

if ($pid <= 0) {
    echo json_encode(['status' => 'error', 'admin_unread' => 0]);
    exit();
}

// Count unread admin messages for this patient
$query = "SELECT COUNT(*) as count 
          FROM admin_messages 
          WHERE receiver_id = ? 
          AND receiver_type = 'patient'
          AND is_read = 0";

$stmt = $database->prepare($query);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode([
    'status' => 'ok',
    'admin_unread' => intval($row['count'])
]);
?>