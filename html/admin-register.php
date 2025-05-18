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
                <h2 style="color: rgb(22, 22, 150); font-weight: 800">Admin Register</h2>
                </div>
                <h4>New here?</h4>
                <h6 class="fw-light">Signing up is easy. It only takes a few steps</h6>
                <form class="pt-3" method="POST" action="register.php">
    <div class="form-group">
        <input type="text" class="form-control form-control-lg" placeholder="First Name" name="fname" required>
    </div>
    <div class="form-group">
        <input type="text" class="form-control form-control-lg" placeholder="Last Name" name="lname" required>
    </div>
    <div class="form-group">
        <input type="email" class="form-control form-control-lg" placeholder="Email" name="email" required>
    </div>
    <div class="form-group">
        <input type="password" class="form-control form-control-lg" placeholder="Password" name="password" required>
    </div>
    <div class="form-group">
        <input type="password" class="form-control form-control-lg" placeholder="Confirm Password" name="confirmpassword" required>
    </div>
    <div class="mb-4">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" required>
            <label class="form-check-label">I agree to all Terms & Conditions</label>
        </div>
    </div>
    <div class="mt-3 d-grid gap-2">
        <button type="submit" name="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">
            SIGN UP
        </button>
    </div>
    <div class="text-center mt-4 fw-light">Already have an account? <a href="adminlogin.php" class="text-primary">Login</a></div>
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