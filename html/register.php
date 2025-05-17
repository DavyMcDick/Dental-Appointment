<?php
include('connect.php');

$error = ""; // Store error messages
$success = ""; // Store success messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['fname']);
    $lastname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];

    // Check for empty fields
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "⚠️ All fields are required!";
    }
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format!";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error = "❌ Passwords do not match!";
    }
    else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT Email FROM user WHERE Email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "❌ Email already exists. Please use a different email.";
        } else {
            // Insert user if email doesn't exist
            $insertStmt = $conn->prepare("INSERT INTO user (Firstname, Lastname, Email, Password) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("ssss", $firstname, $lastname, $email, $hashedPassword);

            if ($insertStmt->execute()) {
                $success = "✅ Registration successful!";
                header("refresh:2;url=login.php"); 
            } else {
                $error = "❌ Error storing data: " . $insertStmt->error;
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registration - Kho Prado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../dist/assets/css/style.css">
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo">
                  <h2 style="color: rgb(22, 22, 150);">Kho Prado</h2>
                </div>
                <h4>New here?</h4>
                <h6 class="fw-light">Signing up is easy. It only takes a few steps</h6>

                <!-- Display Success Message -->
                <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($success); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <!-- Display Error Message -->
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($error); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

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
                            <label class="form-check-label">I agree to all <a href="terms.html">Terms & Conditions</a></label>
                        </div>
                    </div>
                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" name="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">
                            SIGN UP
                        </button>
                    </div>
                    <div class="text-center mt-4 fw-light">Already have an account? <a href="login.php" class="text-primary">Login</a></div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Auto-dismiss alert after 5 seconds -->
    <script>
      setTimeout(() => {
        let alertBox = document.querySelector(".alert");
        if (alertBox) {
          alertBox.style.transition = "opacity 0.5s ease-out";
          alertBox.style.opacity = "0";
          setTimeout(() => alertBox.remove(), 500);
        }
      }, 5000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
