<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $roll = trim($_POST['roll_no']);
    $class = trim($_POST['class']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,\'student\')');
        $pwd = password_hash($roll ?: 'student123', PASSWORD_DEFAULT);
        $stmt->execute([$name,$email,$pwd]);
        $user_id = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO students (user_id, roll_no, class, phone, address) VALUES (?,?,?,?,?)');
        $stmt->execute([$user_id,$roll,$class,$phone,$address]);
        $pdo->commit();
        header('Location: students.php?msg=created');
        exit;
    } catch(Exception $e) {
        $pdo->rollBack();
        $error = 'Failed to create student: ' . $e->getMessage();
    }
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $roll = trim($_POST['roll_no']);
    $class = trim($_POST['class']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare('SELECT user_id FROM students WHERE id=?');
    $stmt->execute([$id]);
    $stu = $stmt->fetch();

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('UPDATE students SET roll_no=?, class=?, phone=?, address=? WHERE id=?');
        $stmt->execute([$roll,$class,$phone,$address,$id]);
        $stmt = $pdo->prepare('UPDATE users SET name=?, email=? WHERE id=?');
        $stmt->execute([$name,$email,$stu['user_id']]);
        $pdo->commit();
        header('Location: students.php?msg=updated');
        exit;
    } catch(Exception $e) {
        $pdo->rollBack();
        $error = 'Failed to update student';
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT user_id FROM students WHERE id=?');
    $stmt->execute([$id]);
    $stu = $stmt->fetch();
    $pdo->beginTransaction();
    try {
        $pdo->prepare('DELETE FROM students WHERE id=?')->execute([$id]);
        if ($stu && $stu['user_id']) {
            $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$stu['user_id']]);
        }
        $pdo->commit();
        header('Location: students.php?msg=deleted');
        exit;
    } catch(Exception $e) {
        $pdo->rollBack();
        $error = 'Failed to delete student';
    }
}

$list = $pdo->query('SELECT s.*, u.name, u.email FROM students s LEFT JOIN users u ON s.user_id=u.id ORDER BY s.id DESC')->fetchAll();
$edit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT s.*, u.name, u.email FROM students s LEFT JOIN users u ON s.user_id=u.id WHERE s.id=?');
    $stmt->execute([(int)$_GET['id']]);
    $edit = $stmt->fetch();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Students</h4>
  <div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Back</a>
    <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
  </div>
</div>
<div class="row g-3">
  <div class="col-md-5">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-3"><?php echo $edit? 'Edit Student' : 'Add Student'; ?></h6>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>
        <form method="post" action="students.php?action=<?php echo $edit? 'update' : 'create'; ?>">
          <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
          <?php if ($edit): ?><input type="hidden" name="id" value="<?php echo (int)$edit['id']; ?>"><?php endif; ?>
          <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required value="<?php echo h($edit['name'] ?? ''); ?>"></div>
          <div class="mb-2"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required value="<?php echo h($edit['email'] ?? ''); ?>"></div>
          <div class="mb-2"><label class="form-label">Roll No</label><input name="roll_no" class="form-control" value="<?php echo h($edit['roll_no'] ?? ''); ?>"></div>
          <div class="mb-2"><label class="form-label">Class</label><input name="class" class="form-control" value="<?php echo h($edit['class'] ?? ''); ?>"></div>
          <div class="mb-2"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?php echo h($edit['phone'] ?? ''); ?>"></div>
          <div class="mb-2"><label class="form-label">Address</label><input name="address" class="form-control" value="<?php echo h($edit['address'] ?? ''); ?>"></div>
          <button class="btn btn-primary mt-2" type="submit"><?php echo $edit? 'Update' : 'Add'; ?></button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-3">All Students</h6>
        <div class="table-responsive">
          <table class="table table-striped table-sm">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Roll</th><th>Class</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($list as $row): ?>
                <tr>
                  <td><?php echo (int)$row['id']; ?></td>
                  <td><?php echo h($row['name']); ?></td>
                  <td><?php echo h($row['email']); ?></td>
                  <td><?php echo h($row['roll_no']); ?></td>
                  <td><?php echo h($row['class']); ?></td>
                  <td class="table-actions">
                    <a class="btn btn-sm btn-outline-primary" href="students.php?action=edit&id=<?php echo (int)$row['id']; ?>">Edit</a>
                    <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')" href="students.php?action=delete&id=<?php echo (int)$row['id']; ?>">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
