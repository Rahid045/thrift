CREATE DATABASE IF NOT EXISTS `thrift`;
USE `thrift`;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `category` ENUM('Heels','Boots') NOT NULL,
  `price` DECIMAL(10,0) NOT NULL,
  `size` VARCHAR(20) NOT NULL,
  `condition` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `availability` ENUM('AVAILABLE','SOLD OUT') NOT NULL DEFAULT 'AVAILABLE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id_index` (`product_id`),
  CONSTRAINT `product_images_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin_users` (`username`, `password`) VALUES
('admin', '$2y$10$I.Q91eeck63hhQ5JQIdV..ssQR9qGJdOeS/rfp9AGUhc8ggSa5Twm')
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`);

INSERT INTO `products` (`name`, `category`, `price`, `size`, `condition`, `description`, `image`, `availability`)
VALUES
('Nyla Black Heels', 'Heels', 50000, '39', 'Excellent', 'Polished black heels in excellent condition, perfect for events and work wear.', 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&w=900&q=80', 'AVAILABLE'),
('Coco Brown Boots', 'Boots', 75000, '40', 'Very Good', 'Warm brown boots with a clean finish and comfortable fit for daily wear.', 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80', 'AVAILABLE'),
('Aisha Velvet Heels', 'Heels', 65000, '38', 'Excellent', 'Elegant velvet heels with a timeless finish and just enough sparkle for special occasions.', 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80', 'SOLD OUT'),
('Maya Leather Boots', 'Boots', 82000, '41', 'Good', 'Stylish leather boots that add an instant upgrade to any outfit.', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80', 'AVAILABLE')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `category` = VALUES(`category`), `price` = VALUES(`price`), `size` = VALUES(`size`), `condition` = VALUES(`condition`), `description` = VALUES(`description`), `image` = VALUES(`image`), `availability` = VALUES(`availability`);
