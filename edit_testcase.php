<?php
$conn = new mysqli("localhost", "root", "", "testing_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM testcase WHERE id = $id");
$row = $result->fetch_assoc();

if (!$row) {
    die("Test case not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Test Case</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Test Case</h2>
        <form method="post" action="update_testcase.php">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Product Name:</label>
                <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($row['Product_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Version:</label>
                <input type="text" name="version" class="form-control" value="<?= htmlspecialchars($row['Version']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Module Name:</label>
                <input type="text" name="module_name" class="form-control" value="<?= htmlspecialchars($row['Module_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control" required><?= htmlspecialchars($row['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Preconditions:</label>
                <textarea name="precondition" class="form-control" required><?= htmlspecialchars($row['precondition']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Test Steps:</label>
                <textarea name="test_steps" class="form-control" required><?= htmlspecialchars($row['test_steps']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Expected Results:</label>
                <textarea name="expected_results" class="form-control" required><?= htmlspecialchars($row['expected_results']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Test Case</button>
        </form>
    </div>
</body>
</html>
