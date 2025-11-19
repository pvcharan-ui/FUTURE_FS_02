<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mini Supermarket Store</title>

  <!-- Google font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- use relative paths so same code works locally & on host -->
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- site JS (deferred) -->
  <script src="assets/js/store.js" defer></script>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="index.php">Mini Supermarket</a>

    <nav class="main-nav" aria-label="Main navigation">
      <a href="index.php" class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>">Home</a>
      <a href="cart.php" class="nav-link <?= $current === 'cart.php' ? 'active' : '' ?>">
        Cart <span id="cart-count">(<?= array_sum($_SESSION['cart'] ?? []) ?: 0 ?>)</span>
      </a>

      <!-- admin uses relative link to login (session-based) -->
      <a href="admin/login.php" class="nav-link <?= $current === 'login.php' || $current === 'orders.php' ? 'active' : '' ?> admin-link">Admin</a>
    </nav>
  </div>
</header>

<main class="container">
