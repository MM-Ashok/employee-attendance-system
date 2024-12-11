<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit();
}
require '../config.php';
$employee_id = $_SESSION['user_id'];
// Fetch any notifications for this employee
$stmt = $pdo->prepare("SELECT notification FROM leave_requests WHERE employee_id = ? AND notification IS NOT NULL");
$stmt->execute([$employee_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<?php include 'inc/header.php'; ?>
<div class="container-fluid page-body-wrapper">
   <?php include 'inc/sidebar.php'; ?>
   <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Dashboard
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="row">
               <?php if (!empty($notifications)): ?>
                  <div id="notification" class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-danger card-img-holder text-white">
                      <div class="card-body">
                        <img src="../assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Your Leave request <i class="mdi mdi-chart-line mdi-24px float-end"></i>
                        </h4>
                        <?php foreach ($notifications as $notification): ?>
                            <h2 class="mb-5"><?php echo htmlspecialchars($notification['notification']); ?></h2>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
              <?php endif; ?>
            </div>
            <button id="start-time-btn" class="btn btn-success">Start Time</button>

<!-- Popup for Time Recording -->
<div id="timeRecordPopup" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Time Record</h2>
        <p id="time-status">Status: Not Started</p>
        <p id="timerDisplay">Timer: 00:00:00</p>
        <button id="pause-btn" class="btn btn-warning" style="display: none;">Pause</button>
        <button id="resume-btn" class="btn btn-primary" style="display: none;">Resume</button>
    </div>
</div>


        </div>
    </div>
</div>
<script>
let timer;
let startTime;
let elapsedTime = 0; // Time in seconds
let isPaused = false; // Track if the timer is paused

document.getElementById('start-time-btn').onclick = function() {
    startTime = new Date();
    elapsedTime = 0;
    document.getElementById('time-status').innerText = 'Status: Started';
    document.getElementById('start-time-btn').style.display = 'none'; // Hide start button
    document.getElementById('pause-btn').style.display = 'block'; // Show pause button
    document.getElementById('timeRecordPopup').style.display = 'block'; // Show the popup
    startTimer();
};

function startTimer() {
    timer = setInterval(() => {
        if (!isPaused) {
            elapsedTime++;
            document.getElementById('timerDisplay').innerText = formatTime(elapsedTime);
        }
    }, 1000);
}

function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
}

document.getElementById('pause-btn').onclick = function() {
    isPaused = true;
    document.getElementById('pause-btn').style.display = 'none'; // Hide pause button
    document.getElementById('resume-btn').style.display = 'block'; // Show resume button
    recordPause(); // Send pause time to server
};

document.getElementById('resume-btn').onclick = function() {
    isPaused = false;
    document.getElementById('resume-btn').style.display = 'none'; // Hide resume button
    document.getElementById('pause-btn').style.display = 'block'; // Show pause button
    recordResume(); // Send resume time to server
};

function recordPause() {
    fetch('record_pause.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ pause_time: new Date().toISOString() })
    })
    .then(response => {
        return response.text(); // Get the response as text first
    })
    .then(text => {
        console.log(text); // Log the text response
        const data = JSON.parse(text); // Try parsing it as JSON
        console.log(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function recordResume() {
    fetch('record_resume.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ resume_time: new Date().toISOString() })
    })
    .then(response => {
        return response.text(); // Get the response as text first
    })
    .then(text => {
        console.log(text); // Log the text response
        const data = JSON.parse(text); // Try parsing it as JSON
        console.log(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


// Close the modal
document.querySelector('.close').onclick = function() {
    document.getElementById('timeRecordPopup').style.display = 'none';
    clearInterval(timer); // Stop the timer
    document.getElementById('start-time-btn').style.display = 'block'; // Show start button again
    document.getElementById('pause-btn').style.display = 'none'; // Hide pause button
    document.getElementById('resume-btn').style.display = 'none'; // Hide resume button
    document.getElementById('timerDisplay').innerText = 'Timer: 00:00:00'; // Reset timer display
    document.getElementById('time-status').innerText = 'Status: Not Started'; // Reset status
};
</script>


<?php include 'inc/footer.php'; ?>