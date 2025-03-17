<?php
$conn = new mysqli("localhost", "root", "", "testing_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $product_name = $conn->real_escape_string($_POST['Product_name']);
    $version = $conn->real_escape_string($_POST['Version']);
    $module_name = $conn->real_escape_string($_POST['Module_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $precondition = $conn->real_escape_string($_POST['precondition']);
    $test_steps = $conn->real_escape_string($_POST['test_steps']);
    $expected_results = $conn->real_escape_string($_POST['expected_results']);

    $sql = "UPDATE testcase SET 
                Product_name = '$product_name', 
                Version = '$version', 
                Module_name = '$module_name', 
                description = '$description', 
                precondition = '$precondition', 
                test_steps = '$test_steps', 
                expected_results = '$expected_results' 
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Test Case updated successfully!'); window.location.href='index1.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
