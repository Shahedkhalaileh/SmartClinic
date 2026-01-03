<?php
session_start();
include("../connection.php");

// التحقق من صلاحيات الأدمن
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'a') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json; charset=utf-8');

// إرسال رسالة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send') {
    $type = $database->real_escape_string($_POST['type']); // 'doctor' or 'patient'
    $id = intval($_POST['id']);
    $message = trim($_POST['message']);
    
    if (empty($message)) {
        exit(json_encode(['success' => false, 'error' => 'Empty message']));
    }
    
    // تحديد patient_id إذا كان المستقبل مريض
    $patient_id = ($type === 'patient') ? $id : null;
    
    $stmt = $database->prepare("INSERT INTO admin_messages (receiver_type, receiver_id, patient_id, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("siis", $type, $id, $patient_id, $message);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
    exit;
}

// تحميل الرسائل
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'load') {
    $type = $database->real_escape_string($_GET['type']);
    $id = intval($_GET['id']);
    
    $stmt = $database->prepare("SELECT message, DATE_FORMAT(sent_at, '%Y-%m-%d %H:%i') as sent_at 
                                FROM admin_messages 
                                WHERE receiver_type = ? AND receiver_id = ? 
                                ORDER BY sent_at ASC");
    $stmt->bind_param("si", $type, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    echo json_encode($messages);
    $stmt->close();
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
?>