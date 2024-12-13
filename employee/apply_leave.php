<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Ensure employee is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // DB connection

// Fetch employee ID from session
$employee_id = $_SESSION['user_id'];

// Check if the employee ID exists in the users table
$check_employee = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$check_employee->execute([$employee_id]);
if ($check_employee->rowCount() == 0) {
    die("Invalid employee ID. Please contact the administrator.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    // Insert leave request into the database
    $stmt = $pdo->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$employee_id, $leave_type, $start_date, $end_date, $reason]);

    $success_message = "Leave request submitted successfully!";
}
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
                </span> Apply Leave
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                     <a href="<?php echo BASE_URL; ?>/employee/dashboard.php" class="btn btn-link btn-fw">Back to dashboard</a>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success_message); ?>
                            </div>
                        <?php endif; ?>
                        <form action="apply_leave.php" method="POST">
                            <div class="form-group">
                                <label for="leave_type">Leave Type:</label>
                                <select class="form-control" name="leave_type" required>
                                    <option value="sick">Sick Leave</option>
                                    <option value="casual">Casual Leave</option>
                                    <option value="vacation">Vacation</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="start_date">Start Date:</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>

                            <div class="form-group">
                                <label for="end_date">End Date:</label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason:</label>
                                <textarea class="form-control" name="reason" rows="4"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>