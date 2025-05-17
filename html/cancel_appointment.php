<?php
session_start();
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];
    $user_id = $_SESSION['user_id'];

    // Ensure the appointment belongs to the logged-in user and is still pending
    $stmt = $conn->prepare("UPDATE confirm SET Status = 'Cancelled' WHERE id = ? AND user_id = ? AND Status = 'Pending'");
    $stmt->bind_param("ii", $appointmentId, $user_id);

    if ($stmt->execute()) {
        echo "Appointment canceled successfully!";
    } else {
        echo "Error canceling appointment. Please try again.";
    }
} else {
    echo "Invalid request!";
}
?>