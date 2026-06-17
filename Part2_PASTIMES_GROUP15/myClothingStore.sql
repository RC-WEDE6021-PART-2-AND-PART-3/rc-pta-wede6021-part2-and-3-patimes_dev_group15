-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 03, 2026 at 06:30 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ClothingStore`
--

-- --------------------------------------------------------

--
-- Table structure for table `CART`
--

CREATE TABLE `CART` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `CART`
--

INSERT INTO `CART` (`cart_id`, `user_id`) VALUES
(1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `CART_ITEMS`
--

CREATE TABLE `CART_ITEMS` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ITEMS`
--

CREATE TABLE `ITEMS` (
  `item_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ITEMS`
--

INSERT INTO `ITEMS` (`item_id`, `user_id`, `title`, `description`, `price`, `image`) VALUES
(1, 1, 'Vintage Hermès Silk Scarf', 'Luxury silk scarf', 245.00, 'images/scarf.jpg'),
(2, 1, 'Classic Burberry Trench Coat', 'Timeless coat', 890.00, 'images/coat.jpg'),
(3, 1, 'Gucci Leather Loafers', 'Stylish shoes', 425.00, 'images/shoes.jpg'),
(4, 1, 'Chanel Handbag', 'Luxury bag', 3200.00, 'images/bag.jpg'),
(5, 1, 'Prada Sunglasses', 'Cool sunglasses', 185.00, 'images/glasses.jpg'),
(6, 1, 'Leather Jacket', 'Premium jacket', 1450.00, 'images/jacket.jpg'),
(7, 1, 'Vintage Hermès Silk Scarf', 'Luxury silk scarf in excellent condition.', 245.00, 'images/scarf.jpg'),
(8, 1, 'Classic Burberry Trench Coat', 'Timeless Burberry trench coat.', 890.00, 'images/trench.jpg'),
(9, 1, 'Gucci Leather Loafers', 'Elegant Gucci leather loafers.', 425.00, 'images/shoes.jpg'),
(10, 1, 'Chanel Classic Handbag', 'Luxury Chanel handbag.', 3200.00, 'images/bag.jpg'),
(11, 1, 'Prada Sunglasses', 'Stylish Prada sunglasses.', 185.00, 'images/glasses.jpg'),
(12, 1, 'Saint Laurent Leather Jacket', 'Premium Saint Laurent leather jacket.', 1450.00, 'images/leather-jacket.jpg'),
(13, 1, 'Dior Silk Dress', 'Elegant Dior silk dress.', 780.00, 'images/dress.jpg'),
(14, 1, 'Rolex Watch Vintage', 'Vintage Rolex watch in excellent condition.', 4500.00, 'images/watch.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `ORDERS`
--

CREATE TABLE `ORDERS` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ORDERS`
--

INSERT INTO `ORDERS` (`order_id`, `user_id`, `order_date`, `delivery_address`) VALUES
(1, 4, '2026-05-03 16:25:00', '606 Thabo Sehume Street');

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'customer',
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `USERS`
--

INSERT INTO `USERS` (`user_id`, `name`, `email`, `username`, `password`, `role`, `status`) VALUES
(1, 'Admin User', 'admin@test.com', 'admin', '0192023a7bbd73250516f069df18b500', 'admin', 'approved'),
(4, 'Test User', 'test@GMAIL.COM', 'test', 'e10adc3949ba59abbe56e057f20f883e', 'seller', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `CART`
--
ALTER TABLE `CART`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `CART_ITEMS`
--
ALTER TABLE `CART_ITEMS`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `ITEMS`
--
ALTER TABLE `ITEMS`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ORDERS`
--
ALTER TABLE `ORDERS`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `CART`
--
ALTER TABLE `CART`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `CART_ITEMS`
--
ALTER TABLE `CART_ITEMS`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ITEMS`
--
ALTER TABLE `ITEMS`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ORDERS`
--
ALTER TABLE `ORDERS`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `CART`
--
ALTER TABLE `CART`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`);

--
-- Constraints for table `CART_ITEMS`
--
ALTER TABLE `CART_ITEMS`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `CART` (`cart_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `ITEMS` (`item_id`);

--
-- Constraints for table `ITEMS`
--
ALTER TABLE `ITEMS`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`);

--
-- Constraints for table `ORDERS`
--
ALTER TABLE `ORDERS`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
