<?php
session_start();
include '../connection.php';

// Check if messages table exists, if not create it
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

$sender = $_POST['sender'];
$receiver = $_POST['receiver'];
$message = trim($_POST['message']);
$sender_type = isset($_POST['sender_type']) ? $_POST['sender_type'] : 'doctor';
$receiver_type = isset($_POST['receiver_type']) ? $_POST['receiver_type'] : 'patient';

if(!empty($message) && $receiver > 0){
    // Check if new columns exist
    $check_columns = $database->query("SHOW COLUMNS FROM messages LIKE 'sender_type'");
    
    if ($check_columns->num_rows > 0) {
        // Use new columns
        $stmt = $database->prepare("INSERT INTO messages (sender_id, receiver_id, message, sender_type, receiver_type, is_read) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("iisss", $sender, $receiver, $message, $sender_type, $receiver_type);
    } else {
        // Use basic insert without new columns
        $stmt = $database->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender, $receiver, $message);
    }
    $stmt->execute();
}
?>
