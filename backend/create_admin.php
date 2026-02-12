<?php
include_once 'config.php';

$email = 'admin@example.com';
$password = 'admin123'; // Default password

try {
    // Check if admin already exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Update existing admin password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $updateReq = $conn->prepare("UPDATE admins SET password = :password WHERE email = :email");
        $updateReq->bindParam(':password', $hashed_password);
        $updateReq->bindParam(':email', $email);
        $updateReq->execute();
        echo "<h1>Success!</h1><p>Admin password reset to: <strong>$password</strong></p>";
    } else {
        // Create new admin
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $insertReq = $conn->prepare("INSERT INTO admins (email, password) VALUES (:email, :password)");
        $insertReq->bindParam(':email', $email);
        $insertReq->bindParam(':password', $hashed_password);
        $insertReq->execute();
        echo "<h1>Success!</h1><p>Admin user created.</p><p>Email: <strong>$email</strong></p><p>Password: <strong>$password</strong></p>";
    }
} catch (PDOException $e) {
    echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
}
?>
