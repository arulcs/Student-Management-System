<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../config/db.php';
$user = current_user();
$stmt = $pdo->prepare('SELECT s.id FROM students s WHERE s.user_id=?');
$stmt->execute([$user['id']]);
$student = $stmt->fetch();
$stmt = $pdo->prepare('SELECT m.exam, sub.name subject, m.max_marks, m.obtained_marks, m.exam_date FROM marks m JOIN subjects sub ON m.subject_id=sub.id WHERE m.student_id=? ORDER BY m.exam_date DESC, sub.name');
$stmt->execute([$student['id']]);
$list = $stmt->fetchAll();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>My Marks</h4>
  <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
</div>
<div class="card"><div class="card-body">
  <table class="table table-striped table-sm"><thead><tr><th>Exam</th><th>Subject</th><th>Marks</th><th>Date</th></tr></thead><tbody>
    <?php foreach($list as $r): ?>
      <tr><td><?php echo h($r['exam']); ?></td><td><?php echo h($r['subject']); ?></td><td><?php echo (int)$r['obtained_marks']; ?>/<?php echo (int)$r['max_marks']; ?></td><td><?php echo h($r['exam_date']); ?></td></tr>
    <?php endforeach; ?>
  </tbody></table>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
