<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // DB connection

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee) {
    echo "Employee not found!";
    exit();
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
                </span> Update Employee
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                    <a href="<?php echo BASE_URL; ?>/admin/view_employees.php" class="btn btn-link btn-fw">Back to list</a>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form action="update_employee.php" method="POST">
                        <input type="hidden" name="id" value="<?= $employee['id']; ?>">
                        <div class="row">
                                <div class="col-sm form-group">
                                    <label for="first_name">First Name:</label>
                                    <input type="text" class="form-control" name="first_name" value="<?= $employee['first_name']; ?>" required>
                                </div>
                                <div class="col-sm orm-group">
                                    <label for="last_name">Last Name:</label>
                                    <input type="text" class="form-control" name="last_name" value="<?= $employee['last_name']; ?>" required>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-sm form-group">
                                    <label for="email">Email:</label>
                                    <input type="text" class="form-control" name="email" value="<?= $employee['email']; ?>" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label for="phone">Phone:</label>
                                    <input type="text" class="form-control" name="phone" value="<?= $employee['phone']; ?>">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-sm form-group">
                                <label for="position">Position:</label>
                                <input type="text" class="form-control" name="position" value="<?= $employee['position']; ?>">
                                </div>
                                <div class="col-sm form-group">
                                    <label for="date_of_joining">Date of Joining:</label>
                                    <input type="date" class="form-control" name="date_of_joining" value="<?= $employee['date_of_joining']; ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Employee</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>
