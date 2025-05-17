<?php
session_start();
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT Password FROM dentist WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if (empty($email)) {
        $error = "Email is empty";
    } else if(empty($password)){
        $error = "Password is empty";
    } else {
    
    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        
        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['email'] = $email;
    
            header("Location: ../dist/dentist/doctor.php");
            exit(); // Stop script execution after redirection
        } else {
            $error = "Incorrect email or password please try again";
        }
    } else {
        $error = "No account found with this email.";
    }

    $stmt->close();
    }
    $conn->close();
}
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
                  <h2 style="color: rgb(22, 22, 150); font-weight: 800">Doctor Login</h2>
                </div>
                <h4>Hello! let's get started</h4>
                <h6 class="fw-light">Sign in to continue.</h6>
                
                <!-- Display error message if login fails -->
                <?php if (!empty($error)): ?>
                <div style="color: red;"><?php echo $error; ?></div>
                <?php endif; ?>

                <form class="pt-3" method="POST" action="">
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
                    <a href="doctorregister.php" class="text-primary">Create</a>
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
