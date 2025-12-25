<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../config/db.php';
$user = current_user();
$stmt = $pdo->prepare('SELECT s.id FROM students s WHERE s.user_id=?');
$stmt->execute([$user['id']]);
$student = $stmt->fetch();
$stmt = $pdo->prepare('SELECT a.att_date, a.status, sub.name subject FROM attendance a JOIN subjects sub ON a.subject_id=sub.id WHERE a.student_id=? ORDER BY a.att_date DESC');
$stmt->execute([$student['id']]);
$list = $stmt->fetchAll();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>My Attendance</h4>
  <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
</div>
<div class="card"><div class="card-body">
  <table class="table table-striped table-sm"><thead><tr><th>Date</th><th>Subject</th><th>Status</th></tr></thead><tbody>
    <?php foreach($list as $r): ?>
      <tr><td><?php echo h($r['att_date']); ?></td><td><?php echo h($r['subject']); ?></td><td><?php echo h(ucfirst($r['status'])); ?></td></tr>
    <?php endforeach; ?>
  </tbody></table>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
