<?php
header('Content-Type: application/json');
include("../connection.php");

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

if ($pid > 0) {
    $stmt = $database->prepare("SELECT message, is_read, sent_at as created_at 
                                FROM admin_messages 
                                WHERE receiver_type = 'patient' 
                                AND receiver_id = ? 
                                ORDER BY sent_at DESC 
                                LIMIT 50");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'message' => htmlspecialchars($row['message']),
            'is_read' => $row['is_read'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode(['status' => 'ok', 'messages' => $messages]);
} else {
    echo json_encode(['status' => 'error', 'messages' => []]);
}
?>