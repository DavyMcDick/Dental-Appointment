<?php
include('connect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM user WHERE ID = ?");
    $stmt->bind_param("i", $id);


    if ($stmt->execute()) {
        header("Location: appointments.php?message=success");
        exit();
    } else {
        header("Location: appointments.php?message=error");
        exit();
    }
}
?>