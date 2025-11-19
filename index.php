<?php
require 'inc/config.php';
include 'inc/header.php';

// simple search and category filter (optional)
$search = trim($_GET['q'] ?? '');
$cat = trim($_GET['category'] ?? '');

$sql = "SELECT * FROM products WHERE 1";
$params = [];
if ($search !== '') {
  $sql .= " AND name LIKE :search";
  $params[':search'] = "%$search%";
}
if ($cat !== '') {
  $sql .= " AND category = :cat";
  $params[':cat'] = $cat;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// categories for filter
$cstmt = $pdo->query("SELECT DISTINCT category FROM products");
$cats = $cstmt->fetchAll(PDO::FETCH_COLUMN);
?>

<section>
  <form method="get" style="margin-bottom:12px">
    <input name="q" placeholder="Search products" value="<?=htmlspecialchars($search)?>">
    <select name="category">
      <option value="">All categories</option>
      <?php foreach($cats as $c): ?>
        <option value="<?=htmlspecialchars($c)?>" <?= $c === $cat ? 'selected' : '' ?>><?=htmlspecialchars($c)?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn">Search</button>
  </form>

  <div class="products">
    <?php foreach($products as $p): ?>
      <div class="product">
        <a href="product.php?id=<?= $p['id'] ?>">
          <img src="assets/img/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        </a>
        <h4><?= htmlspecialchars($p['name']) ?></h4>
        <div class="price">₹ <?= number_format($p['price'],2) ?></div>
        <p style="color:var(--muted);margin:8px 0"><?= htmlspecialchars($p['category']) ?> • Stock: <?= (int)$p['stock'] ?></p>
        <form method="post" action="cart.php">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <input type="number" name="qty" value="1" min="1" max="<?= (int)$p['stock'] ?>" style="width:70px;padding:6px">
          <button class="btn" name="add_to_cart" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>Add to cart</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'inc/footer.php'; ?>
