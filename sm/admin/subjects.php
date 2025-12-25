<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = isset($_POST['csrf']) ? $_POST['csrf'] : '';
    if (!csrf_check($csrf)) { $error = 'Invalid CSRF token'; }
}
if ($action==='create' && $_SERVER['REQUEST_METHOD']==='POST' && !$error){
    try {
        $stmt = $pdo->prepare('INSERT INTO subjects(name,code) VALUES (?,?)');
        $stmt->execute([trim($_POST['name']),trim($_POST['code'])]);
        header('Location: subjects.php?msg=created');
        exit;
    } catch (Exception $e) {
        $error = APP_DEBUG ? $e->getMessage() : 'Failed to create subject';
    }
}
if ($action==='update' && $_SERVER['REQUEST_METHOD']==='POST' && !$error){
    try {
        $stmt = $pdo->prepare('UPDATE subjects SET name=?, code=? WHERE id=?');
        $stmt->execute([trim($_POST['name']),trim($_POST['code']),(int)$_POST['id']]);
        header('Location: subjects.php?msg=updated');
        exit;
    } catch (Exception $e) {
        $error = APP_DEBUG ? $e->getMessage() : 'Failed to update subject';
    }
}
if ($action==='delete' && isset($_GET['id'])){
    $pdo->prepare('DELETE FROM subjects WHERE id=?')->execute([(int)$_GET['id']]);
    header('Location: subjects.php?msg=deleted');exit;
}
$list=$pdo->query('SELECT * FROM subjects ORDER BY id DESC')->fetchAll();
$edit=null;
if ($action==='edit' && isset($_GET['id'])){
    $stmt=$pdo->prepare('SELECT * FROM subjects WHERE id=?');
    $stmt->execute([(int)$_GET['id']]);
    $edit=$stmt->fetch();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Subjects</h4>
  <div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Back</a>
    <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
  </div>
</div>
<div class="row g-3">
  <div class="col-md-5">
    <div class="card"><div class="card-body">
      <h6 class="mb-3"><?php echo $edit? 'Edit Subject':'Add Subject';?></h6>
      <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>
      <?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo h($msg); ?></div><?php endif; ?>
      <form method="post" action="subjects.php?action=<?php echo $edit ? 'update' : 'create'; ?>">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <?php if($edit):?><input type="hidden" name="id" value="<?php echo (int)$edit['id']; ?>"><?php endif;?>
        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required value="<?php echo isset($edit['name']) ? h($edit['name']) : ''; ?>"></div>
        <div class="mb-2"><label class="form-label">Code</label><input name="code" class="form-control" required value="<?php echo isset($edit['code']) ? h($edit['code']) : ''; ?>"></div>
        <button class="btn btn-primary mt-2" type="submit"><?php echo $edit? 'Update':'Add';?></button>
      </form>
    </div></div>
  </div>
  <div class="col-md-7">
    <div class="card"><div class="card-body">
      <h6 class="mb-3">All Subjects</h6>
      <table class="table table-striped table-sm">
        <thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($list as $row):?>
          <tr>
            <td><?php echo (int)$row['id']; ?></td>
            <td><?php echo h($row['name']); ?></td>
            <td><?php echo h($row['code']); ?></td>
            <td class="table-actions">
              <a class="btn btn-sm btn-outline-primary" href="subjects.php?action=edit&id=<?php echo (int)$row['id']; ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')" href="subjects.php?action=delete&id=<?php echo (int)$row['id']; ?>">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div></div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
