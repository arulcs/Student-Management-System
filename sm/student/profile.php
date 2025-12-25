<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../config/db.php';
$user = current_user();
$stmt = $pdo->prepare('SELECT s.*, u.name, u.email FROM students s LEFT JOIN users u ON s.user_id=u.id WHERE s.user_id=?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>My Profile</h4>
  <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
</div>
<div class="card"><div class="card-body">
  <div><strong>Name:</strong> <?php echo h($profile['name']); ?></div>
  <div><strong>Email:</strong> <?php echo h($profile['email']); ?></div>
  <div><strong>Roll No:</strong> <?php echo h($profile['roll_no']); ?></div>
  <div><strong>Class:</strong> <?php echo h($profile['class']); ?></div>
  <div><strong>Phone:</strong> <?php echo h($profile['phone']); ?></div>
  <div><strong>Address:</strong> <?php echo h($profile['address']); ?></div>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
