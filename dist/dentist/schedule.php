<?php
include('connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch availability from DB
$sql = "SELECT date, is_available FROM availability";
$result = $conn->query($sql);
$events = [];

while ($row = $result->fetch_assoc()) {
  $events[] = [
    'title'           => $row['is_available'] ? 'Available' : 'Not Available',
    'start'           => $row['date'],
    'allDay'          => true,
    'display'         => 'background', // Fill the entire cell
    'backgroundColor' => $row['is_available'] ? '#198754' : '#dc3545' // green/red
  ];
}


// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = $conn->real_escape_string($search); // Prevent SQL Injection

// Pagination variables
$limit = 6; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records for pagination (only pending records that match search)
$totalQuery = "SELECT COUNT(*) AS total FROM confirm
               WHERE status = 'Pending'
               AND (
                   Names LIKE '%$search%' 
                   OR Email LIKE '%$search%' 
                   OR Contact LIKE '%$search%' 
                   OR Procedures LIKE '%$search%'
                   OR Time LIKE '%$search%'
                   OR Dates LIKE '%$search%'
               )";

$totalResult = $conn->query($totalQuery);
if ($totalResult) {
    $totalRow = $totalResult->fetch_assoc();
    $totalRecords = $totalRow['total'];
    $totalPages = ceil($totalRecords / $limit);
} else {
    die("Error in Query: " . $conn->error);
}

// Fetch appointments with pagination, search, and pending filter
$query = "SELECT * FROM confirm
          WHERE status = 'Pending'
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
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kho Prado</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">

     <!-- FullCalendar -->
     <link rel="stylesheet" href="../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        contentHeight: 'auto',
        events: <?= json_encode($events); ?>,
        dateClick: function(info) {
          let currentEvent = calendar.getEvents().find(e => e.startStr === info.dateStr);
          let currentStatus = currentEvent
            ? (currentEvent.title === 'Available' ? 'Available' : 'Not Available')
            : 'Not Set';
          let newStatus = prompt(
            "Date: " + info.dateStr + "\nCurrent status: " + currentStatus + "\nEnter new status (available / not available):"
          );
          if (newStatus) {
            newStatus = newStatus.toLowerCase().trim();
            if (newStatus === 'available' || newStatus === 'not available') {
              const is_available = newStatus === 'available' ? 1 : 0;
              const params = new URLSearchParams();
              params.append("date", info.dateStr);
              params.append("is_available", is_available);
              fetch('update_availability.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
              })
              .then(response => response.text())
              .then(data => {
                alert("Availability updated: " + data);
                window.location.reload();
              })
              .catch(err => {
                console.error("Error:", err);
                alert("An error occurred while updating availability.");
              });
            } else {
              alert("Invalid input. Please type 'available' or 'not available'.");
            }
          }
        }
      });
      calendar.render();
    });
    </script>

    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../assets/images/favicon.png" />
  </head>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 10px;
      background: #F4F5F7;
      color: #000;
    }

    .content-wrapper {
     background: #F4F5F7;
     padding: 0px 20px;
    }

    .card-body {
      padding: 0;
    }
    .calendar-container {
      max-width: 800px;
      margin: 0 ;
      background: #ffffff;
      padding: 20px 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      justify-self: center;
    }
    h1 {
      font-size: 1.8rem;
      text-align: center;
      margin-bottom: 25px;
      color: #0d6efd;
    }
    #calendar {
      font-size: 0.9rem;
    }
    .fc-toolbar-title {
      font-size: 1.3rem;
    }
    .fc-daygrid-day-number {
      padding: 4px;
    }
    .fc-daygrid-day {
      padding: 6px !important;
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
              <img src="../assets/images/logo.svg" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="../../index.html">
              <img src="../assets/images/logo-mini.svg" alt="logo" />
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
                    <img src="../assets/images/faces/face10.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Marian Garner </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../assets/images/faces/face12.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">David Grey </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../assets/images/faces/face1.jpg" alt="image" class="img-sm profile-pic">
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
              <a class="nav-link" href="">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="schedule.php">
              <i class="fa fa-stethoscope fa-solid menu-icon" ></i>
                <span class="menu-title">Doctor Schedule</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="confirm.php">
              <i class="fa fa-calendar fa-solid menu-icon" ></i>
                <span class="menu-title">Appointment</span>
              </a>
            </li>
            
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
          <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
         
          <div class="calendar-container">  
                    <div id="calendar"></div>
                  </div>
                  <div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="availabilityModalLabel">Set Availability</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p id="selectedDateDisplay" style="font-weight:bold;"></p>
          <!-- Hidden field to store the clicked date -->
          <input type="hidden" id="selectedDate">

          <div class="mb-3">
            <label for="availabilityStatus" class="form-label">Choose Status:</label>
            <select class="form-select" id="availabilityStatus">
              <option value="available">Available</option>
              <option value="not available">Not Available</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveAvailability()">Save</button>
        </div>
      </div>
    </div>
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
    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/template.js"></script>
    <script src="../assets/js/settings.js"></script>
    <script src="../assets/js/hoverable-collapse.js"></script>
    <script src="../assets/js/todolist.js"></script>

    <script>
function setUnavailable() {
    let date = document.getElementById("disableDate").value;
    if (!date) {
        alert("Please select a date to disable!");
        return;
    }

    $.post("update_availability.php", { date: date, status: "unavailable" }, function(response) {
        alert(response.message);
        if (response.success) {
            location.reload(); // Refresh the admin panel to reflect changes
        }
    }, "json");
}


</script>
<script>
    let availabilityModal; // Will hold our Bootstrap modal instance

    document.addEventListener('DOMContentLoaded', function() {
      // Initialize the Bootstrap modal
      availabilityModal = new bootstrap.Modal(document.getElementById('availabilityModal'), {});

      // Initialize FullCalendar
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        contentHeight: 'auto',
        events: <?= json_encode($events); ?>,

        dateClick: function(info) {
          let currentEvent = calendar.getEvents().find(e => e.startStr === info.dateStr);
          let currentStatus = currentEvent
            ? (currentEvent.title === 'Available' ? 'Available' : 'Not Available')
            : 'Not Set';

          // Populate the modal with the date and current status
          document.getElementById('selectedDate').value = info.dateStr;
          document.getElementById('selectedDateDisplay').innerText = 
            "Date: " + info.dateStr + " | Current Status: " + currentStatus;

          // Pre-select the dropdown based on current status
          if (currentStatus.toLowerCase().includes('available')) {
            document.getElementById('availabilityStatus').value = 'available';
          } else if (currentStatus.toLowerCase().includes('not available')) {
            document.getElementById('availabilityStatus').value = 'not available';
          } else {
            document.getElementById('availabilityStatus').value = 'available'; // default
          }

          // Show the modal
          availabilityModal.show();
        }
      });
      calendar.render();
    });

    function saveAvailability() {
      let date = document.getElementById('selectedDate').value;
      let newStatus = document.getElementById('availabilityStatus').value; 
      let is_available = (newStatus === 'available') ? 1 : 0;

      const params = new URLSearchParams();
      params.append("date", date);
      params.append("is_available", is_available);

      fetch('update_availability.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
      })
      .then(response => response.text())
      .then(data => {
        alert("Availability updated: " + data);
        availabilityModal.hide();      // Hide the modal
        window.location.reload();      // Reload the page to see changes
      })
      .catch(err => {
        console.error("Error:", err);
        alert("An error occurred while updating availability.");
      });
    }
  </script>

  </body>
</html>

<?php
$conn->close();
?>