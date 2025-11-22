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

if ($sender <= 0 || $receiver <= 0) {
    exit;
}

$query = "SELECT * FROM messages 
          WHERE (sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?)
          ORDER BY created_at ASC";

$stmt = $database->prepare($query);
$stmt->bind_param("iiii", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  // CRITICAL: Determine message direction like WhatsApp
  // Rule: Message is SENT (right side, purple) if current user (patient) sent it
  //       Message is RECEIVED (left side, white) if doctor sent it
  
  // Check both sender_id and sender_type for accurate identification
  $isSent = false;
  
  // Primary check: sender_id must match current user (patient)
  if ($row['sender_id'] == $sender) {
    // Double-check with sender_type if available
    if (isset($row['sender_type']) && !empty($row['sender_type'])) {
      if ($row['sender_type'] == 'patient') {
        $isSent = true; // Patient sent it
      } else {
        // sender_id matches but sender_type is 'doctor' - this shouldn't happen, but treat as received
        $isSent = false;
      }
    } else {
      // No sender_type column or it's empty, trust sender_id only
      $isSent = true;
    }
  } else {
    // sender_id doesn't match - definitely received from doctor
    $isSent = false;
  }
  
  $messageClass = $isSent ? 'sent' : 'received';
  $time = date('H:i', strtotime($row['created_at']));
  $messageText = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
  
  // Debug info (remove later): sender_id={$row['sender_id']}, sender={$sender}, sender_type={$row['sender_type']}, isSent={$isSent}
  
  echo "<div class='message $messageClass' style='margin-bottom: 10px;'>
          $messageText
          <div style='font-size:11px; opacity:0.7; margin-top:5px;'>$time</div>
        </div>";
}

$stmt->close();
?>
