<?php
require_once __DIR__ . '/../student/classes/user.php';
require_once __DIR__ . '/../student/classes/student.php';

session_start(); // ensure session

$user = new User();
$student = new Student();

// --- AUTH HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($user->loginUser($email, $password)) {
            $redirect = ($_SESSION['role'] === 'student')
                ? "../student/student_dashboard.php"
                : "../admin/admin_dashboard.php";
            header("Location: $redirect");
        } else {
            $_SESSION['error'] = "Invalid login!";
            header("Location: ../login.php");
        }
        exit;
    }

    if ($_POST['action'] === 'register') {
        $success = $user->registerUser(
            $_POST['name'],
            $_POST['email'],
            $_POST['password'],
            $_POST['role']
        );
        $_SESSION[$success ? 'success' : 'error'] =
            $success ? "Registered successfully! Please log in." : "Registration failed!";
        header("Location: ../login.php");
        exit;
    }
}

// --- LOGOUT ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $user->logout();
    header("Location: ../login.php");
    exit;
}

// --- STUDENT HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_SESSION['user_id'] ?? null;
    if (!$studentId) {
        $_SESSION['error'] = "Unauthorized action.";
        header("Location: ../login.php");
        exit;
    }

    // Handle Excuse Letter
    if ($_POST['action'] === 'submit_excuse') {
        $courseId = (int) $_POST['course_id'];
        $reason = trim($_POST['reason']);
        $filePath = null;

        if (!empty($_FILES['file']['name'])) {
            $uploadDir = __DIR__ . "/../uploads/excuses/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filePath = "uploads/excuses/" . time() . "_" . basename($_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . "/../" . $filePath);
        }

        if ($reason) {
            $student->submitExcuseLetter($studentId, $courseId, $reason, $filePath);
            $_SESSION['success'] = "Excuse letter submitted!";
        } else {
            $_SESSION['error'] = "Reason is required.";
        }

        header("Location: ../student/student_dashboard.php");
        exit;
    }

    // Handle Attendance
    if (in_array($_POST['action'], ['check_in', 'check_out'])) {
        $courseId = (int) $_POST['course_id'];
        $today = date("Y-m-d");

        $todayRecord = $student->getAttendanceByDate($studentId, $courseId, $today);

        if ($_POST['action'] === 'check_in') {
            if ($todayRecord) {
                $_SESSION['error'] = "You already have an attendance record for today in this course.";
            } else {
                $student->logAttendance($studentId, $courseId, 'Present', date("H:i:s"), null);
                $_SESSION['success'] = "Checked in successfully.";
            }
        }

        if ($_POST['action'] === 'check_out') {
            if (!$todayRecord) {
                $_SESSION['error'] = "No check-in found for today in this course.";
            } elseif (!empty($todayRecord['check_out'])) {
                $_SESSION['error'] = "You already checked out for this course today.";
            } else {
                $student->updateAttendance($todayRecord['id'], 'Present', date("H:i:s"));
                $_SESSION['success'] = "Checked out successfully.";
            }
        }

        header("Location: ../student/student_dashboard.php");
        exit;
    }
}