<?php
// backend/update_schema_v4.php
include_once 'config.php';

try {
    // Add course_type column
    $conn->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS course_type ENUM('HND', 'ND') DEFAULT 'ND' AFTER course_study");
    
    // Add merged_pdf column
    $conn->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS merged_pdf VARCHAR(255) DEFAULT NULL AFTER jamb_result");

    echo "Database schema updated successfully (v4). Added course_type and merged_pdf columns.";
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
