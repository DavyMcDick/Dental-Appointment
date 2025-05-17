<?php
include('connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "SELECT * FROM walk_in ORDER BY Dates DESC";

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = $conn->real_escape_string($search); // Prevent SQL Injection

// Pagination variables
$limit = 6; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records for pagination (only pending records that match search)
$totalQuery = "SELECT COUNT(*) AS total FROM walk_in
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
$query = "SELECT * FROM walk_in
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
            <!-- Search Bar -->
            <div class="d-flex justify-content-end mb-3">
  <form method="GET" class="d-flex align-items-center">
    <!-- Search Input -->
    <input type="text" name="search"
           class="form-control me-2"
           placeholder="Search"
           value="<?= htmlspecialchars($search) ?>"
           style="width: 250px; height: 45px;">

    <!-- Search Button -->
    <button type="submit"
            class="btn btn-primary me-2"
            style="height: 45px;">
      Search
    </button>

    <!-- Add Walk-In Patient Button -->
    <button type="button"
            class="btn btn-success"
            data-bs-toggle="modal"
            data-bs-target="#walkinModal"
            style="height: 45px;">
      + Add Walk-In Patient
    </button>
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
                  <th>Teeth</th>
                  <th>Time</th>
                  <th>Date</th>
                  </tr>
                </thead>
                </thead>
                    <tbody id="walkinTableBody">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['ID'] ?></td>
                                    <td><?= $row['Names'] ?></td>
                                    <td><?= $row['Age'] ?></td>
                                    <td><?= $row['Email'] ?></td>
                                    <td><?= $row['Contact'] ?></td>
                                    <td><?= $row['Procedures'] ?></td>
                                    <td><?= $row['Teeth'] ?></td>
                                    <td><?= $row['Time'] ?></td>
                                    <td><?= $row['Dates'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No walk-in patients found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-end mt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="walk_in.php?page=<?= $page - 1; ?>" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="walk_in.php?page=<?= $i; ?>">
                                            <?= $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="walk_in.php?page=<?= $page + 1; ?>" aria-label="Next">
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
    <!-- Bootstrap Walk-In Modal -->
    <div class="modal fade" id="walkinModal" tabindex="-1" aria-labelledby="walkinModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md"> <!-- smaller width -->
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="walkinModalLabel">Add Walk-In Patient</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
      <form id="walkinForm" method="POST" action="add_walkin.php">
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="age" class="form-label">Age</label>
              <input type="number" class="form-control" id="age" name="age" min="4" max="99" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="contact" class="form-label">Contact Number</label>
              <input type="tel" class="form-control" id="contact" name="contact" maxlength="11" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control text-lowercase" id="email" name="email" required>
          </div>

          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" required>
          </div>

          <div class="mb-3">
            <label for="date" class="form-label">Preferred Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Procedures</label>
            <div class="form-control" id="selected-procedures-btn" onclick="toggleDropdown()" style="cursor: pointer;">-- Select Procedures --</div>

            <div class="border rounded p-2 mt-1" style="display: none;" id="procedure-dropdown">
            <input type="hidden" name="formatted_procedures" id="formatted_procedures">

              <?php
              $procedures = [
                  'Braces' => false,
                  'Xray' => false,
                  'Consultation' => false,
                  'Teeth Extraction' => true,
                  'Root Canal Treatment' => true,
                  'Teeth Whitening' => true,
                  'Filling' => true
              ];
              foreach ($procedures as $proc => $requiresTeeth) {
                  echo '<div class="form-check">
                          <input class="form-check-input" type="checkbox" name="procedure[]" value="' . $proc . '" onchange="updateSelectedProceduresDisplay();">
                          <label class="form-check-label">' . $proc . '</label>
                      </div>';
                  if ($requiresTeeth) {
                      echo '<input type="number" class="form-control teeth-input mt-1 mb-2" name="teeth[' . $proc . ']" placeholder="Number of Teeth" min="1" max="34" style="display:none;" oninput="updateEstimatedTime()">';
                  }
              }
              ?>    
            </div>
          </div>

          <div class="mb-3">
            <label for="estimated_time" class="form-label">Estimated Time</label>
            <input type="text" class="form-control" id="estimated_time" name="estimated_time" readonly>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-primary">Add Patient</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
document.getElementById("walkinForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent page reload

    let formData = new FormData(this);

    fetch("add_walkin.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            alert("Walk-in patient added successfully!");
            this.reset(); // Reset the form
            const modal = bootstrap.Modal.getInstance(document.getElementById('walkinModal'));
            modal.hide(); // Hide the modal
            fetchWalkInPatients(); // Refresh table
        } else {
            alert("Error: " + data);
        }
    })
    .catch(error => console.error("Error:", error));
});


