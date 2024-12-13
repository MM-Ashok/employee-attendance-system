<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // Database connection

// Default query
$query = "
    SELECT u.username, w.login_time, w.logout_time, w.hours_worked
    FROM employee_work_hours w
    JOIN users u ON w.employee_id = u.id
";

// Filter logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['filter_type'])) {
        $filter_type = $_POST['filter_type'];

        if ($filter_type === 'specific_date' && !empty($_POST['specific_date'])) {
            $specific_date = $_POST['specific_date'];
            $query .= " WHERE DATE(w.login_time) = :specific_date";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':specific_date', $specific_date);
        } elseif ($filter_type === 'date_range' && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $query .= " WHERE DATE(w.login_time) BETWEEN :start_date AND :end_date";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
        } else {
            $stmt = $pdo->prepare($query);
        }
    } else {
        $stmt = $pdo->prepare($query);
    }
} else {
    $stmt = $pdo->prepare($query);
}

$stmt->execute();
$work_hours_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML and JavaScript -->
<?php include 'inc/header.php'; ?>
<div class="container-fluid page-body-wrapper">
    <?php include 'inc/sidebar.php'; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">Employee Work Hours</h3>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Section -->
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="filter_type">Filter By:</label>
                                <select id="filter_type" name="filter_type" class="form-control" onchange="toggleFilterInputs()">
                                    <option value="">Select Filter</option>
                                    <option value="specific_date">Specific Date</option>
                                    <option value="date_range">Date Range</option>
                                </select>
                            </div>
                            <div id="specific_date_input" style="display: none;">
                                <label for="specific_date">Specific Date:</label>
                                <input type="date" id="specific_date" name="specific_date" class="form-control">
                            </div>
                            <div id="date_range_input" style="display: none;">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Filter</button>
                        </form>

                        <!-- Table Section -->
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Total Hours Worked</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($work_hours_data)): ?>
                                    <?php foreach ($work_hours_data as $entry): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($entry['username'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($entry['login_time'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($entry['logout_time'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($entry['hours_worked'] ?? 'N/A'); ?></td>
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

<!-- JavaScript -->
<script>
    function toggleFilterInputs() {
        const filterType = document.getElementById('filter_type').value;
        const specificDateInput = document.getElementById('specific_date_input');
        const dateRangeInput = document.getElementById('date_range_input');

        specificDateInput.style.display = filterType === 'specific_date' ? 'block' : 'none';
        dateRangeInput.style.display = filterType === 'date_range' ? 'block' : 'none';
    }
</script>
