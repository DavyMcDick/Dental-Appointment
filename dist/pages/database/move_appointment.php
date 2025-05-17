<?php
include('connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!empty($_GET['id']) && !empty($_GET['status'])) {
    $id = intval($_GET['id']); // Ensure ID is an integer
    $status = $_GET['status'];  // Expected values: "Confirmed" or "Cancelled"

    // Validate that status is one of the allowed values
    if (!in_array($status, ['Confirmed', 'Cancelled'])) {
        die("Invalid status value.");
    }

    // Prepare the update query to prevent SQL injection
    $stmt = $conn->prepare("UPDATE confirm SET status = ? WHERE ID = ?");
    $stmt->bind_param("si", $status, $id);
    

    if ($stmt->execute()) {
        // After updating, redirect back to the display page
        header("Location: confirm.php");
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
} else {
    // If no GET parameters are provided, simply redirect back
    header("Location: confirm.php");
    exit();
}

$conn->close();
?>



