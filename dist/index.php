<?php

include('connect.php');
$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
$today = date("Y-m-d");

// 🟦 Total users
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user"))['total'];

// 🟩 Total appointments (from confirm + walk_in)
$totalConfirm = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM confirm"))['total'];
$totalWalkIn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM walk_in"))['total'];
$totalAppointments = $totalConfirm + $totalWalkIn;

// 🟨 Today's Appointments (Dates field in confirm is text, so use LIKE)
$todaysConfirm = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) AS total FROM confirm WHERE Dates LIKE '$today%'"))['total'];

// Walk-in table has `Dates` column as DATE, so direct comparison
$todaysWalkIn = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) AS total FROM walk_in WHERE Dates = '$today'"))['total'];

$todaysAppointments = $todaysConfirm + $todaysWalkIn;

// 🟧 Confirmed (from confirm table)
$confirmed = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) AS total FROM confirm WHERE Status = 'Confirmed'"))['total'];

// 🟥 Walk-in Patients
$walkInPatients = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) AS total FROM walk_in"))['total'];

// 🟫 Canceled (from confirm and walk_in)
$canceledConfirm = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) AS total FROM confirm WHERE Status = 'Cancelled'"))['total'];

$canceledWalkIn = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) AS total FROM walk_in WHERE Status = 'Cancelled'"))['total'];

$canceledAppointments = $canceledConfirm + $canceledWalkIn;
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kho Prado </title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="assets/js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
  </head>
  <body class="with-welcome-text">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="index.html">
              <img src="assets/images/logo.svg" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="index.html">
              <img src="assets/images/logo-mini.svg" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
            <h1 class="welcome-text">Good Morning, <span class="text-black fw-bold"><?php echo $adminName; ?></span></h1>
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
                    <img src="assets/images/faces/face10.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Marian Garner </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face12.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">David Grey </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face1.jpg" alt="image" class="img-sm profile-pic">
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
                <img class="img-xs rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image"> </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <img class="img-md rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image">
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
        
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
              <li class="nav-item">
                <a class="nav-link" href="pages/database/confirm.php">
                  <i class="mdi mdi-grid-large menu-icon"></i>
                  <span class="menu-title">Appointment</span>
                </a>
              </li>
            <li class="nav-item">
              <a class="nav-link" href="pages/database/patient.php">
                <i class="fa fa-ambulance fa-solid menu-icon"></i>
                <span class="menu-title">Patients</span>
              </a>
            </li>
              <li class="nav-item">
                <a class="nav-link" href="pages/database/appointments.php ">
                  <i class="fa fa-users fa-solid menu-icon"></i>
                  <span class="menu-title">User Management</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pages/database/walk_in.php ">
                  <i class="fa fa-wheelchair menu-icon"></i>
                  <span class="menu-title">Walk In Patients</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pages/database/archive.php ">
                  <i class="fa fa-archive menu-icon"></i>
                  <span class="menu-title">Archive</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pages/database/archive.php ">
                  <i class="fa fa-calendar-o menu-icon"></i>
                  <span class="menu-title">Schedule</span>
                </a>
              </li>
            </ul>
            </nav>
            
            <div class="main-panel">
              <div class="content-wrapper">
                <div class="container mt-4">
                  <div class="row align-items-stretch" style="min-height: 100%;">
                    <!-- LEFT COLUMN -->
                    <div class="col-lg-6">
                      <div class="row g-3 h-100">
                        <!-- CARD 1 -->
                        <div class="col-md-6">
                          <div class="card h-100" style="background: #e3f2fd;">
                            <div class="card-body">
                              <h6 class="text-uppercase text-primary">Today's Appointments</h6>
                              <h3 class="fw-bold"><?= $todaysAppointments ?></h3>
                            </div>
                          </div>
                        </div>
                        <!-- CARD 2 -->
                        <div class="col-md-6">
                          <div class="card h-100" style="background: #b3e5fc;">
                            <div class="card-body">
                              <h6 class="text-uppercase text-primary">Total Users</h6>
                              <h3 class="fw-bold"><?= $totalUsers ?></h3>
                            </div>
                          </div>
                        </div>
                        <!-- CARD 3 -->
                        <div class="col-md-6">
                          <div class="card h-100" style="background: #bbdefb;">
                            <div class="card-body">
                              <h6 class="text-uppercase text-primary">Total Appointments</h6>
                              <h3 class="fw-bold"><?= $totalAppointments ?></h3>
                            </div>
                          </div>
                        </div>
                        <!-- CARD 4 -->
                        <div class="col-md-6">
                          <div class="card h-100" style="background: #b2ebf2;">
                            <div class="card-body">
                              <h6 class="text-uppercase text-primary">Confirmed</h6>
                              <h3 class="fw-bold"><?= $confirmed?></h3>
                            </div>
                          </div>
                        </div>
                        <!-- CARD 5 -->
                        <div class="col-md-6">
                          <div class="card h-100" style="background: #b3e5fc;">
                            <div class="card-body">
                              <h6 class="text-uppercase text-primary">Walk-in Patients</h6>
                              <h3 class="fw-bold"><?= $walkInPatients ?></h3>   
                            </div>
                          </div>
                        </div>
                        <!-- CARD 6 -->
                        <div class="col-md-6">
                          <div class="card h-100" style="background: #cfd8dc;">
                            <div class="card-body">
                              <h6 class="text-uppercase text-primary">Canceled</h6>
                              <h3 class="fw-bold"><?= $canceledAppointments ?></h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
            
                    <!-- RIGHT COLUMN: Full Height Chart -->
                    <div class="col-lg-6 d-flex align-items-stretch">
                      <div class="card w-100 h-100">
                        <!-- <div class="card-body d-flex flex-column">
                          <h4 class="card-title">Total Appointments</h4> -->
                          <!-- Chart fills all remaining space -->
                          <!-- <div class="flex-grow-1 d-flex align-items-center">
                            <canvas id="areaChart" style="width: 100%; height: 100%;"></canvas>
                          </div> -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
          <!-- main-panel ends -->
        </div>
         
        </div>
       
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="assets/js/dashboard.js"></script>
    <!-- <script src="assets/js/Chart.roundedBarCharts.js"></script> -->
    <!-- End custom js for this page-->
  </body>
</html>