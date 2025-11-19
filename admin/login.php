<?php
// admin/login.php
require __DIR__ . '/../inc/config.php';
session_start();

// If already logged in, go to orders
if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: /FUTURE_FS_02/admin/orders.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    // Change these values if you want different credentials
    $ADMIN_USER = 'admin';
    $ADMIN_PASS = 'storeadmin';

    if ($user === $ADMIN_USER && $pass === $ADMIN_PASS) {
        // login success
        $_SESSION['is_admin'] = true;
        // regenerate id for safety
        session_regenerate_id(true);
        header('Location: /FUTURE_FS_02/admin/orders.php');
        exit;
    } else {
        $errors[] = "Invalid username or password.";
    }
}

include __DIR__ . '/../inc/header.php';
?>

<section class="card" style="max-width:520px;margin:28px auto">
  <h2>Admin Login</h2>

  <?php if(!empty($errors)): ?>
    <div class="form-alert form-alert-error">
      <?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?>
    </div>
  <?php endif; ?>

  <form method="post" style="display:grid;gap:12px">
    <label>Username</label>
    <input name="username" type="text" required autofocus>

    <label>Password</label>
    <input name="password" type="password" required>

    <div style="display:flex;gap:10px;align-items:center">
      <button class="btn" type="submit">Sign in</button>
      <a href="/FUTURE_FS_02/" class="btn secondary">Back to store</a>
    </div>
  </form>
</section>

<?php include __DIR__ . '/../inc/footer.php'; ?>
