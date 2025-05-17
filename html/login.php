<?php
session_start();
include('connect.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = $password = $error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    if (empty($email)) {
        $error = "Email is required.";
    } elseif (empty($password)) {
        $error = "Password is required.";
    } else {
        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT ID, Firstname, Password FROM user WHERE Email = ?");
        if (!$stmt) {
            die("Query Preparation Failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $firstname, $hashedPassword);
            $stmt->fetch();

            // Verify password (if passwords are hashed)
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $user_id;   // Store user ID
                $_SESSION['user_name'] = $firstname; // Store user's first name
                $_SESSION['email'] = $email;   // Store email
                header("Location: user.php"); // Redirect to dashboard
                exit();
            } else {
                $error = "Incorrect email or password. Please try again.";
            }
        } else {
            $error = "No account found with this email.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dental Clinic Login</title>
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
                <h4>Hello! let's get started</h4>
                <h6 class="fw-light">Sign in to continue.</h6>
                
                <!-- Display error message if login fails -->
                <?php if (!empty($error)): ?>
                <div style="color: red;"><?php echo $error; ?></div>
                <?php endif; ?>

                <form class="pt-3" method="POST" action="login.php">
                  <div class="form-group">
                    <input type="email" class="form-control form-control-lg" placeholder="Email" name="email" required>
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" placeholder="Password" name="password" required>
                  </div>
                  <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn">SIGN IN</button>
                  </div>
                  <div class="text-center mt-4 fw-light"> Don't have an account? 
                    <a href="register.php" class="text-primary">Create</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
