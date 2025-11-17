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

$sender = isset($_GET['sender']) ? intval($_GET['sender']) : 0;
$receiver = isset($_GET['receiver']) ? intval($_GET['receiver']) : 0;

if ($sender > 0 && $receiver > 0) {
    // Check if is_read column exists
    $check_column = $database->query("SHOW COLUMNS FROM messages LIKE 'is_read'");
    
    if ($check_column->num_rows > 0) {
        // Mark messages as read
        $query = "UPDATE messages SET is_read = 1 
                  WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        $stmt = $database->prepare($query);
        $stmt->bind_param("ii", $sender, $receiver);
        $stmt->execute();
    }
}
?>

