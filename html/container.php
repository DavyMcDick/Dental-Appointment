<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .selection-container {
            max-width: 500px;

            background: white;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-radius: 0;
        }
        .btn-primary {
            background-color: #1a32a3;
            border: none;
            font-weight: bold;
            padding: 12px;
            width: 100%;
            border-radius: 0;
        }
        .btn-primary:hover {
            background-color: #162296;
        }
        .btn-custom {
            background-color: white;
            color: #1a32a3;
            border: 2px solid #1a32a3;
            font-weight: bold;
            padding: 12px;
            width: 100%;
            transition: all 0.3s;
            border-radius: 0;
        }
        .btn-custom:hover {
            background-color: #1a32a3;
            color: white;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="selection-container">
    <div class="logo-container">
          <img src="../assets/kho_prado.jpg" alt="" height="150px" margin="20px" font-size="0">
        </div>
        <p class="fw-semibold text-dark">Hello! Let's get started</p>
        <p class="text-muted">Select your role to proceed</p>
        <div class="d-grid gap-3">
            <a href="adminlogin.php" class="btn btn-primary">Admin</a>
            <a href="doctorlogin.php" class="btn btn-custom">Doctor</a>
        </div>
    </div>
</body>
</html>

