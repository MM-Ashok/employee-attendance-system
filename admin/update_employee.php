<?php
require '../config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $date_of_joining = $_POST['date_of_joining'];

    $stmt = $pdo->prepare("UPDATE employees SET first_name = ?, last_name = ?, email = ?, phone = ?, position = ?, date_of_joining = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone, $position, $date_of_joining, $id]);

    header("Location: view_employees.php");
    exit();
}
?>
