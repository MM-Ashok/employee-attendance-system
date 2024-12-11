<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // Database connection

$id = $_GET['id'];
$action = $_GET['action'];

// Check if the request ID is valid
$stmt = $pdo->prepare("SELECT employee_id FROM leave_requests WHERE id = ?");
$stmt->execute([$id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die("Leave request not found.");
}

$employee_id = $request['employee_id'];
$status = ($action === 'approve') ? 'approved' : 'rejected';
$notification_message = ($status === 'approved') ? "Your leave request has been approved." : "Your leave request has been rejected.";

// Update the leave request status and add a notification message
$stmt = $pdo->prepare("UPDATE leave_requests SET status = ?, notification = ? WHERE id = ?");
$stmt->execute([$status, $notification_message, $id]);

// Optionally: Send an email notification to the employee
$employee_stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$employee_stmt->execute([$employee_id]);
$user = $employee_stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $to = $user['username']; // Assuming the username is the email
    $subject = "Leave Request " . ucfirst($status);
    $message = $notification_message;
    mail($to, $subject, $message); // Simple email notification
}

// Redirect back to leave requests page
header("Location: view_leave_requests.php");
exit();
?>
