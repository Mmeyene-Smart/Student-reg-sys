<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

// Check if content type is multipart/form-data
$is_multipart = isset($_SERVER["CONTENT_TYPE"]) && stripos($_SERVER["CONTENT_TYPE"], "multipart/form-data") !== false;

$data = [];
$nd_holder = 0;
$hnd_holder = 0;
$course_type = 'ND';

if ($is_multipart) {
    if (isset($_POST) && is_array($_POST)) {
        $data = $_POST;
    }
    
    // Course type
    if (isset($data['course_type'])) {
        $course_type = $data['course_type'];
    }
} else {
    // Fallback for JSON
    $input = json_decode(file_get_contents("php://input"), true);
    if ($input) {
        $data = $input;
        $course_type = isset($data['course_type']) ? $data['course_type'] : 'ND';
    }
}

// Convert object to array if needed (though $_POST is array)
if (is_object($data)) {
    $data = (array)$data;
}

// Check if data is populated at all
if (empty($data) && empty($_FILES)) {
    http_response_code(400);
    echo json_encode(["message" => "No data provided."]);
    exit();
}

// Basic validation fields
$required_fields = ['surname', 'email', 'other_names', 'dob', 'sex', 'lga_origin', 'nationality', 'phone', 'course_study'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["message" => "Incomplete data: Missing $field"]);
        exit();
    }
}

if (!isset($conn)) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection error"]);
    exit();
}

// Check for duplicate email
$check_query = "SELECT id FROM students WHERE email = :email LIMIT 1";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bindParam(":email", $data['email']);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(["message" => "Email already exists"]);
    exit();
}

// File Upload Handling
$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        http_response_code(500);
        echo json_encode(["message" => "Failed to create upload directory"]);
        exit();
    }
}

$file_paths = [
    'merged_pdf' => null
];

// Helper function
function process_upload($key, $required = false) {
    global $upload_dir;
    
    if (!isset($_FILES[$key]) || $_FILES[$key]['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            return ["error" => "Missing required file: $key"];
        }
        return null;
    }

    $file = $_FILES[$key];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ["error" => "Upload error code " . $file['error'] . " for $key"];
    }

    // Validate type - ONLY PDF
    $allowed_types = ['application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return ["error" => "Invalid file type for $key. ONLY PDF files are allowed."];
    }

    // Generate unique name
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $clean_key = preg_replace('/[^a-zA-Z0-9]/', '_', $key);
    $filename = uniqid($clean_key . '_') . '.' . $ext;
    $target_path = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $filename;
    } else {
        return ["error" => "Failed to move uploaded file $key"];
    }
}

$errors = [];

// Process merged PDF
$res = process_upload('merged_pdf', true);
if (is_array($res) && isset($res['error'])) $errors[] = $res['error'];
else $file_paths['merged_pdf'] = $res;

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["message" => implode(", ", $errors)]);
    exit();
}

// Insert into Database
$query = "INSERT INTO students 
(surname, other_names, email, dob, sex, lga_origin, nationality, phone, course_study, 
course_type, merged_pdf) 
VALUES 
(:surname, :other_names, :email, :dob, :sex, :lga_origin, :nationality, :phone, :course,
:course_type, :merged_pdf)";

try {
    $stmt = $conn->prepare($query);

    $stmt->bindParam(":surname", $data['surname']);
    $stmt->bindParam(":other_names", $data['other_names']);
    $stmt->bindParam(":email", $data['email']);
    $stmt->bindParam(":dob", $data['dob']);
    $stmt->bindParam(":sex", $data['sex']);
    $stmt->bindParam(":lga_origin", $data['lga_origin']);
    $stmt->bindParam(":nationality", $data['nationality']);
    $stmt->bindParam(":phone", $data['phone']);
    $stmt->bindParam(":course", $data['course_study']);
    $stmt->bindParam(":course_type", $course_type);
    $stmt->bindParam(":merged_pdf", $file_paths['merged_pdf']);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Student registered successfully."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Unable to register student."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>
