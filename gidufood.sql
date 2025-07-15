-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 14, 2025 lúc 06:45 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `gidufood`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `about`
--

CREATE TABLE `about` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `facebook` char(255) NOT NULL,
  `youtube` char(255) NOT NULL,
  `phone` char(10) NOT NULL,
  `email` char(255) NOT NULL,
  `address` text NOT NULL,
  `logo` char(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `about`
--

INSERT INTO `about` (`id`, `name`, `facebook`, `youtube`, `phone`, `email`, `address`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'GiDu Food', 'http://www.facebook.com.vn', 'https://www.youtube.com.vn', '0283821286', 'ktcaothang@caothang.edu.vn', 'truong cao dang ky thuat cao thang', 'logo.jpg', '2024-11-30 05:17:55', '2025-06-21 03:29:47');

--
-- Bẫy `about`
--
DELIMITER $$
CREATE TRIGGER `about_Updated_At` BEFORE UPDATE ON `about` FOR EACH ROW SET NEW.updated_at = NOW()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` char(255) NOT NULL,
  `content` text NOT NULL,
  `text_plan` text NOT NULL,
  `image` char(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bẫy `blogs`
--
DELIMITER $$
CREATE TRIGGER `Blogs_Updated_At` BEFORE UPDATE ON `blogs` FOR EACH ROW SET NEW.updated_at = NOW()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `table_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `note` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `table_id`, `product_id`, `size_id`, `quantity`, `note`, `created_at`, `updated_at`) VALUES
(148, 30, 2, 3, 2, NULL, '2025-07-11 15:40:24', '2025-07-11 15:40:24'),
(149, 30, 47, 3, 2, NULL, '2025-07-11 15:42:14', '2025-07-11 15:42:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_item_toppings`
--

CREATE TABLE `cart_item_toppings` (
  `id` int(11) NOT NULL,
  `cart_item_id` int(11) NOT NULL,
  `topping_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_item_toppings`
--

INSERT INTO `cart_item_toppings` (`id`, `cart_item_id`, `topping_id`, `quantity`, `price`) VALUES
(126, 148, 14, 1, 5000.00),
(127, 148, 16, 1, 5000.00),
(128, 148, 17, 6, 5000.00),
(129, 149, 14, 1, 5000.00),
(130, 149, 17, 1, 5000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` char(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Món ăn', 'mon-an', 'Các loại món ăn', 1, '2024-11-29 15:15:33', '2025-07-13 15:11:14'),
(2, 'Đồ uống', 'do-uong', 'Các loại đồ uống', 1, '2025-05-30 06:27:28', '2025-07-01 04:01:27'),
(21, 'Món tráng miệng', 'mon-trang-mieng', 'Các loại kem ngonnn', 1, '2025-07-01 04:04:53', '2025-07-11 08:12:10'),
(22, 'dang muc test', 'dang-muc-test', 'test danh muc', 0, '2025-07-12 02:01:08', '2025-07-12 15:37:49');

--
-- Bẫy `categories`
--
DELIMITER $$
CREATE TRIGGER `Categories_Updated_At` BEFORE UPDATE ON `categories` FOR EACH ROW SET NEW.updated_at = NOW()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` char(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_id` int(11) UNSIGNED NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method_id` int(11) NOT NULL,
  `order_status_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `table_id`, `total_price`, `payment_method_id`, `order_status_id`, `created_at`, `updated_at`) VALUES
(60, 'ORD1751513610', 1, 30000.00, 1, 2, '2025-07-03 03:33:30', '2025-07-13 14:40:39'),
(61, 'ORD1751881192', 1, 35000.00, 1, 1, '2025-07-07 09:39:52', '2025-07-13 14:40:15'),
(62, 'ORD1751881222', 1, 100000.00, 1, 2, '2025-07-07 09:40:22', '2025-07-13 14:28:37'),
(63, 'ORD1751881279', 1, 40000.00, 1, 4, '2025-07-07 09:41:19', '2025-07-09 09:23:28'),
(64, 'ORD1751881301', 1, 25000.00, 1, 4, '2025-07-07 09:41:41', '2025-07-09 09:23:27'),
(65, 'ORD1751898592', 1, 25000.00, 1, 3, '2025-07-07 14:29:52', '2025-07-13 14:28:38'),
(66, 'ORD1751898605', 1, 40000.00, 1, 4, '2025-07-07 14:30:05', '2025-07-09 09:23:25'),
(67, 'ORD1751898628', 1, 30000.00, 1, 4, '2025-07-07 14:30:28', '2025-07-09 09:23:26'),
(68, 'ORD1751980673', 1, 70000.00, 1, 4, '2025-07-08 13:17:53', '2025-07-13 07:09:49'),
(69, 'ORD1752041480', 1, 20000.00, 1, 4, '2025-07-09 06:11:20', '2025-07-13 07:10:05'),
(70, 'ORD1752050528', 1, 100000.00, 1, 4, '2025-07-09 08:42:08', '2025-07-09 09:23:24'),
(72, 'ORD1752120370', 1, 15000.00, 1, 4, '2025-07-10 04:06:10', '2025-07-13 07:09:52'),
(73, 'ORD1752120439', 1, 20000.00, 1, 4, '2025-07-10 04:07:19', '2025-07-13 07:09:53'),
(74, 'ORD1752120542', 1, 20000.00, 2, 4, '2025-07-10 04:09:02', '2025-07-13 07:10:06'),
(75, 'ORD1752198308', 34, 15000.00, 2, 4, '2025-07-11 01:45:08', '2025-07-11 01:47:27'),
(77, 'ORD1752206895', 1, 60000.00, 1, 4, '2025-07-11 04:08:15', '2025-07-13 05:07:56'),
(78, 'ORD1752211527', 1, 50000.00, 2, 4, '2025-07-11 05:25:27', '2025-07-11 05:26:18'),
(79, 'ORD1752211641', 1, 20000.00, 1, 4, '2025-07-11 05:27:21', '2025-07-13 05:07:57'),
(80, 'ORD1752212060', 1, 25000.00, 1, 4, '2025-07-11 05:34:20', '2025-07-13 07:10:04'),
(81, 'ORD1752212119', 1, 15000.00, 2, 4, '2025-07-11 05:35:19', '2025-07-13 05:08:02'),
(82, 'ORD1752212193', 1, 10000.00, 1, 4, '2025-07-11 05:36:33', '2025-07-11 05:37:31'),
(83, 'ORD1752218931', 1, 15000.00, 2, 4, '2025-07-11 07:28:51', '2025-07-11 07:30:21'),
(84, 'ORD1752220643', 1, 15000.00, 1, 4, '2025-07-11 07:57:23', '2025-07-13 07:09:50'),
(85, 'ORD1752221622', 2, 40000.00, 1, 4, '2025-07-11 08:13:42', '2025-07-13 07:09:50'),
(86, 'ORD1752221657', 2, 55000.00, 1, 4, '2025-07-11 08:14:17', '2025-07-13 05:06:52'),
(87, 'ORD1752221695', 2, 30000.00, 2, 4, '2025-07-11 08:14:55', '2025-07-13 05:08:00'),
(88, 'ORD1752222632', 30, 120000.00, 1, 4, '2025-07-11 08:30:32', '2025-07-11 08:33:19'),
(89, 'ORD1752329133', 1, 45000.00, 1, 4, '2025-07-12 14:05:33', '2025-07-13 05:07:59'),
(90, 'ORD1752383197', 1, 25000.00, 1, 4, '2025-07-13 05:06:37', '2025-07-13 05:07:58'),
(91, 'ORD1752383406', 1, 40000.00, 1, 4, '2025-07-13 05:10:06', '2025-07-13 05:10:38'),
(92, 'ORD1752383500', 1, 75000.00, 1, 4, '2025-07-13 05:11:40', '2025-07-13 05:12:18'),
(93, 'ORD1752383578', 1, 26000.00, 1, 4, '2025-07-13 05:12:58', '2025-07-13 05:13:38'),
(94, 'ORD1752389025', 1, 15000.00, 1, 4, '2025-07-13 06:43:45', '2025-07-13 06:44:10'),
(95, 'ORD1752389098', 1, 255000.00, 1, 4, '2025-07-13 06:44:58', '2025-07-13 06:45:42'),
(96, 'ORD1752389187', 1, 126000.00, 1, 4, '2025-07-13 06:46:27', '2025-07-13 06:46:58'),
(97, 'ORD1752401576', 1, 330000.00, 1, 3, '2025-07-13 10:12:56', '2025-07-13 14:28:33'),
(98, 'ORD1752408669', 1, 175000.00, 1, 4, '2025-07-13 12:11:09', '2025-07-13 12:57:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `quantity`, `note`, `total_price`, `order_id`, `size_id`, `product_id`) VALUES
(4, 1, '', 30000.00, 60, 1, 76),
(5, 1, '', 35000.00, 61, 3, 76),
(6, 2, '', 100000.00, 62, 3, 74),
(7, 1, '', 15000.00, 63, 1, 73),
(8, 1, '', 25000.00, 63, 2, 75),
(9, 1, '', 25000.00, 64, 1, 75),
(10, 1, '', 25000.00, 65, 1, 76),
(11, 1, '', 40000.00, 66, 1, 74),
(12, 1, '', 30000.00, 67, 2, 75),
(13, 2, '', 70000.00, 68, 3, 76),
(14, 1, '', 20000.00, 69, 1, 58),
(15, 2, '', 40000.00, 70, 1, 2),
(16, 2, '', 40000.00, 70, 1, 2),
(17, 1, '', 20000.00, 70, 1, 58),
(19, 1, '', 15000.00, 72, 1, 47),
(20, 1, '', 20000.00, 73, 2, 2),
(21, 1, '', 20000.00, 74, 1, 47),
(22, 1, '', 15000.00, 75, 1, 2),
(24, 2, '', 30000.00, 77, 1, 55),
(25, 1, '', 30000.00, 77, 1, 55),
(26, 1, '', 15000.00, 78, 1, 58),
(27, 1, '', 35000.00, 78, 1, 80),
(28, 1, '', 20000.00, 79, 1, 58),
(29, 1, '', 10000.00, 80, 1, 2),
(30, 1, '', 15000.00, 80, 1, 47),
(31, 1, '', 15000.00, 81, 1, 47),
(32, 1, '', 10000.00, 82, 1, 2),
(33, 1, '', 15000.00, 83, 1, 55),
(34, 1, '', 15000.00, 84, 1, 47),
(35, 1, '', 15000.00, 85, 1, 47),
(36, 1, '', 10000.00, 85, 1, 2),
(37, 1, '', 15000.00, 85, 1, 55),
(38, 2, '', 30000.00, 86, 1, 58),
(39, 1, '', 25000.00, 86, 1, 74),
(40, 2, '', 30000.00, 87, 1, 47),
(41, 2, '', 30000.00, 88, 1, 55),
(42, 2, '', 30000.00, 88, 1, 54),
(43, 2, '', 30000.00, 88, 1, 58),
(44, 2, '', 30000.00, 88, 1, 75),
(45, 2, '', 30000.00, 89, 1, 55),
(46, 1, '', 15000.00, 89, 1, 47),
(47, 1, '', 10000.00, 90, 1, 2),
(48, 1, '', 15000.00, 90, 1, 58),
(49, 1, '', 15000.00, 91, 1, 55),
(50, 1, '', 25000.00, 91, 1, 74),
(51, 1, '', 20000.00, 92, 1, 81),
(52, 1, '', 25000.00, 92, 1, 80),
(53, 1, '', 30000.00, 92, 1, 82),
(54, 1, '', 10000.00, 93, 1, 93),
(55, 1, '', 6000.00, 93, 1, 92),
(56, 1, '', 10000.00, 93, 1, 90),
(57, 1, '', 15000.00, 94, 1, 58),
(58, 3, '', 150000.00, 95, 2, 80),
(59, 3, '', 105000.00, 95, 2, 79),
(60, 5, '', 50000.00, 96, 1, 90),
(61, 1, '', 10000.00, 96, 1, 91),
(62, 1, '', 6000.00, 96, 1, 92),
(63, 4, '', 40000.00, 96, 1, 93),
(64, 1, '', 20000.00, 96, 1, 94),
(65, 1, '', 15000.00, 97, 1, 55),
(66, 1, '', 20000.00, 97, 1, 81),
(67, 1, '', 30000.00, 97, 1, 82),
(68, 1, '', 30000.00, 98, 1, 55),
(69, 2, '', 30000.00, 98, 1, 55),
(70, 1, '', 15000.00, 98, 1, 58),
(71, 1, '', 25000.00, 98, 1, 74),
(72, 1, '', 25000.00, 98, 1, 80),
(73, 1, '', 20000.00, 98, 1, 81),
(74, 1, '', 30000.00, 98, 1, 82),
(75, 1, '', 25000.00, 97, 1, 74),
(76, 1, '', 15000.00, 97, 1, 75),
(77, 1, '', 20000.00, 97, 1, 89),
(78, 1, '', 25000.00, 97, 1, 86),
(79, 1, '', 20000.00, 97, 1, 81),
(80, 1, '', 25000.00, 97, 1, 83),
(81, 1, '', 15000.00, 97, 1, 55),
(82, 1, '', 15000.00, 97, 1, 55),
(83, 1, '', 15000.00, 97, 1, 77),
(84, 1, '', 20000.00, 97, 1, 78),
(85, 1, '', 10000.00, 97, 1, 90),
(86, 1, '', 10000.00, 97, 1, 93),
(87, 1, '', 20000.00, 97, 1, 96),
(88, 1, '', 30000.00, 97, 1, 78);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_item_toppings`
--

CREATE TABLE `order_item_toppings` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `topping_id` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_item_toppings`
--

INSERT INTO `order_item_toppings` (`id`, `order_item_id`, `topping_id`, `note`, `price`, `quantity`) VALUES
(3, 4, 1, NULL, 5000.00, 1),
(4, 4, 2, NULL, 5000.00, 1),
(5, 5, 2, NULL, 5000.00, 1),
(6, 6, 3, NULL, 10000.00, 1),
(7, 6, 14, NULL, 5000.00, 1),
(8, 8, 13, NULL, 5000.00, 1),
(9, 9, 17, NULL, 5000.00, 1),
(10, 9, 18, NULL, 5000.00, 1),
(11, 10, 2, NULL, 5000.00, 1),
(12, 11, 3, NULL, 10000.00, 1),
(13, 11, 13, NULL, 5000.00, 1),
(14, 12, 3, NULL, 10000.00, 1),
(15, 13, 2, NULL, 5000.00, 1),
(16, 14, 1, NULL, 5000.00, 1),
(17, 15, 14, NULL, 5000.00, 1),
(18, 15, 16, NULL, 5000.00, 1),
(19, 16, 14, NULL, 5000.00, 1),
(20, 16, 16, NULL, 5000.00, 1),
(21, 17, 1, NULL, 5000.00, 1),
(23, 20, 14, NULL, 5000.00, 1),
(24, 21, 14, NULL, 5000.00, 1),
(25, 22, 14, NULL, 5000.00, 1),
(27, 25, 1, NULL, 5000.00, 1),
(28, 25, 10, NULL, 10000.00, 1),
(29, 27, 1, NULL, 5000.00, 1),
(30, 27, 2, NULL, 5000.00, 1),
(31, 28, 1, NULL, 5000.00, 1),
(32, 58, 1, NULL, 5000.00, 1),
(33, 58, 2, NULL, 5000.00, 1),
(34, 58, 10, NULL, 10000.00, 1),
(35, 59, 1, NULL, 5000.00, 1),
(36, 59, 2, NULL, 5000.00, 1),
(37, 68, 1, NULL, 5000.00, 1),
(38, 68, 10, NULL, 10000.00, 1),
(39, 88, 1, NULL, 5000.00, 1),
(40, 88, 2, NULL, 5000.00, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_status`
--

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_status`
--

INSERT INTO `order_status` (`id`, `name`) VALUES
(3, 'Chờ thanh toán'),
(2, 'Sẳn sàng phục vụ'),
(0, 'Xác nhậnn'),
(5, 'Đã hủy'),
(4, 'Đã thanh toán'),
(1, 'Đang chuẩn bị');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name_method` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name_method`) VALUES
(1, 'COD'),
(2, 'Banking');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` char(255) NOT NULL,
  `description` text NOT NULL,
  `image_food` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `image_food`, `status`, `price`, `created_at`, `updated_at`) VALUES
(2, 2, 'Trà Đào', 'tra-dao', 'trà đào', 'images/1752044486_686e13c604161.jpg', 1, 10000.00, '2025-01-11 02:02:37', '2025-07-09 07:01:26'),
(47, 2, 'Trà tắc', 'tra-tac', 'Trà tắc', 'images/1752044469_686e13b5bd5e9.jpg', 1, 15000.00, '2025-01-11 02:15:54', '2025-07-09 07:01:09'),
(54, 21, 'Kem Chocolate', 'kem-chocolate', 'kem socola', 'images/1752044453_686e13a50b930.jpg', 1, 15000.00, '2025-01-11 02:53:28', '2025-07-09 07:00:53'),
(55, 1, 'Bánh Tráng Trộn', 'banh-trang-tron', 'bánh tráng trộn', 'images/1752044420_686e13843ccfe.jpg', 1, 15000.00, '2025-01-11 02:57:49', '2025-07-09 07:00:20'),
(58, 1, 'Bánh tráng cuộn', 'banh-trang-cuon', 'Báng tráng trộn', 'images/1752044398_686e136e8bec8.jpg', 1, 15000.00, '2025-01-11 03:18:22', '2025-07-09 06:59:58'),
(73, 21, 'Kem vani', 'kem-vani', 'kem', 'images/1751880239_686b922fd4fce.jpg', 1, 15000.00, '2025-06-04 05:31:15', '2025-07-07 09:23:59'),
(74, 2, 'Sữa tươi đường đen', 'sua-tuoi-duong-den', 'sữa tươi trân châu đường đen', 'images/1751852230_686b24c64ac31.jpg', 1, 25000.00, '2025-06-04 07:35:29', '2025-07-07 09:25:27'),
(75, 2, 'Trà sữa truyền thống', 'tra-sua-truyen-thong', 'Trà sửa truyền thống', 'images/tra-sua-truyen-thong.jpg', 1, 15000.00, '2025-06-04 09:01:06', '2025-07-07 09:25:08'),
(76, 1, 'Bánh tráng nướng', 'banh-trang-nuong', 'Bánh tráng nướng', 'images/1751428506_6864ad9aa38e0.jpg', 1, 20000.00, '2025-06-05 03:14:04', '2025-07-07 09:24:50'),
(77, 1, 'Bánh tráng chiên', 'banh-trang-chien', 'Vỏ bánh giòn tan ôm lấy phần nhân thịt mặn mà có khả năng thoả mãn ngay cả thực khách khó chiều nhất. Trổ tài vào bếp để lấy lòng crush ngay thôi', 'images/1752051622_686e2fa653c9f.jpg', 1, 15000.00, '2025-07-09 09:00:22', '2025-07-09 09:00:22'),
(78, 1, 'Tokbokki Bánh Tráng', 'tokbokki-banh-trang', 'bạn đã có thế thưởng thức Tokbokki - món ăn vặt ngon chuẩn Hàn Quốc - với nguyên liệu thuần Việt là bánh tráng. Công thức làm Tokbokki từ bánh tráng đơn giản hơn bạn nghĩ. Tham khảo ngay nhé!', 'images/1752052002_686e31228dad6.jpg', 1, 20000.00, '2025-07-09 09:06:30', '2025-07-09 09:06:42'),
(79, 1, 'Bánh tráng chà bông', 'banh-trang-cha-bong', 'Mang hương vị nguyên bản của bánh tráng cùng với đó là hành phi và mỡ hành béo thơm ngất ngây, đặc biệt là chà bông mềm, dai, tơi đậm vị. Nếm một miếng thôi đã say mê mất rồi.', 'images/1752052789_686e34352120b.jpg', 1, 20000.00, '2025-07-09 09:19:49', '2025-07-09 09:19:49'),
(80, 1, 'Bánh tráng mắm ruốc', 'banh-trang-mam-ruoc', 'Bánh tráng mắm ruốc với hương vị đậm đà chất Việt, giòn dai thơm ngon đã khiến không ít tín đồ ẩm thực gục ngã. Đặc biệt, món ăn này còn rất dễ chế biến tại gia chỉ với vài nguyên liệu đơn giản.', 'images/1752054124_686e396c50d89.jpg', 1, 25000.00, '2025-07-09 09:42:04', '2025-07-09 09:42:04'),
(81, 1, 'Bánh tráng lụi', 'banh-trang-lui', 'bánh tráng lụi Tây Nguyên, món ăn đã đốn tim biết bao nhiêu các bạn trẻ.', 'images/1752054217_686e39c9c2927.jpg', 1, 20000.00, '2025-07-09 09:43:37', '2025-07-09 13:35:50'),
(82, 2, 'Trà sữa Oreo', 'tra-sua-oreo', 'Trà sữa Oreo Cake Cream là trà sữa thông thường, tuy nhiên không dùng bột pha trực tiếp, tạo nên vị trà thơm,chát cùng chút béo của sữa. Tiếp theo, trà sữa Oreo Cake Cream được phủ thêm một lớp cake cream vàng nhạt phía trên và xung quanh thành cốc', 'images/1752071945_686e7f0936efa.jpg', 1, 30000.00, '2025-07-09 09:52:03', '2025-07-09 14:39:05'),
(83, 2, 'Trà sữa khoai môn', 'tra-sua-khoai-mon', 'trà sữa khoai môn tuy không phải là thức uống mới nhưng nó vẫn giữ được độ hot với giới trẻ, đặc biệt là vào các khoảng thời gian hè nắng nóng. Trà sữa khoai môn có vị béo béo của sữa, mùi thơm của khoai môn cùng với chút vị chát nhẹ của trà.', 'images/1752068602_686e71fae61a5.jpg', 1, 25000.00, '2025-07-09 13:43:22', '2025-07-09 14:40:27'),
(84, 2, 'Trà xoài kem', 'tra-xoai-kem', 'Mang nét tươi mới của trà cùng xoài tươi, phủ lên mình một lớp kem cheese thơm ngậy - trà xoài kem cheese sẽ khiến bạn mê mẩn bởi hương vị hài hòa mà cực kì lôi cuốn.', 'images/1752068724_686e72747a95d.jpg', 1, 25000.00, '2025-07-09 13:45:04', '2025-07-09 13:45:24'),
(85, 2, 'Trà sữa matcha đậu đỏ', 'tra-sua-matcha-dau-do', 'Trà sữa Matcha đậu đỏ về cơ bản vẫn là trà sữa trà xanh với vị chát nhẹ của trà, vị thơm mát của matcha cùng với chút ngọt nhẹ của sữa tươi.', 'images/1752068923_686e733ba4952.jpg', 1, 30000.00, '2025-07-09 13:48:43', '2025-07-09 13:48:43'),
(86, 2, 'Trà sữa sương sáo', 'tra-sua-suong-sao', 'Trà sữa sương sáo là sự kết hợp giữa trà sữa truyền thống với phần sương sáo mát lạnh.', 'images/1752069295_686e74afc470b.jpg', 1, 25000.00, '2025-07-09 13:54:55', '2025-07-09 13:54:55'),
(87, 2, 'Trà sữa Earl Grey', 'tra-sua-earl-grey', 'rà sữa Earl Grey có vị khá chát và đắng nhẹ, đặc biệt là mùi thơm rất đặc trưng mà không phải loại trà nào cũng có.', 'images/1752069426_686e753222bfc.jpg', 1, 25000.00, '2025-07-09 13:57:06', '2025-07-09 13:57:06'),
(88, 2, 'Trà dâu', 'tra-dau', 'Trà có màu đỏ tươi, vị mát của dâu hòa quyện với mùi thơm của trà, thích hợp uống vào mùa hè nóng nực để giải nhiệt.', 'images/1752069841_686e76d1448f4.jpg', 1, 20000.00, '2025-07-09 14:04:01', '2025-07-09 14:04:01'),
(89, 2, 'Trà dưa hấu', 'tra-dua-hau', 'Trà dưa hấu với sự kết hợp giữa trà và nước ép dưa hấu sẽ tạo nên một món thức uống ngọt mát, lạ miệng lại còn giúp thanh lọc cơ thể.', 'images/1752069978_686e775acb0f1.jpg', 1, 20000.00, '2025-07-09 14:06:02', '2025-07-09 14:22:17'),
(90, 21, 'Rau câu sơn thủy', 'rau-cau-son-thuy', 'Rau câu sơn thủy nghe tên đã thấy hữu tình còn vẻ ngoài thì vô cùng bắt mắt. 3 tầng màu sắc đẹp nghệ thuật với sự phối hợp giữa 3 nguyên liệu: lá dứa, cafe, sữa đặc.', 'images/1752070922_686e7b0a48400.jpg', 1, 10000.00, '2025-07-09 14:22:02', '2025-07-09 14:22:02'),
(91, 21, 'Rau câu trái cây', 'rau-cau-trai-cay', 'Nguyên liệu là đa dạng các loại trái cây tươi ngon tự chọn như táo, cam, nho, bơ… với màu sắc phong phú vô cùng.', 'images/1752071097_686e7bb96a478.jpg', 1, 10000.00, '2025-07-09 14:24:57', '2025-07-09 14:24:57'),
(92, 21, 'Bánh flan', 'banh-flan', 'Bánh flan cuốn hút vị giác bởi hương trứng beo béo quyện cùng vị sốt caramel ngọt đậm đà. Bề mặt bánh flan láng mịn và mềm mại vô cùng nên trẻ em đến người già đều thưởng thức được.', 'images/1752071242_686e7c4a807a1.jpg', 1, 6000.00, '2025-07-09 14:27:13', '2025-07-09 14:27:22'),
(93, 21, 'Bánh mousse', 'banh-mousse', 'Mousse là một trong các loại bánh trứ danh của Pháp, hội tụ đủ hương vị từ beo béo đến chua chua, ngọt ngọt. Được yêu thích nhất là 3 vị nổi tiếng gồm chanh dây, matcha, chocolate, xoài,…', 'images/1752071412_686e7cf404cde.jpg', 1, 10000.00, '2025-07-09 14:30:12', '2025-07-09 14:30:17'),
(94, 21, 'Panna cotta', 'panna-cotta', 'Món tráng miệng ngon trứ danh đến từ nước Ý – Panna cotta, là chọn lựa phù hợp cho những buổi tiệc đậm chất châu Âu lãng mạn.', 'images/1752071594_686e7daa80064.jpg', 1, 20000.00, '2025-07-09 14:33:05', '2025-07-09 14:33:14'),
(95, 21, 'Kem dừa', 'kem-dua', 'Kem dừa là món ngon quen thuộc của bao đứa trẻ nên khi nhìn thấy kem dừa trên bàn tiệc sẽ gợi nhắc tuổi thơ tươi đẹp của nhiều thực khách.', 'images/1752071722_686e7e2a04324.jpg', 0, 20000.00, '2025-07-09 14:35:22', '2025-07-11 08:39:31'),
(96, 2, 'trà sữa trân châu', 'tra-sua-tran-chau', 'trà sữa', 'images/1752223113_6870cd898462b.jpg', 0, 20000.00, '2025-07-11 08:38:33', '2025-07-11 08:38:59');

--
-- Bẫy `products`
--
DELIMITER $$
CREATE TRIGGER `Products_Updated_At` BEFORE UPDATE ON `products` FOR EACH ROW SET NEW.updated_at = NOW()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_size`
--

CREATE TABLE `product_size` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_size`
--

INSERT INTO `product_size` (`id`, `product_id`, `size_id`) VALUES
(1, 74, 1),
(9, 75, 1),
(14, 74, 2),
(15, 75, 2),
(16, 75, 3),
(22, 76, 1),
(23, 76, 2),
(24, 75, 4),
(25, 73, 1),
(26, 73, 2),
(27, 74, 3),
(28, 58, 1),
(29, 58, 2),
(31, 55, 1),
(32, 55, 2),
(33, 76, 3),
(34, 2, 1),
(35, 2, 2),
(36, 2, 3),
(37, 47, 1),
(38, 47, 2),
(39, 47, 3),
(40, 77, 1),
(41, 77, 2),
(42, 77, 3),
(43, 79, 1),
(44, 79, 2),
(45, 79, 3),
(46, 80, 1),
(47, 80, 2),
(48, 80, 3),
(49, 81, 1),
(50, 81, 2),
(51, 81, 3),
(52, 82, 1),
(53, 82, 2),
(54, 82, 3),
(55, 78, 1),
(56, 78, 2),
(57, 78, 3),
(58, 83, 1),
(59, 83, 2),
(60, 83, 3),
(61, 83, 4),
(62, 84, 1),
(63, 84, 2),
(64, 84, 3),
(65, 84, 4),
(66, 85, 1),
(67, 85, 2),
(68, 85, 3),
(69, 85, 4),
(70, 86, 1),
(71, 86, 2),
(72, 86, 3),
(73, 86, 4),
(74, 87, 1),
(75, 87, 2),
(76, 87, 3),
(77, 87, 4),
(78, 88, 1),
(79, 88, 2),
(80, 88, 3),
(81, 88, 4),
(82, 89, 1),
(83, 89, 2),
(84, 89, 3),
(85, 89, 4),
(86, 90, 1),
(87, 91, 1),
(88, 91, 2),
(89, 92, 1),
(90, 92, 2),
(91, 93, 1),
(92, 93, 2),
(93, 94, 1),
(94, 94, 2),
(95, 95, 1),
(96, 95, 2),
(97, 96, 1),
(98, 96, 2),
(99, 96, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_topping`
--

CREATE TABLE `product_topping` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `topping_id` int(11) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_topping`
--

INSERT INTO `product_topping` (`id`, `product_id`, `topping_id`, `quantity`) VALUES
(33, 76, 2, 1),
(35, 58, 1, 1),
(36, 76, 1, 1),
(39, 75, 3, 1),
(40, 55, 1, 1),
(42, 55, 10, 1),
(44, 74, 3, 1),
(45, 55, 2, 1),
(46, 55, 7, 1),
(48, 76, 8, 1),
(49, 74, 13, 1),
(50, 74, 14, 1),
(51, 75, 13, 1),
(52, 75, 14, 1),
(53, 75, 15, 1),
(54, 75, 16, 1),
(55, 75, 17, 1),
(56, 75, 18, 1),
(57, 75, 19, 1),
(58, 74, 15, 1),
(59, 74, 16, 1),
(60, 74, 17, 1),
(61, 74, 18, 1),
(62, 74, 19, 1),
(63, 47, 14, 1),
(64, 47, 17, 1),
(65, 2, 14, 1),
(66, 2, 16, 1),
(67, 2, 17, 1),
(68, 82, 14, 1),
(69, 82, 15, 1),
(70, 82, 17, 1),
(71, 82, 18, 1),
(72, 81, 1, 1),
(73, 81, 2, 1),
(74, 81, 10, 1),
(75, 80, 1, 1),
(76, 80, 2, 1),
(77, 80, 10, 1),
(78, 79, 1, 1),
(79, 79, 2, 1),
(80, 79, 10, 1),
(81, 78, 1, 1),
(82, 78, 2, 1),
(83, 78, 10, 1),
(84, 77, 1, 1),
(85, 77, 2, 1),
(86, 77, 10, 1),
(87, 84, 14, 1),
(88, 84, 15, 1),
(89, 84, 16, 1),
(90, 84, 17, 1),
(91, 84, 18, 1),
(92, 89, 13, 1),
(93, 89, 14, 1),
(94, 89, 16, 1),
(95, 89, 17, 1),
(96, 83, 14, 1),
(97, 83, 15, 1),
(98, 83, 16, 1),
(99, 83, 17, 1),
(100, 83, 18, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sizes`
--

INSERT INTO `sizes` (`id`, `name`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 'S', 0.00, 1, '2025-06-04 15:14:02', '2025-06-04 15:14:02'),
(2, 'M', 5000.00, 1, '2025-06-04 15:14:02', '2025-06-04 15:14:02'),
(3, 'L', 10000.00, 1, '2025-06-04 15:14:02', '2025-06-04 15:14:02'),
(4, 'XL', 15000.00, 1, '2025-06-04 15:14:02', '2025-06-04 15:14:02'),
(5, 'XXL', 20000.00, 1, '2025-06-04 08:58:19', '2025-06-04 08:58:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tables`
--

CREATE TABLE `tables` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `table_status_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tables`
--

INSERT INTO `tables` (`id`, `name`, `qr_code`, `token`, `table_status_id`, `created_at`, `updated_at`) VALUES
(1, 'T01', 'qr_table_H2XGraok18m4ZYFEWYmNUELSKrLIh5PP80tcEqLG.png', 'H2XGraok18m4ZYFEWYmNUELSKrLIh5PP80tcEqLG', 2, '2025-06-11 14:42:02', '2025-07-13 12:43:40'),
(2, 'T02', 'qr_table_kqY9D0LWJeA1HWPPCvysZukfftEbBUpoQ6Ne2QEQ.png', 'kqY9D0LWJeA1HWPPCvysZukfftEbBUpoQ6Ne2QEQ', 1, '2025-06-11 14:42:02', '2025-07-13 12:42:32'),
(30, 'T03', 'qr_table_A2NRWVQW2gpmHo5UNOsCmuFSMyqLAAzeYmnagVCL.png', 'A2NRWVQW2gpmHo5UNOsCmuFSMyqLAAzeYmnagVCL', 1, '2025-06-18 03:45:04', '2025-07-11 08:44:23'),
(31, 'T04', 'qr_table_sP41mK59JFkCqGR9kIu2StWDW52s3ne4n33sejZe.png', 'sP41mK59JFkCqGR9kIu2StWDW52s3ne4n33sejZe', 1, '2025-06-18 03:53:21', '2025-07-11 06:34:07'),
(32, 'T05', 'qr_table_mU18LI2yWOlo5qXAUPqlXuwyGRLu0We2iQzzNptc.png', 'mU18LI2yWOlo5qXAUPqlXuwyGRLu0We2iQzzNptc', 1, '2025-06-18 04:00:57', '2025-07-11 06:34:10'),
(33, 'T06', 'qr_table_qZ03nrnAZwsJvUN6Rjvm7BgEv8lHn2GIQOCOSoCX.png', 'qZ03nrnAZwsJvUN6Rjvm7BgEv8lHn2GIQOCOSoCX', 1, '2025-06-26 07:42:10', '2025-07-02 08:25:06'),
(34, 'T07', 'qr_table_Foz2a3VzedGZa2YV1luWQJMoFLqyZu4nYLk71XpN.png', 'Foz2a3VzedGZa2YV1luWQJMoFLqyZu4nYLk71XpN', 1, '2025-06-26 07:42:15', '2025-07-11 06:34:30'),
(35, 'T08', 'qr_table_J9beDJmzSWLBhUhHhSZ23UR1zb8jk69UyO1zUTZf.png', 'J9beDJmzSWLBhUhHhSZ23UR1zb8jk69UyO1zUTZf', 1, '2025-06-26 07:42:39', '2025-07-11 06:34:21'),
(36, 'T09', 'qr_table_vmss0keQWJsMT6pqkXytjHQ6tL8yL8wMdEmcpTgz.png', 'vmss0keQWJsMT6pqkXytjHQ6tL8yL8wMdEmcpTgz', 1, '2025-06-26 07:42:44', '2025-07-11 06:34:34'),
(37, 'T10', 'qr_table_F90TD98YeZS2VMA0tt7lIyOSRlKt1U9A0QSj7liO.png', 'F90TD98YeZS2VMA0tt7lIyOSRlKt1U9A0QSj7liO', 1, '2025-06-26 07:42:52', '2025-07-11 06:34:44'),
(38, 'T11', 'qr_table_3wJPT0eVwCOUR9GC5EmLb25ac82QBBsdzaGskByf.png', '3wJPT0eVwCOUR9GC5EmLb25ac82QBBsdzaGskByf', 1, '2025-06-26 07:42:58', '2025-07-11 06:34:48'),
(43, 'T12', 'qr_table_4bz5jYzV8kN32Z0djvGwpCHCjBe5e3145S8PjTh3.png', '4bz5jYzV8kN32Z0djvGwpCHCjBe5e3145S8PjTh3', 1, '2025-07-07 06:38:54', '2025-07-12 02:40:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `table_status`
--

CREATE TABLE `table_status` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `table_status`
--

INSERT INTO `table_status` (`id`, `name`) VALUES
(1, 'Trống'),
(2, 'Đang sử dụng'),
(3, 'Đang dọn bàn'),
(4, 'Ẩn bàn');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `toppings`
--

CREATE TABLE `toppings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `toppings`
--

INSERT INTO `toppings` (`id`, `name`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Trứng cút', 5000.00, 1, '2025-06-04 15:14:43', '2025-06-04 15:14:43'),
(2, 'Xúc xích', 5000.00, 1, '2025-06-04 15:14:43', '2025-06-04 15:14:43'),
(3, 'Trân châu', 10000.00, 1, '2025-06-04 15:14:43', '2025-06-04 15:14:43'),
(7, 'Trứng', 5000.00, 1, '2025-06-04 15:14:43', '2025-06-04 15:14:43'),
(8, 'Bánh tráng ', 5000.00, 1, '2025-06-04 15:14:43', '2025-06-04 15:14:43'),
(10, 'Tóp mỡ', 10000.00, 1, '2025-06-04 08:59:34', '2025-06-04 08:59:34'),
(13, 'Thạch rau câu', 5000.00, 1, '2025-07-01 03:00:45', '2025-07-01 03:00:45'),
(14, 'Trân châu hoàng kim', 5000.00, 1, '2025-07-01 03:02:08', '2025-07-01 03:02:08'),
(15, 'Pudding', 5000.00, 1, '2025-07-01 03:06:06', '2025-07-01 03:06:06'),
(16, 'Thạch củ năng', 5000.00, 1, '2025-07-01 03:06:33', '2025-07-01 03:06:33'),
(17, 'Hạt thủy tinh', 5000.00, 1, '2025-07-01 03:07:01', '2025-07-01 03:07:01'),
(18, 'Kem cheese', 5000.00, 1, '2025-07-01 03:14:44', '2025-07-01 03:14:44'),
(19, 'Kem trứng', 5000.00, 1, '2025-07-01 03:14:57', '2025-07-01 03:14:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` char(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` char(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `gender` varchar(3) NOT NULL,
  `date_of_birth` date NOT NULL,
  `image` char(255) DEFAULT NULL,
  `phone` char(10) NOT NULL,
  `email` char(255) NOT NULL,
  `role` char(3) NOT NULL DEFAULT 'KH',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `gender`, `date_of_birth`, `image`, `phone`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES
(11, 'NguyenThuyAnhThu', '$2y$12$OvcXuzJ0C9vUxV0h8WuXQ./TAiyK6zAom0a3lR9zv62kv.hk.gaji', 'Nguyễn Thùy Anh Thư', 'Nữ', '2025-01-10', '', '0123456785', 'Thu@gmail.com', 'NV', 1, '2025-01-10 14:00:34', '2025-06-27 03:14:38'),
(12, 'NguyenQuocDo', '$2y$12$Tauzu2jcOMNd5jfcRmAiD.BNs.f4rtMTDD1fjoixre0od2WcdEtty', 'Nguyễn Quốc Đô', 'Nam', '2025-01-11', '', '0362636315', 'quocdo@gmail.com', 'NV', 1, '2025-01-11 10:29:09', '2025-06-27 03:14:42'),
(13, 'DangKhanhDong', '$2y$12$ArpTp3Oigia4huwdjfTjhudCbmY9BbCskI0YEVbFeEH0nhWFrG8x2', 'Đặng Khánh Đông', 'Nam', '2025-01-11', '', '0325646495', 'khanhdong@gmail.com', 'QL', 1, '2025-01-11 10:29:57', '2025-01-12 03:20:02'),
(15, 'NguyenTruongGiang', '$2y$12$nd0/nPrxSclusX2oSNUG0ePuz7Zl5Afx5MyCmw0qF3enzHIKCm5ne', 'Nguyễn Trường Giang', 'Nam', '2025-01-11', '', '0765984134', 'giang@gmail.com', 'QL', 1, '2025-01-11 10:31:53', '2025-06-26 14:35:50'),
(16, 'NguyenNgocHoang', '$2y$12$hcKz0gMCrEPYvLPxjt/CeuwNoPggy5bNFNnH5ireFvrPmwmD2cPuO', 'Nguyễn Ngọc Hoàng', 'Nam', '2025-01-11', '', '0369585731', 'hoang@gmail.com', 'NV', 1, '2025-01-11 10:32:48', '2025-06-27 03:14:35'),
(20, 'giang0123', '$2y$12$Xn7XAoXsuDZNDHrGOFMP/ufaHsG4YiPI4STAmSWEb4dlH4.Qjz1uy', 'Nguyen truong Giang', 'Nam', '2003-06-25', NULL, '0375330740', 'pan58858@gmail.com', 'NV', 1, '2025-07-11 02:13:57', '2025-07-11 02:13:57');

--
-- Bẫy `users`
--
DELIMITER $$
CREATE TRIGGER `Users_Update_at` BEFORE UPDATE ON `users` FOR EACH ROW SET NEW.updated_at = NOW()
$$
DELIMITER ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Blogs_Unq_Slug` (`slug`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_table` (`table_id`),
  ADD KEY `fk_cart_product` (`product_id`),
  ADD KEY `fk_cart_size` (`size_id`);

--
-- Chỉ mục cho bảng `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_item_id` (`cart_item_id`),
  ADD KEY `topping_id` (`topping_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Categories_Unq_Slug` (`slug`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_paymethod` (`payment_method_id`),
  ADD KEY `fk_order_table` (`table_id`),
  ADD KEY `fk_order_status` (`order_status_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderitems_size` (`size_id`),
  ADD KEY `fk_orderitems_orders` (`order_id`),
  ADD KEY `fk_orderitems_product` (`product_id`);

--
-- Chỉ mục cho bảng `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_id` (`order_item_id`),
  ADD KEY `topping_id` (`topping_id`);

--
-- Chỉ mục cho bảng `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Order_Status_Unq_Name` (`name`);

--
-- Chỉ mục cho bảng `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Products_Unq_Slug` (`slug`),
  ADD KEY `fk_category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_size`
--
ALTER TABLE `product_size`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Chỉ mục cho bảng `product_topping`
--
ALTER TABLE `product_topping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `topping_id` (`topping_id`);

--
-- Chỉ mục cho bảng `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tables_status_id` (`table_status_id`);

--
-- Chỉ mục cho bảng `table_status`
--
ALTER TABLE `table_status`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `toppings`
--
ALTER TABLE `toppings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Users_Unq_Username` (`username`),
  ADD UNIQUE KEY `Users_Unq_Phone` (`phone`),
  ADD UNIQUE KEY `Users_Unq_Email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `about`
--
ALTER TABLE `about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT cho bảng `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT cho bảng `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT cho bảng `order_status`
--
ALTER TABLE `order_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT cho bảng `product_size`
--
ALTER TABLE `product_size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT cho bảng `product_topping`
--
ALTER TABLE `product_topping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT cho bảng `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT cho bảng `table_status`
--
ALTER TABLE `table_status`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `toppings`
--
ALTER TABLE `toppings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_cart_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`),
  ADD CONSTRAINT `fk_cart_table` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`);

--
-- Các ràng buộc cho bảng `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  ADD CONSTRAINT `fk_cartitem_id` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`id`),
  ADD CONSTRAINT `fk_item_topping` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_paymethod` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  ADD CONSTRAINT `fk_order_status` FOREIGN KEY (`order_status_id`) REFERENCES `order_status` (`id`),
  ADD CONSTRAINT `fk_order_table` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitems_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `fk_orderitems_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_orderitems_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`);

--
-- Các ràng buộc cho bảng `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  ADD CONSTRAINT `order_item_toppings_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_toppings_ibfk_2` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `product_size`
--
ALTER TABLE `product_size`
  ADD CONSTRAINT `product_size_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_size_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_topping`
--
ALTER TABLE `product_topping`
  ADD CONSTRAINT `product_topping_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_topping_ibfk_2` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tables`
--
ALTER TABLE `tables`
  ADD CONSTRAINT `tables_ibfk_1` FOREIGN KEY (`table_status_id`) REFERENCES `table_status` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
