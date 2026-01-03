<?php
session_start();
include("../connection.php");

header('Content-Type: application/json');

$sender = isset($_POST['sender']) ? intval($_POST['sender']) : 0;
$receiver = isset($_POST['receiver']) ? intval($_POST['receiver']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$sender_type = isset($_POST['sender_type']) ? $_POST['sender_type'] : '';
$receiver_type = isset($_POST['receiver_type']) ? $_POST['receiver_type'] : '';

if ($sender <= 0 || $receiver <= 0 || empty($message) || empty($sender_type) || empty($receiver_type)) {
    echo json_encode([
        'success' => false, 
        'message' => 'بيانات غير صحيحة'
    ]);
    exit();
}

$valid_types = ['patient', 'doctor', 'admin'];
if (!in_array($sender_type, $valid_types) || !in_array($receiver_type, $valid_types)) {
    echo json_encode([
        'success' => false, 
        'message' => 'نوع المستخدم غير صحيح'
    ]);
    exit();
}

// استخدام created_at بدلاً من timestamp
$query = "INSERT INTO messages (sender_id, receiver_id, message, sender_type, receiver_type, created_at, is_read) 
          VALUES (?, ?, ?, ?, ?, NOW(), 0)";

$stmt = $database->prepare($query);

if (!$stmt) {
    echo json_encode([
        'success' => false, 
        'message' => 'خطأ في تجهيز الاستعلام'
    ]);
    exit();
}

$stmt->bind_param("iisss", $sender, $receiver, $message, $sender_type, $receiver_type);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال الرسالة بنجاح'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'فشل إرسال الرسالة'
    ]);
}

$stmt->close();
?>