<?php
include('connect.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (`Email`, `Password`) VALUES (?, ?)");
    $stmt->bind_param("ss",$email, $hashedPassword);
    
    if ($stmt->execute()) {
        header("Location: main.php");
    } else {
        echo "<script>alert('Data Not Stored');</script>";
    }
    
    $stmt->close();
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
            <h2 class="text-2xl font-bold">Welcome to SmileCare</h2>
            <p>Your trusted dental care provider. Book appointments, get expert advice, and maintain your oral health.</p>
            <div class="bg-blue-600 p-4 rounded-lg">
                <!-- <p>"A healthy smile starts with good dental care."</p> -->
                <div class="flex items-center mt-4">
                    <!-- <img src="" alt="Dentist" class="rounded-lg mr-2 "> -->
                    <div>
                        <!-- <p class="font-semibold">Dr. Emily R.</p>
                        <p class="text-sm">Dental Specialist</p> -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Section -->
        <div class="w-full md:w-2/3 p-6">
            <h2 class="text-xl font-bold mb-2">Login</h2>
            <p class="text-gray-600 mb-4">Access your dental records and appointments</p>
            <form method="POST">
                <label class="block text-sm font-medium text-gray-700 my-2" >Email</label>
                <input type="email" placeholder="Enter Your Email" class="w-full p-2 border rounded mb-3" name="email">
                <div class="flex justify-between items-center w-full mt-2">
                <div></div>
                <button class="w-48 bg-blue-500 text-white p-2 rounded mt-3" name="submit">Login</button>
                </div>
            </form>
            <div class="flex justify-between items-center w-full mt-2">
            <div></div>
            <p class="text-gray-600 text-sm mt-4">Don't have an account? <a href="signup.php" class="text-blue-500">Sign In</a></p>
            </div>
        </div>
    </div>
</body>
</html>