// Estimate time dynamically based on procedure
function updateEstimatedTime() {
    const procedureTimes = {
        'Braces': 30, 'Xray': 10, 'Consultation': 15,
        'Teeth Extraction': 30, 'Root Canal Treatment': 60, 'Teeth Whitening': 20, 'Filling': 45
    };

    const procedure = document.getElementById('procedure').value;
    const numberOfTeeth = parseInt(document.getElementById('teeth_number').value) || 1;
    const baseTime = procedureTimes[procedure] || 0;

    const totalTime = ['Teeth Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Filling'].includes(procedure)
        ? baseTime * numberOfTeeth
        : baseTime;

        document.getElementById('estimated_time').value = totalTime;

}

// Optional: Refresh walk-in table after insert
function fetchWalkInPatients() {
    fetch("fetch_walkin.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("walkinTableBody").innerHTML = data;
        })
        .catch(error => console.error("Error fetching walk-in data:", error));
}
</script>
<script>
function toggleDropdown() {
    const list = document.getElementById('procedure-dropdown');
    list.style.display = (list.style.display === "block") ? "none" : "block";
}

// Function to update estimated time based on selected procedures
function updateEstimatedTime() {
    const procedureTimes = {
        'Braces': 30,  // Fixed time, does not depend on teeth count
        'Xray': 10,  
        'Consultation': 15,  
        'Teeth Extraction': 30,  // Per tooth
        'Root Canal Treatment': 60,  // Per tooth
        'Teeth Whitening': 20,  // Per tooth
        'Filling': 45
    };

    let totalTime = 0;

    // Get all selected procedures
    const checkedProcedures = document.querySelectorAll('input[name="procedure[]"]:checked');
    checkedProcedures.forEach(procCheckbox => {
        let procName = procCheckbox.value;
        let baseTime = procedureTimes[procName] || 0;

        // Check if procedure requires teeth count
        let isToothBased = ['Teeth Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Filling'].includes(procName);
        if (isToothBased) {
            let teethInput = procCheckbox.parentElement.nextElementSibling;
            let teethCount = teethInput && teethInput.value ? parseInt(teethInput.value) : 1; 
            totalTime += baseTime * teethCount;
        } else {
            totalTime += baseTime;
        }
    });

    // Ensure "mins" is always displayed, even if totalTime is 0
    const timeElement = document.getElementById('estimated_time').value = totalTime > 0 ? totalTime + " mins" : "0 mins";

    timeElement.value = totalTime;
}

// Function to update dropdown button with selected procedures
function updateSelectedProceduresDisplay() {
    const selectedCheckboxes = document.querySelectorAll('input[name="procedure[]"]:checked');
    const displayList = [];

    // Update dropdown display and generate formatted summary
    selectedCheckboxes.forEach(cb => {
        const procName = cb.value;
        const isToothBased = ['Teeth Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Filling'].includes(procName);
        let label = procName;

        if (isToothBased) {
            const teethInput = cb.parentElement.nextElementSibling;
            const teethCount = teethInput && teethInput.value ? parseInt(teethInput.value) : 1;
            const toothLabel = teethCount === 1 ? 'tooth' : 'teeth';
            label += ` (${teethCount} ${toothLabel})`;
        }

        displayList.push(label);
    });

    const button = document.getElementById("selected-procedures-btn");
    button.innerText = displayList.length > 0 ? displayList.join(', ') : "-- Select Procedures --";

    // Update hidden input for backend
    document.getElementById("formatted_procedures").value = displayList.join(', ');

    // Show/hide tooth input
    document.querySelectorAll('.teeth-input').forEach(input => input.style.display = "none");

    selectedCheckboxes.forEach(cb => {
        const teethInput = cb.parentElement.nextElementSibling;
        if (teethInput && teethInput.classList.contains('teeth-input')) {
            teethInput.style.display = "block";
        }
    });

    updateEstimatedTime();
}


// Attach event listeners to all checkboxes
document.querySelectorAll('input[name="procedure[]"]').forEach(cb => {
    cb.addEventListener("change", updateSelectedProceduresDisplay);
});

// Run on page load to update button with previously selected procedures
document.addEventListener("DOMContentLoaded", function() {
    updateSelectedProceduresDisplay();
    updateEstimatedTime();
});

// Close dropdown if clicked outside
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('procedure-dropdown');
    const button = document.getElementById("selected-procedures-btn");

    if (!button.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
