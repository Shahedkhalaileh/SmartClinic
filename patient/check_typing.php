<?php
include '../connection.php';

$sender = isset($_GET['sender']) ? intval($_GET['sender']) : 0;
$receiver = isset($_GET['receiver']) ? intval($_GET['receiver']) : 0;

if ($sender > 0 && $receiver > 0) {
    // Check if typing_status table exists
    $table_check = $database->query("SHOW TABLES LIKE 'typing_status'");
    if ($table_check->num_rows > 0) {
        // Check if receiver is typing to sender
        $stmt = $database->prepare("SELECT is_typing FROM typing_status 
                                    WHERE sender_id = ? AND receiver_id = ? AND is_typing = 1");
        $stmt->bind_param("ii", $receiver, $sender);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['is_typing' => (bool)$row['is_typing']]);
        } else {
            echo json_encode(['is_typing' => false]);
        }
    } else {
        echo json_encode(['is_typing' => false]);
    }
} else {
    echo json_encode(['is_typing' => false]);
}
?>

