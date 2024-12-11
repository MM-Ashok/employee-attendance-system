<?php
require '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $pdo->beginTransaction();

    try {
        $stmtUser = $pdo->prepare("DELETE FROM users WHERE employee_id = ?");
        $stmtUser->execute([$id]);
        $stmtEmployee = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmtEmployee->execute([$id]);
        $pdo->commit();
        header("Location: view_employees.php?message=Employee+deleted+successfully");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: view_employees.php?error=Failed+to+delete+employee");
    }
} else {
    header("Location: view_employees.php?error=Invalid+employee+ID");
}
exit();
?>
