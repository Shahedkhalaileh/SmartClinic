<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

include '../connection.php';

$current_user_id = isset($_GET['sender']) ? intval($_GET['sender']) : 0;
$other_user_id = isset($_GET['receiver']) ? intval($_GET['receiver']) : 0;

if ($current_user_id <= 0 || $other_user_id <= 0) {
    echo '<div style="text-align: center; color: white; padding: 20px;">Invalid parameters</div>';
    exit;
}

$query = "SELECT * FROM messages 
          WHERE ((sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?))
          ORDER BY created_at ASC";

$stmt = $database->prepare($query);
if (!$stmt) {
    echo '<div style="text-align: center; color: white; padding: 20px;">Database error</div>';
    exit;
}

$stmt->bind_param("iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);

if (!$stmt->execute()) {
    echo '<div style="text-align: center; color: white; padding: 20px;">Query error</div>';
    $stmt->close();
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '<div style="text-align: center; color: white; padding: 20px;">No messages yet. Start the conversation!</div>';
    $stmt->close();
    exit;
}

while ($row = $result->fetch_assoc()) {
    $isSent = ($row['sender_id'] == $current_user_id);
    $messageClass = $isSent ? 'sent' : 'received';
    $time = date('h:i A', strtotime($row['created_at']));
    $messageText = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
    
    echo "<div class='message $messageClass'>
          $messageText
          <div class='message-time'>$time</div>
        </div>";
}

$stmt->close();
$database->close();
?>