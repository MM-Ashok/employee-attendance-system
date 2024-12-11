<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // Include your database configuration

// Fetch all leave requests along with employee details
$stmt = $pdo->prepare("
    SELECT lr.id, u.username, lr.leave_type, lr.start_date, lr.end_date, lr.reason, lr.status
    FROM leave_requests lr
    JOIN users u ON lr.employee_id = u.id
");
$stmt->execute();
$leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                </span> Leave Requests
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                    <span></span><a href="dashboard.php" class="btn btn-link btn-fw">Back to dashboard</a>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Employee Username</th>
                                    <th scope="col">Leave Type</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>

                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($leave_requests as $request): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($request['id']); ?></td>
                                        <td><?php echo htmlspecialchars($request['username']); ?></td>
                                        <td><?php echo htmlspecialchars($request['leave_type']); ?></td>
                                        <td><?php echo htmlspecialchars($request['start_date']); ?></td>
                                        <td><?php echo htmlspecialchars($request['end_date']); ?></td>
                                        <?php if ($request['status'] == 'approved'){ ?>
                                           <td><label class="badge badge-gradient-success"><?php echo ucfirst(htmlspecialchars($request['status'])); ?></label></td>
                                        <?php } else { ?>
                                           <td><label class="badge badge-gradient-danger"><?php echo ucfirst(htmlspecialchars($request['status'])); ?></label></td>
                                          <?php } ?>
                                        <td>
                                            <a href="approve_leave.php?id=<?php echo $request['id']; ?>&action=approve" class="btn btn-inverse-success btn-fw">Approve</a>
                                            <a href="approve_leave.php?id=<?php echo $request['id']; ?>&action=reject" class="btn btn-inverse-danger btn-fw">Reject</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                </div>
            </div>
            </div>
        </div>
    </div>
<?php include 'inc/footer.php'; ?>
