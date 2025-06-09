-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 11:15 AM
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
-- Database: `toko_kopi`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupon_sends`
--

CREATE TABLE `coupon_sends` (
  `id` int(11) NOT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `coupon_code` varchar(100) DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `max_discount` int(11) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon_sends`
--

INSERT INTO `coupon_sends` (`id`, `recipient_email`, `coupon_code`, `discount`, `max_discount`, `expiry_date`, `sent_at`) VALUES
(1, 'cindypujilestari8@gmail.com', 'KOPIKUKI-30-7E274', 30, 40000, '2025-06-09', '2025-06-08 13:37:35'),
(2, 'sulton843@gmail.com', 'KOPIKUKI-30-7E274', 30, 40000, '2025-06-09', '2025-06-08 13:37:40'),
(3, 'katessketje@gmail.com', 'KOPIKUKI-30-7E274', 30, 40000, '2025-06-09', '2025-06-08 13:37:44'),
(4, 'nabilasalwaal2105@gmail.com', 'KOPIKUKI-30-7E274', 30, 40000, '2025-06-09', '2025-06-08 13:37:50'),
(5, 'puan.akeyla37@gmail.com', 'KOPIKUKI-30-7E274', 30, 40000, '2025-06-09', '2025-06-08 13:37:55'),
(6, 'cindypujilestari8@gmail.com', 'KOPIKUKI-10-142EC', 10, 50000, '2025-06-14', '2025-06-08 16:05:21'),
(7, 'sulton843@gmail.com', 'KOPIKUKI-10-142EC', 10, 50000, '2025-06-14', '2025-06-08 16:05:25'),
(8, 'katessketje@gmail.com', 'KOPIKUKI-10-142EC', 10, 50000, '2025-06-14', '2025-06-08 16:05:30'),
(9, 'nabilasalwaal2105@gmail.com', 'KOPIKUKI-10-142EC', 10, 50000, '2025-06-14', '2025-06-08 16:05:36'),
(10, 'puan.akeyla37@gmail.com', 'KOPIKUKI-10-142EC', 10, 50000, '2025-06-14', '2025-06-08 16:05:40');

-- --------------------------------------------------------

--
-- Table structure for table `customer_reviews`
--

