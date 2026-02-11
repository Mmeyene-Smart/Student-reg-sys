<?php
// backend/api/register.php
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (
    !isset($data->surname) || 
    !isset($data->email) ||
    !isset($data->other_names) ||
    !isset($data->dob) ||
    !isset($data->sex) ||
    !isset($data->lga_origin) ||
    !isset($data->nationality) ||
    !isset($data->phone) ||
    !isset($data->course_study)
) {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data"]);
    exit();
}

// Check for duplicate email
$check_query = "SELECT id FROM students WHERE email = :email LIMIT 1";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bindParam(":email", $data->email);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(["message" => "Email already exists"]);
    exit();
}

$query = "INSERT INTO students 
(surname, other_names, email, dob, sex, lga_origin, nationality, phone, course_study) 
VALUES 
(:surname, :other_names, :email, :dob, :sex, :lga_origin, :nationality, :phone, :course)";

$stmt = $conn->prepare($query);

$stmt->bindParam(":surname", $data->surname);
$stmt->bindParam(":other_names", $data->other_names);
$stmt->bindParam(":email", $data->email);
$stmt->bindParam(":dob", $data->dob);
$stmt->bindParam(":sex", $data->sex);
$stmt->bindParam(":lga_origin", $data->lga_origin);
$stmt->bindParam(":nationality", $data->nationality);
$stmt->bindParam(":phone", $data->phone);
$stmt->bindParam(":course", $data->course_study);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["message" => "Student registered successfully."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Unable to register student."]);
}
?>
