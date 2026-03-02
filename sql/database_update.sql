CREATE DATABASE IF NOT EXISTS customers_db;
USE customers_db;

-- Drop obsolete tables if they exist
DROP TABLE IF EXISTS `cart`;

-- Users table (upgraded with roles and password hashing space)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Since the old table was named customers_db, we migrate them to users and then drop the old one if needed later, but we can do it now if it's empty.
INSERT IGNORE INTO `users` (`username`, `email`, `password`)
SELECT `Username`, `Email`, `Password` FROM `customers_db`;

DROP TABLE IF EXISTS `customers_db`;

-- Menu Items table (to make the menu dynamic)
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial menu items
INSERT IGNORE INTO `menu_items` (`id`, `name`, `description`, `price`, `image_path`) VALUES
(1, 'Chicken Sandwich', 'Delicious crispy chicken sandwich with our signature sauce.', 10.99, 'Images/chickensandwich.jpg'),
(2, 'Wings', 'Spicy and tangy chicken wings cooked to perfection.', 8.99, 'Images/wings.jpg'),
(3, 'BBQ Chicken Sandwich', 'Tender BBQ chicken with pickles and slaw.', 12.99, 'Images/bbqsandwich.jpg'),
(4, 'Teriyaki Chicken', 'Sweet and savory teriyaki chicken glaze.', 11.99, 'Images/teriyaki.jpg');

-- Orders table (to save checkout history)
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
  `shipping_address` text,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items table (links orders to menu items)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart table (upgraded to link user and item ID)
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_cart_item` (`user_id`, `menu_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
