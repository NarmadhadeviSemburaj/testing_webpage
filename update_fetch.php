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

// Define the current page
$current_page = basename($_SERVER['PHP_SELF']);

// Assume logged-in user's name is stored in the session
$logged_in_user = $_SESSION['emp_name'] ?? 'Unknown';

// Fetch distinct products
$sql_products = "SELECT DISTINCT Product_name FROM testcase";
$result_products = $conn->query($sql_products);

// Fetch distinct versions
$sql_versions = "SELECT DISTINCT Version FROM testcase";
$result_versions = $conn->query($sql_versions);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Test Case</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Your existing CSS styles */
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
            position: sticky;
            top: 20px;
            align-self: flex-start;
            height: calc(100vh - 40px);
            overflow-y: auto;
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
        .sidebar a:hover, .sidebar a.active {
            background-color: #007bff;
            color: #fff;
        }
        .content-container {
            flex: 1;
            background-color: #fff; /* White background */
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-x: auto; /* Enable horizontal scrolling */
        }
        .table {
            width: 100%; /* Make the table take up the full width of its container */
        }
        .table td, .table th {
            white-space: nowrap; /* Prevent text from wrapping */
            min-width: 150px; /* Set a minimum width for each column */
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
        .table tbody tr:hover {
            background-color: #f1f1f1; /* Light grey on hover */
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .user-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .user-info i {
            font-size: 20px;
            margin-right: 5px;
        }
        .user-info h4 {
            font-size: 16px;
            margin: 5px 0 0;
            color: #333;
        }
        .admin-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .admin-section h4 {
            cursor: pointer;
            margin-bottom: 10px;
        }
        .admin-links {
            display: none; /* Initially hidden */
        }
        /* Move Download APK button to the top-right */
        .download-apk-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar-container">
            <!-- User Info Section -->
            <div class="user-info">
                <i class="fas fa-user"></i>
                <h4><?php echo htmlspecialchars($_SESSION['user']); ?></h4>
            </div>

            <!-- Sidebar Menu -->
            <div class="sidebar">
                <a href="summary.php" class="<?php echo ($current_page == 'summary.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="update_tc3.php" class="<?php echo ($current_page == 'update_tc3.php') ? 'active' : ''; ?>">
                    <i class="fas fa-vial"></i> Testing
                </a>
                <a href="bug_details.php" class="<?php echo ($current_page == 'bug_details.php') ? 'active' : ''; ?>">
                    <i class="fas fa-bug"></i> Bug Reports
                </a>
                <a href="logout.php" class="text-danger <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>

                <?php if ($_SESSION['is_admin']): ?>
                    <div class="admin-section">
                        <h4 onclick="toggleAdminLinks()"><i class="fas fa-cogs"></i> Admin <i class="fas fa-cogs"></i></h4>
                        <div class="admin-links">
                            <a href="employees.php" class="<?php echo ($current_page == 'employees.php') ? 'active' : ''; ?>">
                                <i class="fas fa-users"></i> Employees
                            </a>
                            <a href="apk_up.php" class="<?php echo ($current_page == 'apk_up.php') ? 'active' : ''; ?>">
                                <i class="fas fa-upload"></i> APK Admin
                            </a>
                            <a href="index1.php" class="<?php echo ($current_page == 'index1.php') ? 'active' : ''; ?>">
                                <i class="fas fa-list-alt"></i> TCM
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-container">
            <!-- Download APK Button (Moved to Top-Right) -->
            <a href="fetch1.php" class="btn btn-primary download-apk-btn">
                <i class="fas fa-download"></i> Download APK
            </a>

            <!-- Rest of the content -->
            <h2>Testing</h2>
            <form method="POST" class="mb-4" id="filterForm">
                <div class="row">
                    <div class="col-md-6">
                        <label for="product_name" class="form-label">Select Product:</label>
                        <select name="product_name" id="product_name" required class="form-select">
                            <option value="">-- Select Product --</option>
                            <?php while ($row = $result_products->fetch_assoc()) { ?>
                                <option value="<?= htmlspecialchars($row['Product_name']); ?>">
                                    <?= htmlspecialchars($row['Product_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="version" class="form-label">Select Version:</label>
                        <select name="version" id="version" required class="form-select">
                            <option value="">-- Select Version --</option>
                            <?php while ($row = $result_versions->fetch_assoc()) { ?>
                                <option value="<?= htmlspecialchars($row['Version']); ?>">
                                    <?= htmlspecialchars($row['Version']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Container to display test cases dynamically -->
            <div id="testCasesContainer"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to fetch test cases dynamically
        

        // Add event listeners to dropdowns
        document.getElementById('product_name').addEventListener('change', fetchTestCases);
        document.getElementById('version').addEventListener('change', fetchTestCases);
    </script>
</body>
</html>