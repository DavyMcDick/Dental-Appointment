<?php
session_start(); // Start session to access user ID
include('connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: booking.php"); // Redirect if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$error_message = ""; // Store error messages

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $age = intval($_POST['age']);
    $email = $_POST['email'];   
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $procedure = $_POST['procedure'];
    $time = $_POST['estimated_time'];
    $date = $_POST['date'];

    // Check if fields are empty
    if (empty($name) || empty($age) || empty($email) || empty($contact) || empty($address) || empty($procedure) || empty($date)) {
        $error_message = "All fields are required.";
    }

    // Validate procedure selection
    $validProcedures = ['Braces', 'Xray', 'Consultation', 'Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Surgery'];
    if (!in_array($procedure, $validProcedures)) {
        $error_message = "Invalid Procedure Selected.";
    }

    // Insert data if no errors
    if (!$error_message) {
        $insertStmt = $conn->prepare("INSERT INTO confirm (user_id, Names, Age, Email, Contact, Address, Procedures, Time, Dates, Status) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $insertStmt->bind_param("isissssss", $user_id, $name, $age, $email, $contact, $address, $procedure, $time, $date);

        if ($insertStmt->execute()) {
            header("Location: my_appointment.php?success=1"); // Redirect to 'My Appointment' page
            exit();
        } else {
            $error_message = "Data Not Stored: " . $insertStmt->error;
        }
        $insertStmt->close();
    }

    $conn->close();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dental Appointment</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="../style/style.css">
</head>  
<style>
.error-message {
    background: #ffdddd;
    color: #d8000c;
    padding: 10px;
    border-left: 5px solid #d8000c;
    margin-bottom: 15px;
    border-radius: 5px;
}

.success-message {
    background: #ddffdd;
    color: #4f8a10;
    padding: 10px;
    border-left: 5px solid #4f8a10;
    margin-bottom: 15px;
    border-radius: 5px;
}

</style>
<body>
  <div class="main">


    <!-- HOME SECTION -->

    <div class="home-section" id="home">
      <div class="nav-section">
        <div class="logo-container"><i class="fa-solid fa-tooth" style="color: rgb(22, 22, 150)"></i>Kho Prado</div>
          <ul class="menu-container">
            <li><a href="book.php">Book Appointment</a></li> 
            <li><a href="appointment.php">My Appointment</a></li>
            <li><a href="appointment.php">Schedule</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <a href="../index.php">
                <button class="btn1">Logout</button>
            </a>
          </ul>
      </div>
      <div class="hero-wrapper">
       <div class="book-main">
        <div class="hero-text-container"><section class="appointment" id="appointment">
        <div class="container" style="margin: 30px auto;">
    <h2>Appointment Request Form</h2>
    <p>Please be informed that this is not yet a confirmed booking. Our Patient Support Team will contact you to confirm your appointment. Thank you.</p>

    <!-- Error Message Display -->
    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Success Message -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="success-message">Appointment successfully booked!</div>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" >

        <label for="age">Age</label>
        <input type="number" id="age" name="age">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" style="text-transform: lowercase;" >

        <label for="contact">Contact Number</label>
        <input type="tel" id="contact" name="contact" maxlength="11" >

        <label for="contact">Address</label>
        <input type="text" id="address" name="address" >

        <label for="procedure">Procedure</label>
        <select id="procedure" name="procedure" onchange="updateEstimatedTime()" >
            <option value="">-- Select --</option>
            <option value="Braces">Braces</option>
            <option value="Xray">Xray</option>
            <option value="Consultation">Consultation</option>
            <option value="Extraction">Extraction</option>
            <option value="Root Canal Treatment">Root Canal Treatment</option>
            <option value="Teeth Whitening">Teeth Whitening</option>
            <option value="Surgery">Surgery</option>
        </select>

        <label for="estimated_time">Estimated Time:</label>
        <input type="text" id="estimated_time" name="estimated_time" readonly>

        <label for="date">Preferred Date</label>
        <input type="date" id="date" name="date" min="<?= date('Y-m-d'); ?>" >

        <button type="submit" name="submit" style="background-color: blue;">Submit</button>
    </form>
</div>


   <div class="loader" id="loader">
    <svg
      xmlns:xlink="https://www.w3.org/1999/xlink"
      xmlns="https://www.w3.org/2000/svg"
      width="550"
      height="210"
      viewBox="0 0 550 210"
    >
      <path
        d="m0,130.08h44.51c7.08-3.45,11.54-24.65,19.42-24.81s13.23,22.54,21.03,24.81c10.03,2.92,29.69-14.6,39.91-12.4,4.58.98,9.34,12.36,14.02,12.4,3.54.03,7.25-9.31,10.79-9.17,3.24.13,6.17,7.93,9.17,9.17s9.68-1.48,12.4,0c2.4,1.3,3.45,10.3,5.93,9.17,3.23-1.48,2.82-103.01,8.09-103.01,6.96,0,12.35,137.53,16.72,137.53,3.9,0-.09-36.61,8.49-43.69,3.41-2.81,13.69,1.93,17.66,0,7.17-3.49,11.72-24.71,19.69-25.08,8.62-.4,15.39,22.86,23.73,25.08,8.99,2.38,26.51-12.76,35.6-10.79,5.58,1.21,11.46,15.82,17.12,15.1,3.88-.49,4.87-12.59,8.76-12.94,3.01-.28,5.7,7.46,8.49,8.63s9.75-1.43,12.54,0,4.03,9.39,7.01,9.71c4.98.54,2.64-103.55,8.63-103.55,5.16,0,8.8,111.51,12.94,111.64,5.02.16,5.01-15.2,9.3-17.8s15.02,2.06,19.42,0c7.39-3.46,12.74-25.17,20.9-25.08,8.97.09,13.68,25.85,22.38,28.04,9.17,2.31,25.4-15.93,34.79-14.83,4.95.58,11.31,10.3,16.04,11.87h44.51"
        stroke-linejoin="round"
        stroke-width="2"
        fill="none"
        stroke="#3492eb"
      ></path>
    </svg>
  </div>

<script src="../dist/javascript/procedure.js"></script>
  <script>
    var loader = document.getElementById("loader");
    var content = document.getElementById("content");

    // Wait for the page to load
    window.addEventListener("load", function () {
      setTimeout(function () {
        // Fade out the loader after the page is fully loaded
        loader.classList.add("hidden");

        // Show the content
        content.style.display = "block";
      },5000); // Optional: add a delay before hiding the loader
    });
  </script>
</body>







<?php
include('connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);



if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $age = intval($_POST['age']);
    $email = $_POST['email'];   
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $procedure = $_POST['procedure'];
    $time = $_POST['estimated_time'];
    $date = $_POST['date'];

    // Check if fields are empty
    if (empty($name) || empty($age) || empty($email) || empty($contact) || empty($address) || empty($procedure) || empty($date)) {
        $error_message = "All fields are required.";
    }

    // Validate procedure selection
    $validProcedures = ['Braces', 'Xray', 'Consultation', 'Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Surgery'];
    if (!in_array($procedure, $validProcedures)) {
        $error_message = "Invalid Procedure Selected.";
    }

    // Check if email already exists
    if (!$error_message) {
        $checkStmt = $conn->prepare("SELECT Email FROM confirm WHERE Email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error_message = "Email already exists. Please use a different email.";
        }
        $checkStmt->close();
    }

    // Insert data if no errors
    if (!$error_message) {
        $insertStmt = $conn->prepare("INSERT INTO confirm (Names, Age, Email, Contact, Address, Procedures, Time, Dates) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sissssss", $name, $age, $email, $contact, $address, $procedure, $time, $date);

        if ($insertStmt->execute()) {
            header("Location: book.php?success=1"); // Redirect with success message
            exit();
        } else {
            $error_message = "Data Not Stored: " . $insertStmt->error;
        }
        $insertStmt->close();
    }

    $conn->close();
}
?>
