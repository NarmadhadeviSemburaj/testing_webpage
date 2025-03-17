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

$product = isset($_GET['product']) ? $_GET['product'] : '';
$version = isset($_GET['version']) ? $_GET['version'] : '';

// Build the SQL query based on filters
$sql = "SELECT * FROM testcase WHERE 1=1";
if (!empty($product)) {
    $sql .= " AND Product_name = '" . $conn->real_escape_string($product) . "'";
}
if (!empty($version)) {
    $sql .= " AND Version = '" . $conn->real_escape_string($version) . "'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='table table-bordered table-striped'>
            <thead class='table-primary'>
                <tr>
                    <th>Product</th>
                    <th>Version</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>Preconditions</th>
                    <th>Test Steps</th>
                    <th>Expected Results</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['Product_name']) . "</td>
                <td>" . htmlspecialchars($row['Version']) . "</td>
                <td>" . htmlspecialchars($row['Module_name']) . "</td>
                <td>" . htmlspecialchars($row['description']) . "</td>
                <td>" . htmlspecialchars($row['preconditions'] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($row['test_steps']) . "</td>
                <td>" . htmlspecialchars($row['expected_results']) . "</td>
                <td>
                    <button class='btn btn-warning btn-sm edit-btn' data-id='" . $row['id'] . "' data-bs-toggle='modal' data-bs-target='#testCaseModal'>Edit</button>
                    <a href='delete_testcase1.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure?');\">Delete</a>
                </td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='text-center'>No test cases found for the selected filters.</p>";
}

$conn->close();
?>