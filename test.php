<?php
session_start();
include('connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Ensure user ID is retrieved once

// Handle search input securely
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination variables
$limit = 6; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total appointments for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM confirm
               WHERE user_id = ? 
               AND (Names LIKE ? OR Email LIKE ? OR Contact LIKE ? OR Procedures LIKE ?)";

$stmt = $conn->prepare($totalQuery);
$searchParam = "%$search%";
$stmt->bind_param("issss", $user_id, $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRecords = ($totalResult->num_rows > 0) ? $totalResult->fetch_assoc()['total'] : 0;
$totalPages = ($totalRecords > 0) ? ceil($totalRecords / $limit) : 1;

// Fetch the paginated appointment records
$query = "SELECT id, Names, Age, Email, Contact, Address, Procedures, Teeth, Time, Dates, Status 
          FROM confirm
          WHERE user_id = ? 
          AND (Names LIKE ? OR Email LIKE ? OR Contact LIKE ? OR Procedures LIKE ?)
          ORDER BY ID ASC 
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", $user_id, $searchParam, $searchParam, $searchParam, $searchParam, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dental Appointment</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
<div class="card-body">

  <!-- Search Bar -->
  <div class="d-flex justify-content-end mb-3">
    <form method="GET" class="d-flex">
      <input type="text" name="search" class="form-control me-2"
             placeholder="Search"
             value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn btn-primary">Search</button>
    </form>
  </div>

  <!-- Appointment Table -->
  <div class="table-responsive pt-3">
    <table class="table table-bordered text-center">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Age</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Procedure</th>
          <th>Estimated Time</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['Names']) ?></td>
              <td><?= $row['Age'] ?></td>
              <td><?= htmlspecialchars($row['Email']) ?></td>
              <td><?= htmlspecialchars($row['Contact']) ?></td>
              <td><?= htmlspecialchars($row['Procedures']) ?></td>
              <td><?= $row['Time'] ?></td>
              <td><?= $row['Dates'] ?></td>
              <td><?= $row['Status'] ?></td>
              <td>
                <?php if (strcasecmp(trim($row['Status']), 'Pending') === 0): ?>
                  <button class="btn btn-danger btn-sm cancel-btn" data-id="<?= $row['id']; ?>">Cancel</button>
                <?php else: ?>
                  <span class="text-muted">N/A</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-muted">No appointments found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-end mt-3">
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">«</a>
          </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">»</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

</div>



                <script>
                    document.querySelectorAll('.cancel-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            let appointmentId = this.getAttribute('data-id');
                            if (confirm("Are you sure you want to cancel this appointment?")) {
                                fetch('cancel_appointment.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `appointment_id=${appointmentId}`
                                })
                                .then(response => response.text())
                                .then(data => {
                                    alert(data);
                                    location.reload();
                                });
                            }
                        });
                    });
                </script>
</body>
</html>
<?php $conn->close(); ?>
