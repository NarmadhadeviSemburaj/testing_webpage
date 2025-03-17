<?php
session_start();
require 'db_config.php'; // Include database connection

// Ensure only admins can access
if (!isset($_SESSION['user']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

// Fetch folders dynamically
$folders = array_filter(glob('uploads/*'), 'is_dir');

// Fetch APK versions dynamically if requested via AJAX
if (isset($_GET['fetch_versions'])) {
    $folder = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['fetch_versions']); // Sanitize input
    $folder_path = "uploads/$folder";

    $versions = [];

    if (is_dir($folder_path)) {
        $files = array_values(array_diff(scandir($folder_path), array('.', '..')));

        foreach ($files as $file) {
            if (preg_match('/V\d+(\.\d+)+/', $file, $matches)) {
                $versions[] = ['filename' => $file, 'version' => $matches[0]];
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($versions);
    exit(); // Stop further execution for AJAX request
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $module_name = $_POST['module_name'];
    $description = $_POST['description'];
    $precondition = $_POST['precondition'];
    $test_steps = $_POST['test_steps'];
    $expected_results = $_POST['expected_results'];
    $bug_type = $_POST['bug_type'];
    $product_name = $_POST['product_name'];
    $version = $_POST['version'];

    // Validate inputs
    if (!empty($module_name) && !empty($description) && !empty($test_steps) && !empty($expected_results) && !empty($product_name) && !empty($version)) {
        $stmt = $conn->prepare("INSERT INTO testcase (Module_name, description, precondition, test_steps, expected_results, bug_type, Product_name, Version) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $module_name, $description, $precondition, $test_steps, $expected_results, $bug_type, $product_name, $version);

        if ($stmt->execute()) {
            echo "<script>alert('Test case added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding test case.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Case Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center text-primary">Test Case Management</h2>

        <div class="card shadow p-4">
            <h4 class="text-center">Add Test Case</h4>
            <form method="POST">
                <div class="mb-3">
                    <label>Select Product:</label>
                    <select id="folderSelect" name="product_name" class="form-select" required>
                        <option value="">Select Product</option>
                        <?php foreach ($folders as $folder): ?>
                            <option value="<?= basename($folder) ?>"><?= basename($folder) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Select Version:</label>
                    <select id="versionSelect" name="version" class="form-select" required disabled>
                        <option value="">Select Version</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Module Name:</label>
                    <input type="text" name="module_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description:</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Precondition:</label>
                    <textarea name="precondition" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label>Test Steps:</label>
                    <textarea name="test_steps" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Expected Results:</label>
                    <textarea name="expected_results" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Bug Type:</label>
                    <select name="bug_type" class="form-select">
                        <option value="UI">UI</option>
                        <option value="Functional">Functional</option>
                        <option value="Performance">Performance</option>
                        <option value="Security">Security</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit Test Case</button>
            </form>
        </div>
    </div>

    <footer class="text-center mt-4">Test Case Management System © 2025</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let versionMap = {}; // Stores file names mapped to version names

        document.getElementById("folderSelect").addEventListener("change", function() {
            let folder = this.value;
            let versionSelect = document.getElementById("versionSelect");

            versionSelect.innerHTML = "<option value=''>Loading...</option>";
            versionSelect.disabled = true;
            versionMap = {}; // Reset version mapping

            if (folder) {
                fetch(testing.php?fetch_versions=${encodeURIComponent(folder)})
                .then(response => response.json())
                .then(data => {
                    console.log("Received Versions:", data); // Debugging

                    versionSelect.innerHTML = "<option value=''>Select Version</option>";

                    data.forEach(item => {
                        versionMap[item.version] = item.filename; // Store filename
                        versionSelect.innerHTML += <option value="${item.version}">${item.version}</option>;
                    });

                    versionSelect.disabled = false;
                })
                .catch(error => {
                    console.error("Error fetching versions:", error);
                });
            } else {
                versionSelect.innerHTML = "<option value=''>Select Version</option>";
                versionSelect.disabled = true;
            }
        });
    </script>
</body>
</html>