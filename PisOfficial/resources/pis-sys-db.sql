-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2026 at 07:31 PM
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
(3, 'Christian Villanueva', '', 'undefined', 'null', '2026-04-13 17:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `link_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `link_id`, `created_at`) VALUES
(1, 4, 'Order Request Approved', 'Order #5 has been Approved. Admin Note: sdfdfsgre', 'order', 0, '5', '2026-04-13 11:10:21'),
(2, 4, 'Order Request Approved', 'Order #9 has been Approved. Admin Note: asfsdf', 'order', 0, '9', '2026-04-13 17:19:40'),
(3, 4, 'Order Request Approved', 'Order #10 has been Approved. Admin Note: No additional remarks.', 'order', 0, '10', '2026-04-14 03:53:42'),
(4, 4, 'Order Request Approved', 'Order #11 has been Approved. Admin Note: No additional remarks.', 'order', 0, '11', '2026-04-14 04:10:25');

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

-- --------------------------------------------------------

--
-- Table structure for table `payment_tracker`
--

CREATE TABLE `payment_tracker` (
  `id` int(11) NOT NULL,
  `trans_id` int(11) NOT NULL,
  `amount_paid` double(15,2) DEFAULT NULL,
  `date_paid` datetime DEFAULT current_timestamp(),
  `payment_method` varchar(100) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(18, 'Executive Chair', 'EC-03', 'The chair who cant be move', 'Chair', 12599.00, 0, 1, '1776183210_EC-03DarkBrown.png', '2026-04-14 16:13:30', 1);

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
(32, 18, 1, 1, 'Rack A-1', 1);

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
(20, 18, 'Brown', 4, '1776183556_v_EC-03DarkBrown.png', 1),
(21, 18, 'Black', 4, '1776186914_v_EC-05Black.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `showroom_logs`
--

CREATE TABLE `showroom_logs` (
  `log_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `action` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `showroom_logs`
--

INSERT INTO `showroom_logs` (`log_id`, `variant_id`, `action`, `qty`, `date_added`) VALUES
(5, 20, 'INVENTORY_ADJUSTMENT', 1, '2026-04-15 01:26:21'),
(6, 21, 'INVENTORY_ADJUSTMENT', 1, '2026-04-15 01:27:02');

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
(20, 20, 1, 0, '2026-04-15 00:00:00'),
(21, 21, 1, 0, '2026-04-15 00:00:00');

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
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(4, 'showroom01', '$2y$10$xTGjk0X4A90LNcwlnQFZhebogHxlJnkC1negwhwdOqeT.pZk0E2L6', 'Christian Villanueva', 'showroom', 0, '2026-04-13 10:39:57'),
(5, 'warehouse01', '$2y$10$PXciRcx/CGlXb9EEnCMon.LIRqpevgBAREEGjfMnjbVbW35Q8q8Iy', 'Christian Villanueva', 'warehouse', 1, '2026-04-13 10:40:34');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_logs`
--

CREATE TABLE `warehouse_logs` (
  `log_id` int(11) NOT NULL,
  `comp_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `warehouse_logs`
--

INSERT INTO `warehouse_logs` (`log_id`, `comp_id`, `action`, `qty`, `date_added`) VALUES
(3, NULL, 'Build Production (Variant ID 11) by User #2', 1, '2026-04-13 17:32:44'),
(4, 2, 'Recipe Consumption (Build Variant ID 11)', -1, '2026-04-13 17:32:44'),
(5, 3, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 17:32:44'),
(6, NULL, 'Build Production (Variant ID 11) by User #2', 1, '2026-04-13 17:32:51'),
(7, 2, 'Recipe Consumption (Build Variant ID 11)', -1, '2026-04-13 17:32:51'),
(8, 3, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 17:32:51'),
(9, NULL, 'Build Production (Variant ID 11) by User #2', 1, '2026-04-13 17:33:15'),
(10, 2, 'Recipe Consumption (Build Variant ID 11)', -1, '2026-04-13 17:33:15'),
(11, 3, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 17:33:15'),
(12, NULL, 'Build Production (Variant ID 11) by User #2', 1, '2026-04-13 17:55:09'),
(13, 2, 'Recipe Consumption (Build Variant ID 11)', -1, '2026-04-13 17:55:09'),
(14, 3, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 17:55:09'),
(15, NULL, 'Build Production (Variant ID 11) by User #2', 3, '2026-04-13 18:14:03'),
(16, 2, 'Recipe Consumption (Build Variant ID 11)', -3, '2026-04-13 18:14:03'),
(17, 3, 'Recipe Consumption (Build Variant ID 11)', -6, '2026-04-13 18:14:03'),
(18, NULL, 'Build Production (Variant ID 11) by User #2', 2, '2026-04-13 18:16:51'),
(19, 2, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 18:16:51'),
(20, 3, 'Recipe Consumption (Build Variant ID 11)', -4, '2026-04-13 18:16:51'),
(21, NULL, 'Build Production (Variant ID 11) by User #2', -1, '2026-04-13 18:17:07'),
(22, 2, 'Recipe Consumption (Build Variant ID 11)', 1, '2026-04-13 18:17:07'),
(23, 3, 'Recipe Consumption (Build Variant ID 11)', 2, '2026-04-13 18:17:07'),
(24, NULL, 'Build Production (Variant ID 11) by User #2', 1, '2026-04-13 18:18:59'),
(25, 2, 'Recipe Consumption (Build Variant ID 11)', -1, '2026-04-13 18:18:59'),
(26, 3, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 18:18:59'),
(27, NULL, 'Build Production (Variant ID 11) by User #2', 1, '2026-04-13 18:19:06'),
(28, 2, 'Recipe Consumption (Build Variant ID 11)', -1, '2026-04-13 18:19:06'),
(29, 3, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 18:19:06'),
(30, NULL, 'Build Production (Variant ID 11) by User #2', 1, '2026-04-13 18:19:06'),
(31, 2, 'Recipe Consumption (Build Variant ID 11)', -1, '2026-04-13 18:19:06'),
(32, 3, 'Recipe Consumption (Build Variant ID 11)', -2, '2026-04-13 18:19:06'),
(33, NULL, 'Warehouse Variant Adjustment (Variant ID 11) by User #2', 2, '2026-04-13 18:21:41'),
(34, 2, 'Recipe Adjusted (Variant ID 11 multiplier: 2)', 2, '2026-04-13 18:21:41'),
(35, 3, 'Recipe Adjusted (Variant ID 11 multiplier: 2)', 4, '2026-04-13 18:21:41'),
(36, NULL, 'Warehouse Variant Adjustment (Variant ID 11) by User #2', 1, '2026-04-13 18:21:52'),
(37, 2, 'Recipe Adjusted (Variant ID 11 multiplier: 1)', 1, '2026-04-13 18:21:52'),
(38, 3, 'Recipe Adjusted (Variant ID 11 multiplier: 1)', 2, '2026-04-13 18:21:52'),
(40, NULL, 'Sold Variant ID 11 (Order #1)', 1, '2026-04-13 18:55:31'),
(44, NULL, 'Warehouse Variant Adjustment (Variant ID 12) by User #2', 4, '2026-04-13 19:48:04'),
(45, 1, 'Recipe Adjusted (Variant ID 12 multiplier: 4)', 8, '2026-04-13 19:48:04'),
(46, NULL, 'Warehouse Sale (Finalized Request #5)', 1, '2026-04-14 01:18:39'),
(47, 1, 'Stock Transfer to Showroom (Variant ID 13)', -8, '2026-04-14 09:38:53'),
(48, NULL, 'Warehouse Variant Adjustment (Variant ID 13) by User #2', 1, '2026-04-14 09:39:11'),
(49, 1, 'Recipe Adjusted (Variant ID 13 multiplier: 1)', 1, '2026-04-14 09:39:11'),
(50, 1, 'Stock Transfer to Showroom (Variant ID 13)', -1, '2026-04-14 09:39:27'),
(51, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 4, '2026-04-14 09:49:32'),
(52, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 4)', 8, '2026-04-14 09:49:32'),
(53, NULL, 'Warehouse Variant Adjustment (Variant ID 15) by User #2', 2, '2026-04-14 09:49:32'),
(54, 1, 'Recipe Adjusted (Variant ID 15 multiplier: 2)', 4, '2026-04-14 09:49:32'),
(55, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 3, '2026-04-14 09:49:48'),
(56, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 3)', 6, '2026-04-14 09:49:48'),
(57, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 2, '2026-04-14 09:50:11'),
(58, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 2)', 4, '2026-04-14 09:50:11'),
(59, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 1, '2026-04-14 09:50:54'),
(60, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 1)', 1, '2026-04-14 09:50:54'),
(61, NULL, 'Warehouse Variant Adjustment (Variant ID 15) by User #2', 1, '2026-04-14 09:50:54'),
(62, 1, 'Recipe Adjusted (Variant ID 15 multiplier: 1)', 1, '2026-04-14 09:50:54'),
(63, NULL, 'Warehouse Variant Adjustment (Variant ID 17) by User #2', 1, '2026-04-14 09:52:50'),
(64, 3, 'Recipe Adjusted (Variant ID 17 multiplier: 1)', 2, '2026-04-14 09:52:50'),
(65, 2, 'Recipe Adjusted (Variant ID 17 multiplier: 1)', 1, '2026-04-14 09:52:50'),
(66, NULL, 'Warehouse Variant Adjustment (Variant ID 11) by User #2', 1, '2026-04-14 09:52:50'),
(67, 3, 'Recipe Adjusted (Variant ID 11 multiplier: 1)', 2, '2026-04-14 09:52:50'),
(68, 2, 'Recipe Adjusted (Variant ID 11 multiplier: 1)', 1, '2026-04-14 09:52:50'),
(69, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 7, '2026-04-14 09:53:15'),
(70, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 7)', 7, '2026-04-14 09:53:15'),
(71, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 20, '2026-04-14 09:55:01'),
(72, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 20)', 20, '2026-04-14 09:55:01'),
(73, NULL, 'Warehouse Variant Adjustment (Variant ID 11) by User #2', 4, '2026-04-14 09:58:19'),
(74, 3, 'Recipe Adjusted (Variant ID 11 multiplier: 4)', 8, '2026-04-14 09:58:19'),
(75, 2, 'Recipe Adjusted (Variant ID 11 multiplier: 4)', 4, '2026-04-14 09:58:19'),
(76, NULL, 'Warehouse Variant Adjustment (Variant ID 17) by User #2', 2, '2026-04-14 09:58:26'),
(77, 3, 'Recipe Adjusted (Variant ID 17 multiplier: 2)', 4, '2026-04-14 09:58:26'),
(78, 2, 'Recipe Adjusted (Variant ID 17 multiplier: 2)', 2, '2026-04-14 09:58:26'),
(79, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 1, '2026-04-14 09:58:53'),
(80, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 1)', 1, '2026-04-14 09:58:53'),
(81, NULL, 'Warehouse Variant Adjustment (Variant ID 15) by User #2', 1, '2026-04-14 09:58:53'),
(82, 1, 'Recipe Adjusted (Variant ID 15 multiplier: 1)', 1, '2026-04-14 09:58:53'),
(83, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 2, '2026-04-14 09:59:07'),
(84, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 2)', 2, '2026-04-14 09:59:07'),
(85, NULL, 'Warehouse Variant Adjustment (Variant ID 15) by User #2', 2, '2026-04-14 09:59:07'),
(86, 1, 'Recipe Adjusted (Variant ID 15 multiplier: 2)', 2, '2026-04-14 09:59:07'),
(87, 3, 'Stock Transfer to Showroom (Variant ID 11)', -10, '2026-04-14 10:02:40'),
(88, 2, 'Stock Transfer to Showroom (Variant ID 11)', -5, '2026-04-14 10:02:40'),
(89, NULL, 'Warehouse Variant Adjustment (Variant ID 16) by User #2', 39, '2026-04-14 11:49:07'),
(90, 1, 'Recipe Adjusted (Variant ID 16 multiplier: 39)', 39, '2026-04-14 11:49:07'),
(91, NULL, 'Warehouse Variant Adjustment (Variant ID 12) by User #2', 4, '2026-04-14 11:50:45'),
(92, 1, 'Recipe Adjusted (Variant ID 12 multiplier: 4)', 8, '2026-04-14 11:50:45'),
(93, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 1, '2026-04-15 01:15:24'),
(94, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 1)', 1, '2026-04-15 01:15:24'),
(95, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 1, '2026-04-15 01:16:04'),
(96, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 1)', 1, '2026-04-15 01:16:04'),
(97, NULL, 'Warehouse Variant Adjustment (Variant ID 20) by User #2', 1, '2026-04-15 01:17:42'),
(98, 1, 'Recipe Adjusted (Variant ID 20 multiplier: 1)', 1, '2026-04-15 01:17:42'),
(99, NULL, 'Warehouse Variant Adjustment (Variant ID 20) by User #2', 1, '2026-04-15 01:26:14'),
(100, 1, 'Recipe Adjusted (Variant ID 20 multiplier: 1)', 1, '2026-04-15 01:26:14'),
(101, 1, 'Stock Transfer to Showroom (Variant ID 20)', -1, '2026-04-15 01:26:21'),
(102, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 1, '2026-04-15 01:26:26'),
(103, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 1)', 1, '2026-04-15 01:26:26'),
(104, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 1, '2026-04-15 01:26:33'),
(105, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 1)', 1, '2026-04-15 01:26:33'),
(106, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 1, '2026-04-15 01:26:41'),
(107, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 1)', 1, '2026-04-15 01:26:41'),
(108, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 3, '2026-04-15 01:26:48'),
(109, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 3)', 3, '2026-04-15 01:26:48'),
(110, 1, 'Stock Transfer to Showroom (Variant ID 21)', -1, '2026-04-15 01:27:02'),
(111, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 4, '2026-04-15 01:27:08'),
(112, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 4)', 4, '2026-04-15 01:27:08'),
(113, NULL, 'Warehouse Variant Adjustment (Variant ID 21) by User #2', 1, '2026-04-15 01:27:16'),
(114, 1, 'Recipe Adjusted (Variant ID 21 multiplier: 1)', 1, '2026-04-15 01:27:16');

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
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notif_user` (`user_id`);

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
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_sl_variant` (`variant_id`);

--
-- Indexes for table `showroom_stocks`
--
ALTER TABLE `showroom_stocks`
  ADD PRIMARY KEY (`stock_id`),
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
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_wl_comp_new` (`comp_id`);

--
-- Indexes for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD PRIMARY KEY (`id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `components`
--
ALTER TABLE `components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payment_tracker`
--
ALTER TABLE `payment_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product_components`
--
ALTER TABLE `product_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `product_variant`
--
ALTER TABLE `product_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `showroom_logs`
--
ALTER TABLE `showroom_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `showroom_stocks`
--
ALTER TABLE `showroom_stocks`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `warehouse_logs`
--
ALTER TABLE `warehouse_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `showroom_logs`
--
ALTER TABLE `showroom_logs`
  ADD CONSTRAINT `fk_sl_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variant` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `warehouse_logs`
--
ALTER TABLE `warehouse_logs`
  ADD CONSTRAINT `fk_wl_comp_new` FOREIGN KEY (`comp_id`) REFERENCES `components` (`id`) ON DELETE CASCADE;

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
