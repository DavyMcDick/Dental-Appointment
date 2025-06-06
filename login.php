<?php
session_start();
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT Password FROM user WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if (empty($email)) {
        $error = "Email is empty";
    } else if(empty($password)){
        $error = "Password is empty";
    } else {
    
    if ($stmt->num_rows == 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['email'] = $email;
  
            header("Location: index.php");
            exit(); 
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
            <p>Your trusted dental care provider. Book appointments, get expert advice, and maintain your oral health.</p>
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
            <h2 class="text-xl font-bold mb-2">Login</h2>
            <p class="text-gray-600 mb-4">Access your dental records and appointments</p>
            
            <?php if (!empty($error)): ?>
            <div id="error-message" style="color: red" class="my-2"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST"> 
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" placeholder="Enter Your Email" class="w-full p-2 border rounded mb-3" name="email"oninput=" clearError()">
                
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" placeholder="Enter Your Password" class="w-full p-2 border rounded mb-3" name="password" oninput="clearError()">
                <div class="flex justify-between items-center w-full mt-2">
                <div></div> <!-- Empty div to keep alignment -->
        <input type="checkbox" onclick="togglePassword()" class="mr-2">
        <label class="text-sm">Show Password</label>

                <a href="forgot.php" class="text-sm text-blue-500 hover:underline">Forgot Password?</a>
                </div>
                <button class="w-full bg-blue-500 text-white p-2 rounded mt-3" name="submit">Login</button>
            </form>
            
            <p class="text-gray-600 text-sm mt-4">Don't have an account? <a href="signup.php" class="text-blue-500">Sign Up</a></p>
        </div>
    </div> 

    <script src="error.js"></script>

</body>
</html>
