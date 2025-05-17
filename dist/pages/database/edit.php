<?php
include('connect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM appointment WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $procedure = $_POST['procedure'];
    $date = $_POST['date'];

    $updateStmt = $conn->prepare("UPDATE appointment SET Names=?, Age=?, Email=?, Contact=?, Procedures=?, Dates=? WHERE ID=?");
    $updateStmt->bind_param("sissssi", $name, $age, $email, $contact, $procedure, $date, $id);

    if ($updateStmt->execute()) {
        header("Location: appointments.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Button to Open the Modal -->
<button type="button" class="btn btn-primary m-3" data-bs-toggle="modal" data-bs-target="#editAppointmentModal">
    Edit Appointment
</button>

<!-- Edit Appointment Modal -->
<div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $row['Names']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control" name="age" value="<?php echo $row['Age']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $row['Email']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" class="form-control" name="contact" value="<?php echo $row['Contact']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Procedure</label>
                        <select class="form-select" name="procedure">
                            <option value="Braces" <?php if ($row['Procedures'] == 'Braces') echo 'selected'; ?>>Braces</option>
                            <option value="Xray" <?php if ($row['Procedures'] == 'Xray') echo 'selected'; ?>>Xray</option>
                            <option value="Consultation" <?php if ($row['Procedures'] == 'Consultation') echo 'selected'; ?>>Consultation</option>
                            <option value="Extraction" <?php if ($row['Procedures'] == 'Extraction') echo 'selected'; ?>>Extraction</option>
                            <option value="Root Canal" <?php if ($row['Procedures'] == 'Root Canal') echo 'selected'; ?>>Root Canal</option>
                            <option value="Teeth Whitening" <?php if ($row['Procedures'] == 'Teeth Whitening') echo 'selected'; ?>>Teeth Whitening</option>
                            <option value="Surgery" <?php if ($row['Procedures'] == 'Surgery') echo 'selected'; ?>>Surgery</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Appointment Date</label>
                        <input type="date" class="form-control" name="date" value="<?php echo $row['Dates']; ?>" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
