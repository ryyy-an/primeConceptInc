-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2026 at 01:38 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pis-sys-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `source` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `variant_id`, `qty`, `source`) VALUES
(21, 2, 30, 1, 'WH'),
(23, 2, 31, 1, 'WH');

-- --------------------------------------------------------

--
-- Table structure for table `components`
--

CREATE TABLE `components` (
  `id` int(11) NOT NULL,
  `component_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `components`
--

INSERT INTO `components` (`id`, `component_name`) VALUES
(1, 'Chair Unit'),
(2, 'Table Top'),
(3, 'Table Leg');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `client_type` varchar(100) DEFAULT NULL,
  `gov_branch` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `contact_no`, `client_type`, `gov_branch`, `created_at`) VALUES
(1, 'Christian Villanueva', '0929702', 'private', 'null', '2026-04-13 10:55:31'),
(5, 'Pedro Penduco', '09987654321', 'Private / Individual', 'null', '2026-04-15 15:11:28'),
(6, 'Christian Villanueva', '09123456789', 'government', 'Department of Education', '2026-04-15 15:13:51'),
(7, 'Dave Mclarry', '09123456789', 'Private / Individual', 'null', '2026-04-15 15:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `target_role` varchar(20) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `wh_status` varchar(50) DEFAULT 'Pending',
  `customer_id` int(11) DEFAULT NULL,
  `temp_customer_name` varchar(255) DEFAULT NULL,
  `shipping_type` varchar(100) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `payment_mode` varchar(100) DEFAULT NULL,
  `admin_discount` int(11) DEFAULT 0,
  `total_ammount` double(15,2) DEFAULT 0.00,
  `balance` double(15,2) DEFAULT 0.00,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `created_by`, `status`, `wh_status`, `customer_id`, `temp_customer_name`, `shipping_type`, `delivery_address`, `payment_mode`, `admin_discount`, `total_ammount`, `balance`, `comments`, `created_at`) VALUES
(12, 7, 'For Review', 'Pending', NULL, 'Pedro Penduco', NULL, NULL, NULL, 0, 12500.00, 12500.00, NULL, '2026-04-13 16:00:00'),
(21, 7, 'Cancelled', 'Pending', NULL, 'Dave Mclarry', NULL, NULL, NULL, 0, 12500.00, 12500.00, '', '2026-04-13 16:00:00'),
(22, 7, 'Success', 'To Release', 5, 'Pedro Penduco', 'pickup', '', 'eWallet', 0, 12500.00, 0.00, '', '2026-04-14 16:00:00'),
(23, 7, 'Success', 'To Release', 7, 'Dave Mclarry', 'pickup', '', 'eWallet', 0, 25000.00, 0.00, '', '2026-04-14 16:00:00'),
(25, 2, 'For Review', 'To Release', 6, 'Christian Villanueva', 'delivery', 'Pagsanjan, Laguna', 'cash', 0, 12500.00, 0.00, '', '2026-04-15 15:13:51'),
(26, 2, 'For Review', 'To Release', 6, 'Christian Villanueva', 'pickup', '', 'cash', 0, 12500.00, 0.00, '', '2026-04-15 15:41:31'),
(28, 7, 'For Review', 'Pending', NULL, 'Dave Mclarry', NULL, NULL, NULL, 0, 13500.00, 13500.00, NULL, '2026-04-16 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `get_from` varchar(50) DEFAULT NULL,
  `unit_price` double(15,2) DEFAULT NULL,
  `wh_item_status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `variant_id`, `qty`, `get_from`, `unit_price`, `wh_item_status`) VALUES
