<?php
// admin/orders.php - session-based admin area
require __DIR__ . '/../inc/config.php';
session_start();

// if not logged in, redirect to login
if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: /FUTURE_FS_02/admin/login.php');
    exit;
}

include __DIR__ . '/../inc/header.php';

// fetch orders
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>
<section class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2>Orders</h2>
    <div>
      <a href="/FUTURE_FS_02/admin/logout.php" class="btn secondary">Logout</a>
    </div>
  </div>

  <?php if(empty($orders)): ?>
    <p>No orders yet.</p>
  <?php else: ?>
    <?php foreach($orders as $o): ?>
      <div style="border:1px solid #eef6ff;padding:12px;margin-bottom:12px;border-radius:8px">
        <strong>Order #<?= $o['id'] ?></strong> — <?= htmlspecialchars($o['customer_name']) ?> (<?= htmlspecialchars($o['email']) ?>) — ₹ <?= number_format($o['total'],2) ?><br>
        <small><?= $o['created_at'] ?></small>
        <div style="margin-top:8px">
          <strong>Items:</strong>
          <ul>
            <?php
              $stmt = $pdo->prepare("SELECT oi.qty,oi.price,p.name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?");
              $stmt->execute([$o['id']]);
              $items = $stmt->fetchAll();
              foreach($items as $it) {
                echo "<li>".htmlspecialchars($it['name'])." × ".(int)$it['qty']." — ₹ ".number_format($it['price'] * $it['qty'],2)."</li>";
              }
            ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../inc/footer.php'; ?>
