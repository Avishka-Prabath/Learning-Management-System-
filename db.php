<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "edumart_db";

// Try 127.0.0.1:8889 (MAMP standard)
$conn = new mysqli("127.0.0.1", "root", "", "edumart_db", 3306);

// Fallback to standard 3306 with empty password (XAMPP standard)
if ($conn->connect_error) {
    $conn = @new mysqli("127.0.0.1", "root", "");
}

// Fallback to standard 3306 with password 'root' (MAMP alternative standard)
if ($conn->connect_error) {
    $conn = @new mysqli("127.0.0.1", "root", "root");
}

// If all database connection attempts fail
if ($conn->connect_error) {
    die("Database connection failed. Please ensure MySQL is running: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db);

// Create tables
// 1. Admins
$conn->query("CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 2. Teachers
$conn->query("CREATE TABLE IF NOT EXISTS `teachers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(10) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `phone` VARCHAR(20),
    `department` VARCHAR(100),
    `assigned_module` VARCHAR(150),
    `bio` TEXT,
    `profile_photo` VARCHAR(255),
    `password` VARCHAR(255) NOT NULL,
    `status` VARCHAR(20) DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 3. Students
$conn->query("CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` VARCHAR(50) UNIQUE NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `phone` VARCHAR(20),
    `password` VARCHAR(255) NOT NULL,
    `status` VARCHAR(20) DEFAULT 'Pending',
    `profile_photo` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 4. Courses
// 4. Courses
$conn->query("CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_code` VARCHAR(20) UNIQUE NOT NULL,
    `course_name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `teacher_id` INT NULL,
    `category` VARCHAR(50) DEFAULT 'computing',
    `duration` VARCHAR(50) DEFAULT '3 Years',
    `credits` VARCHAR(50) DEFAULT '120 Credits',
    `price` VARCHAR(50) DEFAULT 'LKR 450,000',
    `image` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 5. Enrollments
$conn->query("CREATE TABLE IF NOT EXISTS `enrollments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `payment_status` VARCHAR(20) DEFAULT 'Pending',
    `payment_slip` VARCHAR(255) NULL,
    `amount` DECIMAL(10,2) DEFAULT 0.00,
    `enrollment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` VARCHAR(20) DEFAULT 'Pending'
) ENGINE=InnoDB");

// 6. Announcements
$conn->query("CREATE TABLE IF NOT EXISTS `announcements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `target_audience` VARCHAR(50) DEFAULT 'All',
    `posted_by` VARCHAR(50) DEFAULT 'Admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 7. Assignments
$conn->query("CREATE TABLE IF NOT EXISTS `assignments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NULL,
    `course_name` VARCHAR(150) NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `file_path` VARCHAR(255) NULL,
    `due_date` DATE NULL,
    `deadline_date` DATE NULL,
    `deadline_time` TIME NULL,
    `type` VARCHAR(50) DEFAULT 'Assignment',
    `status` VARCHAR(20) DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Alter assignments table to add missing columns if they don't exist
$val = $conn->query("SHOW COLUMNS FROM `assignments` LIKE 'course_name'");
if ($val->num_rows == 0) {
    $conn->query("ALTER TABLE `assignments` ADD COLUMN `course_name` VARCHAR(150) NULL AFTER `course_id`");
    $conn->query("ALTER TABLE `assignments` ADD COLUMN `deadline_date` DATE NULL AFTER `due_date`");
    $conn->query("ALTER TABLE `assignments` ADD COLUMN `deadline_time` TIME NULL AFTER `deadline_date`");
    $conn->query("ALTER TABLE `assignments` ADD COLUMN `type` VARCHAR(50) DEFAULT 'Assignment' AFTER `deadline_time`");
    $conn->query("ALTER TABLE `assignments` ADD COLUMN `status` VARCHAR(20) DEFAULT 'Active' AFTER `type`");
}

// Migrate students table — add extended registration fields
$studentColumns = [
    'enrollment_id'     => "INT NULL AFTER `id`",
    'campus_id'         => "VARCHAR(50) NULL AFTER `student_id`",
    'campus_email'      => "VARCHAR(100) NULL AFTER `email`",
    'full_name'         => "VARCHAR(150) NULL AFTER `last_name`",
    'nic'               => "VARCHAR(20) NULL AFTER `full_name`",
    'dob'               => "DATE NULL AFTER `nic`",
    'gender'            => "VARCHAR(20) NULL AFTER `dob`",
    'address'           => "TEXT NULL AFTER `phone`",
    'course_id'         => "INT NULL AFTER `address`",
    'study_mode'        => "VARCHAR(50) DEFAULT 'Full-Time' AFTER `course_id`",
    'intake'            => "VARCHAR(50) NULL AFTER `study_mode`",
    'qualification'     => "VARCHAR(100) NULL AFTER `intake`",
    'school'            => "VARCHAR(150) NULL AFTER `qualification`",
    'guardian_name'     => "VARCHAR(100) NULL AFTER `school`",
    'guardian_phone'    => "VARCHAR(20) NULL AFTER `guardian_name`",
    'guardian_relation' => "VARCHAR(50) NULL AFTER `guardian_phone`",
];
foreach ($studentColumns as $col => $definition) {
    $check = $conn->query("SHOW COLUMNS FROM `students` LIKE '$col'");
    if ($check && $check->num_rows == 0) {
        $conn->query("ALTER TABLE `students` ADD COLUMN `$col` $definition");
    }
}

// Migrate enrollments table — support full application data
$enrollmentColumns = [
    'full_name'         => "VARCHAR(150) NULL AFTER `id`",
    'nic'               => "VARCHAR(20) NULL AFTER `full_name`",
    'dob'               => "DATE NULL AFTER `nic`",
    'gender'            => "VARCHAR(20) NULL AFTER `dob`",
    'phone'             => "VARCHAR(20) NULL AFTER `gender`",
    'email'             => "VARCHAR(100) NULL AFTER `phone`",
    'address'           => "TEXT NULL AFTER `email`",
    'study_mode'        => "VARCHAR(50) DEFAULT 'Full-Time' AFTER `course_id`",
    'intake'            => "VARCHAR(50) NULL AFTER `study_mode`",
    'qualification'     => "VARCHAR(100) NULL AFTER `intake`",
    'school'            => "VARCHAR(150) NULL AFTER `qualification`",
    'guardian_name'     => "VARCHAR(100) NULL AFTER `school`",
    'guardian_phone'    => "VARCHAR(20) NULL AFTER `guardian_name`",
    'guardian_relation' => "VARCHAR(50) NULL AFTER `guardian_phone`",
    'notes'             => "TEXT NULL AFTER `guardian_relation`",
];
foreach ($enrollmentColumns as $col => $definition) {
    $check = $conn->query("SHOW COLUMNS FROM `enrollments` LIKE '$col'");
    if ($check && $check->num_rows == 0) {
        $conn->query("ALTER TABLE `enrollments` ADD COLUMN `$col` $definition");
    }
}
// Allow student_id to be NULL for application-only records
$conn->query("ALTER TABLE `enrollments` MODIFY COLUMN `student_id` INT NULL");

// Migrate teachers table — add username for instructor login
$checkUsername = $conn->query("SHOW COLUMNS FROM `teachers` LIKE 'username'");
if ($checkUsername && $checkUsername->num_rows == 0) {
    $conn->query("ALTER TABLE `teachers` ADD COLUMN `username` VARCHAR(50) NULL AFTER `email`");
}

// Migrate announcements — code uses `message`, schema had `content`
$checkMessage = $conn->query("SHOW COLUMNS FROM `announcements` LIKE 'message'");
if ($checkMessage && $checkMessage->num_rows == 0) {
    $checkContent = $conn->query("SHOW COLUMNS FROM `announcements` LIKE 'content'");
    if ($checkContent && $checkContent->num_rows > 0) {
        $conn->query("ALTER TABLE `announcements` CHANGE COLUMN `content` `message` TEXT NOT NULL");
    } else {
        $conn->query("ALTER TABLE `announcements` ADD COLUMN `message` TEXT NOT NULL AFTER `title`");
    }
}

// Create schedules table for admin calendar
$conn->query("CREATE TABLE IF NOT EXISTS `schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `type` VARCHAR(50) DEFAULT 'Live Class',
    `event_date` DATE NOT NULL,
    `start_time` TIME NULL,
    `end_time` TIME NULL,
    `instructor_id` INT NULL,
    `meeting_link` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 8. Categories
$conn->query("CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// 9. Live Classes
$conn->query("CREATE TABLE IF NOT EXISTS `live_classes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `class_title` VARCHAR(255) NOT NULL,
    `module_name` VARCHAR(255) NOT NULL,
    `course_batch` VARCHAR(100) NOT NULL,
    `class_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `platform` VARCHAR(100) DEFAULT 'Zoom',
    `status` VARCHAR(50) DEFAULT 'Scheduled',
    `meeting_link` VARCHAR(500) NOT NULL,
    `instructor_name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");


// Seed default Admin if empty
$checkAdmin = $conn->query("SELECT id FROM admins LIMIT 1");
if ($checkAdmin->num_rows == 0) {
    $adminPass = password_hash("admin123", PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admins (username, email, password) VALUES ('admin', 'admin@edumart.ac.lk', '$adminPass')");
}

// Seed Teachers if empty
$checkTeachers = $conn->query("SELECT id FROM teachers LIMIT 1");
if ($checkTeachers->num_rows == 0) {
    $t1Pass = password_hash("teacher123", PASSWORD_DEFAULT);
    $conn->query("INSERT INTO teachers (title, full_name, email, phone, department, assigned_module, bio, password, status) VALUES 
        ('Dr.', 'Saman Perera', 'saman.p@edumart.ac.lk', '+94 77 123 4567', 'Software Engineering', 'Software Architecture', '10+ years software engineering experience. Specializes in PHP, Modern JS, and scalable architecture.', '$t1Pass', 'Active'),
        ('Prof.', 'Anura Jayawardena', 'anura.j@edumart.ac.lk', '+94 71 987 6543', 'Data Science', 'Data Science Fundamentals', 'Former Senior Analyst at TechCorp. Specialist in Python Data Analysis and Machine Learning.', '$t1Pass', 'Active'),
        ('Mrs.', 'Nimali Silva', 'nimali.s@edumart.ac.lk', '+94 76 555 4321', 'Cyber Security', 'Web Development', 'Passionate about design systems, Figma prototyping, and user-centered design research.', '$t1Pass', 'Active')");
}

// Seed Courses if empty
$checkCourses = $conn->query("SELECT id FROM courses LIMIT 1");
if ($checkCourses->num_rows == 0) {
    $t1Res = $conn->query("SELECT id FROM teachers WHERE email='saman.p@edumart.ac.lk'")->fetch_assoc();
    $t2Res = $conn->query("SELECT id FROM teachers WHERE email='anura.j@edumart.ac.lk'")->fetch_assoc();
    $t3Res = $conn->query("SELECT id FROM teachers WHERE email='nimali.s@edumart.ac.lk'")->fetch_assoc();
    
    $t1_id = $t1Res ? $t1Res['id'] : 'NULL';
    $t2_id = $t2Res ? $t2Res['id'] : 'NULL';
    $t3_id = $t3Res ? $t3Res['id'] : 'NULL';
    
    $conn->query("INSERT INTO courses (course_code, course_name, description, teacher_id, image) VALUES 
        ('SE-2026', 'BSc (Hons) in Software Engineering', 'Learn modern programming paradigms, system architecture, database design, and cloud deployments.', $t1_id, 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=600&q=80'),
        ('DS-2026', 'BSc (Hons) in Data Science & AI', 'Master probability, statistical computing, deep learning, NLP, and Big Data technologies.', $t2_id, 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80'),
        ('CS-2026', 'BSc (Hons) in Cyber Security', 'Dive deep into secure coding, ethical hacking, digital forensics, and network defense strategies.', $t3_id, 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80'),
        ('ICT-2026', 'BSc (Hons) in Information & Comm. Tech', 'A broad field covering database systems, networking, system administration, and web applications.', $t3_id, 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=600&q=80')");
}

// Seed default categories if empty
$checkCategories = $conn->query("SELECT id FROM categories LIMIT 1");
if ($checkCategories->num_rows == 0) {
    $conn->query("INSERT INTO categories (name, description) VALUES 
        ('Computing', 'Software engineering, web development, cybersecurity, etc.'),
        ('Business', 'Business management, HR, digital marketing, finance, etc.'),
        ('Data Science', 'Data analysis, statistics, machine learning, AI, etc.')");
}

// Seed default live classes if empty
$checkLive = $conn->query("SELECT id FROM live_classes LIMIT 1");
if ($checkLive->num_rows == 0) {
    $conn->query("INSERT INTO live_classes (class_title, module_name, course_batch, class_date, start_time, end_time, platform, status, meeting_link, instructor_name) VALUES
        ('Database Systems Discussion', 'Database Administration', 'Software Engineering', CURDATE(), '15:00:00', '17:00:00', 'Zoom', 'Ready', 'https://zoom.us', 'Dr. Saman Perera'),
        ('Machine Learning Workshop', 'Data Science Fundamentals', 'Data Science & AI', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '11:00:00', 'Zoom', 'Scheduled', 'https://zoom.us', 'Prof. Anura Jayawardena'),
        ('Web Security Basics', 'Web Development', 'Cyber Security', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:00:00', '15:00:00', 'Zoom', 'Scheduled', 'https://zoom.us', 'Mrs. Nimali Silva')");
}
?>
