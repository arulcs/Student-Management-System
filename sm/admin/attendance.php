<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

// Load students and subjects
$students = $pdo->query('SELECT s.id, u.name FROM students s LEFT JOIN users u ON s.user_id=u.id ORDER BY u.name')->fetchAll();
$subjects = $pdo->query('SELECT * FROM subjects ORDER BY name')->fetchAll();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
    $student_id = (int)($_POST['student_id'] ?? 0);
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $date = $_POST['att_date'] ?? date('Y-m-d');
    $status = $_POST['status'] === 'present' ? 'present' : 'absent';
    $stmt = $pdo->prepare('INSERT INTO attendance(student_id,subject_id,att_date,status) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status)');
    $stmt->execute([$student_id,$subject_id,$date,$status]);
    $msg = 'Saved attendance';
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Attendance</h4>
  <div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Back</a>
    <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
  </div>
</div>
<?php if ($msg): ?><div class="alert alert-success"><?php echo h($msg); ?></div><?php endif; ?>
<div class="card"><div class="card-body">
  <form method="post">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Student</label>
        <select name="student_id" class="form-select" required>
          <option value="">Select</option>
          <?php foreach($students as $s): ?>
            <option value="<?php echo (int)$s['id']; ?>"><?php echo h($s['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Subject</label>
        <select name="subject_id" class="form-select" required>
          <option value="">Select</option>
          <?php foreach($subjects as $sub): ?>
            <option value="<?php echo (int)$sub['id']; ?>"><?php echo h($sub['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Date</label>
        <input type="date" name="att_date" value="<?php echo date('Y-m-d'); ?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="present">Present</option>
          <option value="absent">Absent</option>
        </select>
      </div>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Save</button>
  </form>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
