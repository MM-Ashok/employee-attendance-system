<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../config.php'; // Database connection

$admin_id = $_SESSION['user_id']; // Ensure `admin_id` is stored in session upon login

// Fetch existing profile information
$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$user = $stmt->fetch();

// Check if there's a success message and clear it after displaying
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Check if there's an error message and clear it after displaying
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_updated = false;

    // Update profile picture if a new file is uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $filename = basename($_FILES['profile_image']['name']);
        $target_file = $upload_dir . $filename;

        // Move uploaded file to target location
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->execute([$target_file, $admin_id]);
            $profile_updated = true;
        }
    }

    // Update password only if both fields are filled
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $admin_id]);
            $profile_updated = true;
            $_SESSION['success_message'] = "Password has been reset successfully!";
        } else {
            $_SESSION['error_message'] = "Passwords do not match!";
            header("Location: change_profile.php");
            exit();
        }
    } elseif (!empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
        $_SESSION['error_message'] = "Please fill in both password fields if you wish to update the password.";
        header("Location: change_profile.php");
        exit();
    }

    // Set success message if any profile changes were made
    if ($profile_updated) {
        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: change_profile.php");
        exit();
    }
}
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
                </span> Change Profile
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                     <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-link btn-fw">Back to list</a>
                  </li>
                </ul>
              </nav>
            </div>

            <!-- Display success message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <!-- Display error message -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form action="change_profile.php" method="POST" enctype="multipart/form-data">
                            <?php if ($user['profile_image']): ?>
                                <div class="row">
                                    <div class="col-sm form-group">
                                        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" width="100" height="100">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="profile_image">Profile Image:</label>
                                    <input type="file" name="profile_image" class="form-control" id="profile_image">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm form-group">
                                    <label for="new_password">New Password (optional):</label>
                                    <input type="password" name="new_password" class="form-control" id="new_password">
                                </div>

                                <div class="col-sm form-group">
                                    <label for="confirm_password">Confirm Password (optional):</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>
