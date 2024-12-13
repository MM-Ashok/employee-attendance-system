<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config.php';
date_default_timezone_set('Asia/Kolkata'); // Set the timezone to India Standard Time (IST)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['work_id'])) {
    $work_id = $_SESSION['work_id'];
    $input = json_decode(file_get_contents('php://input'));

    if (isset($input->pause_time)) {
        // Convert the ISO 8601 date to MySQL datetime format
        $pause_time = new DateTime($input->pause_time);
        $pause_time->setTimezone(new DateTimeZone('Asia/Kolkata')); // Ensure the time is in IST
        $pause_time = $pause_time->format('Y-m-d H:i:s'); // Format to MySQL DATETIME

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare("UPDATE employee_work_hours SET pause_time = ? WHERE id = ?");
        if ($stmt->execute([$pause_time, $work_id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update pause time']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'pause_time not set']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
