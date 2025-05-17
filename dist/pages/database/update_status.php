<?php
include('connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid request method']);
  exit;
}

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !$status) {
  echo json_encode(['success' => false, 'message' => 'Missing parameters']);
  exit;
}

// Validate status
$allowedStatuses = ['Pending', 'In Progress', 'Complete', 'Cancelled'];
if (!in_array($status, $allowedStatuses)) {
  echo json_encode(['success' => false, 'message' => 'Invalid status']);
  exit;
}

// Update database
$stmt = $conn->prepare("UPDATE confirm SET Status = ? WHERE ID = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>