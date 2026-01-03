<?php
include("../connection.php");

header('Content-Type: application/json');

$docid = isset($_GET['docid']) ? intval($_GET['docid']) : 0;

if ($docid > 0) {
    $query = "SELECT COUNT(*) as admin_unread FROM admin_messages 
              WHERE receiver_type = 'doctor' 
              AND receiver_id = ? 
              AND is_read = 0";

    $stmt = $database->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $docid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo json_encode([
            "status" => "ok",
            "admin_unread" => (int) $row['admin_unread']
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