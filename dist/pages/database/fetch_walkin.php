<?php
include('connect.php');

// Fetch walk-in patients
$query = "SELECT * FROM walk_in ORDER BY Dates DESC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['ID']}</td>
                <td>" . htmlspecialchars($row['Names']) . "</td>
                <td>{$row['Age']}</td>
                <td>{$row['Email']}</td>
                <td>" . htmlspecialchars($row['Contact']) . "</td>
                <td>" . htmlspecialchars($row['Procedures']) . "</td>
                <td>{$row['Teeth']}</td>
                <td>{$row['Time']} mins</td>
                <td>{$row['Dates']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No walk-in patients found</td></tr>";
}
?>