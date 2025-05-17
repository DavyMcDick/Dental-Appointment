<?php
session_start();
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $is_available = $_POST['is_available'] ?? '';

    if (empty($date) || $is_available === '') {
        echo "Invalid input.";
        exit;
    }

    $is_available = intval($is_available);

    // Use a prepared statement to update availability
    $stmt = $conn->prepare("REPLACE INTO availability (date, is_available) VALUES (?, ?)");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }
    $stmt->bind_param("si", $date, $is_available);
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
