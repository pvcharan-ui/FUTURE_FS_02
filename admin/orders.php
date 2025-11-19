<?php
// admin/orders.php - polished, session-safe admin orders page
// safe session start
if (session_status() === PHP_SESSION_NONE) session_start();

// require config & DB
require __DIR__ . '/../inc/config.php';

// require admin login (uncomment for production)
// if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header('Location: login.php');
//     exit;
// }

// use admin header we created earlier
include __DIR__ . '/../inc/admin_header.php';

// handle CSV export
if (!empty($_GET['export']) && $_GET['export'] === '1') {
    $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=orders_export_' . date('Ymd_His') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Order ID','Customer Name','Email','Address','Total','Created At','Items']);
    foreach ($orders as $o) {
        $stmt = $pdo->prepare("SELECT oi.qty,oi.price,p.name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?");
        $stmt->execute([$o['id']]);
        $items = $stmt->fetchAll();
        $itemsText = [];
        foreach ($items as $it) {
            $itemsText[] = "{$it['name']} x{$it['qty']} @ {$it['price']}";
        }
        fputcsv($out, [$o['id'],$o['customer_name'],$o['email'],$o['address'],$o['total'],$o['created_at'],implode('; ',$itemsText)]);
    }
    fclose($out);
    exit;
}

// fetch orders (optionally filter by q)
$q = trim($_GET['q'] ?? '');

// IMPORTANT FIX:
// Use two separate parameter names if you need the same value in two places.
// That avoids "Invalid parameter number" errors on some PDO setups.
$sql = "SELECT * FROM orders";
$params = [];
if ($q !== '') {
    // use distinct placeholders :q1 and :q2 and bind both
    $sql .= " WHERE customer_name LIKE :q1 OR email LIKE :q2";
    $params[':q1'] = "%$q%";
    $params[':q2'] = "%$q%";
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

?>

<style>
/* small admin-orders specific tweaks */
.admin-toolbar{display:flex;justify-content:space-between;align-items:center;margin:18px 0 12px;gap:12px}
.admin-actions{display:flex;gap:10px;align-items:center}
.admin-search input{padding:8px;border-radius:8px;border:1px solid rgba(15,23,42,0.06)}
.order-card{border-radius:12px;padding:18px;margin-bottom:16px;background:#fff;border:1px solid rgba(15,23,42,0.03);box-shadow:0 8px 24px rgba(12,18,33,0.03)}
.order-meta{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap}
.order-meta .left{max-width:72%}
.order-meta h3{margin:0 0 6px 0}
.order-meta .meta{color:#6c757d;font-size:13px}
.items-list{margin-top:12px}
.items-list table{width:100%;border-collapse:collapse}
.items-list th,.items-list td{padding:8px;border-bottom:1px solid rgba(15,23,42,0.04);text-align:left}
.items-list th{background:rgba(15,23,42,0.02);font-weight:700}
.no-orders{color:#6c757d;padding:24px;background:#fff;border:1px dashed rgba(15,23,42,0.04);border-radius:10px}
@media(max-width:700px){
  .order-meta .left{max-width:100%}
  .admin-toolbar{flex-direction:column;align-items:flex-start}
}
</style>

<section class="card" style="background:transparent;box-shadow:none;padding:0">
  <div class="admin-toolbar">
    <div>
      <h1 style="margin:0">Orders</h1>
      <p style="margin:6px 0 0 0;color:#6c757d">Total orders: <?= count($orders) ?></p>
    </div>

    <div class="admin-actions">
      <div class="admin-search">
        <form method="get" style="display:flex;gap:8px;align-items:center">
          <input type="search" name="q" placeholder="Search name or email" value="<?= htmlspecialchars($q) ?>">
          <button class="btn" type="submit">Search</button>
          <a class="btn secondary" href="orders.php?export=1" style="text-decoration:none">Export CSV</a>
        </form>
      </div>
    </div>
  </div>

  <?php if (empty($orders)): ?>
    <div class="no-orders">No orders found.</div>
  <?php else: ?>
    <?php foreach ($orders as $o): ?>
      <div class="order-card">
        <div class="order-meta">
          <div class="left">
            <h3>Order #<?= htmlspecialchars($o['id']) ?> — <?= htmlspecialchars($o['customer_name']) ?> <span style="color:#6c757d">(<?= htmlspecialchars($o['email']) ?>)</span></h3>
            <div class="meta"><?= nl2br(htmlspecialchars($o['address'])) ?></div>
          </div>

          <div class="right" style="text-align:right">
            <div style="font-weight:800;font-size:18px">₹ <?= number_format($o['total'],2) ?></div>
            <div class="meta" style="margin-top:6px"><?= htmlspecialchars($o['created_at']) ?></div>
          </div>
        </div>

        <div class="items-list">
          <strong>Items:</strong>
          <table>
            <thead>
              <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Line</th></tr>
            </thead>
            <tbody>
              <?php
                $stmt2 = $pdo->prepare("SELECT oi.qty, oi.price, p.name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?");
                $stmt2->execute([$o['id']]);
                $items = $stmt2->fetchAll();
                foreach ($items as $it):
              ?>
                <tr>
                  <td><?= htmlspecialchars($it['name']) ?></td>
                  <td style="width:60px"><?= (int)$it['qty'] ?></td>
                  <td style="width:120px">₹ <?= number_format($it['price'],2) ?></td>
                  <td style="width:120px">₹ <?= number_format($it['price'] * $it['qty'],2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<?php
// include admin footer
include __DIR__ . '/../inc/admin_footer.php';
