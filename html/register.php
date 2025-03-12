<?php
include('connect.php');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (isset($_POST['submit'])) {
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    //  Move empty fields check to the top
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required');</script>";
        exit();
    }

    $fields = [
        'fname' => 'Firstname required',
        'lname' => 'Lastname required',
        'email' => 'Email required',
        'password' => 'Password required',
        'confirmpassword' => 'Confirm Password required'
    ];
    
    foreach ($fields as $key => $message) {
        if (empty($_POST[$key])) {
            echo "<script>alert($message);</script>";
            exit();
        }
    }

    if ($password !== $confirm_password) {
        $error="Passwords do not match";
        exit();
    }

    //  Check if email already exists
    $checkStmt = $conn->prepare("SELECT Email FROM user WHERE Email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 1) {
        echo "<script>alert('Email already exists. Please use a different email.');</script>";
        exit();
    }
    $checkStmt->close(); // Close first statement

    // Insert user data into database
    $insertStmt = $conn->prepare("INSERT INTO user (Firstname, Lastname, Email, Password) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssss", $firstname, $lastname, $email, $hashedPassword);

    if ($insertStmt->execute()) {
        header("Location: login.php");
        exit(); // Stop further execution
    } else {
        echo "<script>alert('Data Not Stored: " . $insertStmt->error . "');</script>";
    }

    $insertStmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Star Admin2 </title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../dist/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../dist/assets/vendors/mdi/css/materialdesignicons.min.css">
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
    <link rel="stylesheet" href="../dist/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href=".assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo">
                  <img src="../dist/assets/images/logo.svg" alt="logo">
                </div>
                <h4>New here?</h4>
                <h6 class="fw-light">Signing up is easy. It only takes a few steps</h6>
                <form class="pt-3">
                  <div class="form-group">
                    <input type="text" class="form-control form-control-lg" id="exampleInputUsername1" placeholder="First Name" name="fname">
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control form-control-lg" id="exampleInputUsername1" placeholder="Last Name" name="lname">
                  </div>
                  <div class="form-group">
                    <input type="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Email" name="email">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password" name="password">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder=" Confirm Password" name="confirmpassword">
                  </div>
                  <div class="mb-4">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input"> I agree to all Terms & Conditions </label>
                    </div>
                  </div>
                  <div class="mt-3 d-grid gap-2">
                    <a class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn" href="../../index.html">SIGN UP</a>
                  </div>
                  <div class="text-center mt-4 fw-light"> Already have an account? <a href="login.html" class="text-primary">Login</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
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
    <!-- endinject -->
  </body>
</html>