<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // Database connection

// Initialize variables
$filter_type = '';
$employee_id = '';
$selected_date = '';
$working_hours_data = [];

// Fetch all employees and their working hours
function getAllEmployeesWorkingHours($pdo)
{
    $query = "SELECT u.username, DATE(w.login_time) AS work_date, SUM(TIME_TO_SEC(w.hours_worked)) AS total_seconds
              FROM employee_work_hours w
              JOIN users u ON w.employee_id = u.id
              GROUP BY w.employee_id, DATE(w.login_time)";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch employee list
$employee_stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'employee'");
$employee_stmt->execute();
$employees = $employee_stmt->fetchAll(PDO::FETCH_ASSOC);

// Display all employees initially
$working_hours_data = getAllEmployeesWorkingHours($pdo);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filter_type = $_POST['filter_type'] ?? '';
    $employee_id = $_POST['employee_id'] ?? '';
    $selected_date = $_POST['selected_date'] ?? '';

    $query = "SELECT u.username, DATE(w.login_time) AS work_date, SUM(TIME_TO_SEC(w.hours_worked)) AS total_seconds ";
    $conditions = [];
    $params = [];

    // Filter by employee if selected
    if (!empty($employee_id)) {
        $conditions[] = "w.employee_id = :employee_id";
        $params[':employee_id'] = $employee_id;
    }

    // Filter by time period
    if (!empty($selected_date)) {
        $conditions[] = "DATE(w.login_time) = :selected_date";
        $params[':selected_date'] = $selected_date;
    } elseif ($filter_type === 'per_day') {
        $conditions[] = "DATE(w.login_time) = CURDATE()";
    } elseif ($filter_type === 'monthly') {
        $conditions[] = "MONTH(w.login_time) = MONTH(CURDATE()) AND YEAR(w.login_time) = YEAR(CURDATE())";
    }

    $query .= "FROM employee_work_hours w
               JOIN users u ON w.employee_id = u.id";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }
    $query .= " GROUP BY w.employee_id, DATE(w.login_time)";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $working_hours_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Helper function to convert seconds to "Hours and Minutes"
function formatTime($total_seconds)
{
    $hours = floor($total_seconds / 3600);
    $minutes = floor(($total_seconds % 3600) / 60);
    return "{$hours} Hours and {$minutes} Minutes";
}
?>

<!-- HTML -->
<?php include 'inc/header.php'; ?>
<div class="container-fluid page-body-wrapper">
    <?php include 'inc/sidebar.php'; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">Display Employee Total Working Hours</h3>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Section -->
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="filter_type">Select Time Period:</label>
                                <select id="filter_type" name="filter_type" class="form-control">
                                    <option value="">All</option>
                                    <option value="per_day" <?php echo ($filter_type === 'per_day') ? 'selected' : ''; ?>>Per Day</option>
                                    <option value="monthly" <?php echo ($filter_type === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="employee_id">Select Employee:</label>
                                <select id="employee_id" name="employee_id" class="form-control">
                                    <option value="">All Employees</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?php echo $employee['id']; ?>" <?php echo ($employee_id == $employee['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($employee['username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                <label for="selected_date">Select Date:</label>
                                <input type="date" id="selected_date" name="selected_date" class="form-control" value="<?php //echo htmlspecialchars($selected_date); ?>">
                            </div> -->
                            <button type="submit" class="btn btn-primary mt-3">Filter</button>
                        </form>

                        <!-- Table Section -->
                        <table class="table table-striped mt-4">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Date</th>
                                    <th>Total Hours Worked</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($working_hours_data)): ?>
                                    <?php foreach ($working_hours_data as $entry): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($entry['username'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($entry['work_date'] ?? 'N/A'); ?></td>
                                            <td><?php echo formatTime($entry['total_seconds'] ?? 0); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
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
