<?php
include 'db_config.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['id'])) {
        echo json_encode(["error" => "Test case ID is missing!"]);
        exit;
    }

    $testcase_id = $_POST['id'];
    $bug_type = htmlspecialchars($_POST['bug_type']);
    $device_name = htmlspecialchars($_POST['device_name']);
    $android_version = htmlspecialchars($_POST['android_version']);
    $tested_by_name = htmlspecialchars($_POST['tested_by_name']);
    $tested_at = htmlspecialchars($_POST['tested_at']);
    $actual_result = htmlspecialchars($_POST['actual_result']);
    $testing_result = htmlspecialchars($_POST['testing_result']);

    $file_attachment = "";

    if (!empty($_FILES['file_attachment']['name'])) {
        $upload_dir = "uploads/";

        // Ensure the upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES["file_attachment"]["name"]);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'png', 'pdf', 'docx', 'xlsx'];

        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            echo json_encode(["error" => "Invalid file type!"]);
            exit;
        }

        $file_attachment = $upload_dir . uniqid() . "_" . $file_name;
        
        if (!move_uploaded_file($_FILES["file_attachment"]["tmp_name"], $file_attachment)) {
            echo json_encode(["error" => "File upload failed!"]);
            exit;
        }
    } else {
        // Keep the existing file if no new file uploaded
        $query = "SELECT file_attachment FROM testcase WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $testcase_id);
        $stmt->execute();
        $stmt->bind_result($existing_file);
        $stmt->fetch();
        $stmt->close();
        $file_attachment = $existing_file;
    }

    // Update Query
    $sql = "UPDATE testcase SET 
            bug_type=?, device_name=?, android_version=?, file_attachment=?, tested_by_name=?, 
            tested_at=?, actual_result=?, testing_result=? WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", 
        $bug_type, $device_name, $android_version, $file_attachment, $tested_by_name, 
        $tested_at, $actual_result, $testing_result, $testcase_id
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => "Test case updated successfully!"]);
    } else {
        echo json_encode(["error" => "Database update failed!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
