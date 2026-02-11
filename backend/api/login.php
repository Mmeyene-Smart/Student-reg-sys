<?php
// backend/api/login.php
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data"]);
    exit();
}

$query = "SELECT id, email, password FROM admins WHERE email = :email LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(":email", $data->email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (password_verify($data->password, $row['password'])) {
        http_response_code(200);
        echo json_encode([
            "message" => "Login successful",
            "token" => base64_encode(json_encode(["id" => $row['id'], "email" => $row['email']])) // Simple mock token
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Invalid credentials"]);
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Invalid credentials"]);
}
?>
