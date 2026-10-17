<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDb();
$stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC LIMIT 4');
$featuredProducts = $stmt->fetchAll();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main>
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="eyebrow">Refurbished fashion</span>
                <h1>Stylish heels and boots in second-hand.</h1>
                <p>Thrift with Ramo brings you quality Second-hand footwear in Uganda, with elegant styles, affordable pricing, and quick WhatsApp ordering.</p>
                <div class="hero-actions">
                    <a href="<?php echo BASE_URL; ?>/shop.php" class="primary-btn">Shop now</a>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo rawurlencode('Hello Thrift with Ramo, I would like to order a pair of shoes.'); ?>" class="secondary-btn" target="_blank" rel="noopener noreferrer">Order on WhatsApp</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <img src="uploads/black boot.jpeg" alt="Heels and boots display" />
                </div>
                <div class="badge-box">
                    <span>Fresh arrivals</span>
                    <strong>UGX 20k..</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <span class="eyebrow">Featured picks</span>
                    <h2>Fresh from the shop</h2>
                </div>
                <a href="<?php echo BASE_URL; ?>/shop.php" class="ghost-btn">Browse all shoes</a>
            </div>

            <div class="product-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <article class="product-card">
                        <img src="<?php echo productImageUrl($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="card-body">
                            <div class="card-top">
                                <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <span class="status-tag <?php echo $product['availability'] === 'AVAILABLE' ? 'available' : 'sold-out'; ?>"><?php echo htmlspecialchars($product['availability']); ?></span>
                            </div>
                            <div class="product-meta">
                                <span><?php echo htmlspecialchars($product['category']); ?></span>
                                <span>Size <?php echo htmlspecialchars($product['size']); ?></span>
                            </div>
                            <p class="price"><?php echo formatPrice($product['price']); ?></p>
                            <div class="card-actions">
                                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo (int) $product['id']; ?>" class="link-btn">View</a>
                                <?php if ($product['availability'] === 'AVAILABLE'): ?>
                                    <a href="<?php echo buildWhatsAppLink($product['name'], $product['size'], $product['price']); ?>" class="link-btn primary" target="_blank" rel="noopener noreferrer">Order</a>
                                <?php else: ?>
                                    <span class="link-btn disabled" aria-disabled="true">Sold out</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
