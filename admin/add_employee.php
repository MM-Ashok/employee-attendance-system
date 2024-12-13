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

$error = ""; // To store any error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $date_of_joining = $_POST['date_of_joining'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Check if the email or username already exists
    $checkStmt = $pdo->prepare("SELECT * FROM employees WHERE email = :email");
    $checkStmt->execute([':email' => $email]);

    $checkUserStmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $checkUserStmt->execute([':username' => $username]);

    if ($checkStmt->rowCount() > 0) {
        $error = "The email address is already in use. Please choose another.";
    } elseif ($checkUserStmt->rowCount() > 0) {
        $error = "The username is already in use. Please choose another.";
    } else {
        // Insert employee data into employees table
        $stmt = $pdo->prepare("INSERT INTO employees (first_name, last_name, email, phone, position, date_of_joining) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $phone, $position, $date_of_joining]);

        // Get the last inserted employee ID
        $employee_id = $pdo->lastInsertId();

        // Insert user data into users table
        $stmt = $pdo->prepare("INSERT INTO users (employee_id, username, password, role) VALUES (?, ?, ?, 'employee')");
        $stmt->execute([$employee_id, $username, $password]);

        // Redirect to employee list view after successful registration
        header("Location: view_employees.php");
        exit();
    }
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
                </span> Add New Employee
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
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form action="add_employee.php" method="POST">
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="first_name">First Name:</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label for="last_name">Last Name:</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label for="phone">Phone:</label>
                                    <input type="text" class="form-control" name="phone">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="position">Position:</label>
                                    <input type="text" class="form-control" name="position">
                                </div>
                                <div class="col-sm form-group">
                                    <label for="date_of_joining">Date of Joining:</label>
                                    <input type="date" class="form-control" name="date_of_joining">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                <div class="col-sm form-group">
                                    <label for="password">Password:</label>
                                    <input type="password" class="form-control" name="password" required>
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
