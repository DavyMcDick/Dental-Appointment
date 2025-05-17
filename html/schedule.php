<?php
session_start();
include 'connect.php';
date_default_timezone_set('Asia/Manila');

$currentUserId = $_SESSION['user_id'];
$today = date('Y-m-d');
$now = new DateTime();

// Fetch all confirmed appointments (including status)
$stmt = $conn->prepare("SELECT * FROM confirm WHERE Status IN ('Confirmed', 'Pending', 'In Progress', 'Complete', 'Cancelled')");
$stmt->execute();
$result = $stmt->get_result();

// Procedure times (original durations without buffer)
$procedureTimes = [
    'Braces' => 30,
    'Xray' => 10, 
    'Consultation' => 15,
    'Teeth Extraction' => 30,
    'Root Canal Treatment' => 60,
    'Teeth Whitening' => 20,
    'Filling' => 45
];
$perToothProcedures = ['Braces', 'Teeth Extraction', 'Root Canal Treatment', 'Teeth Whitening', 'Filling'];

// Gather patients and identify current user's appointment
$patients = [];
$userScheduled = false;
$appointmentDate = null;

while ($row = $result->fetch_assoc()) {
    if ($row['user_id'] == $currentUserId) {
        $userScheduled = true;
        $appointmentDate = $row['Dates'];
    }

    $procedures = preg_split('/,\s*/', $row['Procedures']);

    $numTeeth = (int)$row['Teeth'];
    $estimatedMinutes = 0;

    foreach ($procedures as $procRaw) {
        $proc = trim($procRaw);
        $teethForThisProc = 1; // Default to 1 if not specified
        
        // Extract teeth count from procedure (e.g., "Extraction (2 teeth)")
        if (preg_match('/\((\d+)\s*teeth?\)/i', $procRaw, $matches)) {
            $teethForThisProc = (int)$matches[1];
        }
        
        // Remove parentheses for matching
        $procFormatted = preg_replace('/\s*\(.*?\)/', '', $proc);
        $procFormatted = ucwords(strtolower($procFormatted));
        
        $baseTime = $procedureTimes[$procFormatted] ?? 0;
        
        if (in_array($procFormatted, $perToothProcedures)) {
            $estimatedMinutes += $baseTime * $teethForThisProc;
        } else {
            $estimatedMinutes += $baseTime;
        }
    }

    $row['estimated_time'] = $estimatedMinutes;
    $patients[] = $row;
}
$stmt->close();         

// If not scheduled, show message
if (!$userScheduled) {
    echo "<div class='alert alert-info' style='max-width: 600px; margin: 50px auto; text-align: center;'>
            <i class='fas fa-calendar-times fa-3x mb-3' style='color: #6c757d;'></i>
            <h3>You have no confirmed appointments</h3>
            <p class='text-muted'>Please schedule an appointment to view your booking details</p>
          </div>";
    exit;
}

// Calculate reveal time (2 days before at 11:59 PM)
$showScheduleTime = new DateTime($appointmentDate . ' 23:59:00');
$showScheduleTime->modify('-2 days');

