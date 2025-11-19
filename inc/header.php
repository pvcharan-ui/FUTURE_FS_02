<?php
if(session_status() === PHP_SESSION_NONE) session_start();
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

  <link rel="stylesheet" href="/FUTURE_FS_02/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="/FUTURE_FS_02/">Mini Supermarket</a>

    <nav class="main-nav" aria-label="Main navigation">
      <a href="/FUTURE_FS_02/" class="nav-link <?= $current==='index.php' ? 'active' : '' ?>">Home</a>
      <a href="/FUTURE_FS_02/cart.php" class="nav-link <?= $current==='cart.php' ? 'active' : '' ?>">Cart <span class="cart-count">(<?=
        array_sum($_SESSION['cart'] ?? []) ?: 0 ?>)</span></a>
      <!-- open admin in new tab so browser shows auth prompt immediately -->
      <a href="/FUTURE_FS_02/admin/orders.php" target="_blank" rel="noopener noreferrer" class="nav-link admin-link">Admin</a>
    </nav>
  </div>
</header>

<main class="container">
