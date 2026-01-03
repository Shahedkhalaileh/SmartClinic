<?php
include("../connection.php");

header('Content-Type: application/json');

$docid = isset($_GET['docid']) ? intval($_GET['docid']) : 0;

if ($docid > 0) {
    $query = "SELECT id, message, is_read, sent_at as created_at 
              FROM admin_messages 
              WHERE receiver_type = 'doctor' 
              AND receiver_id = ? 
              ORDER BY sent_at DESC";

    $stmt = $database->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $docid);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id' => $row['id'],
                'message' => htmlspecialchars($row['message']),
                'is_read' => (int) $row['is_read'],
                'created_at' => $row['created_at']
            ];
        }

        echo json_encode([
            "status" => "ok",
            "messages" => $messages
        ]);

        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Database query failed"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid doctor ID"
    ]);
}

$database->close();
?>