if ($now < $showScheduleTime) {
    $targetDate = $showScheduleTime->format('F j, Y');
    $apptDate = date('F j, Y', strtotime($appointmentDate));
    ?>

    <style>
        .countdown-card {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px 30px;
            border-radius: 16px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .countdown-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .countdown-header i {
            margin-right: 12px;
            font-size: 28px;
        }
        
        .countdown-display {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
        }
        
        .countdown-unit {
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 20px 15px;
            min-width: 80px;
            backdrop-filter: blur(5px);
        }
        
        .countdown-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .countdown-label {
            font-size: 14px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .appointment-info {
            margin-top: 25px;
            font-size: 18px;
        }
        
        .appointment-date {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 50px;
            margin-top: 10px;
        }
        
        .appointment-date i {
            margin-right: 8px;
        }
        
        .release-info {
            margin-top: 20px;
            font-size: 14px;
            opacity: 0.8;
        }
    </style>

    <div class="countdown-card">
        <div class="countdown-header">
            <i class="fas fa-clock"></i>
            Your Appointment Details Are Coming Soon
        </div>
        
        <p>We'll reveal your schedule details shortly before your appointment</p>
        
        <div class="countdown-display" id="countdown">
            <div class="countdown-unit">
                <div class="countdown-number">--</div>
                <div class="countdown-label">Days</div>
            </div>
            <div class="countdown-unit">
                <div class="countdown-number">--</div>
                <div class="countdown-label">Hours</div>
            </div>
            <div class="countdown-unit">
                <div class="countdown-number">--</div>
                <div class="countdown-label">Minutes</div>
            </div>
            <div class="countdown-unit">
                <div class="countdown-number">--</div>
                <div class="countdown-label">Seconds</div>
            </div>
        </div>
        
        <div class="appointment-info">
            <div>Your appointment is scheduled for</div>
            <div class="appointment-date">
                <i class="fas fa-calendar-day"></i>
                <strong><?= $apptDate ?></strong>
            </div>
        </div>
        
        <div class="release-info">
            Details will be available at 11:59 PM on <?= $targetDate ?>
        </div>
    </div>

    <script>
        const countdownElement = document.getElementById('countdown');
        const targetTime = new Date("<?= $showScheduleTime->format('Y-m-d H:i:s') ?>").getTime();
        const units = countdownElement.querySelectorAll('.countdown-unit');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetTime - now;

            if (distance <= 0) {
                countdownElement.innerHTML = `
                    <div style="padding: 30px; background: rgba(255,255,255,0.2); border-radius: 12px;">
                        <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <div style="font-size: 24px; font-weight: 600;">Your schedule is now available!</div>
                    </div>`;
                location.reload();
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            units[0].querySelector('.countdown-number').textContent = days;
            units[1].querySelector('.countdown-number').textContent = String(hours).padStart(2, '0');
            units[2].querySelector('.countdown-number').textContent = String(minutes).padStart(2, '0');
            units[3].querySelector('.countdown-number').textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>

    <?php
    exit;
}

// Filter patients by appointment date
$filteredPatients = array_filter($patients, fn($p) => $p['Dates'] === $appointmentDate);
usort($filteredPatients, fn($a, $b) => $a['estimated_time'] - $b['estimated_time']);

$clinicTime = new DateTime($appointmentDate . ' 09:00');
$lunchStart = new DateTime($appointmentDate . ' 11:30');
$lunchEnd = new DateTime($appointmentDate . ' 13:00');
$lunchBreakInserted = false;
$counter = 1;
$bufferMinutes = 5; // 5-minute buffer between appointments
?>

<style>
    .schedule-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .schedule-header {
        color: #495057;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    .schedule-table {
        width: 100%;
        border-collapse: collapse;
    }
    .schedule-table th {
        background: #f8f9fa;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #eee;
    }
    .schedule-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .schedule-table tr:hover {
        background-color: #f8f9fa;
    }
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-confirmed {
        background-color: #d1e7ff;
        color: #0a58ca;
    }
    .status-in-progress {
        background-color: #cff4fc;
        color: #087990;
    }
    .status-complete {
        background-color: #d1fae5;
        color: #047857;
    }
    .status-cancelled {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .lunch-break {
        background-color: #fff8e1 !important;
        font-weight: 500;
        color: #5c3b00;
    }
    .user-appointment {
        background-color: #e6f7e6 !important;
    }
    .time-slot {
        font-weight: 500;
        color: #0d6efd;
    }
</style>

<div class="schedule-container">
    <h2 class="schedule-header">
        <i class="far fa-calendar-alt me-2"></i>
        Your Appointment Schedule for <?= date('F j, Y', strtotime($appointmentDate)) ?>
    </h2>
    
    <table class="schedule-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Procedures</th>
                <th>Duration</th>
                <th>Teeth</th>
                <th>Scheduled Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filteredPatients as $patient): 
                $startTime = clone $clinicTime;
                $endTime = clone $startTime;
                $endTime->modify("+{$patient['estimated_time']} minutes");

                // Check if we need to insert lunch break
                if (!$lunchBreakInserted && $endTime > $lunchStart) {
                    echo "<tr class='lunch-break'>
                            <td colspan='7'>
                                <i class='fas fa-utensils me-2'></i>
                                Lunch Break (11:30 AM - 1:00 PM)
                            </td>
                          </tr>";
                    $clinicTime = clone $lunchEnd;
                    $startTime = clone $clinicTime;
                    $endTime = clone $startTime;
                    $endTime->modify("+{$patient['estimated_time']} minutes");
                    $lunchBreakInserted = true;
                }

                $actualTimeRange = "<span class='time-slot'>" . $startTime->format('h:i A') . " - " . $endTime->format('h:i A') . "</span>";
                
                // Add buffer after this appointment (except last one)
                if ($counter < count($filteredPatients)) {
                    $clinicTime->modify("+{$patient['estimated_time']} minutes +{$bufferMinutes} minutes");
                } else {
                    $clinicTime->modify("+{$patient['estimated_time']} minutes");
                }

                $isCurrentUser = ($patient['user_id'] == $currentUserId);
                
                // Mask name for other patients
                $patientName = htmlspecialchars($patient['Names']);
                if (!$isCurrentUser) {
                    $len = strlen($patientName);
                    $first = substr($patientName, 0, 1);
                    $last = substr($patientName, -1);
                    $patientName = ($len >= 4) ? $first . str_repeat('*', $len - 2) . $last : $first . str_repeat('*', $len - 1);
                }

                // Determine status
                $status = $patient['Status'] ?? 'Pending';
                $statusClass = '';
                switch(strtolower($status)) {
                    case 'pending': $statusClass = 'status-pending'; break;
                    case 'confirmed': $statusClass = 'status-confirmed'; break;
                    case 'in progress': $statusClass = 'status-in-progress'; break;
                    case 'complete': $statusClass = 'status-complete'; break;
                    case 'cancelled': $statusClass = 'status-cancelled'; break;
                }
            ?>
            <tr <?= $isCurrentUser ? "class='user-appointment'" : "" ?>>
                <td><?= $counter++ ?></td>
                <td><?= $patientName ?></td>
                <td><?= htmlspecialchars($patient['Procedures']) ?></td>
                <td><?= $patient['estimated_time'] ?> mins</td>
                <td><?= htmlspecialchars($patient['Teeth']) ?></td>
                <td><?= $actualTimeRange ?></td>
                <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>