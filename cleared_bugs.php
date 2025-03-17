<?php
include 'db_config.php'; // Include database connection

// Fetch only bugs that were changed from 'Fail' to 'Pass'

		$sql = "SELECT id, product_name, version, Module_name, description, bug_type, expected_results, actual_result, 
               tested_by_name, tested_at  
        FROM testcase 
        WHERE testing_result = 'Pass' 
        AND bug_type = 'Nil' 
        ORDER BY tested_at DESC";


$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleared Bugs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

<h2>Cleared Bugs (Previously Failed)</h2>
<a href="bug_details.php" class="btn btn-primary mb-3">Back to Bug Details</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Version</th>
            <th>Module</th>
            <th>Description</th>
            <th>Expected Result</th>
            <th>Actual Result</th>
            <th>Tested By</th>
            <th>Tested At</th>
            
            
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['product_name']); ?></td>
                <td><?= htmlspecialchars($row['version']); ?></td>
                <td><?= htmlspecialchars($row['Module_name']); ?></td>
                <td><?= htmlspecialchars($row['description']); ?></td>
                <td><?= htmlspecialchars($row['expected_results']); ?></td>
                <td><?= htmlspecialchars($row['actual_result']); ?></td>
                <td><?= htmlspecialchars($row['tested_by_name']); ?></td>
                <td><?= date('d M Y, H:i', strtotime($row['tested_at'])); ?></td>
                
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
