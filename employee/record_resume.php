<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config.php';
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['work_id'])) {
    $work_id = $_SESSION['work_id'];
    $input = json_decode(file_get_contents('php://input'));

    if (isset($input->resume_time)) {
        // Convert the ISO 8601 date to MySQL datetime format
        $resume_time = new DateTime($input->resume_time);
        $resume_time->setTimezone(new DateTimeZone('Asia/Kolkata')); // Ensure the time is in IST
        $resume_time = $resume_time->format('Y-m-d H:i:s'); // Format to MySQL DATETIME

        $stmt = $pdo->prepare("UPDATE employee_work_hours SET resume_time = ? WHERE id = ?");
        if ($stmt->execute([$resume_time, $work_id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update resume time']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'resume_time not set']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

?>
