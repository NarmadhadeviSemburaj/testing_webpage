<?php
session_start();
include 'db_config.php';

// Check if user is an admin
if (!isset($_SESSION['user']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = $_POST['emp_id'];
    $emp_name = $_POST['emp_name'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];
    $designation = $_POST['designation'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $sql = "UPDATE employees SET emp_name='$emp_name', email='$email', mobile_number='$mobile_number', designation='$designation', is_admin='$is_admin' WHERE emp_id='$emp_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Employee updated successfully'); window.location.href='employees.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
