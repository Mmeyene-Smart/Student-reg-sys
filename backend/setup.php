<?php
// backend/setup.php
include_once 'config.php';

// Create tables
try {
    // Read schema
    $sql = "
    CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        surname VARCHAR(100) NOT NULL,
        other_names VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        dob DATE NOT NULL,
        sex ENUM('Male', 'Female', 'Other') NOT NULL,
        lga_origin VARCHAR(100) NOT NULL,
        nationality VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        course_study VARCHAR(150) NOT NULL,
        status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    
    // Split queries if needed, but PDO->exec can handle multiple if configured or we run one by one.
    // Let's run them one by one to be safe.
    $conn->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        surname VARCHAR(100) NOT NULL,
        other_names VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        dob DATE NOT NULL,
        sex ENUM('Male', 'Female', 'Other') NOT NULL,
        lga_origin VARCHAR(100) NOT NULL,
        nationality VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        course_study VARCHAR(150) NOT NULL,
        status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $conn->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Check if admin exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE email = 'info@elthomppoly.edu.ng'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $password = password_hash('elthomppoly@2026', PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO admins (email, password) VALUES ('info@elthomppoly.edu.ng', :password)");
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        echo "Admin user created (info@elthomppoly.edu.ng / elthomppoly@2026).<br>";
    } else {
        // Force update password for existing user if script is re-run
        $password = password_hash('elthomppoly@2026', PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE admins SET password = :password WHERE email = 'info@elthomppoly.edu.ng'");
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        echo "Admin password updated (info@elthomppoly.edu.ng / elthomppoly@2026).<br>";
    }
    
    echo "Database setup completed successfully.";
    
} catch(PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>
