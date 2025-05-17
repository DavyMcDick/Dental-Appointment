<?php
session_start();
include('connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$error_message = "";
$selected_procedures = [];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT date FROM availability WHERE is_available = 0";
$result = $conn->query($sql);

$unavailableDates = [];
while ($row = $result->fetch_assoc()) {
  $unavailableDates[] = $row['date'];
}

$unavailableDatesJson = json_encode($unavailableDates); // <== Add this

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $name    = trim($_POST['name']);
    $age     = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $email   = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $time    = trim($_POST['estimated_time']);
    $date = isset($_POST['appointmentDate']) ? trim($_POST['appointmentDate']) : '';

    // Get selected procedures and teeth numbers from the form
    $selected_procedures = isset($_POST['procedure']) ? (array) $_POST['procedure'] : [];
    $teeth_counts        = isset($_POST['teeth']) ? $_POST['teeth'] : [];

    $validProcedures = [
        'Braces', 'Xray', 'Consultation', 
        'Teeth Extraction', 'Root Canal Treatment', 
        'Teeth Whitening', 'Filling'
    ];

    // Procedures that require a teeth count input
    $teethRequiredProcedures = [
        'Teeth Extraction', 
        'Root Canal Treatment', 
        'Teeth Whitening',
        'Filling'
    ];

    // Validate required fields
    if (!$name || !$age || !$email || !$contact || !$address || empty($selected_procedures) || !$date) {
        $error_message = "All fields are required.";
    } elseif (array_diff($selected_procedures, $validProcedures)) {
        $error_message = "Invalid Procedure Selected.";
    } else {
        // Build a string with all selected procedures AND track total teeth
        $procedureList = [];
        $total_teeth   = 0;  // Accumulate all tooth counts here

        foreach ($selected_procedures as $proc) {
            // If procedure requires a teeth count, retrieve it
            if (in_array($proc, $teethRequiredProcedures)) {
                $teeth = (isset($teeth_counts[$proc]) && is_numeric($teeth_counts[$proc]))
                         ? (int)$teeth_counts[$proc]
                         : 1; // Default to 1 if not provided
                
                // Add to total
                $total_teeth += $teeth;
                
                // Build string like "Extraction (2 teeth)"
                $procedureList[] = $proc . " (" . $teeth . " teeth)";
            } else {
                // Procedures without tooth count
                $procedureList[] = $proc;
            }
        }

        // Convert the procedures array into a single comma-separated string
        $procedure_str = implode(', ', $procedureList);

        $status = "Pending"; // Default status for new appointments

        $insertStmt = $conn->prepare("
            INSERT INTO confirm 
            (user_id, Names, Age, Email, Contact, Address, Procedures, Teeth, Time, Dates, Status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if (!$insertStmt) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters, with $total_teeth for the 'teeth' column
        $insertStmt->bind_param(
            "isissssisss", 
            $user_id, 
            $name, 
            $age, 
            $email, 
            $contact, 
            $address, 
            $procedure_str, 
            $total_teeth, 
            $time, 
            $date,
            $status
        );


        if ($insertStmt->execute()) {
            $_SESSION['success_message'] = "Appointment successfully booked!";
            header("Location: booking.php?success=1");
            exit();
        } else {
            $error_message = "Data Not Stored: " . $insertStmt->error;
        }
        $insertStmt->close();
    }
}
?>



<!-- The rest of your HTML form stays mostly the same -->










<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dental Appointment</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="../style/style.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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

.dropdown-checkbox {
    position: relative;
    width: 100%;
}

.dropdown-button {
    padding: 10px;
    background: #f0f0f0;
    border: 1px solid #ccc;
    cursor: pointer;
    border-radius: 4px;
}

.dropdown-list {
    display: none;
    position: absolute;
    background: #fff;
    border: 1px solid #ccc;
    z-index: 10;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    border-radius: 4px;
    padding: 10px;
}


.dropdown-checkbox {
    position: relative;
    width: 100%;
}

.dropdown-button {
    padding: 10px;
    background: #f0f0f0;
    border: 1px solid #ccc;
    cursor: pointer;
    border-radius: 4px;
}

.dropdown-list {
    display: none;
    position: absolute;
    background: #fff;
    border: 1px solid #ccc;
    z-index: 10;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    border-radius: 4px;
    padding: 10px;
}

.dropdown-list label {
    display: block;
    margin-bottom: 5px;
    cursor: pointer;
}



</style>
<body>
  <div class="main">


    <!-- HOME SECTION -->

    <div class="home-section" id="home">
      <div class="nav-section">
      <div class="logo-container">
          <img src="../assets/kho_prado.jpg" alt="" height="150px" margin="20px" font-size="0">
        </div>
          <ul class="menu-container">
            <li><a href="book.php">Book Appointment</a></li> 
            <li><a href="appointment.php">My Appointment</a></li>
            <li><a href="schedule.php">Schedule</a></li>
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
    <input type="text" id="name" name="name">

    <label for="age">Age</label>
    <input type="number" id="age" name="age" min="4" max="99">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" style="text-transform: lowercase;">

    <label for="contact">Contact Number</label>
    <input type="tel" id="contact" name="contact" maxlength="11">

    <label for="contact">Address</label>
    <input type="text" id="address" name="address">

    <label for="appointment-date">Preferred Date</label>
    <input type="text" class="form-control" id="appointmentDate"   name="appointmentDate" placeholder="Select a date" required readonly>

    <label for="procedure">Procedures</label>
    <div class="dropdown-checkbox">
    <div class="dropdown-button" onclick="toggleDropdown()" id="selected-procedures-btn">
        -- Select Procedures --
    </div>

    <div class="dropdown-list" id="procedure-dropdown">
        <?php
        $procedures = [
            'Braces' => false,
            'Xray' => false,
            'Consultation' => false,
            'Teeth Extraction' => true,
            'Root Canal Treatment' => true,
            'Teeth Whitening' => true,
            'Filling' => true
        ];

        foreach ($procedures as $proc => $requiresTeeth) {
            echo '<label>
                    <input type="checkbox" name="procedure[]" value="' . $proc . '" onchange="updateSelectedProceduresDisplay(); updateEstimatedTime();"> ' . $proc . '
                </label>';
            if ($requiresTeeth) {
                echo '<input type="number" class="teeth-input" name="teeth[' . $proc . ']" placeholder="Number of Teeth" min="1" max="34" style="display:none;" oninput="updateEstimatedTime()">';
            }
        }
        ?>
    </div>
</div>
    <label for="estimated_time">Estimated Time:</label>
    <input type="text" id="estimated_time" name="estimated_time" readonly>


    <button type="submit" name="submit" style="background-color: blue;">Submit</button>
</form>

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
  <script>
function toggleDropdown() {
    const list = document.getElementById('procedure-dropdown');
    list.style.display = (list.style.display === "block") ? "none" : "block";
}

// Function to update estimated time based on selected procedures
function updateEstimatedTime() {
    const procedureTimes = {
        'Braces': 30,  // Fixed time, does not depend on teeth count
        'Xray': 10,  
        'Consultation': 15,  
        'Teeth Extraction': 30,  // Per tooth
        'Root Canal Treatment': 60,  // Per tooth
        'Teeth Whitening': 20,  // Per tooth
        'Filling': 45  // Per tooth
    };

    let totalTime = 0;

    // Get all selected procedures
  const checkedProcedures = document.querySelectorAll('input[name="procedure[]"]:checked');
checkedProcedures.forEach(procCheckbox => {
    let procName = procCheckbox.value;
    let baseTime = procedureTimes[procName] || 0;

    // Check if procedure requires teeth count
    let isToothBased = ['Teeth Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Filling'].includes(procName);
    if (isToothBased) {
        let teethInput = procCheckbox.parentElement.nextElementSibling;
        let teethCount = teethInput && teethInput.value ? parseInt(teethInput.value) : 1;
        
        // Calculate time
        totalTime += baseTime * teethCount;
        
        // Adjust display text based on count
        let toothText = teethCount === 1 ? 'tooth' : 'teeth';
        console.log(`${procName} for ${teethCount} ${toothText}`);
    } else {
        totalTime += baseTime;
    }
});

    // Ensure "mins" is always displayed, even if totalTime is 0
    const timeElement = document.getElementById('estimated_time');
    timeElement.value = totalTime + ' mins';
}

// Function to update dropdown button with selected procedures
function updateSelectedProceduresDisplay() {
    const selectedCheckboxes = document.querySelectorAll('input[name="procedure[]"]:checked');
    const selectedValues = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    // Update dropdown button text
    const button = document.getElementById("selected-procedures-btn");
    button.innerText = selectedValues.length > 0 ? selectedValues.join(', ') : "-- Select Procedures --";

    // Show or hide teeth number input fields based on selection
    document.querySelectorAll('.teeth-input').forEach(input => {
        input.style.display = "none"; // Hide all first
    });

    selectedCheckboxes.forEach(cb => {
        let teethInput = cb.parentElement.nextElementSibling;
        if (teethInput && teethInput.classList.contains('teeth-input')) {
            teethInput.style.display = "block";
        }
    });

    // Also update estimated time when procedures are selected
    updateEstimatedTime();
}

// Attach event listeners to all checkboxes
document.querySelectorAll('input[name="procedure[]"]').forEach(cb => {
    cb.addEventListener("change", updateSelectedProceduresDisplay);
});

// Run on page load to update button with previously selected procedures
document.addEventListener("DOMContentLoaded", function() {
    updateSelectedProceduresDisplay();
    updateEstimatedTime();
});

// Close dropdown if clicked outside
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('procedure-dropdown');
    const button = document.getElementById("selected-procedures-btn");

    if (!button.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

</script>
    <script>
    var unavailableDates = <?= $unavailableDatesJson; ?>;

    $(function() {
  $("#appointmentDate").datepicker({
    dateFormat: 'yy-mm-dd',
    minDate: 3, // Start 2 days from today
    beforeShowDay: function(date) {
      const dateStr = $.datepicker.formatDate('yy-mm-dd', date);
      const day = date.getDay(); // 0 = Sunday, 6 = Saturday

      // Disable weekends
      if (day === 0 || day === 6) {
        return [false, "", "Weekends not allowed"];
      }

      // Disable unavailable dates from DB
      if (unavailableDates.includes(dateStr)) {
        return [false, "", "Dentist Not Available"];
      }

      return [true, "", ""];
    }
  });
});

  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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