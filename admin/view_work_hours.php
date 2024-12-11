<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // Database connection

// Query to get total hours, employee name, login time, and logout time
$stmt = $pdo->prepare("
    SELECT u.username,
           w.login_time,
           w.logout_time,
           w.hours_worked
    FROM employee_work_hours w
    JOIN users u ON w.employee_id = u.id
");
$stmt->execute();
$work_hours_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Header -->
<?php include 'inc/header.php'; ?>

    <div class="container-fluid page-body-wrapper">
      <?php include 'inc/sidebar.php'; ?>
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Employee Work Hours
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                     <span></span><a href="dashboard.php" class="btn btn-link btn-fw">Back to dashboard</a>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Employee Name</th>
                                    <th scope="col">Login Time</th>
                                    <th scope="col">Logout Time</th>
                                    <th scope="col">Total Hours Worked</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($work_hours_data)): ?>
                                    <?php foreach ($work_hours_data as $entry): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($entry['username']); ?></td>
                                            <td><?php echo htmlspecialchars($entry['login_time']); ?></td>
                                            <td><?php echo htmlspecialchars($entry['logout_time']); ?></td>
                                            <td><?php echo htmlspecialchars($entry['hours_worked']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
<!-- Footer -->
<?php include 'inc/footer.php'; ?>
