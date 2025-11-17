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

$sender = $_GET['sender'];
$receiver = $_GET['receiver'];

$query = "SELECT * FROM messages 
          WHERE (sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?)
          ORDER BY created_at ASC";

$stmt = $database->prepare($query);
$stmt->bind_param("iiii", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  // Check if message was sent by the current user (patient)
  // Use both sender_id and sender_type to ensure correct identification
  $isSent = false;
  if (isset($row['sender_type']) && isset($row['receiver_type'])) {
    // If sender_type column exists, use it for more accurate check
    $isSent = ($row['sender_id'] == $sender && $row['sender_type'] == 'patient');
  } else {
    // Fallback to sender_id only
    $isSent = ($row['sender_id'] == $sender);
  }
  
  $messageClass = $isSent ? 'sent' : 'received';
  $time = date('H:i', strtotime($row['created_at']));
  
  echo "<div class='message $messageClass'>
          {$row['message']}
          <div style='font-size:11px; opacity:0.7; margin-top:5px;'>$time</div>
        </div>";
}
?>
