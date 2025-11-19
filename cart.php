<?php
// cart.php
require 'inc/config.php';
include 'inc/header.php';

// ensure session cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle legacy POST fallback (in case someone submits old non-AJAX form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // update qtys posted as qty[ID] = value
    foreach ($_POST['qty'] ?? [] as $pid => $q) {
        $pid = (int)$pid;
        $q = max(0, (int)$q);
        if ($q === 0) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $_SESSION['cart'][$pid] = $q;
        }
    }
    // redirect to avoid re-post
    header('Location: cart.php');
    exit;
}

// Build cart items array and total safely
$items = [];
$total = 0.0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    // prepare placeholders for IN()
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();

    // map rows to items and compute totals
    foreach ($rows as $r) {
        $pid = (int)$r['id'];
        $qty = (int)($_SESSION['cart'][$pid] ?? 0);
        if ($qty <= 0) continue;
        $line = $qty * (float)$r['price'];
        $items[] = [
            'id' => $pid,
            'name' => $r['name'],
            'price' => (float)$r['price'],
            'qty' => $qty,
            'stock' => (int)$r['stock'],
            'line_total' => $line
        ];
        $total += $line;
    }
}
?>

<section class="card">
  <h2>Your Cart</h2>

  <?php if (empty($items)): ?>
    <p>Your cart is empty. <a href="index.php">Shop now</a></p>
  <?php else: ?>
    <form id="cart-update-form" method="post">
      <table class="cart-table" aria-describedby="cart-summary">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Line</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= htmlspecialchars($it['name']) ?></td>
              <td>₹ <?= number_format($it['price'], 2) ?></td>
              <td>
                <input
                  type="number"
                  name="qty[<?= $it['id'] ?>]"
                  value="<?= $it['qty'] ?>"
                  min="0"
                  max="<?= $it['stock'] ?>"
                  style="width:80px;padding:6px"
                >
              </td>
              <td>₹ <?= number_format($it['line_total'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="cart-actions" style="margin-top:12px;align-items:center">
        <button type="button" id="update-cart-btn" class="btn-secondary">Update Cart</button>
        <strong style="margin-left:20px">Total: ₹ <?= number_format($total, 2) ?></strong>
        <a href="checkout.php" class="btn" style="margin-left:20px">Proceed to Checkout</a>
      </div>
    </form>
  <?php endif; ?>
</section>

<?php include 'inc/footer.php'; ?>
