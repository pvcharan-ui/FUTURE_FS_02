<?php
require 'inc/config.php';
include 'inc/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) {
  echo "<p>Product not found.</p>";
  include 'inc/footer.php';
  exit;
}
?>

<div class="product-detail card">
  <img src="assets/img/<?= htmlspecialchars($p['image']) ?>" alt="">
  <div>
    <h2><?= htmlspecialchars($p['name']) ?></h2>
    <p class="price">₹ <?= number_format($p['price'],2) ?></p>
    <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
    <p><strong>Stock:</strong> <?= (int)$p['stock'] ?></p>
    <form method="post" action="cart.php">
      <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
      <input type="number" name="qty" value="1" min="1" max="<?= (int)$p['stock'] ?>" style="width:80px;padding:6px">
      <button class="btn" name="add_to_cart" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>Add to cart</button>
    </form>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
