<?php
include('connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle search and date
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = $conn->real_escape_string($search);

$date_filter = isset($_GET['search_date']) && $_GET['search_date'] !== '' 
    ? $_GET['search_date'] 
    : date('Y-m-d'); // Default to today

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records
$totalQuery = "SELECT COUNT(*) AS total FROM confirm 
               WHERE Status = 'Confirmed'
               AND Dates = '$date_filter'
               AND (
                   Names LIKE '%$search%' 
                   OR Email LIKE '%$search%' 
                   OR Contact LIKE '%$search%' 
                   OR Procedures LIKE '%$search%'
                   OR Time LIKE '%$search%'
                   OR Dates LIKE '%$search%'
               )";

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch data
$query = "SELECT * FROM confirm 
          WHERE Status IN ('Confirmed', 'Pending', 'In Progress', 'Complete', 'Cancelled')
          AND Dates = '$date_filter'
          AND (
              Names LIKE '%$search%' 
              OR Email LIKE '%$search%' 
              OR Contact LIKE '%$search%' 
              OR Procedures LIKE '%$search%'
              OR Time LIKE '%$search%'
              OR Dates LIKE '%$search%'
          )
          ORDER BY ID ASC
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
?>




<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kho Prado</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="../../assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../../assets/images/favicon.png" />
  </head>
  <style>
  .status-btn {
    min-width: 120px;
    text-align: left;
    position: relative;
    padding-right: 30px;
  }
  .status-btn:after {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
  }
</style>
  <body>
    <div class="container-scroller">
      <!-- partial:../../partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="../../index.html">
              <img src="../../assets/images/logo.svg" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="../../index.html">
              <img src="../../assets/images/logo-mini.svg" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
              <h1 class="welcome-text">Good Morning, <span class="text-black fw-bold">John Doe</span></h1>
              <h3 class="welcome-sub-text">Your performance summary this week </h3>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item d-none d-lg-block">
              <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                <span class="input-group-addon input-group-prepend border-right">
                  <span class="icon-calendar input-group-text calendar-icon"></span>
                </span>
                <input type="text" class="form-control">
              </div>
            </li>
            <li class="nav-item">
              <form class="search-form" action="#">
                <i class="icon-search"></i>
                <input type="search" class="form-control" placeholder="Search Here" title="Search here">
              </form>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                <i class="icon-bell"></i>
                <span class="count"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
                <a class="dropdown-item py-3 border-bottom">
                  <p class="mb-0 fw-medium float-start">You have 4 new notifications </p>
                  <span class="badge badge-pill badge-primary float-end">View all</span>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-alert m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">Application Error</h6>
                    <p class="fw-light small-text mb-0"> Just now </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-lock-outline m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">Settings</h6>
                    <p class="fw-light small-text mb-0"> Private message </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-airballoon m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">New user registration</h6>
                    <p class="fw-light small-text mb-0"> 2 days ago </p>
                  </div>
                </a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="icon-mail icon-lg"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
                <a class="dropdown-item py-3">
                  <p class="mb-0 fw-medium float-start">You have 7 unread mails </p>
                  <span class="badge badge-pill badge-primary float-end">View all</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../../assets/images/faces/face10.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Marian Garner </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../../assets/images/faces/face12.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">David Grey </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../../assets/images/faces/face1.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Travis Jenkins </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
              </div>
            </li>
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle" src="../../assets/images/faces/face8.jpg" alt="Profile image"> </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <img class="img-md rounded-circle" src="../../assets/images/faces/face8.jpg" alt="Profile image">
                  <p class="mb-1 mt-3 fw-semibold">Allen Moreno</p>
                  <p class="fw-light text-muted mb-0">allenmoreno@gmail.com</p>
                </div>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span class="badge badge-pill badge-danger">1</span></a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="../../index.php">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../database/confirm.php">
                <i class="fa fa-calendar fa-solid menu-icon"></i>
                <span class="menu-title">Appointment</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../database/patient.php">
                <i class="fa fa-ambulance fa-solid menu-icon"></i>
                <span class="menu-title">Patients</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../database/appointments.php">
                <i class="fa fa-users fa-solid menu-icon"></i>
                <span class="menu-title">User Management</span>
              </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../database/walk_in.php">
                  <i class="fa fa-wheelchair menu-icon"></i>
                  <span class="menu-title">Walk In Patients</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../database/archive.php">
                  <i class="fa fa-archive menu-icon"></i>
                  <span class="menu-title">Archive</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../database/schedule.php ">
                  <i class="fa fa-calendar-o menu-icon"></i>
                  <span class="menu-title">Schedule</span>
                </a>
              </li>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
          <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <!-- <h4 class="card-title">Bordered table</h4>
            <p class="card-description"> Add class <code>.table-bordered</code> -->
     <!-- Search + Date Filter -->
<div class="d-flex justify-content-end mb-3">
  <form method="GET" class="d-flex">
      <!-- Search Input -->
      <input type="text" name="search" class="form-control me-2" 
             placeholder="Search" value="<?= htmlspecialchars($search) ?>">

      <!-- Date Filter -->
      <input type="date" name="search_date" class="form-control me-2" 
             value="<?= htmlspecialchars($date_filter) ?>">

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary">Search</button>
  </form>
</div>
            </p>
            <div class="table-responsive pt-3">
<table class="table table-bordered" id="scheduleTable">
  <thead>
    <tr>
      <th>Time Slot</th>
      <th>Name</th>
      <th>Age</th>
      <th>Contact</th>
      <th>Procedure</th>
      <th>Teeth</th>
      <th>Duration</th>
      <th>Date</th>
      <th>Status</th> <!-- New Status Column -->
    </tr>
  </thead>
  <tbody>
    <?php
    if ($result->num_rows > 0) {
      $appointments = [];
      while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
        $status = $row['Status'] ?? 'Pending'; // Default to Pending if not set
        
        // Determine button color based on status
        $statusClass = '';
        switch(strtolower($status)) {
          case 'pending': $statusClass = 'btn-warning'; break;
          case 'in progress': $statusClass = 'btn-info'; break;
          case 'complete': $statusClass = 'btn-success'; break;
          case 'cancelled': $statusClass = 'btn-danger'; break;
          default: $statusClass = 'btn-secondary';
        }
        
        echo "<tr data-id='{$row['ID']}' data-duration='" . (int)str_replace(" mins", "", $row['Time']) . "'>
                <td class='time-slot'>-</td>
                <td>{$row['Names']}</td>
                <td>{$row['Age']}</td>
                <td>{$row['Contact']}</td>
                <td>{$row['Procedures']}</td>
                <td>{$row['Teeth']}</td>
                <td>{$row['Time']}</td>
                <td>{$row['Dates']}</td>
                <td>
                  <div class='dropdown'>
                    <button class='btn $statusClass dropdown-toggle status-btn' type='button' 
                            data-bs-toggle='dropdown' aria-expanded='false'
                            data-current-status='$status'>
                      $status
                    </button>
                    <ul class='dropdown-menu status-options'>
                      <li><a class='dropdown-item' href='#' data-status='Pending'>Pending</a></li>
                      <li><a class='dropdown-item' href='#' data-status='In Progress'>In Progress</a></li>
                      <li><a class='dropdown-item' href='#' data-status='Complete'>Complete</a></li>
                      <li><a class='dropdown-item' href='#' data-status='Cancelled'>Cancelled</a></li>
                    </ul>
                  </div>
                </td>
              </tr>";
      }
    } else {
      echo "<tr><td colspan='9'>No appointments found</td></tr>";
    }
    ?>
  </tbody>
