-- Database: u678570154_student_reg_sy

CREATE DATABASE IF NOT EXISTS u678570154_student_reg_sy;
USE u678570154_student_reg_sy;

-- Students Table
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
    course_type ENUM('HND', 'ND') DEFAULT 'ND',
    merged_pdf VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default Admin User (Password: admin123)
-- You can change this later.Ideally we should hash passwords.
-- Using PHP password_hash('admin123', PASSWORD_BCRYPT) gives something like:
-- $2y$10$YourHashHere...
INSERT INTO admins (email, password) VALUES 
('admin@example.com', '$2y$10$8sA2N5Sx/1z.S9.g8.h.DO7.j9.k0.L1.m2.n3.o4.p5'); 
-- Note: The hash above is a placeholder. I will use a real hash in the PHP code to demonstrate.
