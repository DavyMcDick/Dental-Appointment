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

