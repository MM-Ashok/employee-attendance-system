<?php


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$profile_image = '../uploads/default_profile.png'; // Default image
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Set the profile image if it exists
    if ($user && !empty($user['profile_image'])) {
        $profile_image = $user['profile_image'];
    }
}

?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a href="#" class="nav-link">
                <div class="nav-profile-image">
                   <img src="<?= htmlspecialchars($profile_image) ?>" alt="Profile Image" width="40" height="40" class="rounded-circle">
                  <span class="login-status online"></span>
                  <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                  <span class="font-weight-bold mb-2"><?php echo $_SESSION['username']; ?></span>
                  <span class="text-secondary text-small">Project Manager</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/dashboard.php">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-title">Employee Management</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-crosshairs-gps menu-icon"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/view_employees.php">View Employee List</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/add_employee.php">Add Employee</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/view_leave_requests.php">View Leave Request</a>
                  </li>
                </ul>
              </div>
            </li>

            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <span class="menu-title">Track Management</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-lock menu-icon"></i>
              </a>
              <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/view_work_hours.php"> Track Employee Time </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/display_working_hours.php"> Report </a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </nav>