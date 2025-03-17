<?php
session_start();
include 'db_config.php';

// Check if user is an admin
if (!isset($_SESSION['user']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

// Validate Employee ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid Employee ID!'); window.location.href='employees.php';</script>";
    exit();
}

$emp_id = $_GET['id'];

// Use Prepared Statement
$stmt = $conn->prepare("SELECT * FROM employees WHERE emp_id = ?");
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    echo "<script>alert('Employee not found!'); window.location.href='employees.php';</script>";
    exit();
}

// Define the current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Include the front-end file
include 'edit_employees.php';
?>