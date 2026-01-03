<?php
include("../connection.php");

header('Content-Type: application/json');

$docid = isset($_POST['docid']) ? intval($_POST['docid']) : 0;

if ($docid > 0) {
    $query = "UPDATE admin_messages 
              SET is_read = 1 
              WHERE receiver_type = 'doctor' 
              AND receiver_id = ? 
              AND is_read = 0";

    $stmt = $database->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $docid);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "ok",
                "affected_rows" => $stmt->affected_rows
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update messages"
            ]);
        }

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