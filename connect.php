<?php
$servername = "localhost";
$username = "root";
$password = "2208";
$dbase = "test2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbase);
//Check connection
// if ($conn->connect_error) {
//   die("Connection failed: " . $conn->connect_error);
// }
// echo "Connected successfully";
?>