<?php
include '../connection.php';

$sender = isset($_POST['sender']) ? intval($_POST['sender']) : 0;
$receiver = isset($_POST['receiver']) ? intval($_POST['receiver']) : 0;
$is_typing = isset($_POST['is_typing']) ? intval($_POST['is_typing']) : 0;

if ($sender > 0 && $receiver > 0) {
    // Create typing_status table if it doesn't exist
    $table_check = $database->query("SHOW TABLES LIKE 'typing_status'");
    if ($table_check->num_rows == 0) {
        $create_table = "CREATE TABLE IF NOT EXISTS `typing_status` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `sender_id` int(11) NOT NULL,
            `receiver_id` int(11) NOT NULL,
            `is_typing` TINYINT(1) DEFAULT 0,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_pair` (`sender_id`, `receiver_id`),
            KEY `receiver_id` (`receiver_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $database->query($create_table);
    }
    
    // Insert or update typing status
    $stmt = $database->prepare("INSERT INTO typing_status (sender_id, receiver_id, is_typing) VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE is_typing = ?, updated_at = NOW()");
    $stmt->bind_param("iiii", $sender, $receiver, $is_typing, $is_typing);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>



