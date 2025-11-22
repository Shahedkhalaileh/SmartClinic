<?php
session_start();
include '../connection.php';

// Get parameters
$current_user_id = isset($_GET['sender']) ? intval($_GET['sender']) : 0; // Doctor ID
$other_user_id = isset($_GET['receiver']) ? intval($_GET['receiver']) : 0; // Patient ID

if ($current_user_id <= 0 || $other_user_id <= 0) {
    exit;
}

// Fetch ALL messages between current user (doctor) and other user (patient)
$query = "SELECT * FROM messages 
          WHERE ((sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?))
          ORDER BY created_at ASC";

$stmt = $database->prepare($query);
if (!$stmt) {
    exit;
}

$stmt->bind_param("iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);
if (!$stmt->execute()) {
    $stmt->close();
    exit;
}

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // CRITICAL: Determine message direction like WhatsApp
    // Rule: Message is SENT (right side, purple) if current user (doctor) sent it
    //       Message is RECEIVED (left side, white) if patient sent it
    
    // Check both sender_id and sender_type for accurate identification
    $isSent = false;
    
    // Primary check: sender_id must match current user (doctor)
    if ($row['sender_id'] == $current_user_id) {
      // Double-check with sender_type if available
      if (isset($row['sender_type']) && !empty($row['sender_type'])) {
        if ($row['sender_type'] == 'doctor') {
          $isSent = true; // Doctor sent it
        } else {
          // sender_id matches but sender_type is 'patient' - this shouldn't happen, but treat as received
          $isSent = false;
        }
      } else {
        // No sender_type column or it's empty, trust sender_id only
        $isSent = true;
      }
    } else {
      // sender_id doesn't match - definitely received from patient
      $isSent = false;
    }
    
    $messageClass = $isSent ? 'sent' : 'received';
    $time = date('H:i', strtotime($row['created_at']));
    $messageText = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
    
    // Debug info (remove later): sender_id={$row['sender_id']}, current_user={$current_user_id}, sender_type={$row['sender_type']}, isSent={$isSent}
    
    echo "<div class='message $messageClass' style='margin-bottom: 10px;'>
          $messageText
          <div style='font-size:11px; opacity:0.7; margin-top:5px;'>$time</div>
        </div>";
}

$stmt->close();
?>
