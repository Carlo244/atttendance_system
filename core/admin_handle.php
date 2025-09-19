<?php
session_start();
require_once __DIR__ . '/../admin/classes/admin.php';

$admin = new Admin();

// ADD COURSE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_course') {
    $admin->addCourse($_POST['course_name'], $_POST['year_level']);
    header("Location: ../admin/admin_dashboard.php");
    exit;
}

// EDIT COURSE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit_course') {
    $admin->updateCourse($_POST['id'], $_POST['course_name'], $_POST['year_level']);
    header("Location: ../admin/admin_dashboard.php");
    exit;
}

// DELETE COURSE
if (isset($_GET['action']) && $_GET['action'] === 'delete_course') {
    $admin->deleteCourse($_GET['id']);
    header("Location: ../admin/admin_dashboard.php");
    exit;
}

// ENROLL STUDENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'enroll_student') {
    $admin->enrollStudent($_POST['student_id'], $_POST['course_id']);
    header("Location: ../admin/admin_dashboard.php");
    exit;
}

// UNENROLL STUDENT
if (isset($_GET['action']) && $_GET['action'] === 'unenroll_student') {
    $admin->unenrollStudent($_GET['student_id'], $_GET['course_id']);
    header("Location: ../admin/admin_dashboard.php");
    exit;
}

// APPROVE EXCUSE LETTER
if (isset($_GET['action']) && $_GET['action'] === 'approve_excuse') {
    $admin->updateExcuseStatus($_GET['id'], 'Approved');
    header("Location: ../admin/admin_dashboard.php");
    exit;
}

// REJECT EXCUSE LETTER
if (isset($_GET['action']) && $_GET['action'] === 'reject_excuse') {
    $admin->updateExcuseStatus($_GET['id'], 'Rejected');
    header("Location: ../admin/admin_dashboard.php");
    exit;
}

// LOGOUT
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $admin->logout();
    header("Location: ../login.php");
    exit;
}