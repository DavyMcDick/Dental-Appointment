<?php
include('../connec.php');

if (isset($_POST['submit'])) {
    $firstname = trim($_POST['fname']);
    $lastname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];

    // Check for empty fields
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required');</script>";
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
        exit();
    }

    // Check password match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match');</script>";
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT Email FROM admin WHERE Email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 1) { // Fix: Changed >1 to >0
        echo "<script>alert('Email already exists. Please use a different email.');</script>";
        exit();
    }
    $checkStmt->close();

    // Insert new user
    $insertStmt = $conn->prepare("INSERT INTO admin (Firstname, Lastname, Email, Password) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssss", $firstname, $lastname, $email, $hashedPassword);

    if ($insertStmt->execute()) {
        echo "<script>alert('Registration successful! Redirecting to login...'); window.location.href='adminlogin.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error storing data: " . $insertStmt->error . "');</script>";
    }

    $insertStmt->close();
    $conn->close();
}
?>