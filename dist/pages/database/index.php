<?php
include('connect.php');

// Fetch all appointments
$query = "SELECT * FROM confirm ORDER BY Dates ASC";
$result = $conn->query($query);

$pending = [];
$confirmed = [];
$canceled = [];

while ($row = $result->fetch_assoc()) {
    if ($row['Status'] == 'Confirmed') {
        $confirmed[] = $row;
    } elseif ($row['Status'] == 'Canceled') {
        $canceled[] = $row;
    } else {
        $pending[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Appointments</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h2>Pending Appointments</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Procedures</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php foreach ($pending as $row) { ?>
        <tr>
            <td><?= $row['ID'] ?></td>
            <td><?= $row['Names'] ?></td>
            <td><?= $row['Age'] ?></td>
            <td><?= $row['Email'] ?></td>
            <td><?= $row['Contact'] ?></td>
            <td><?= $row['Procedures'] ?></td>
            <td><?= $row['Dates'] ?></td>
            <td>
                <a href='update_status.php?id=<?= $row['ID'] ?>&status=Confirmed' 
                   onclick='return confirm("Confirm this appointment?")'>Confirm</a> | 
                <a href='update_status.php?id=<?= $row['ID'] ?>&status=Canceled' 
                   onclick='return confirm("Cancel this appointment?")'>Cancel</a>
            </td>
        </tr>
    <?php } ?>
</table>

<h2>Confirmed Appointments</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Procedures</th>
        <th>Date</th>
    </tr>
    <?php foreach ($confirmed as $row) { ?>
        <tr>
            <td><?= $row['ID'] ?></td>
            <td><?= $row['Names'] ?></td>
            <td><?= $row['Age'] ?></td>
            <td><?= $row['Email'] ?></td>
            <td><?= $row['Contact'] ?></td>
            <td><?= $row['Procedures'] ?></td>
            <td><?= $row['Dates'] ?></td>
        </tr>
    <?php } ?>
</table>

<h2>Canceled Appointments</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Procedures</th>
        <th>Date</th>
    </tr>
    <?php foreach ($canceled as $row) { ?>
        <tr>
            <td><?= $row['ID'] ?></td>
            <td><?= $row['Names'] ?></td>
            <td><?= $row['Age'] ?></td>
            <td><?= $row['Email'] ?></td>
            <td><?= $row['Contact'] ?></td>
            <td><?= $row['Procedures'] ?></td>
            <td><?= $row['Dates'] ?></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
