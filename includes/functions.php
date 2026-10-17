<?php
require_once __DIR__ . '/../config.php';

function sanitizeInput(string $value): string
{
    return trim(strip_tags($value));
}

function normalizePriceValue($amount): int
{
    return (int) round((float) $amount);
}

function formatPrice($amount): string
{
    return 'UGX ' . number_format(normalizePriceValue($amount), 0, ',', ' ');
}

function productImageUrl(?string $image): string
{
    if (empty($image)) {
        return BASE_URL . '/assets/images/default-shoe.svg';
    }

    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    return BASE_URL . '/' . ltrim($image, '/');
}

function buildWhatsAppLink(string $productName, string $size, string $price): string
{
    $formattedPrice = number_format(normalizePriceValue($price), 0, ',', ' ');
    $message = "Hello Thrift with Ramo, I am interested in ordering:%0A";
    $message .= "Shoe: {$productName}%0A";
    $message .= "Size: {$size}%0A";
    $message .= "Price: UGX {$formattedPrice}%0A";
    $message .= "Is this shoe still available?";

    return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . urlencode($message);
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function handleImageUpload(string $fieldName = 'image'): array
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['status' => 'empty'];
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => 'error', 'message' => 'The image upload failed. Please try again.'];
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        return ['status' => 'error', 'message' => 'Only JPG, JPEG, PNG, and WebP images are allowed.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['status' => 'error', 'message' => 'Image size must be under 2MB.'];
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }

    $safeName = 'shoe_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $targetPath = UPLOAD_DIR . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['status' => 'error', 'message' => 'The image could not be uploaded to the server.'];
    }

    return ['status' => 'success', 'path' => 'uploads/' . $safeName];
}

function handleImageUploads(string $fieldName = 'images'): array
{
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName]['name'])) {
        return ['status' => 'empty', 'paths' => []];
    }

    $files = $_FILES[$fieldName];
    $paths = [];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $fileCount = count(array_filter($files['name'], static fn ($name) => $name !== ''));

    if ($fileCount > 10) {
        return ['status' => 'error', 'message' => 'You can upload a maximum of 10 images at a time.'];
    }

    foreach ($files['name'] as $index => $name) {
        if ($name === '') {
            continue;
        }

        $error = $files['error'][$index] ?? UPLOAD_ERR_NO_FILE;
        if ($error !== UPLOAD_ERR_OK) {
            foreach ($paths as $path) {
                deleteImageFile($path);
            }
            return ['status' => 'error', 'message' => 'An image upload failed. Please try again.'];
        }

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            foreach ($paths as $path) {
                deleteImageFile($path);
            }
            return ['status' => 'error', 'message' => 'Only JPG, JPEG, PNG, and WebP images are allowed.'];
        }

        if (($files['size'][$index] ?? 0) > 2 * 1024 * 1024) {
            foreach ($paths as $path) {
                deleteImageFile($path);
            }
            return ['status' => 'error', 'message' => 'Each image must be under 2MB.'];
        }

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }

        $safeName = 'shoe_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = UPLOAD_DIR . $safeName;
        if (!move_uploaded_file($files['tmp_name'][$index], $targetPath)) {
            foreach ($paths as $path) {
                deleteImageFile($path);
            }
            return ['status' => 'error', 'message' => 'An image could not be uploaded to the server.'];
        }

        $paths[] = 'uploads/' . $safeName;
    }

    return $paths === []
        ? ['status' => 'empty', 'paths' => []]
        : ['status' => 'success', 'paths' => $paths];
}

function getProductImages(PDO $pdo, array $product): array
{
    $images = [];
    if (!empty($product['image'])) {
        $images[] = $product['image'];
    }

    if (!empty($product['id'])) {
        $stmt = $pdo->prepare('SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC');
        $stmt->execute([(int) $product['id']]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $image) {
            if (!empty($image)) {
                $images[] = $image;
            }
        }
    }

    return $images !== [] ? $images : [null];
}

function deleteImageFile(?string $imagePath): void
{
    if (empty($imagePath) || preg_match('/^https?:\/\//i', $imagePath)) {
        return;
    }

    $absolutePath = __DIR__ . '/../' . ltrim($imagePath, '/');
    if (file_exists($absolutePath) && is_file($absolutePath)) {
        unlink($absolutePath);
    }
}
