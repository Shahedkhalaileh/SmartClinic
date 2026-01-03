<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

include("../connection.php");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$get_count = isset($_GET['get_count']) ? intval($_GET['get_count']) : 0;

if ($user_id <= 0 || $doctor_id <= 0) {
    echo json_encode(['has_unread' => false, 'count' => 0]);
    exit;
}

try {
    $stmt = $database->prepare("
        SELECT COUNT(*) as unread 
        FROM messages 
        WHERE sender_id = ? 
        AND sender_type = 'doctor'
        AND receiver_id = ? 
        AND receiver_type = 'patient'
        AND is_read = 0
    ");

    $stmt->bind_param("ii", $doctor_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = intval($row['unread']);

    echo json_encode([
        'has_unread' => ($count > 0),
        'count' => $count
    ]);

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in check_unread_by_doctor.php: " . $e->getMessage());
    echo json_encode(['has_unread' => false, 'count' => 0, 'error' => true]);
}
?>