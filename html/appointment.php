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

$user_id = $_SESSION['user_id'];

$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total rows for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM confirm WHERE user_id = ?";
$stmt = $conn->prepare($totalQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRecords = ($totalResult->num_rows > 0) ? $totalResult->fetch_assoc()['total'] : 0;
$totalPages = ($totalRecords > 0) ? ceil($totalRecords / $limit) : 1;

// Fetch paginated data
$query = "SELECT id, Names, Age, Email, Contact, Address, Procedures, Teeth, Time, Dates, Status 
          FROM confirm
          WHERE user_id = ?
          ORDER BY ID ASC 
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<STYLE>

.small-table {
  font-size: 0.85rem;
}
</STYLE>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dental Appointment</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../style/style.css">
</head>
<body>
  <div class="main">
    <div class="home-section" id="home">
      <div class="nav-section">
        <div class="logo-container"><i class="fa-solid fa-tooth" style="color: rgb(22, 22, 150)"></i>Kho Prado</div>
          <ul class="menu-container">
            <li><a href="booking.php">Book Appointment</a></li> 
            <li><a href="appointment.php">My Appointment</a></li>
            <li><a href="schedule.php">Schedule</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <a href="../index.php">
            <button class="btn1">Logout</button>
            </a>
          </ul>
      </div>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">

                <div class="table-responsive pt-3">
                <table class="table table-bordered text-center small-table">    
                        <thead>
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
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['Names'] ?></td>
                                    <td><?= $row['Age'] ?></td>
                                    <td><?= $row['Email'] ?></td>
                                    <td><?= $row['Contact'] ?></td>
                                    <td><?= $row['Procedures'] ?></td>
                                    <td><?= $row['Time'] ?></td>
                                    <td><?= $row['Dates'] ?></td>
                                    <td><?= $row['Status'] ?></td>
                                    <td>
                                        <?php if (strcasecmp(trim($row['Status']), 'Pending') === 0): ?>
                                            <button class="cancel-btn btn btn-danger btn-sm" data-id="<?= $row['id']; ?>">Cancel</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                    <div class="d-flex justify-content-end mt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="appointment.php?page=<?= $page - 1; ?>" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="appointment.php?page=<?= $i; ?>">
                                            <?= $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="appointments.php?page=<?= $page + 1; ?>" aria-label="Next">
                                            <span aria-hidden="true">»</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
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
