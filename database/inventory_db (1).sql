-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2025 at 07:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `cart_quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `products_id`, `cart_quantity`) VALUES
(21, 24, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Oil'),
(2, 'Lubricant'),
(3, 'Chemical');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `transaction_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `transaction_type` enum('Add','Remove','Sale','Return','Adjustment') NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` >= 0),
  `remarks` varchar(255) DEFAULT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`transaction_id`, `products_id`, `transaction_type`, `quantity`, `remarks`, `transaction_date`) VALUES
(1, 1, 'Add', 10, 'n/a', '2025-06-15 17:50:21'),
(2, 2, 'Add', 2, 'n/a', '2025-06-15 17:58:56'),
(3, 2, 'Add', 10, 'n/a', '2025-06-16 14:14:34'),
(4, 1, 'Add', 20, 'n/a', '2025-06-22 18:05:56'),
(5, 2, 'Add', 10, 'n/a', '2025-06-22 18:47:48'),
(7, 2, 'Add', 3, 'n/a', '2025-06-23 03:27:46'),
(8, 1, 'Add', 10, 'n/a', '2025-06-23 03:27:56'),
(9, 1, 'Add', 10, 'n/a', '2025-06-23 03:28:07'),
(10, 1, 'Add', 1, 'n/a', '2025-06-23 03:28:20');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_status` enum('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `order_status`) VALUES
(1, 17, '2025-06-14 12:11:58', 9000.00, 'Completed'),
(2, 9, '2025-06-14 18:03:08', 2000.00, 'Pending'),
(3, 9, '2025-06-15 19:09:19', 3000.00, 'Pending'),
(4, 9, '2025-06-16 08:16:33', 1000.00, 'Completed'),
(5, 9, '2025-06-16 08:16:47', 1000.00, 'Completed'),
(6, 9, '2025-06-16 08:17:34', 1000.00, 'Completed'),
(7, 9, '2025-06-16 08:19:56', 4800.00, 'Completed'),
(8, 9, '2025-06-16 08:20:58', 1000.00, 'Completed'),
(9, 9, '2025-06-16 08:21:56', 1000.00, 'Completed'),
(10, 9, '2025-06-16 08:30:12', 2000.00, ''),
(11, 9, '2025-06-16 08:30:27', 2000.00, ''),
(12, 9, '2025-06-16 09:10:30', 1000.00, ''),
(13, 9, '2025-06-16 09:16:28', 2000.00, ''),
(14, 9, '2025-06-22 13:13:14', 2000.00, 'Completed'),
(15, 9, '2025-06-22 13:14:47', 1000.00, 'Completed'),
(16, 9, '2025-06-22 13:15:15', 1000.00, 'Completed'),
(17, 9, '2025-06-22 13:30:33', 122.00, ''),
(18, 9, '2025-06-22 19:51:40', 2000.00, 'Completed'),
(19, 9, '2025-06-22 19:51:56', 3000.00, 'Completed'),
(20, 9, '2025-06-22 20:13:26', 3000.00, ''),
(21, 9, '2025-06-23 04:42:06', 5000.00, ''),
(40, 24, '2025-08-08 06:59:32', 4000.00, 'Pending'),
(41, 24, '2025-08-08 06:59:32', NULL, 'Pending'),
(42, 24, '2025-08-08 06:59:32', NULL, 'Pending'),
(43, 24, '2025-08-08 06:59:32', NULL, 'Pending'),
(44, 24, '2025-08-08 06:59:32', 1000.00, 'Completed'),
(45, 24, '2025-08-08 06:59:32', 1000.00, 'Pending'),
(46, 24, NULL, NULL, 'Pending'),
(47, 24, NULL, NULL, 'Pending'),
(48, 24, NULL, NULL, 'Pending'),
(49, 24, '2025-08-08 07:37:01', 1000.00, 'Pending'),
(50, 24, '2025-08-08 07:37:20', 3000.00, 'Pending'),
(51, 24, '2025-08-08 07:40:51', 4000.00, 'Pending'),
(52, 24, '2025-08-08 08:27:38', 1000.00, 'Pending'),
(53, 24, '2025-08-08 08:27:53', 1000.00, 'Pending'),
(54, 24, '2025-08-08 08:28:03', 1000.00, 'Pending'),
(55, 24, '2025-08-10 12:17:31', 1000.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `products_id` int(11) DEFAULT NULL,
  `order_quantity` int(11) DEFAULT NULL,
  `order_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `products_id`, `order_quantity`, `order_price`) VALUES
(1, 1, 1, 3, 1000.00),
(2, 1, 2, 6, 1000.00),
(3, 2, 1, 2, 1000.00),
(4, 3, 1, 1, 1000.00),
(5, 3, 2, 2, 1000.00),
(6, 4, 2, 1, 1000.00),
(7, 5, 2, 1, 1000.00),
(8, 6, 1, 1, 1000.00),
(10, 8, 1, 1, 1000.00),
(11, 9, 1, 1, 1000.00),
(12, 10, 1, 2, 1000.00),
(13, 11, 1, 1, 1000.00),
(14, 11, 2, 1, 1000.00),
(17, 12, 1, 1, 1000.00),
(18, 13, 1, 2, 1000.00),
(19, 14, 1, 2, 1000.00),
(20, 15, 2, 1, 1000.00),
(21, 16, 2, 1, 1000.00),
(23, 18, 2, 2, 1000.00),
(24, 19, 1, 3, 1000.00),
(25, 20, 2, 3, 1000.00),
(26, 21, 1, 2, 1000.00),
(27, 21, 2, 3, 1000.00),
(29, 40, 1, 1, 1000.00),
(30, 40, 2, 2, 1000.00),
(31, 40, 8, 1, 1000.00),
(32, 44, 8, 1, 1000.00),
(33, 45, 8, 1, 1000.00),
(34, 46, 8, 1, 1000.00),
(35, 47, 8, 1, 1000.00),
(36, 48, 8, 4, 1000.00),
(37, 49, 2, 1, 1000.00),
(38, 50, 8, 3, 1000.00),
(39, 51, 2, 2, 1000.00),
(40, 51, 8, 2, 1000.00),
(41, 52, 2, 1, 1000.00),
(42, 53, 2, 1, 1000.00),
(43, 54, 8, 1, 1000.00),
(44, 55, 2, 1, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment_and_invoicing`
--

CREATE TABLE `payment_and_invoicing` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_and_invoicing`
--

INSERT INTO `payment_and_invoicing` (`payment_id`, `order_id`, `user_id`, `payment_method`, `amount_paid`, `payment_date`) VALUES
(1, 17, 9, 'Cash', 122.00, '2025-06-23'),
(2, 8, 9, 'Card', 1000.00, '2025-06-23'),
(3, 7, 9, 'Card', 4800.00, '2025-06-23'),
(4, 4, 9, 'Cash', 1000.00, '2025-06-23'),
(5, 20, 9, 'Cash', 3000.00, '2025-06-23'),
(6, 20, 9, 'Cash', 3000.00, '2025-06-23'),
(7, 20, 9, 'Cash', 3000.00, '2025-06-23'),
(8, 19, 9, 'Cash', 3000.00, '2025-06-23'),
(9, 5, 9, 'Cash', 1000.00, '2025-06-23'),
(10, 18, 9, 'Cash', 2000.00, '2025-06-23'),
(11, 18, 9, 'Cash', 2000.00, '2025-06-23'),
(12, 18, 9, 'Cash', 2000.00, '2025-06-23'),
(13, 6, 9, 'Cash', 1000.00, '2025-06-23'),
(14, 21, 9, 'Card', 5000.00, '2025-06-23'),
(15, 44, 24, 'Cash', 1000.00, '2025-08-08');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `products_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_stock` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`products_id`, `product_name`, `product_price`, `product_stock`, `category_id`, `supplier_id`) VALUES
(1, 'Schaeffer\'s Specialized Lubricants', 1000.00, 0, 2, NULL),
(2, 'Petrofer', 1000.00, 2, 1, NULL),
(8, 'Omega: The Ultimate Lubricant', 1000.00, 0, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_and_delivery`
--

CREATE TABLE `shipping_and_delivery` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tracking_number` varchar(100) NOT NULL,
  `shipping_method` varchar(50) NOT NULL,
  `estimated_delivery_date` date NOT NULL,
  `delivery_status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_and_delivery`
--

INSERT INTO `shipping_and_delivery` (`delivery_id`, `order_id`, `user_id`, `tracking_number`, `shipping_method`, `estimated_delivery_date`, `delivery_status`, `created_at`) VALUES
(1, 17, 9, 'TRK6858287a458ef', 'Standard', '2025-06-30', '', '2025-06-22 16:00:23'),
(2, 8, 9, 'TRK68582ea09208a', 'Express', '2025-06-25', '', '2025-06-22 16:26:29'),
(3, 7, 9, 'TRK68583202896d5', 'Standard', '2025-06-26', '', '2025-06-22 16:40:45'),
(4, 4, 9, 'TRK6858380fb53be', 'Standard', '2025-06-24', '', '2025-06-22 17:06:36'),
(5, 20, 9, 'TRK68584c91955ae', 'Standard', '2025-06-23', '', '2025-06-22 18:34:05'),
(6, 19, 9, 'TRK6858581b67ded', 'Standard', '2025-06-23', '', '2025-06-22 19:23:15'),
(7, 5, 9, 'TRK68585e4f1d2b2', 'Standard', '2025-06-23', '', '2025-06-22 19:49:43'),
(8, 18, 9, 'TRK68586331de1fd', 'Standard', '2025-06-23', '', '2025-06-22 20:10:38'),
(9, 18, 9, 'TRK6858633e40cfc', 'Standard', '2025-06-01', '', '2025-06-22 20:10:48'),
(10, 18, 9, 'TRK6858637f68621', 'Standard', '2025-06-23', '', '2025-06-22 20:11:50'),
(11, 6, 9, 'TRK685864519557b', 'Standard', '2025-06-23', '', '2025-06-22 20:15:23'),
(12, 21, 9, 'TRK6858bf010a108', 'Pickup', '2025-06-23', '', '2025-06-23 02:42:17'),
(13, 44, 24, 'TRK689530b2909f5', 'Standard', '2025-08-08', '', '2025-08-07 23:03:26');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `supplier_phonenumber` varchar(15) DEFAULT NULL,
  `supplier_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `supplier_name`, `supplier_phonenumber`, `supplier_email`) VALUES
(2, 'Kangkonga', '09451038854', 'joshmojica@gmail.com'),
(5, 'mung', '094545454545', 'joshomojica@gmail.com'),
(8, 'Kangkong', '09451038853', 'joshmojica@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_orders`
--

CREATE TABLE `supplier_orders` (
  `supplier_order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `expected_delivery_date` date NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `order_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','inventory_staff','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `contact_number`, `address`, `password`, `role`, `created_at`) VALUES
(1, 'Marielleee1', 'Bautista', 'mxrxelle2', 'bautistamo@students.nu-lipa.edu.ph', NULL, NULL, '$2y$10$BsO/YdOlRoPJJ6nR6mDUp.JteHRa/MG88whyWohRaj/j4nXjZLCb6', 'admin', '2025-06-09 03:52:05'),
(2, 'Marielle', 'Bautista', 'mxrxelle1', 'bautistamo1@students.nu-lipa.edu.ph', NULL, NULL, '$2y$10$9yGp5VxhMUtuUTSjT6whJeK5Z7EKp/.FTEka1W8nWqAKE0WQXdNQS', 'admin', '2025-06-09 04:18:26'),
(3, 'Marielle', 'Bautista', 'mxrxelle12', 'bautistamo12@students.nu-lipa.edu.ph', NULL, NULL, '$2y$10$Lf7kpvjuIiUuhZVUSuDbaOk7Gskcptit5y9uRpooQeykC/U2tnnX.', 'admin', '2025-06-09 05:24:03'),
(8, 'Marielle', 'Bautista', 'mxrxelle1234567', 'bautistamo1234567@students.nu-lipa.edu.ph', NULL, NULL, '$2y$10$5GdqtfeUAL516BhesBVC4eCe1EaAfnlP.ig4TiQaLvbI2QucO.Gk2', 'admin', '2025-06-09 05:43:31'),
(9, 'Marielle', 'Bautista', 'mxrxelle12345678', 'bautistamo12345678@students.nu-lipa.edu.ph', NULL, NULL, '$2y$10$bwAOSkX9WUQm8S8a8bNd7usAu9g1kv2HUF38UHUZKdahMvXdX2jcO', 'admin', '2025-06-09 05:44:20'),
(11, 'Marielle', 'Bautista', 'mxrxelle1234567890', 'bautistamo1234567890@students.nu-lipa.edu.ph', NULL, NULL, '$2y$10$vhK5GFi0lL4e7Dn48yQJX.ZGqrPukyyrG8nEHcMKj2PWl0srUcgHq', 'admin', '2025-06-09 05:49:22'),
(13, 'Marielle', 'Bautista', 'mxrxelle123456789012', 'bautistamo123456789012@students.nu-lipa.edu.ph', NULL, NULL, '$2y$10$VH8u71C6S9hMhb11GSFnJO2tWw7kixto11DnXy.S3bZggjTNtmPSa', '', '2025-06-09 06:09:08'),
(19, 'Mariellee', 'Bautista', 'mxrxelle123456781', 'bautistamo@students.nu-lipa.edu.ph1', NULL, NULL, '$2y$10$u65vnHOTuw5oSmuzR0pwROV1Gsu0keG.ygl30PwIEeX12fuffORHy', '', '2025-06-22 20:40:42'),
(20, 'Marielle', 'Bautista', 'mxrxelle', 'bautistamo@students.lipa', NULL, NULL, '$2y$10$vcaqDPzLw1rq6tAuEm.uLe436LTmZRrn6hlxi7hJ8OV50kEjGPtQy', '', '2025-06-22 20:45:37'),
(21, 'marielle', 'martinez', 'yamster', 'yamster@gmail.com', NULL, NULL, '$2y$10$lUGpLv0ir5dibKsOcMg.lOiBcFc95dnGKmBPCGl0oKRYtfftuD7.u', 'admin', '2025-08-07 05:46:22'),
(22, 'dario', 'boy', 'darioboy', 'darbs@123.com', NULL, NULL, '$2y$10$03t4gDp8Q7bmI8V8hT0VQ.8PuruBuOBl25LacXgH1qyy.tWDdLjvG', '', '2025-08-07 06:04:13'),
(23, 'darbs', 'patootie', 'bbyboi', 'babyboy@123.com', NULL, NULL, '$2y$10$z8uL7MRDvS5nRJ7cd1/nSOC2.wP2qjcOep/qoarSiHgcBc4koW7bK', 'inventory_staff', '2025-08-07 06:07:18'),
(24, 'Mariellee', 'Bautista', 'cute', 'cute@nga.com', '', '', '$2y$10$ejtDOFz3y2yBr8b.8x//1umXpXZwFJ8zqPgky2deheVdxHit2FFTu', 'customer', '2025-08-07 08:00:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `products_id` (`products_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `product_id` (`products_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `products_id` (`products_id`);

--
-- Indexes for table `payment_and_invoicing`
--
ALTER TABLE `payment_and_invoicing`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`products_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `shipping_and_delivery`
--
ALTER TABLE `shipping_and_delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `supplier_orders`
--
ALTER TABLE `supplier_orders`
  ADD PRIMARY KEY (`supplier_order_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `payment_and_invoicing`
--
ALTER TABLE `payment_and_invoicing`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `products_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shipping_and_delivery`
--
ALTER TABLE `shipping_and_delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `supplier_orders`
--
ALTER TABLE `supplier_orders`
  MODIFY `supplier_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `products` (`products_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`products_id`) REFERENCES `products` (`products_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `products` (`products_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_and_invoicing`
--
ALTER TABLE `payment_and_invoicing`
  ADD CONSTRAINT `payment_and_invoicing_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `payment_and_invoicing_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`Category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`);

--
-- Constraints for table `shipping_and_delivery`
--
ALTER TABLE `shipping_and_delivery`
  ADD CONSTRAINT `shipping_and_delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipping_and_delivery_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_orders`
--
ALTER TABLE `supplier_orders`
  ADD CONSTRAINT `supplier_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
