<?php
require 'inc/config.php';
include 'inc/header.php';

// init cart session
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// handle add / update / remove
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add_to_cart'])) {
    $pid = (int)$_POST['product_id'];
    $qty = max(1,(int)$_POST['qty']);
    // check stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $stock = $stmt->fetchColumn();
    if ($stock === false) {
      $msg = "Product not found.";
    } else {
      $qty = min($qty, (int)$stock);
      if(isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] += $qty;
      else $_SESSION['cart'][$pid] = $qty;
    }
    header("Location: cart.php");
    exit;
  }

  if (isset($_POST['update'])) {
    foreach($_POST['qty'] as $pid=>$q) {
      $pid = (int)$pid;
      $q = max(0,(int)$q);
      if ($q === 0) unset($_SESSION['cart'][$pid]);
      else $_SESSION['cart'][$pid] = $q;
    }
    header("Location: cart.php");
    exit;
  }
}

// fetch cart items
$items = [];
$total = 0.0;
if(!empty($_SESSION['cart'])) {
  $ids = array_keys($_SESSION['cart']);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $rows = $stmt->fetchAll();
  foreach($rows as $r) {
    $pid = $r['id'];
    $qty = $_SESSION['cart'][$pid];
    $r['qty'] = $qty;
    $r['line_total'] = $qty * $r['price'];
    $items[] = $r;
    $total += $r['line_total'];
  }
}
?>

<section class="card">
  <h2>Your Cart</h2>
  <?php if(empty($items)): ?>
    <p>Your cart is empty. <a href="index.php">Shop now</a></p>
  <?php else: ?>
    <form method="post">
      <table class="cart-table">
        <thead>
          <tr><th>Product</th><th>Price</th><th>Qty</th><th>Line</th></tr>
        </thead>
        <tbody>
          <?php foreach($items as $it): ?>
            <tr>
              <td><?=htmlspecialchars($it['name'])?></td>
              <td>₹ <?=number_format($it['price'],2)?></td>
              <td><input type="number" name="qty[<?=$it['id']?>]" value="<?=$it['qty']?>" min="0" max="<?=$it['stock']?>" style="width:70px;padding:6px"></td>
              <td>₹ <?=number_format($it['line_total'],2)?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div style="margin-top:12px">
        <button class="btn-secondary" type="submit" name="update">Update Cart</button>
        <strong style="margin-left:20px">Total: ₹ <?= number_format($total,2) ?></strong>
        <a href="checkout.php" class="btn" style="margin-left:20px">Proceed to Checkout</a>
      </div>
    </form>
  <?php endif; ?>
</section>

<?php include 'inc/footer.php'; ?>
