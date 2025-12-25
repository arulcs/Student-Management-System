<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
if ((current_user()['role'] ?? '') !== 'student') { header('Location: ' . APP_BASE_URL . '/admin/dashboard.php'); exit; }
require_once __DIR__ . '/../config/db.php';

$user = current_user();
$stmt = $pdo->prepare('SELECT * FROM students WHERE user_id = ?');
$stmt->execute([$user['id']]);
$student = $stmt->fetch();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Student Dashboard</h4>
  <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
</div>
<div class="card"><div class="card-body">
  <h6 class="mb-2">Welcome, <?php echo h($user['name']); ?>!</h6>
  <div>Roll: <?php echo h($student['roll_no'] ?? ''); ?> | Class: <?php echo h($student['class'] ?? ''); ?></div>
  <div class="mt-3">
    <a class="btn btn-sm btn-primary" href="profile.php">Profile</a>
    <a class="btn btn-sm btn-primary" href="attendance.php">Attendance</a>
    <a class="btn btn-sm btn-primary" href="marks.php">Marks</a>
    <a class="btn btn-sm btn-secondary" href="report.php">Download Report (PDF)</a>
  </div>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
