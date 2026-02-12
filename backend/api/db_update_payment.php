<?php
include_once '../config.php';

try {
    // Add payment_status column if it doesn't exist
    $sql = "ALTER TABLE students ADD COLUMN payment_status ENUM('Unpaid', 'Paid', 'Verified') DEFAULT 'Unpaid' AFTER status";
    $conn->exec($sql);
    echo "Column 'payment_status' added successfully.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'payment_status' already exists.<br>";
    } else {
        echo "Error adding column: " . $e->getMessage() . "<br>";
    }
}
?>
