<?php
session_start();

// ✅ منع التخزين المؤقت تماماً
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

include '../connection.php';

// Check if messages table exists
$table_check = $database->query("SHOW TABLES LIKE 'messages'");
if ($table_check->num_rows == 0) {
    $create_table = "CREATE TABLE IF NOT EXISTS `messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `sender_id` int(11) NOT NULL,
        `receiver_id` int(11) NOT NULL,
        `message` text NOT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `is_read` TINYINT(1) DEFAULT 0,
        `sender_type` VARCHAR(10) DEFAULT 'patient',
        `receiver_type` VARCHAR(10) DEFAULT 'doctor',
        PRIMARY KEY (`id`),
        KEY `sender_id` (`sender_id`),
        KEY `receiver_id` (`receiver_id`),
        KEY `is_read` (`is_read`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $database->query($create_table);
}

$sender = isset($_POST['sender']) ? intval($_POST['sender']) : 0;
$receiver = isset($_POST['receiver']) ? intval($_POST['receiver']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$sender_type = isset($_POST['sender_type']) ? $_POST['sender_type'] : 'doctor';
$receiver_type = isset($_POST['receiver_type']) ? $_POST['receiver_type'] : 'patient';

// Validate inputs
if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

if ($receiver <= 0 || $sender <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid sender or receiver ID']);
    exit;
}

try {
    // Check if new columns exist
    $check_columns = $database->query("SHOW COLUMNS FROM messages LIKE 'sender_type'");
    $has_columns = $check_columns->num_rows > 0;

    if ($has_columns) {
        // Use new columns
        $stmt = $database->prepare("INSERT INTO messages (sender_id, receiver_id, message, sender_type, receiver_type, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $database->error);
        }
        
        $stmt->bind_param("iisss", $sender, $receiver, $message, $sender_type, $receiver_type);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $insert_id = $stmt->insert_id;
        $stmt->close();
        
        // ✅ Log للتأكد
        error_log("send_message.php - Message inserted: ID=$insert_id, sender=$sender, receiver=$receiver, type=$sender_type->$receiver_type");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Message sent successfully',
            'insert_id' => $insert_id,
            'sender' => $sender,
            'receiver' => $receiver,
            'timestamp' => time()
        ]);
        
    } else {
        // Use basic insert without new columns
        $stmt = $database->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $database->error);
        }
        
        $stmt->bind_param("iis", $sender, $receiver, $message);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $insert_id = $stmt->insert_id;
        $stmt->close();
        
        error_log("send_message.php - Basic message inserted: ID=$insert_id");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Message sent successfully',
            'insert_id' => $insert_id,
            'timestamp' => time()
        ]);
    }
    
} catch (Exception $e) {
    error_log("send_message.php error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$database->close();
?>