<?php
session_start();
require '../config.php'; // DB connection file
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT * FROM employees";
$stmt = $pdo->query($query);
$employees = $stmt->fetchAll();
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
                </span> Employee List
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                    <span></span><a href="add_employee.php" class="btn btn-gradient-light">Add New Employee <i class="fa fa-address-book"></i></a>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Position</th>
                            <th scope="col">Date of Joining</th>
                            <th scope="col">Actions</th>
                        </tr>
                        <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td class="py-1"><img src="../assets/images/faces/pic-1.png" alt="image"></td>
                            <td><?= $employee['first_name'] . ' ' . $employee['last_name']; ?></td>
                            <td><?= $employee['email']; ?></td>
                            <td><?= $employee['phone']; ?></td>
                            <td><?= $employee['position']; ?></td>
                            <td><?= $employee['date_of_joining']; ?></td>
                            <td>
                                <a href="edit_employee.php?id=<?= $employee['id']; ?>"><i class="fa fa-edit" style="font-size: 20px;"></i></a>

                                <a href="delete_employee.php?id=<?= $employee['id']; ?>" onclick="return confirm('Are you sure? You want to delete');"><i class="mdi mdi-delete menu-icon" style="font-size: 20px;"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
<?php include 'inc/footer.php'; ?>
