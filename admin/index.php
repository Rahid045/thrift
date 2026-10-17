<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$pdo = getDb();
$notice = ['type' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_product' || $action === 'update_product') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? 'Heels');
        $price = normalizePriceValue($_POST['price'] ?? 0);
        $size = sanitizeInput($_POST['size'] ?? '');
        $condition = sanitizeInput($_POST['condition'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $availability = sanitizeInput($_POST['availability'] ?? 'AVAILABLE');

        if ($name === '' || $size === '' || $condition === '' || $description === '' || $price <= 0) {
            $notice = ['type' => 'error', 'message' => 'Please complete all required product fields.'];
        } else {
            $upload = handleImageUploads('images');

            if ($upload['status'] === 'error') {
                $notice = ['type' => 'error', 'message' => $upload['message']];
            } else {
                $imagePaths = $upload['paths'] ?? [];

                if ($action === 'add_product') {
                    $imagePath = array_shift($imagePaths) ?: null;
                    $stmt = $pdo->prepare('INSERT INTO products (name, category, price, size, `condition`, description, image, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$name, $category, $price, $size, $condition, $description, $imagePath, $availability]);
                    $productId = (int) $pdo->lastInsertId();
                    if ($imagePaths !== []) {
                        $imageStmt = $pdo->prepare('INSERT INTO product_images (product_id, image) VALUES (?, ?)');
                        foreach ($imagePaths as $path) {
                            $imageStmt->execute([$productId, $path]);
                        }
                    }
                    $notice = ['type' => 'success', 'message' => 'The new shoe was added successfully.'];
                } else {
                    $productId = (int) ($_POST['product_id'] ?? 0);
                    $existing = $pdo->prepare('SELECT image FROM products WHERE id = ? LIMIT 1');
                    $existing->execute([$productId]);
                    $currentProduct = $existing->fetch();

                    if ($productId > 0 && $currentProduct) {
                        $sql = 'UPDATE products SET name = ?, category = ?, price = ?, size = ?, `condition` = ?, description = ?, availability = ?';
                        $params = [$name, $category, $price, $size, $condition, $description, $availability];

                        if (empty($currentProduct['image']) && $imagePaths !== []) {
                            $sql .= ', image = ?';
                            $params[] = array_shift($imagePaths);
                        }

                        $sql .= ' WHERE id = ?';
                        $params[] = $productId;

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        if ($imagePaths !== []) {
                            $imageStmt = $pdo->prepare('INSERT INTO product_images (product_id, image) VALUES (?, ?)');
                            foreach ($imagePaths as $path) {
                                $imageStmt->execute([$productId, $path]);
                            }
                        }
                        $notice = ['type' => 'success', 'message' => 'The product was updated successfully.'];
                    }
                }
            }
        }
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $productId = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        foreach (getProductImages($pdo, $product) as $image) {
            deleteImageFile($image);
        }
        $deleteStmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $deleteStmt->execute([$productId]);
        $notice = ['type' => 'success', 'message' => 'The product was deleted successfully.'];
    }
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $productId = (int) $_GET['toggle'];
    $stmt = $pdo->prepare('SELECT availability FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $newStatus = $product['availability'] === 'AVAILABLE' ? 'SOLD OUT' : 'AVAILABLE';
        $update = $pdo->prepare('UPDATE products SET availability = ? WHERE id = ?');
        $update->execute([$newStatus, $productId]);
        $notice = ['type' => 'success', 'message' => 'The product availability was updated successfully.'];
    }
}

$editId = isset($_GET['edit']) && is_numeric($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editingProduct = null;

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editingProduct = $stmt->fetch();
}

$products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?php echo APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
</head>
<body>
    <main class="admin-shell">
        <div class="container">
            <div class="admin-panel">
                <div class="admin-toolbar">
                    <div>
                        <span class="eyebrow">Owner dashboard</span>
                        <h1>Manage shoes</h1>
                    </div>
                    <div class="table-actions">
                        <a href="<?php echo BASE_URL; ?>" class="small-action">View website</a>
                        <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="small-action danger">Logout</a>
                    </div>
                </div>

                <?php if ($notice['message'] !== ''): ?>
                    <div class="alert <?php echo $notice['type'] === 'success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($notice['message'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="form-grid" action="<?php echo BASE_URL; ?>/admin/index.php<?php echo $editingProduct ? '?edit=' . (int) $editingProduct['id'] : ''; ?>">
                    <input type="hidden" name="action" value="<?php echo $editingProduct ? 'update_product' : 'add_product'; ?>">
                    <?php if ($editingProduct): ?>
                        <input type="hidden" name="product_id" value="<?php echo (int) $editingProduct['id']; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div>
                            <label for="name">Shoe name</label>
                            <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($editingProduct['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div>
                            <label for="category">Category</label>
                            <select id="category" name="category">
                                <option value="Heels" <?php echo (($editingProduct['category'] ?? 'Heels') === 'Heels') ? 'selected' : ''; ?>>Heels</option>
                                <option value="Boots" <?php echo (($editingProduct['category'] ?? 'Heels') === 'Boots') ? 'selected' : ''; ?>>Boots</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label for="price">Price (UGX)</label>
                            <input id="price" name="price" type="number" min="1" step="1" value="<?php echo htmlspecialchars((string) normalizePriceValue($editingProduct['price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div>
                            <label for="size">Size</label>
                            <input id="size" name="size" type="text" value="<?php echo htmlspecialchars($editingProduct['size'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label for="condition">Condition</label>
                            <input id="condition" name="condition" type="text" value="<?php echo htmlspecialchars($editingProduct['condition'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div>
                            <label for="availability">Availability</label>
                            <select id="availability" name="availability">
                                <option value="AVAILABLE" <?php echo (($editingProduct['availability'] ?? 'AVAILABLE') === 'AVAILABLE') ? 'selected' : ''; ?>>AVAILABLE</option>
                                <option value="SOLD OUT" <?php echo (($editingProduct['availability'] ?? 'AVAILABLE') === 'SOLD OUT') ? 'selected' : ''; ?>>SOLD OUT</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($editingProduct['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div>
                        <label for="images">Product pictures (select multiple; JPG, JPEG, PNG, WebP, max 2MB each)</label>
                        <input id="images" name="images[]" type="file" accept=".jpg,.jpeg,.png,.webp" multiple>
                        <?php if ($editingProduct): ?>
                            <p style="margin-top: 0.7rem; color: var(--muted);">New pictures are added to the product. Existing pictures:</p>
                            <div class="admin-image-list">
                                <?php foreach (getProductImages($pdo, $editingProduct) as $image): ?>
                                    <img src="<?php echo productImageUrl($image); ?>" alt="Current picture of <?php echo htmlspecialchars($editingProduct['name'], ENT_QUOTES, 'UTF-8'); ?>" class="product-thumb">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="primary-btn"><?php echo $editingProduct ? 'Save' : 'Upload and Save'; ?></button>
                    <?php if ($editingProduct): ?>
                        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="ghost-btn">Cancel edit</a>
                    <?php endif; ?>
                </form>

                <div class="product-table-wrap" style="margin-top: 2rem;">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Size</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><img src="<?php echo productImageUrl($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumb"></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td><?php echo formatPrice($product['price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['size']); ?></td>
                                    <td><span class="status-tag <?php echo $product['availability'] === 'AVAILABLE' ? 'available' : 'sold-out'; ?>"><?php echo htmlspecialchars($product['availability']); ?></span></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="<?php echo BASE_URL; ?>/admin/index.php?edit=<?php echo (int) $product['id']; ?>" class="small-action">Edit</a>
                                            <a href="<?php echo BASE_URL; ?>/admin/index.php?toggle=<?php echo (int) $product['id']; ?>" class="small-action"><?php echo $product['availability'] === 'AVAILABLE' ? 'Sold Out' : 'Available'; ?></a>
                                            <a href="<?php echo BASE_URL; ?>/admin/index.php?delete=<?php echo (int) $product['id']; ?>" class="small-action danger" onclick="return confirm('Delete this shoe permanently?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>