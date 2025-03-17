<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

// Check if the user is an admin
$sql = "SELECT is_admin FROM employees WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !$user['is_admin']) {
    echo "<script>alert('Access Denied! Only admins can delete employees.'); window.location.href='employees.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $emp_id = $_GET['id'];

    // Check if the employee exists
    $check_sql = "SELECT * FROM employees WHERE emp_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $emp_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Delete employee
        $delete_sql = "DELETE FROM employees WHERE emp_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("s", $emp_id);

        if ($delete_stmt->execute()) {
            echo "<script>alert('Employee deleted successfully!'); window.location.href='employees.php';</script>";
        } else {
            echo "<script>alert('Error deleting employee.'); window.location.href='employees.php';</script>";
        }
    } else {
        echo "<script>alert('Employee not found.'); window.location.href='employees.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='employees.php';</script>";
}
?>
