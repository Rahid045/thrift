<?php
require_once __DIR__ . '/../config.php';
$siteName = APP_NAME;
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Thrift with Ramo sells quality refurbished heels and boots in Uganda.">
    <title><?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a href="<?php echo BASE_URL; ?>" class="brand">
            <span class="brand-mark">T</span>
            <span>Thrift with Ramo</span>
        </a>

        <button class="menu-toggle" aria-label="Open menu">☰</button>

        <nav class="site-nav">
            <a href="<?php echo BASE_URL; ?>" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo BASE_URL; ?>/shop.php" class="<?php echo $currentPage === 'shop.php' ? 'active' : ''; ?>">Shop</a>
            <a href="<?php echo BASE_URL; ?>/about.php" class="<?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About</a>
            <a href="<?php echo BASE_URL; ?>/contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
            <a href="<?php echo BASE_URL; ?>/admin/login.php" class="admin-link">Admin</a>
        </nav>

        <a class="whatsapp-button small" href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo rawurlencode('Hello Thrift with Ramo, I would like to place an order.'); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
    </div>
</header>
