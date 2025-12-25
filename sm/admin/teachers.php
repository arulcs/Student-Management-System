<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? 'list';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
}
if ($action==='create' && $_SERVER['REQUEST_METHOD']==='POST'){
    $stmt=$pdo->prepare('INSERT INTO teachers(name,email,phone) VALUES (?,?,?)');
    $stmt->execute([trim($_POST['name']),trim($_POST['email']),trim($_POST['phone'])]);
    header('Location: teachers.php?msg=created');exit;
}
if ($action==='update' && $_SERVER['REQUEST_METHOD']==='POST'){
    $stmt=$pdo->prepare('UPDATE teachers SET name=?, email=?, phone=? WHERE id=?');
    $stmt->execute([trim($_POST['name']),trim($_POST['email']),trim($_POST['phone']),(int)$_POST['id']]);
    header('Location: teachers.php?msg=updated');exit;
}
if ($action==='delete' && isset($_GET['id'])){
    $pdo->prepare('DELETE FROM teachers WHERE id=?')->execute([(int)$_GET['id']]);
    header('Location: teachers.php?msg=deleted');exit;
}
$list=$pdo->query('SELECT * FROM teachers ORDER BY id DESC')->fetchAll();
$edit=null;
if ($action==='edit' && isset($_GET['id'])){
    $stmt=$pdo->prepare('SELECT * FROM teachers WHERE id=?');
    $stmt->execute([(int)$_GET['id']]);
    $edit=$stmt->fetch();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Teachers</h4>
  <div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Back</a>
    <a href="<?php echo APP_BASE_URL; ?>/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
  </div>
</div>
<div class="row g-3">
  <div class="col-md-5">
    <div class="card"><div class="card-body">
      <h6 class="mb-3"><?php echo $edit? 'Edit Teacher':'Add Teacher';?></h6>
      <form method="post" action="teachers.php?action=<?=$edit?'update':'create'?>">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <?php if($edit):?><input type="hidden" name="id" value="<?=(int)$edit['id']?>"><?php endif;?>
        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required value="<?php echo h($edit['name']??''); ?>"></div>
        <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?php echo h($edit['email']??''); ?>"></div>
        <div class="mb-2"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?php echo h($edit['phone']??''); ?>"></div>
        <button class="btn btn-primary mt-2" type="submit"><?php echo $edit? 'Update':'Add';?></button>
      </form>
    </div></div>
  </div>
  <div class="col-md-7">
    <div class="card"><div class="card-body">
      <h6 class="mb-3">All Teachers</h6>
      <table class="table table-striped table-sm">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($list as $row):?>
          <tr>
            <td><?=(int)$row['id']?></td>
            <td><?=h($row['name'])?></td>
            <td><?=h($row['email'])?></td>
            <td><?=h($row['phone'])?></td>
            <td class="table-actions">
              <a class="btn btn-sm btn-outline-primary" href="teachers.php?action=edit&id=<?= (int)$row['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')" href="teachers.php?action=delete&id=<?= (int)$row['id'] ?>">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div></div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
