<?php
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

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : 'patient';

if ($user_id > 0) {
    // Check if is_read column exists
    $check_column = $database->query("SHOW COLUMNS FROM messages LIKE 'is_read'");
    
    if ($check_column->num_rows > 0) {
        // Count unread messages that were RECEIVED by this user (not sent by them)
        // This ensures notifications only show for messages received, not sent
        $query = "SELECT COUNT(*) as count FROM messages 
                  WHERE receiver_id = ? AND sender_id != ? AND is_read = 0";
        $stmt = $database->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo json_encode(['count' => intval($row['count'])]);
    } else {
        // If is_read column doesn't exist, count all unread messages
        $query = "SELECT COUNT(*) as count FROM messages 
                  WHERE receiver_id = ? AND sender_id != ?";
        $stmt = $database->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo json_encode(['count' => intval($row['count'])]);
    }
} else {
    echo json_encode(['count' => 0]);
}
?>

