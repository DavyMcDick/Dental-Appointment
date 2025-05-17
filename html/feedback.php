<?php
include('connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = $conn->real_escape_string($search);

// Pagination variables
$limit = 6; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count only users who filled out the appointment
$totalQuery = "SELECT COUNT(*) AS total FROM confirm
               WHERE TRIM(Names) <> '' 
               AND TRIM(Email) <> '' 
               AND TRIM(Contact) <> '' 
               AND TRIM(Procedures) <> '' 
               AND TRIM(Dates) <> '' 
               AND (Names LIKE '%$search%' 
               OR Email LIKE '%$search%' 
               OR Contact LIKE '%$search%' 
               OR Procedures LIKE '%$search%')";

$totalResult = $conn->query($totalQuery);
$totalRecords = ($totalResult) ? $totalResult->fetch_assoc()['total'] : 0;
$totalPages = ($totalRecords > 0) ? ceil($totalRecords / $limit) : 1;

// Fetch only users who filled appointments
$query = "SELECT * FROM confirm
          WHERE TRIM(Names) <> '' 
          AND TRIM(Email) <> '' 
          AND TRIM(Contact) <> '' 
          AND TRIM(Procedures) <> '' 
          AND TRIM(Dates) <> '' 
          AND (Names LIKE '%$search%' 
          OR Email LIKE '%$search%' 
          OR Contact LIKE '%$search%' 
          OR Procedures LIKE '%$search%')
          ORDER BY ID ASC 
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);


session_start();
include('connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's appointments
$query = "SELECT * FROM confirm WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
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
  <link rel="stylesheet" href="../style/style.css">
</head>
<body>
  <div class="main">


    <!-- HOME SECTION -->

    <div class="home-section" id="home">
      <div class="nav-section">
        <div class="logo-container"><i class="fa-solid fa-tooth" style="color: rgb(22, 22, 150)"></i>Kho Prado</div>
          <ul class="menu-container">
            <li><a href="book.php">Book Appointment</a></li> 
            <li><a href="appointment.php">My Appointment</a></li>
            <li><a href="schedule.php">Schedule</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <a href="">
             <button class="btn1">Logout</button>
            </a>
          </ul>
        </div>
      <div class="hero-wrapper">
        
       <div class="book-main">
       </div>
    </div>


    <div class="main-panel">
          <div class="content-wrapper">
          <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <!-- <h4 class="card-title">Bordered table</h4>
            <p class="card-description"> Add class <code>.table-bordered</code> -->
            <!-- Search Bar -->
            <div class="d-flex justify-content-end">
              <form method="GET" class="d-flex">
                  <input type="text" name="search" class="form-control me-2" 
                        placeholder="Search" 
                        value="<?= htmlspecialchars($search) ?>">
                  <button type="submit" class="btn btn-primary">Search</button>
              </form>
            </div>

            </p>
            <div class="table-responsive pt-3">
              <table class="table table-bordered">
              <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Procedure</th>
                                <th>Estimated Time</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['ID'] ?></td>
                                        <td><?= $row['Names'] ?></td>
                                        <td><?= $row['Age'] ?></td>
                                        <td><?= $row['Email'] ?></td>
                                        <td><?= $row['Contact'] ?></td>
                                        <td><?= $row['Procedures'] ?></td>
                                        <td><?= $row['Time'] ?></td>
                                        <td><?= $row['Dates'] ?></td>
                                        <td><?= $row['Status'] ?></td>

                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="9" class="text-center">No records found</td></tr>
                            <?php endif; ?>
                        </tbody>

              </table>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-end mt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="confirm.php?page=<?= $page - 1; ?>" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="confirm.php?page=<?= $i; ?>">
                                            <?= $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="confirm.php?page=<?= $page + 1; ?>" aria-label="Next">
                                            <span aria-hidden="true">»</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                     </div>
            </div>
          </div>
        </div>
      </div>
          </div>
        </div>





   </div>
</body>