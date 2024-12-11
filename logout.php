<?php
session_start();
require 'config.php'; // DB connection

// Set timezone to IST
date_default_timezone_set('Asia/Kolkata');

if (isset($_SESSION['work_id']) && $_SESSION['role'] === 'employee') {
    $work_id = $_SESSION['work_id'];
    $logout_time = date("Y-m-d H:i:s");

    // Fetch the login time for the current work_id
    $stmt = $pdo->prepare("SELECT login_time FROM employee_work_hours WHERE id = ?");
    $stmt->execute([$work_id]);
    $work_entry = $stmt->fetch();

    if ($work_entry) {
        // Calculate hours worked
        $login_time = new DateTime($work_entry['login_time']);
        $logout_time_obj = new DateTime($logout_time);
        $hours_worked = $login_time->diff($logout_time_obj)->format('%H:%I:%S'); // Format as H:i:s

        // Update the employee_work_hours table with logout time and hours worked
        $stmt = $pdo->prepare("UPDATE employee_work_hours SET logout_time = ?, hours_worked = ? WHERE id = ?");
        $stmt->execute([$logout_time, $hours_worked, $work_id]);
    }
}

// Clear session data and redirect to login page
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