CREATE TABLE `customer_reviews` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) NOT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 0,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_reviews`
--

INSERT INTO `customer_reviews` (`id`, `customer_name`, `review_text`, `rating`, `review_date`, `approved`, `email`) VALUES
(3, 'Wayan bowo jonata', 'menurut saya kopi dan brounisnya enak akan tetapi mungkin bisa di tambahkan makanan yang asin juga', 5, '2025-06-05 07:09:17', 1, NULL),
(4, 'zahra', 'menurut aku rasa kopinya unik', 5, '2025-06-06 03:39:34', 1, NULL),
(7, 'Desta rahma  irayani', 'Tempatnya cozy banget, cocok buat kerja atau sekadar baca buku. Plus kopi dan kukisnya bikin betah seharian!', 5, '2025-06-08 04:25:15', 1, NULL),
(8, 'Radhitya agrayasa', 'Kopi di sini bikin mata melek tapi hati tenang! Espresso-nya kuat tapi tidak pahit berlebihan, benar-benar nikmat.', 5, '2025-06-08 04:25:57', 1, NULL),
(12, 'Cindy puji lestari', 'Jika kamu mencari tempat yang sempurna untuk melepas penat atau sekadar menikmati momen tenang, kopi&kuki adalah jawabannya. Begitu masuk, aroma kopi yang harum langsung menyambut, berpadu manis dengan wangi kukis yang baru keluar dari oven — kombinasi yang bikin hati hangat.', 5, '2025-06-08 06:00:34', 1, 'cindypujilestari8@gmail.com'),
(15, 'Cindy puji lestari', 'Jika kamu mencari tempat yang sempurna untuk melepas penat atau sekadar menikmati momen tenang, kopi&kuki adalah jawabannya. Begitu masuk, aroma kopi yang harum langsung menyambut, berpadu manis dengan wangi kukis yang baru keluar dari oven — kombinasi yang bikin hati hangat.', 5, '2025-06-08 06:05:12', 1, 'cindypujilestari8@gmail.com'),
(16, 'nabilasalwaal', 'saya senang sekali bisa mencoba kukis yang super lembut dan enak ', 5, '2025-06-08 08:07:46', 0, 'nabilasalwaal2105@gmail.com'),
(17, 'Cindy puji lestari', 'Jika kamu mencari tempat yang sempurna untuk melepas penat atau sekadar menikmati momen tenang, kopi&kuki adalah jawabannya. Begitu masuk, aroma kopi yang harum langsung menyambut, berpadu manis dengan wangi kukis yang baru keluar dari oven — kombinasi yang bikin hati hangat.\r\n\r\n', 5, '2025-06-08 08:11:57', 0, 'cindypujilestari8@gmail.com'),
(18, 'Cindy puji lestari', 'Jika kamu mencari tempat yang sempurna untuk melepas penat atau sekadar menikmati momen tenang, kopi&kuki adalah jawabannya. Begitu masuk, aroma kopi yang harum langsung menyambut, berpadu manis dengan wangi kukis yang baru keluar dari oven — kombinasi yang bikin hati hangat.', 5, '2025-06-08 08:13:31', 0, 'cindypujilestari8@gmail.com'),
(19, 'nabilasalwaal', 'Kupi&Kuki selalu berhasil bikin hariku lebih cerah. Kopinya pas di lidah, kukinya bikin senyum tiap gigitan. Wajib coba!', 5, '2025-06-08 08:37:42', 1, 'nabilasalwaal2105@gmail.com'),
(20, 'nabilasalwaal', 'Kupi&Kuki selalu berhasil bikin hariku lebih cerah. Kopinya pas di lidah, kukinya bikin senyum tiap gigitan. Wajib coba!', 5, '2025-06-08 08:41:10', 0, 'nabilaalghaida@gmail.com'),
(22, 'Akeyla m', 'Datang cuma pengen nyoba kopi, pulang bawa 2 bungkus kuki dan hati yang bahagia. Mana tempatnya lucu banget lagi. Ini sih bukan sekadar ngopi, tapi healing', 4, '2025-06-08 09:09:32', 1, 'puan.akeyla37@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `price`, `created_at`, `image`) VALUES
(2, 'Latte', 25000.00, '2025-06-03 12:49:00', 'gambar2.jpg'),
(3, 'Mocha', 30000.00, '2025-06-03 12:50:23', 'gambar3.jpg'),
(4, 'Americano', 20000.00, '2025-06-03 12:50:51', 'Rectangle 13.png'),
(5, 'Waffle Ice', 35000.00, '2025-06-05 01:02:54', 'WhatsApp Image 2025-06-04 at 22.24.30_a158c29b.jpg'),
(6, 'Lava Cake', 35000.00, '2025-06-05 01:22:03', 'WhatsApp Image 2025-06-04 at 22.24.29_99b37088.jpg'),
(8, 'Berry Bliss Pie', 22000.00, '2025-06-05 01:30:44', 'WhatsApp Image 2025-06-04 at 22.24.30_d1e40d4d.jpg'),
(9, 'StrawMoo Delight', 45000.00, '2025-06-08 04:48:57', 'Gambar WhatsApp 2025-06-08 pukul 11.44.42_f14d730d.jpg'),
(10, 'Moonlight Coffee', 35000.00, '2025-06-08 04:53:34', 'Gambar WhatsApp 2025-06-08 pukul 11.44.43_112677c3.jpg'),
(11, 'Kopi Ghosting', 27000.00, '2025-06-08 04:57:40', 'Gambar WhatsApp 2025-06-08 pukul 11.44.42_6408a183.jpg'),
(13, 'Cotton Bloom', 26000.00, '2025-06-08 05:06:38', 'Gambar WhatsApp 2025-06-08 pukul 12.00.19_8db4d107.jpg'),
(16, 'Choco Chunk Monster', 20000.00, '2025-06-08 05:30:46', 'WhatsApp Image 2025-06-08 at 12.00.19_9f36ade1.jpg'),
(17, 'Creamy Berry Blush', 35000.00, '2025-06-08 05:32:16', 'WhatsApp Image 2025-06-08 at 11.44.42_e432431d.jpg'),
(18, 'Velvet Aren', 27000.00, '2025-06-08 05:40:30', 'Gambar WhatsApp 2025-06-08 pukul 11.44.43_e9a48f6f.jpg'),
(19, 'Strawberry Pie', 27000.00, '2025-06-08 08:52:07', 'Gambar WhatsApp 2025-06-08 pukul 15.50.07_b37b93c8.jpg'),
(20, 'Berry Fizz Pop', 20000.00, '2025-06-08 08:53:56', 'Gambar WhatsApp 2025-06-08 pukul 15.50.08_3d69ea0c.jpg'),
(21, 'Crispy Potato Pop', 15000.00, '2025-06-08 08:56:03', 'Gambar WhatsApp 2025-06-08 pukul 15.50.08_4f410216.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `items` text NOT NULL COMMENT 'JSON array of ordered items'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `order_date`, `total_amount`, `status`, `payment_method`, `notes`, `items`) VALUES
(1, 'Sulthon', '2025-06-04 21:35:52', 30000.00, 'completed', 'QRIS', 'Less Ice', '[{\"menu_id\":3,\"name\":\"Mocha\",\"price\":\"30000.00\",\"quantity\":1,\"image\":\"gambar3.jpg\"}]'),
(2, 'Akeyla', '2025-06-04 21:49:50', 25000.00, 'completed', 'QRIS', 'Less Sugar', '[{\"menu_id\":2,\"name\":\"Latte\",\"price\":\"25000.00\",\"quantity\":1,\"image\":\"gambar2.jpg\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `created_at`) VALUES
(1, 'sulton843@gmail.com', '2025-06-03 11:19:18'),
(2, 'cindypujilestari8@gmail.com', '2025-06-03 11:46:23'),
(3, 'puan.akeyla37@gmail.com', '2025-06-05 02:50:48'),
(4, 'nabilasalwaal2105@gmail.com', '2025-06-05 04:27:08'),
(5, 'katessketje@gmail.com', '2025-06-06 12:10:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user',
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`, `reset_token`, `token_expiry`) VALUES
(1, 'CASS', 'cass@cass.com', '$2y$10$EG7HujP66ajqVQltPGpY../GWe8H3gJ2ZB0sn8rEm9Kq6akBJgHF6', '2025-06-03 09:32:53', 'admin', NULL, NULL),
(2, 'kopikukibdl', 'kopikukibdl@gmail.com', 'autentik123', '2025-06-05 07:00:00', 'admin', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coupon_sends`
--
ALTER TABLE `coupon_sends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coupon_sends`
--
ALTER TABLE `coupon_sends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;