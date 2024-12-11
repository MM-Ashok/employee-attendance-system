<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../config.php'; // Include your database configuration

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
                </span> Dashboard
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="row">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Recent Leave Request</h4>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th> Employee Username </th>
                            <th> Leave Type </th>
                            <th> Reason </th>
                            <th> Status </th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leave_requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['username']); ?></td>
                                    <td><?php echo htmlspecialchars($request['leave_type']); ?></td>
                                    <td>
                                      <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal_<?php echo $request['id']; ?>">Show Reason <i class="mdi mdi-play-circle ms-1"></i></button>
                                        <div class="modal fade" id="exampleModal_<?php echo $request['id']; ?>" tabindex="-1" aria-labelledby="exampleModal_<?php echo $request['id']; ?>Label" style="display: none;" aria-hidden="true">
                                          <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Reason</h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                  <span aria-hidden="true">×</span>
                                                </button>
                                              </div>
                                              <div class="modal-body">
                                                 <?php echo $request['reason']; ?>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                    </td>
                                    <?php if ($request['status'] == 'approved'){ ?>
                                        <td><label class="badge badge-gradient-success"><?php echo ucfirst(htmlspecialchars($request['status'])); ?></label></td>
                                    <?php } else { ?>
                                        <td><label class="badge badge-gradient-danger"><?php echo ucfirst(htmlspecialchars($request['status'])); ?></label></td>
                                      <?php } ?>
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
    </div>
</div>
<?php include 'inc/footer.php'; ?>