<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "UPDATE testcase SET testing_result = 'Pass', bug_type = 'Nil' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
