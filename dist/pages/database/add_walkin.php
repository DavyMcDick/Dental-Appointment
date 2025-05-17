<?php
session_start();
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $age      = (int)$_POST['age'];
    $email    = isset($_POST['email']) ? trim($_POST['email']) : "";
    $contact  = trim($_POST['contact']);
    $address  = trim($_POST['address']);
    $date     = $_POST['date'];
    $time = trim($_POST['estimated_time']); 
    $status   = "Pending";
    $user_id  = NULL;

    // Format procedure string with teeth
    $procedureArray = $_POST['procedure'];
    $teethArray     = isset($_POST['teeth']) ? $_POST['teeth'] : [];

    $procedureList = [];
    $teethTotal = 0;

    foreach ($procedureArray as $proc) {
        if (in_array($proc, ['Teeth Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Filling']) && isset($teethArray[$proc])) {
            $count = (int)$teethArray[$proc];
            $teethTotal += $count;
            $toothLabel = $count === 1 ? "tooth" : "teeth";
            $procedureList[] = $proc . " ({$count} {$toothLabel})";
        } else {
            $procedureList[] = $proc;
        }
    }

    $procedure = implode(", ", $procedureList);

    // Insert into walk_in
    $stmt1 = $conn->prepare("INSERT INTO walk_in (Names, Age, Email, Contact, Address, Dates, Procedures, Teeth, Time, Status) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("sisssssiss", $name, $age, $email, $contact, $address, $date, $procedure, $teethTotal, $time, $status);
    
    // Insert into confirm
    $stmt2 = $conn->prepare("INSERT INTO confirm (user_id, Names, Age, Email, Contact, Address, Dates, Procedures, Teeth, Time, Status) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("isissssssss", $user_id, $name, $age, $email, $contact, $address, $date, $procedure, $teethTotal, $time, $status);

    $exec1 = $stmt1->execute();
    $exec2 = $stmt2->execute();

    if ($exec1 && $exec2) {
        echo "success";
    } else {
        echo "Error in Walk-in Insert: " . $stmt1->error . "\n";
        echo "Error in Confirm Insert: " . $stmt2->error . "\n";
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();
}
?>
