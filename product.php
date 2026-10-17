<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);
$product = null;

if ($id > 0) {
    $stmt = getDb()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

if (!$product) {
    require_once __DIR__ . '/includes/header.php';
    echo '<main class="section"><div class="container"><div class="info-card"><h1>Product not found</h1><p>The selected shoe is no longer available or the link is incorrect.</p><a class="primary-btn" href="'.BASE_URL.'/shop.php">Go to shop</a></div></div></main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$productImages = getProductImages(getDb(), $product);
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="section">
    <div class="container details-layout">
        <div class="image-panel">
            <img id="product-main-image" src="<?php echo productImageUrl($productImages[0]); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php if (count($productImages) > 1): ?>
                <div class="product-image-thumbnails" aria-label="More pictures of this product">
                    <?php foreach ($productImages as $index => $image): ?>
                        <button type="button" class="gallery-thumb<?php echo $index === 0 ? ' active' : ''; ?>" data-image-src="<?php echo htmlspecialchars(productImageUrl($image), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Show product picture <?php echo $index + 1; ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                            <img src="<?php echo productImageUrl($image); ?>" alt="">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-summary">
            <span class="eyebrow"><?php echo htmlspecialchars($product['category']); ?></span>
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="summary-row">
                <span class="status-tag <?php echo $product['availability'] === 'AVAILABLE' ? 'available' : 'sold-out'; ?>"><?php echo htmlspecialchars($product['availability']); ?></span>
                <strong class="price"><?php echo formatPrice($product['price']); ?></strong>
            </div>

            <div class="meta-list">
                <div class="meta-item"><span>Size</span><strong><?php echo htmlspecialchars($product['size']); ?></strong></div>
                <div class="meta-item"><span>Condition</span><strong><?php echo htmlspecialchars($product['condition']); ?></strong></div>
                <div class="meta-item"><span>Category</span><strong><?php echo htmlspecialchars($product['category']); ?></strong></div>
            </div>

            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <?php if ($product['availability'] === 'AVAILABLE'): ?>
                <a href="<?php echo buildWhatsAppLink($product['name'], $product['size'], $product['price']); ?>" class="whatsapp-button" target="_blank" rel="noopener noreferrer">Order on WhatsApp</a>
            <?php else: ?>
                <button class="ghost-btn" disabled style="cursor: not-allowed; opacity: 0.7;">Sold Out</button>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.gallery-thumb').forEach((button) => {
        button.addEventListener('click', () => {
            const mainImage = document.getElementById('product-main-image');
            mainImage.src = button.dataset.imageSrc;
            document.querySelectorAll('.gallery-thumb').forEach((thumbnail) => {
                const selected = thumbnail === button;
                thumbnail.classList.toggle('active', selected);
                thumbnail.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
        });
    });
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
