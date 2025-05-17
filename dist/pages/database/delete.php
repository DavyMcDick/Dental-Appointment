<?php
include('connect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM appointment WHERE ID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: appointments.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
