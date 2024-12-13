<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // Database connection

// Fetch employee list
$employee_stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'employee'");
$employee_stmt->execute();
$employees = $employee_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML -->
<?php include 'inc/header.php'; ?>
<div class="container-fluid page-body-wrapper">
    <?php include 'inc/sidebar.php'; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">Employee List</h3>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!-- Employee Table -->
                        <table class="table table-striped mt-4">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($employees)): ?>
                                    <?php foreach ($employees as $employee): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($employee['id']); ?></td>
                                            <td><?php echo htmlspecialchars($employee['username']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">No employees found</td>
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
