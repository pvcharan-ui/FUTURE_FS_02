<?php
// admin/index.php - modern dashboard
require __DIR__ . '/../inc/config.php';
session_start();

// If you want to require login, keep this; for local testing you can comment it.
// if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header('Location: login.php');
//     exit;
// }
include __DIR__ . '/../inc/admin_header.php';
?>

<style>
/* admin styles (small) */
.admin-wrapper { max-width:1100px; margin:40px auto; padding:22px; }
.admin-title{ font-size:30px; font-weight:700; margin-bottom:6px; }
.admin-sub{ color:#6c757d; margin-bottom:22px; }
.admin-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; }
.admin-card{ background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 24px rgba(0,0,0,0.06); }
.admin-card h3{ margin-top:0; }
.btn-admin{ display:inline-block; padding:10px 14px; border-radius:8px; text-decoration:none; background:#2563eb; color:#fff; }
.btn-secondary{ background:#f1f5f9; color:#111; padding:8px 12px; border-radius:8px; text-decoration:none; }
.top-right-links{ text-align:right; margin-bottom:18px; }
</style>

<div class="admin-wrapper">
  <div class="top-right-links">
    <a href="orders.php">View Orders</a> &nbsp; <a href="logout.php">Logout</a>
  </div>

  <div class="admin-title">Admin Dashboard</div>
  <div class="admin-sub">Quick links and management tools</div>

  <div class="admin-grid">
    <div class="admin-card">
      <h3>Orders</h3>
      <p>View and export customer orders.</p>
      <p><a href="orders.php" class="btn-admin">Open Orders</a></p>
    </div>

    <div class="admin-card">
      <h3>Products</h3>
      <p>Manage products, stock & pricing (coming soon).</p>
      <p><a class="btn-secondary" style="opacity:.6; cursor:not-allowed;">Coming soon</a></p>
    </div>

    <div class="admin-card">
      <h3>Store Front</h3>
      <p>Open the public store.</p>
      <p><a href="../index.php" class="btn-secondary">Open Store</a></p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../inc/admin_footer.php'; ?>
