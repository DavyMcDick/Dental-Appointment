<?php
header('Content-Type: application/json');
$pdo = new PDO("mysql:host=localhost;dbname=your_db;charset=utf8", "your_user", "your_password");

// Fetch available dates
$stmt = $pdo->query("SELECT date FROM availability WHERE status = 'available'");
$availableDates = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $availableDates[] = $row['date'];
}

// Fetch unavailable dates
$stmt = $pdo->query("SELECT date FROM availability WHERE status = 'unavailable'");
$unavailableDates = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $unavailableDates[] = $row['date'];
}

// Return JSON with available and unavailable dates
echo json_encode(['available' => $availableDates, 'unavailable' => $unavailableDates]);
?>
