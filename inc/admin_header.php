<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Mini Supermarket</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin:0; font-family:'Inter', sans-serif; background:#f8fafc;
    }
    .admin-topbar {
      background:#fff;
      padding:16px 28px;
      box-shadow:0 1px 3px rgba(0,0,0,0.08);
      display:flex;
      justify-content:space-between;
      align-items:center;
    }
    .admin-logo {
      font-size:20px; font-weight:700;
      color:#1e3a8a; text-decoration:none;
    }
    .admin-nav a {
      margin-left:18px;
      color:#2563eb;
      text-decoration:none;
      font-weight:500;
    }
    .admin-nav a:hover { text-decoration:underline; }

    main {
      padding:30px;
      max-width:1300px;
      margin:auto;
    }
  </style>
</head>

<body>
<header class="admin-topbar">
  <a class="admin-logo" href="index.php">Admin Panel</a>

  <nav class="admin-nav">
    <a href="index.php">Dashboard</a>
    <a href="orders.php">Orders</a>
    <a href="logout.php">Logout</a>
    <a href="../index.php" target="_blank">Open Store</a>
  </nav>
</header>

<main>
