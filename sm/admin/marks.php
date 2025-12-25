<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$students = $pdo->query('SELECT s.id, u.name FROM students s LEFT JOIN users u ON s.user_id=u.id ORDER BY u.name')->fetchAll();
$subjects = $pdo->query('SELECT * FROM subjects ORDER BY name')->fetchAll();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
    $student_id = (int)$_POST['student_id'];
    $subject_id = (int)$_POST['subject_id'];
    $exam = trim($_POST['exam']);
    $max = (int)$_POST['max_marks'];
    $obt = (int)$_POST['obtained_marks'];
    $date = $_POST['exam_date'] ?: null;
    $stmt = $pdo->prepare('INSERT INTO marks(student_id,subject_id,exam,max_marks,obtained_marks,exam_date) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE max_marks=VALUES(max_marks), obtained_marks=VALUES(obtained_marks), exam_date=VALUES(exam_date)');
    $stmt->execute([$student_id,$subject_id,$exam,$max,$obt,$date]);
    $msg = 'Saved marks';
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Marks</h4>
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
          <?php foreach($students as $s): ?><option value="<?=(int)$s['id']?>"><?php echo h($s['name']); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Subject</label>
        <select name="subject_id" class="form-select" required>
          <option value="">Select</option>
          <?php foreach($subjects as $sub): ?><option value="<?=(int)$sub['id']?>"><?php echo h($sub['name']); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Exam</label>
        <input name="exam" class="form-control" required placeholder="Midterm/Final">
      </div>
      <div class="col-md-1">
        <label class="form-label">Max</label>
        <input type="number" name="max_marks" value="100" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Obtained</label>
        <input type="number" name="obtained_marks" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Exam Date</label>
        <input type="date" name="exam_date" class="form-control">
      </div>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Save</button>
  </form>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
