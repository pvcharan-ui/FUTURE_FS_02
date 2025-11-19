<?php
require 'inc/config.php';
include 'inc/header.php';

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
  echo "<p>Your cart is empty. <a href='index.php'>Shop now</a></p>";
  include 'inc/footer.php';
  exit;
}

// fetch items and compute total (same as cart)
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$rows = $stmt->fetchAll();
$total = 0;
$items = [];
foreach($rows as $r) {
  $pid = $r['id'];
  $qty = $_SESSION['cart'][$pid];
  $items[] = ['id'=>$pid,'name'=>$r['name'],'qty'=>$qty,'price'=>$r['price'],'stock'=>$r['stock']];
  $total += $qty * $r['price'];
}

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $address = trim($_POST['address'] ?? '');

  if($name===''||$email===''||$address==='') $errors[] = "All fields required.";
  elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";

  if(empty($errors)) {
    // start transaction
    $pdo->beginTransaction();
    try {
      $stmt = $pdo->prepare("INSERT INTO orders (customer_name,email,address,total) VALUES (?,?,?,?)");
      $stmt->execute([$name,$email,$address,$total]);
      $orderId = $pdo->lastInsertId();

      $insertItem = $pdo->prepare("INSERT INTO order_items (order_id,product_id,qty,price) VALUES (?,?,?,?)");
      $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

      foreach($items as $it) {
        // ensure stock available
        if($it['qty'] > $it['stock']) throw new Exception("Not enough stock for {$it['name']}");

        $insertItem->execute([$orderId,$it['id'],$it['qty'],$it['price']]);
        $updateStock->execute([$it['qty'],$it['id'],$it['qty']]);
        if ($updateStock->rowCount() === 0) throw new Exception("Stock update failed for {$it['name']}");
      }

      $pdo->commit();
      // clear cart
      unset($_SESSION['cart']);
      header("Location: thankyou.php?order=".$orderId);
      exit;
    } catch(Exception $e) {
      $pdo->rollBack();
      $errors[] = "Order failed: " . $e->getMessage();
    }
  }
}
?>

<section class="card">
  <h2>Checkout</h2>
  <?php if(!empty($errors)): ?>
    <div class="form-alert form-alert-error"><?php foreach($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?></div>
  <?php endif; ?>

  <div>
    <h3>Order summary</h3>
    <ul>
      <?php foreach($items as $it): ?>
        <li><?=htmlspecialchars($it['name'])?> × <?= (int)$it['qty'] ?> — ₹ <?= number_format($it['price'] * $it['qty'],2) ?></li>
      <?php endforeach; ?>
    </ul>
    <p><strong>Total: ₹ <?= number_format($total,2) ?></strong></p>
  </div>

  <form method="post" style="margin-top:12px">
    <label>Name</label><br>
    <input name="name" required style="padding:8px;width:100%"><br><br>
    <label>Email</label><br>
    <input name="email" type="email" required style="padding:8px;width:100%"><br><br>
    <label>Address</label><br>
    <textarea name="address" rows="4" required style="width:100%;padding:8px"></textarea><br><br>
    <button class="btn" type="submit">Place Order (simulate payment)</button>
  </form>
</section>

<?php include 'inc/footer.php'; ?>
