<?php include 'inc/header.php'; ?>
<section class="card">
  <h2>Thank you!</h2>
  <p>Your order #<?=htmlspecialchars($_GET['order'] ?? '')?> has been placed. We will process it shortly.</p>
  <p><a href="index.php">Continue shopping</a></p>
</section>
<?php include 'inc/footer.php'; ?>
