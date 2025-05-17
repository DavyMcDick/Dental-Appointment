<?php
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("UPDATE user SET Firstname=?, Lastname=?, Email=?, Password=? WHERE ID=?");
    $stmt->bind_param("ssssi", $name, $lname, $email, $password, $id);

    if ($stmt->execute()) {
        header("Location: appointments.php?message=updated");
    } else {
        header("Location: appointments.php?message=update_error");
    }
    exit();
}
?>
