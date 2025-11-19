<?php
// FUTURE_FS_02/index.php (public store)
require 'inc/config.php';
include 'inc/header.php';

// optional filters
$search = trim($_GET['q'] ?? '');
$cat = trim($_GET['category'] ?? '');

// build query safely
$sql = "SELECT * FROM products WHERE 1";
$params = [];
if ($search !== '') { $sql .= " AND name LIKE :search"; $params[':search'] = "%$search%"; }
if ($cat !== '')    { $sql .= " AND category = :cat"; $params[':cat'] = $cat; }
$sql .= " ORDER BY name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// categories for filter
$cstmt = $pdo->query("SELECT DISTINCT category FROM products");
$cats = $cstmt->fetchAll(PDO::FETCH_COLUMN);
?>

<section class="hero-wrap card" style="padding:18px">
  <h1 class="hero-title">Mini Supermarket</h1>
  <p class="hero-sub">Fresh groceries and essentials — add to cart & checkout.</p>

  <form method="get" style="margin-top:12px;display:flex;gap:8px;align-items:center">
    <input name="q" placeholder="Search products" value="<?= htmlspecialchars($search) ?>" style="padding:8px;border-radius:8px;border:1px solid rgba(15,23,42,0.06);">
    <select name="category" style="padding:8px;border-radius:8px;border:1px solid rgba(15,23,42,0.06);">
      <option value="">All categories</option>
      <?php foreach ($cats as $cOpt): ?>
        <option value="<?= htmlspecialchars($cOpt) ?>" <?= $cOpt === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cOpt) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Search</button>
  </form>
</section>

<?php
if (!is_array($products)) $products = [];
?>

<div class="products" style="margin-top:20px">
  <?php if (empty($products)): ?>
    <p>No products available right now. Check back later.</p>
  <?php else: ?>
    <?php foreach ($products as $p): 
      if (!is_array($p) || !isset($p['id'])) continue;
      $id = (int)$p['id'];
      $name = htmlspecialchars($p['name'] ?? 'Untitled');
      $price = number_format((float)($p['price'] ?? 0), 2);
      $image = htmlspecialchars($p['image'] ?? 'placeholder.png');
      $cat = htmlspecialchars($p['category'] ?? '');
      $stock = (int)($p['stock'] ?? 0);
    ?>
      <div class="product">
        <a href="product.php?id=<?= $id ?>"><img src="assets/img/<?= $image ?>" alt="<?= $name ?>"></a>
        <h4><?= $name ?></h4>
        <div class="price">₹ <?= $price ?></div>
        <p class="meta"><?= $cat ?> • Stock: <?= $stock ?></p>

        <form class="add-to-cart-form" data-product-id="<?= $id ?>">
          <div style="display:flex;gap:8px;align-items:center;margin-top:10px">
            <input class="qty" type="number" name="qty" value="1" min="1" max="<?= $stock ?>" style="width:74px;padding:6px">
            <button type="button" class="btn add-btn" <?= $stock <= 0 ? 'disabled' : '' ?>>
              <?= $stock <= 0 ? 'Out of stock' : 'Add to cart' ?>
            </button>
          </div>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'inc/footer.php'; ?>
