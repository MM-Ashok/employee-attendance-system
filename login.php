<?php
session_start();
require 'config.php'; // DB connection

// Set timezone to IST
date_default_timezone_set('Asia/Kolkata');

// Redirect to the appropriate dashboard if the user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } else {
        header("Location: employee/dashboard.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store user information in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Record login time in the employee_work_hours table only for employees
        if ($user['role'] === 'employee') {
            $login_time = date("Y-m-d H:i:s");
            $stmt = $pdo->prepare("INSERT INTO employee_work_hours (employee_id, login_time) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $login_time]);

            // Get the last inserted ID for work session tracking
            $_SESSION['work_id'] = $pdo->lastInsertId();
        }

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: employee/dashboard.php");
        }
        exit();
    } else {
        $error_message = "Invalid username or password";
    }
}
?>

<!-- Header -->
<?php include 'inc/header.php'; ?>
<!-- Main Content -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="login-container">
        <h3 class="text-center mb-4">Login</h3>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
</div>
<!-- Footer -->
<?php include 'inc/footer.php'; ?>
