<?php
include '../connection.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;

if ($user_id > 0 && $doctor_id > 0) {
    // Check if is_read column exists
    $check_column = $database->query("SHOW COLUMNS FROM messages LIKE 'is_read'");
    
    if ($check_column->num_rows > 0) {
        // Count unread messages from this doctor to this patient
        $query = "SELECT COUNT(*) as count FROM messages 
                  WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        $stmt = $database->prepare($query);
        $stmt->bind_param("ii", $doctor_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo json_encode(['has_unread' => intval($row['count']) > 0]);
    } else {
        echo json_encode(['has_unread' => false]);
    }
} else {
    echo json_encode(['has_unread' => false]);
}
?>



