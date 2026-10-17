<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : 'all';
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
$statusValue = 'all';

if ($status === 'available') {
    $statusValue = 'AVAILABLE';
} elseif ($status === 'sold-out') {
    $statusValue = 'SOLD OUT';
}

$where = [];
$params = [];

if ($category !== 'all') {
    $where[] = 'category = ?';
    $params[] = $category;
}

if ($statusValue !== 'all') {
    $where[] = 'availability = ?';
    $params[] = $statusValue;
}

if ($search !== '') {
    $where[] = '(name LIKE ? OR category LIKE ? OR size LIKE ?)';
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql = 'SELECT * FROM products';
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC';

$stmt = getDb()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="eyebrow">Shop our collection</span>
                <h1>Browse all shoes</h1>
            </div>
        </div>

        <div class="shop-filters">
            <a href="<?php echo BASE_URL; ?>/shop.php" class="tag-filter <?php echo $category === 'all' ? 'active' : ''; ?>">All</a>
            <a href="<?php echo BASE_URL; ?>/shop.php?category=Heels" class="tag-filter <?php echo $category === 'Heels' ? 'active' : ''; ?>">Heels</a>
            <a href="<?php echo BASE_URL; ?>/shop.php?category=Boots" class="tag-filter <?php echo $category === 'Boots' ? 'active' : ''; ?>">Boots</a>
            <a href="<?php echo BASE_URL; ?>/shop.php?status=available" class="tag-filter <?php echo $status === 'available' ? 'active' : ''; ?>">Available</a>
            <a href="<?php echo BASE_URL; ?>/shop.php?status=sold-out" class="tag-filter <?php echo $status === 'sold-out' ? 'active' : ''; ?>">Sold Out</a>
        </div>

        <form class="search-box" method="get" action="shop.php">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search by name, size, or category" aria-label="Search products">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Search</button>
        </form>

        <div class="product-grid">
            <?php if (empty($products)): ?>
                <div class="info-card" style="grid-column: 1 / -1;">
                    <h3>No shoes match your search.</h3>
                    <p>Try another size, category, or availability status.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <img src="<?php echo productImageUrl($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
                                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo (int)$product['id']; ?>" class="link-btn">View</a>
                                <?php if ($product['availability'] === 'AVAILABLE'): ?>
                                    <a href="<?php echo buildWhatsAppLink($product['name'], $product['size'], $product['price']); ?>" class="link-btn primary" target="_blank" rel="noopener noreferrer">Order</a>
                                <?php else: ?>
                                    <span class="link-btn disabled" aria-disabled="true">Sold out</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