</table>

<!-- Pass PHP data to JavaScript -->
<script>
  const appointments = <?php echo json_encode($appointments); ?>;
</script>

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
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../../assets/js/off-canvas.js"></script>
    <script src="../../assets/js/template.js"></script>
    <script src="../../assets/js/settings.js"></script>
    <script src="../../assets/js/hoverable-collapse.js"></script>
    <script src="../../assets/js/todolist.js"></script>

    <script>
  async function hideRow(element, message) {
    if (!confirm(message)) {
      return false;
    }
    // Hide the row permanently in the UI
    const row = element.closest('tr');
    row.style.display = 'none';
    
   
    // Prevent the default navigation behavior
    return false;
  }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const table = document.getElementById('scheduleTable');
  const tbody = table.querySelector('tbody');
  const rows = Array.from(tbody.querySelectorAll('tr[data-duration]'));

  // 1. Sort by duration (shortest first)
  rows.sort((a, b) => parseInt(a.getAttribute('data-duration')) - parseInt(b.getAttribute('data-duration')));

  // 2. Clear and rebuild table
  tbody.innerHTML = '';
  rows.forEach(row => tbody.appendChild(row));

  // 3. Clinic hours configuration
  const openingTime = new Date();
  openingTime.setHours(9, 0, 0); // 9:00 AM open
  
  const lunchStart = new Date();
  lunchStart.setHours(11, 30, 0); // 11:30 AM lunch
  
  const lunchEnd = new Date();
  lunchEnd.setHours(13, 0, 0); // 1:00 PM resume
  
  const closingTime = new Date();
  closingTime.setHours(17, 0, 0); // 5:00 PM close

  // 4. Schedule appointments
  let currentTime = new Date(openingTime);
  
  rows.forEach(row => {
    const duration = parseInt(row.getAttribute('data-duration'));
    const endTime = new Date(currentTime.getTime() + duration * 60000);
    
    // Check if appointment would cross lunch
    if (currentTime < lunchStart && endTime > lunchStart) {
      // Move to after lunch
      currentTime = new Date(lunchEnd);
      endTime.setTime(currentTime.getTime() + duration * 60000);
    }
    
    // Check if beyond closing time
    if (endTime > closingTime) {
      // Move to next business day or show warning
      console.warn("Appointment exceeds clinic hours:", row);
    }
    
    // Format time slot
    row.querySelector('.time-slot').textContent = 
      `${formatTime(currentTime)} - ${formatTime(endTime)}`;
    
    // Update current time with 5-min buffer
    currentTime = new Date(endTime.getTime() + 5 * 60000);
    
    // Skip lunch break if needed
    if (currentTime >= lunchStart && currentTime < lunchEnd) {
      currentTime = new Date(lunchEnd);
    }
  });

  function formatTime(date) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
  }
});
</script>
<script>
  // Status Update Handling
document.addEventListener('DOMContentLoaded', function() {
  // Handle status selection
  document.querySelectorAll('.status-options .dropdown-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const newStatus = this.getAttribute('data-status');
      const btn = this.closest('.dropdown').querySelector('.status-btn');
      const row = this.closest('tr');
      const appointmentId = row.getAttribute('data-id');
      
      // Update UI immediately
      btn.textContent = newStatus;
      btn.setAttribute('data-current-status', newStatus);
      
      // Update button class
      btn.className = 'btn dropdown-toggle status-btn ' + getStatusClass(newStatus);
      
      // Send AJAX update
      updateStatusInDatabase(appointmentId, newStatus);
    });
  });
  
  function getStatusClass(status) {
    switch(status.toLowerCase()) {
      case 'pending': return 'btn-warning';
      case 'in progress': return 'btn-info';
      case 'complete': return 'btn-success';
      case 'cancelled': return 'btn-danger';
      default: return 'btn-secondary';
    }
  }
  
  function updateStatusInDatabase(id, newStatus) {
    fetch('update_status.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id=${id}&status=${encodeURIComponent(newStatus)}`
    })
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        alert('Error updating status: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to update status');
    });
  }
});
</script>

  </body>
</html>

<?php
$conn->close();
?>