<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = find_user_by_email($email);
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        if ($user['role'] === 'admin') {
            header('Location: ' . APP_BASE_URL . '/admin/dashboard.php');
        } else {
            header('Location: ' . APP_BASE_URL . '/student/dashboard.php');
        }
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Login</h5>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo h($error); ?></div>
        <?php endif; ?>
        <form method="post">
          <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100" type="submit">Sign in</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
