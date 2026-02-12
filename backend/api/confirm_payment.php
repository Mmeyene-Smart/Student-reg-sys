<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['student_id'])) {
    http_response_code(400);
    echo json_encode(["message" => "Missing student ID"]);
    exit();
}

$student_id = $input['student_id'];

try {
    // Update payment_status to 'Paid'
    // We assume the column exists (added via migration)
    $sql = "UPDATE students SET payment_status = 'Paid' WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $student_id);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Payment status updated successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to update payment status."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>
