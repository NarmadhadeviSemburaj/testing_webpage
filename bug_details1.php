<?php
session_start(); // Start the session
include 'db_config.php';

// Handle "Clear" button action (AJAX request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Get the session user (assuming it's stored in $_SESSION['username'])
    $cleared_by = isset($_SESSION['user']) ? $_SESSION['user'] : 'Unknown';

    // Prepare statement to update the testcase with NOW() for result_changed_at
    $sql = "UPDATE testcase 
            SET testing_result = 'Pass', 
                bug_type = NULL, 
                result_changed_at = NOW(), 
                cleared_flag = 1, 
                cleared_by = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cleared_by, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    
    $stmt->close();
    $conn->close();
    exit;
}

// Fetch distinct product names, versions, and bug types
$sql_products = "SELECT DISTINCT Product_name FROM testcase";
$result_products = $conn->query($sql_products);

$sql_versions = "SELECT DISTINCT Version FROM testcase";
$result_versions = $conn->query($sql_versions);

$sql_bug_types = "SELECT DISTINCT bug_type FROM testcase WHERE bug_type IS NOT NULL";
$result_bug_types = $conn->query($sql_bug_types);

// Define the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Failed Test Cases</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
        .user-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .user-info i {
            font-size: 18px;
            margin-right: 5px;
        }
        .user-info h4 {
            font-size: 14px;
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
        .filter-section {
            margin-bottom: 20px;
        }
        .clear-bugs-btn {
            margin-bottom: 20px;
            text-align: right;
        }
        /* Ensure table is scrollable */
        .table-responsive {
            overflow-x: auto;
        }
        /* File attachment styling */
        .file-attachment img, .file-attachment video {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
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
                        <h4 onclick="toggleAdminLinks()"><i class="fas fa-cogs"></i> Admin <i class="fas fa-chevron-down"></i></h4>
                        <div class="admin-links">
                            <a href="employees.php" class="<?php echo ($current_page == 'employees.php') ? 'active' : ''; ?>">
                                <i class="fas fa-users"></i> Employees
                            </a>
                            <a href="apk_up.php" class="<?php echo ($current_page == 'apk_up.php') ? 'active' : ''; ?>">
                                <i class="fas fa-upload"></i> APK Admin
                            </a>
                            <a href="index1.php" class="<?php echo ($current_page == 'index1.php') ? 'active' : ''; ?>">
                                <i class="fas fa-list-alt"></i> Test Case Manager
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-container">
            <!-- "View Cleared Bugs" Button -->
            <div class="clear-bugs-btn">
                <a href="cleared_bugs7.php" class="btn btn-success">View Cleared Bugs</a>
            </div>

            <h2>Failed Test Cases</h2>

            <!-- Filter Section -->
            <div class="row filter-section">
                <div class="col-md-4">
                    <label for="filterProduct">Filter by Product:</label>
                    <select id="filterProduct" class="form-control">
                        <option value="">All</option>
                        <?php while ($row = $result_products->fetch_assoc()) { ?>
                            <option value="<?php echo htmlspecialchars($row['Product_name']); ?>">
                                <?php echo htmlspecialchars($row['Product_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterVersion">Filter by Version:</label>
                    <select id="filterVersion" class="form-control">
                        <option value="">All</option>
                        <?php while ($row = $result_versions->fetch_assoc()) { ?>
                            <option value="<?php echo htmlspecialchars($row['Version']); ?>">
                                <?php echo htmlspecialchars($row['Version']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterBugType">Filter by Bug Type:</label>
                    <select id="filterBugType" class="form-control">
                        <option value="">All</option>
                        <?php while ($row = $result_bug_types->fetch_assoc()) { ?>
                            <option value="<?php echo htmlspecialchars($row['bug_type']); ?>">
                                <?php echo htmlspecialchars($row['bug_type']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Failed Test Cases Table -->
            <div class="table-responsive">
                <table class="table table-bordered mt-4" id="failedTestCasesTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Version</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>Bug Type</th>
                            <th>Device</th>
                            <th>Android Version</th>
                            <th>Tested By</th>
                            <th>Tested At</th>
                            <th>Actual Result</th>
                            <th>Expected Result</th>
                            <th>Attachment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM testcase WHERE testing_result = 'Fail'";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row_" . $row['id'] . "'>
                                    <td>" . htmlspecialchars($row['Product_name']) . "</td>
                                    <td>" . htmlspecialchars($row['Version']) . "</td>
                                    <td>" . htmlspecialchars($row['Module_name']) . "</td>
                                    <td>" . htmlspecialchars($row['description']) . "</td>
                                    <td>" . htmlspecialchars($row['bug_type']) . "</td>
                                    <td>" . htmlspecialchars($row['device_name']) . "</td>
                                    <td>" . htmlspecialchars($row['android_version']) . "</td>
                                    <td>" . htmlspecialchars($row['tested_by_name']) . "</td>
                                    <td>" . date('Y-m-d H:i', strtotime($row['tested_at'])) . "</td>
                                    <td>" . htmlspecialchars($row['actual_result']) . "</td>
                                    <td>" . htmlspecialchars($row['expected_results']) . "</td>
                                    <td class='file-attachment'>";

                            if (!empty($row['file_attachment'])) {
                                $file_url = htmlspecialchars($row['file_attachment'], ENT_QUOTES, 'UTF-8');
                                $file_extension = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));

                                $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                $video_extensions = ['mp4', 'webm', 'ogg'];

                                if (in_array($file_extension, $image_extensions)) {
                                    echo '<img src="' . $file_url . '" alt="Attachment">';
                                } elseif (in_array($file_extension, $video_extensions)) {
                                    echo '<video width="120" height="100" controls>
                                            <source src="' . $file_url . '" type="video/mp4">
                                          </video>';
                                } else {
                                    echo '<a href="' . $file_url . '" target="_blank">View File</a>';
                                }
                            } else {
                                echo "No File";
                            }
                            
                            echo "</td>
                                    <td>
                                        <button class='btn btn-danger clear-btn' data-id='" . $row['id'] . "'>Clear</button>
                                    </td>
                                </tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            let table = $('#failedTestCasesTable').DataTable();

            function applyFilters() {
                let product = $('#filterProduct').val().toLowerCase();
                let version = $('#filterVersion').val().toLowerCase();
                let bugType = $('#filterBugType').val().toLowerCase();
                
                table.columns(0).search(product);
                table.columns(1).search(version);
                table.columns(4).search(bugType);
                table.draw();
            }

            $('#filterProduct, #filterVersion, #filterBugType').on('change', applyFilters);

            $('.clear-btn').click(function () {
                let testCaseId = $(this).data('id');
                if (confirm("Are you sure you want to clear this test case?")) {
                    $.post('', { id: testCaseId }, function (response) {
                        if (response.trim() === 'success') {
                            $('#row_' + testCaseId).fadeOut();
                        } else {
                            alert('Error updating test case');
                        }
                    });
                }
            });
        });

        // Function to toggle the visibility of admin links
        function toggleAdminLinks() {
            const adminLinks = document.querySelector('.admin-links');
            adminLinks.style.display = adminLinks.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>