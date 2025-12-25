<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$counts = [
  'students' => $pdo->query('SELECT COUNT(*) c FROM students')->fetch()['c'] ?? 0,
  'teachers' => $pdo->query('SELECT COUNT(*) c FROM teachers')->fetch()['c'] ?? 0,
  'subjects' => $pdo->query('SELECT COUNT(*) c FROM subjects')->fetch()['c'] ?? 0,
];
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Admin Dashboard</h4>
  <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
</div>
<div class="row g-3">
  <div class="col-md-4"><div class="card"><div class="card-body"><h6>Students</h6><div class="display-6"><?php echo $counts['students']; ?></div></div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><h6>Teachers</h6><div class="display-6"><?php echo $counts['teachers']; ?></div></div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><h6>Subjects</h6><div class="display-6"><?php echo $counts['subjects']; ?></div></div></div></div>
</div>
<hr>
<div class="list-group">
  <a class="list-group-item" href="students.php">Manage Students</a>
  <a class="list-group-item" href="teachers.php">Manage Teachers</a>
  <a class="list-group-item" href="subjects.php">Manage Subjects</a>
  <a class="list-group-item" href="attendance.php">Record Attendance</a>
  <a class="list-group-item" href="marks.php">Enter Marks</a>
  <a class="list-group-item" href="export_csv.php">Export CSV (All Students)</a>
  <a class="list-group-item" href="export_pdf.php">Export PDF (All Students)</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
