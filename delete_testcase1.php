<?php
$conn = new mysqli("localhost", "root", "", "testing_db");

$id = $_GET['id'];
$conn->query("DELETE FROM testcase WHERE id=$id");

$conn->close();
header("Location: index1.php");
exit();
?>
