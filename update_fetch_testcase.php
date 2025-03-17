<?php
session_start();
if (!isset($_SESSION['user'])) {
    die(json_encode(["error" => "Unauthorized access!"]));
}

include 'db_config.php';

// Check if 'product_name' and 'version' are provided in the request
if (!isset($_POST['product_name'], $_POST['version'])) {
    die(json_encode(["error" => "Product name and version are required!"]));
}

// Sanitize input data
$product_name = htmlspecialchars($_POST['product_name']);
$version = htmlspecialchars($_POST['version']);

// Fetch test cases based on product and version
$sql = "SELECT * FROM testcase WHERE Product_name = ? AND Version = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "Failed to prepare the SQL statement: " . $conn->error]));
}

$stmt->bind_param("ss", $product_name, $version);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die(json_encode(["error" => "Failed to execute the query: " . $stmt->error]));
}

if ($result->num_rows > 0) {
    // Fetch all test cases
    $testcases = [];
    while ($row = $result->fetch_assoc()) {
        $testcases[] = $row;
    }
    echo json_encode($testcases); // Return test cases as JSON
} else {
    echo json_encode([]); // Return an empty array if no test cases are found
}

$stmt->close();
$conn->close();
?>