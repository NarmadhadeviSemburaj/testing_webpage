<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "testing_db");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'fetch_products':
        $sql = "SELECT DISTINCT Product_name FROM testcase";
        $result = $conn->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row['Product_name'];
        }
        echo json_encode($products);
        break;

    case 'fetch_versions':
        $sql = "SELECT DISTINCT Version FROM testcase";
        $result = $conn->query($sql);
        $versions = [];
        while ($row = $result->fetch_assoc()) {
            $versions[] = $row['Version'];
        }
        echo json_encode($versions);
        break;

    case 'fetch_test_cases':
        $product = $_GET['product'] ?? '';
        $version = $_GET['version'] ?? '';
        $sql = "SELECT * FROM testcase WHERE 1=1";
        if (!empty($product)) {
            $sql .= " AND Product_name = '$product'";
        }
        if (!empty($version)) {
            $sql .= " AND Version = '$version'";
        }
        $result = $conn->query($sql);
        $testCases = [];
        while ($row = $result->fetch_assoc()) {
            $testCases[] = $row;
        }
        echo json_encode($testCases);
        break;

    case 'upload_excel':
        if ($_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['excel_file']['tmp_name'];
            // Process the Excel file and insert data into the database
            // (You can use a library like PhpSpreadsheet for this)
            echo json_encode(['success' => 'File uploaded successfully']);
        } else {
            echo json_encode(['error' => 'File upload failed']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

$conn->close();
?>
