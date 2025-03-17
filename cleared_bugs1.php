<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

// Fetch only cleared (passed) test cases
$sql = "SELECT * FROM failed_testcases WHERE testing_result = 'Pass' ORDER BY tested_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("<div class='alert alert-danger'>Query Failed: " . $conn->error . "</div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleared Bugs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #f0f0f0; /* Light Grey Background */
        }
        .wrapper {
            display: flex;
            min-height: 100vh;
            padding: 20px;
        }
        .sidebar-container {
            width: 200px;
            background-color: #fff; /* Sidebar color */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            margin: 10px 0;
            text-decoration: none;
            color: #333;
            border-radius: 10px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .content-container {
            flex: 1;
            background-color: #fff; /* White background */
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .table thead th {
            background-color: #007bff; /* Blue background for table header */
            color: #fff; /* White text for table header */
            padding: 12px; /* Increased padding */
            font-size: 14px; /* Slightly larger font size */
            text-align: center; /* Center align header text */
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05); /* Light grey for odd rows */
        }
        .table td, .table th {
            padding: 10px; /* Increased padding for table cells */
            font-size: 13px; /* Slightly smaller font size for cells */
            vertical-align: middle; /* Center align cell content vertically */
        }
        .table tbody tr:hover {
            background-color: #f1f1f1; /* Light grey on hover */
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar in a separate container -->
        <div class="sidebar-container">
            <h3>Menu</h3>
            <div class="sidebar">
                <a href="summary.php">Dashboard</a>
                <a href="employees.php">Employees</a>
                <a href="apk_up.php">APK Admin</a>
                <a href="fetch1.php">APK Download</a>
                <a href="index1.php">Test Cases</a>
                <a href="update_tc3.php">Testing</a>
                <a href="bug_details.php">Bug Reports</a>
                <a href="logout.php" class="text-danger">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-container">
            <h2>Cleared Bugs</h2>
            <a href="bug_details.php" class="btn btn-primary mb-3">Back to Bug Details</a>

            <?php if ($result->num_rows > 0) { ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Test Case ID</th>
                            <th>Product Name</th>
                            <th>Version</th>
                            <th>Module</th>
                            <th>Bug Type</th>
                            <th>Device Name</th>
                            <th>Android Version</th>
                            <th>Actual Result</th>
                            <th>Tested By</th>
                            <th>Tested At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($row['failed_id']); ?></td>
                                <td><?= htmlspecialchars($row['testcase_id']); ?></td>
                                <td><?= htmlspecialchars($row['product_name']); ?></td>
                                <td><?= htmlspecialchars($row['version']); ?></td>
                                <td><?= htmlspecialchars($row['module_name']); ?></td>
                                <td><?= htmlspecialchars($row['bug_type']); ?></td>
                                <td><?= htmlspecialchars($row['device_name']); ?></td>
                                <td><?= htmlspecialchars($row['android_version']); ?></td>
                                <td><?= htmlspecialchars($row['actual_result']); ?></td>
                                <td><?= htmlspecialchars($row['tested_by_name']); ?></td>
                                <td><?= date('d M Y, H:i', strtotime($row['tested_at'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="alert alert-warning">No cleared bugs found.</div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>