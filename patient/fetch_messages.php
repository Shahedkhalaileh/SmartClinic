<?php
session_start();
include("../connection.php");

$sender = isset($_GET['sender']) ? intval($_GET['sender']) : 0;
$receiver = isset($_GET['receiver']) ? intval($_GET['receiver']) : 0;

if ($sender <= 0 || $receiver <= 0) {
    exit();
}

// استخدام created_at
$query = "SELECT * FROM messages 
          WHERE (sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?)
          ORDER BY created_at ASC";

$stmt = $database->prepare($query);
$stmt->bind_param("iiii", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 40px 25px; text-align: center;">
            <div style="background: white; border-radius: 20px; padding: 35px 25px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); max-width: 320px; width: 100%;">
                <div style="font-size: 56px; margin-bottom: 15px;">💬</div>
                <p style="font-size: 16px; font-weight: 800; color: #4a31b9;">لا توجد رسائل بعد</p>
            </div>
          </div>';
    exit();
}

while ($row = $result->fetch_assoc()) {
    $msg = htmlspecialchars($row['message']);
    $time = date('h:i A', strtotime($row['created_at']));
    $is_sent = ($row['sender_id'] == $sender);
    
    $class = $is_sent ? 'sent' : 'received';
    
    echo '<div class="message ' . $class . '">';
    echo $msg;
    echo '<div class="message-time">' . $time . '</div>';
    echo '</div>';
}
?>