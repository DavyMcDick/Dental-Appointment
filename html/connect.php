<?php
$servername = "localhost";
$username = "root";
$password = "2208";
$dbase = "kho prado";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbase);
//Check connection
// if ($conn->connect_error) {
//   die("Connection failed: " . $conn->connect_error);
// }
// echo "Connected successfully";
?>