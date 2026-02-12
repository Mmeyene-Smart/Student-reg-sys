<?php
// backend/update_schema_v5.php
include_once 'config.php';

try {
    // 1. Update existing 'Non-HND' records to 'ND'
    $conn->exec("UPDATE students SET course_type = 'ND' WHERE course_type = 'Non-HND'");
    
    // 2. Modify the ENUM column to use 'ND' instead of 'Non-HND'
    // First we add 'ND' to the enum
    $conn->exec("ALTER TABLE students MODIFY COLUMN course_type ENUM('HND', 'ND') DEFAULT 'ND'");

    echo "Database schema updated successfully (v5). Renamed 'Non-HND' to 'ND' and updated records.";
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
