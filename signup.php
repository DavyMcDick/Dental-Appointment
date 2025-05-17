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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Clinic Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col md:flex-row w-full max-w-4xl">
        <!-- Left Section -->
        <div class="w-full md:w-1/2 bg-blue-500 text-white p-6 rounded-lg flex flex-col justify-between">
            <!-- <h2 class="text-2xl font-bold">Welcome to Kho Prado</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Est architecto corrupti, illum quibusdam suscipit illo, sed consequuntur harum similique ipsa amet, nisi repellendus at animi!.</p>
            <div class="bg-blue-600 p-4 rounded-lg">
                <p>"A healthy smile starts with good dental care."</p>
                <div class="flex items-center mt-4">
                    <img src="" alt="Dentist" class="rounded-lg mr-2 ">
                    <div>
                        <p class="font-semibold">Dentist Name</p>
                        <p class="text-sm">Dental Specialist</p>
                    </div>
                </div>
            </div> -->
        </div>
        
        <!-- Right Section -->
        <div class="w-full md:w-2/3 p-6">
            <h2 class="text-xl font-bold mb-2">Sign Up</h2>
            <p class="text-gray-600 mb-4">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quas, sit.</p>
            
            <!-- <?php if (!empty($error)): ?>
            <div id="error-message" style="color: red" class="my-2"><?php echo $error; ?></div>
            <?php endif; ?> -->

            <form method="POST"> 
                <label class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" placeholder="Enter Your First Name" class="w-full p-2 border rounded mb-3" name="fname">
                <label class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" placeholder="Enter Your Last Name" class="w-full p-2 border rounded mb-3" name="lname" require>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" placeholder="Enter Your Email" class="w-full p-2 border rounded mb-3" name="email">
                
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" placeholder="Enter Your Password" class="w-full p-2 border rounded mb-3" name="password">
                
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" placeholder="Confirm Password" class="w-full p-2 border rounded mb-3" name="confirmpassword">
                
                <button class="w-full bg-blue-500 text-white p-2 rounded mt-3" name="submit">Login</button>
            </form>
            
            <p class="text-gray-600 text-sm mt-4">Already have an account? <a href="login.php" class="text-blue-500">Log in</a></p>
        </div>
    </div>
    <script scr="error.js"></script>
</body>
</html>