(14, 12, 29, 1, 'WH', 12500.00, 'pending'),
(15, 21, 30, 1, 'SR', 12500.00, 'pending'),
(16, 22, 29, 1, 'WH', 12500.00, 'pending'),
(17, 23, 29, 1, 'SR', 12500.00, 'pending'),
(18, 23, 29, 1, 'WH', 12500.00, 'pending'),
(21, 25, 29, 1, 'WH', 12500.00, 'pending'),
(22, 26, 29, 1, 'SR', 12500.00, 'pending'),
(24, 28, 31, 1, 'WH', 13500.00, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `payment_tracker`
--

CREATE TABLE `payment_tracker` (
  `id` int(11) NOT NULL,
  `trans_id` int(11) NOT NULL,
  `amount_paid` double(15,2) DEFAULT NULL,
  `date_paid` datetime DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_tracker`
--

INSERT INTO `payment_tracker` (`id`, `trans_id`, `amount_paid`, `date_paid`, `due_date`, `payment_method`, `reference_no`, `remarks`, `status`) VALUES
(9, 9, 25000.00, '2026-04-15 00:00:00', NULL, 'eWallet', 'SR-REF-23', 'Finalized Showroom Request', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` double(15,2) DEFAULT NULL,
  `discount` int(11) DEFAULT 0,
  `is_on_sale` tinyint(1) DEFAULT 0,
  `default_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `code`, `description`, `category`, `price`, `discount`, `is_on_sale`, `default_image`, `created_at`, `is_deleted`) VALUES
(23, 'Executive Chair', 'EC-03', 'The Chair Who Cant Be Moved', 'Chair', 12500.00, 0, 0, '1776203235_EC-03DarkBrown.png', '2026-04-14 18:39:14', 0),
(24, 'Executive Chair', 'EC-14', 'The White Chair', 'Chair', 13500.00, 0, 0, '1776415288_EC-14White.jpg', '2026-04-17 08:41:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_components`
--

CREATE TABLE `product_components` (
  `id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `comp_id` int(11) NOT NULL,
  `qty_needed` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_components`
--

INSERT INTO `product_components` (`id`, `prod_id`, `comp_id`, `qty_needed`, `location`, `is_deleted`) VALUES
(38, 23, 1, 1, 'Rack A-4', 0),
(39, 24, 1, 1, 'Rack A-4', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_variant`
--

CREATE TABLE `product_variant` (
  `id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `variant` varchar(255) DEFAULT NULL,
  `min_buildable_qty` int(11) DEFAULT 0,
  `variant_image` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_variant`
--

INSERT INTO `product_variant` (`id`, `prod_id`, `variant`, `min_buildable_qty`, `variant_image`, `is_deleted`) VALUES
(29, 23, 'Brown', 4, '1776203235_v_EC-05Black.png', 0),
(30, 23, 'Black', 4, '1776203235_v_EC-03DarkBrown.png', 0),
(31, 24, 'White', 3, '1776415288_v_EC-14White.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `showroom_logs`
--

CREATE TABLE `showroom_logs` (
  `log_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `log_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `showroom_logs`
--

INSERT INTO `showroom_logs` (`log_id`, `variant_id`, `prod_id`, `action`, `qty`, `log_date`) VALUES
(2, 31, NULL, 'INVENTORY_ADJUSTMENT', 1, '2026-04-15 02:48:32'),
(3, 29, NULL, 'INVENTORY_ADJUSTMENT', 1, '2026-04-15 06:31:56'),
(4, 30, NULL, 'INVENTORY_ADJUSTMENT', 1, '2026-04-15 12:44:12'),
(5, 29, NULL, 'Sold (Order #26)', -1, '2026-04-15 23:41:32'),
(6, 29, NULL, 'Showroom Sale (Finalized Request #23)', 1, '2026-04-15 23:43:30');

-- --------------------------------------------------------

--
-- Table structure for table `showroom_stocks`
--

CREATE TABLE `showroom_stocks` (
  `stock_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `qty_on_hand` int(11) DEFAULT 0,
  `min_display_qty` int(11) DEFAULT 0,
  `last_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `showroom_stocks`
--

INSERT INTO `showroom_stocks` (`stock_id`, `variant_id`, `qty_on_hand`, `min_display_qty`, `last_update`) VALUES
(30, 29, 1, 0, '2026-04-16 01:39:30'),
(31, 30, 2, 0, '2026-04-15 00:00:00'),
(36, 31, 0, 0, '2026-04-17 16:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `or_number` varchar(100) DEFAULT NULL,
  `amount` double(15,2) DEFAULT NULL,
  `interest` int(11) DEFAULT 0,
  `total_with_interest` double(15,2) DEFAULT NULL,
  `installment_term` int(11) DEFAULT 0,
  `payment_type` varchar(50) DEFAULT 'Full',
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `transaction_date`, `or_number`, `amount`, `interest`, `total_with_interest`, `installment_term`, `payment_type`, `status`) VALUES
(9, 23, '2026-04-15 00:00:00', 'SR-REF-23', 25000.00, 0, 25000.00, 1, 'Full', 'Success');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `role`, `is_online`, `created_at`) VALUES
(2, 'ryan13', '$2y$10$fE/..bplwBEObVde8Rnt3.7eP6MFiXfR3hMf7VVD.fTU0Ad8Xv97i', 'Ryan', 'admin', 1, '2026-04-13 06:39:25'),
(3, 'ichan', '$2y$10$yjNEVBd2TofS8eNQuhlq2.noVSBi0hkLjy/6..WFGZihh/t1E3xwW', 'Christian', 'admin', 0, '2026-04-13 06:40:04'),
(7, 'showroom01', '$2y$10$/ntWXUyeMIuh8cTPyOe3Ee0vTA35BhVJABpGg8xKYrZUysvJ0CLNu', 'Wilson McCopper', 'showroom', 1, '2026-04-14 19:13:56'),
(8, 'warehouse01', '$2y$10$E.EUhoaSa8QUD9vbXDf2aegzfrZa0qKIjGN7ZfCQLPZbjFbn70L5C', 'Mimi Meyers', 'warehouse', 1, '2026-04-14 19:14:14');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_logs`
--

CREATE TABLE `warehouse_logs` (
  `log_id` int(11) NOT NULL,
  `comp_id` int(11) DEFAULT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `log_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `warehouse_logs`
--

INSERT INTO `warehouse_logs` (`log_id`, `comp_id`, `prod_id`, `variant_id`, `action`, `qty`, `log_date`) VALUES
(30, 1, 24, 31, 'Recipe Adjusted (Variant ID 31 multiplier: 1)', 1, '2026-04-17 16:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_stocks`
--

CREATE TABLE `warehouse_stocks` (
  `id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `product_comp_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `qty_on_hand` int(11) NOT NULL DEFAULT 0,
  `last_update` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `warehouse_stocks`
--

INSERT INTO `warehouse_stocks` (`id`, `prod_id`, `product_comp_id`, `variant_id`, `qty_on_hand`, `last_update`) VALUES
(50, 23, 38, 29, 1, '2026-04-15'),
(51, 23, 38, 30, 3, '2026-04-15'),
(57, 24, 39, 31, 1, '2026-04-17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_variant` (`variant_id`);

--
-- Indexes for table `components`
--
ALTER TABLE `components`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `target_user_id` (`target_user_id`),
  ADD KEY `target_role` (`target_role`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`created_by`),
  ADD KEY `fk_orders_customer` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_oi_order` (`order_id`),
  ADD KEY `fk_oi_variant` (`variant_id`);

--
-- Indexes for table `payment_tracker`
--
ALTER TABLE `payment_tracker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pt_trans` (`trans_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `product_components`
--
ALTER TABLE `product_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pc_product` (`prod_id`),
  ADD KEY `fk_pc_component` (`comp_id`);

--
-- Indexes for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_variant_product` (`prod_id`);

--
-- Indexes for table `showroom_logs`
--
ALTER TABLE `showroom_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `showroom_stocks`
--
ALTER TABLE `showroom_stocks`
  ADD PRIMARY KEY (`stock_id`),
  ADD UNIQUE KEY `unique_showroom_variant` (`variant_id`),
  ADD KEY `fk_ss_variant` (`variant_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `or_number` (`or_number`),
  ADD KEY `fk_trans_order` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `warehouse_logs`
--
ALTER TABLE `warehouse_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stock_link` (`prod_id`,`variant_id`,`product_comp_id`),
  ADD KEY `fk_wh_prod` (`prod_id`),
  ADD KEY `fk_wh_variant` (`variant_id`),
  ADD KEY `fk_wh_prod_comp` (`product_comp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `components`
--
ALTER TABLE `components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `payment_tracker`
--
ALTER TABLE `payment_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `product_components`
--
ALTER TABLE `product_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `product_variant`
--
ALTER TABLE `product_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `showroom_logs`
--
ALTER TABLE `showroom_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `showroom_stocks`
--
ALTER TABLE `showroom_stocks`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `warehouse_logs`
--
ALTER TABLE `warehouse_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oi_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`id`);

--
-- Constraints for table `payment_tracker`
--
ALTER TABLE `payment_tracker`
  ADD CONSTRAINT `fk_pt_trans` FOREIGN KEY (`trans_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_components`
--
ALTER TABLE `product_components`
  ADD CONSTRAINT `fk_pc_component` FOREIGN KEY (`comp_id`) REFERENCES `components` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pc_product` FOREIGN KEY (`prod_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variant`
--
ALTER TABLE `product_variant`
  ADD CONSTRAINT `fk_variant_product` FOREIGN KEY (`prod_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `showroom_stocks`
--
ALTER TABLE `showroom_stocks`
  ADD CONSTRAINT `fk_ss_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_trans_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD CONSTRAINT `fk_wh_prod` FOREIGN KEY (`prod_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wh_prod_comp` FOREIGN KEY (`product_comp_id`) REFERENCES `product_components` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wh_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
