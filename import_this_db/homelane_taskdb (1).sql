-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2022 at 07:33 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homelane_taskdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_URL` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `modified_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `menu_name`, `menu_URL`, `created_on`, `created_by`, `modified_on`, `modified_by`, `deleted_at`) VALUES
(1, 'Super Menu-Exist', 'http://localhost/homelane-task/public/super/menu/1', '2022-11-03 09:56:16', 'Super Admin', NULL, NULL, NULL),
(2, 'Super Menu-2', 'http://localhost/homelane-task/public/super/menu/2', '2022-11-03 09:56:18', 'Super Admin', NULL, NULL, NULL),
(3, 'Super A-Third Party', 'https://html5-tutorial.net/form-validation/validating-urls/', '2022-11-03 09:56:18', 'Super Admin', '2022-11-08 14:27:42', 'User 1:Super Admin', NULL),
(6, 'Super Menu-371', 'http://localhost/homelane-task/public/menu-371', '2022-11-03 18:30:00', 'Super', '2022-11-04 03:13:03', 'User 1:Super Admin', '2022-11-08 13:25:10'),
(7, 'Admin Menu-112', 'http://localhost/homelane-task/public/menu-112', '2022-11-03 18:30:00', 'Super', '2022-11-04 03:13:03', 'User 1:Super Admin', NULL),
(8, 'Admin Menu-1112', 'http://localhost/homelane-task/public/menu-1112', '2022-11-01 18:30:00', 'Super Ad', '2022-11-03 13:19:42', 'User 1:Super Admin', NULL),
(9, 'Executive Menu-13', 'https://html522-tutorial.net/form-validation/validating-urls/', '2022-11-03 18:30:00', 'Super', '2022-11-01 18:30:00', 'User 1:Super Admin', NULL),
(10, 'Executive Menu-1', 'http://localhost/homelane-task/public/menu-112133', '2022-11-03 18:30:00', 'Super', '2022-11-04 17:17:04', 'User 1:Super Admin', NULL),
(11, 'Associate Menu-3', 'http://localhost/homelane-task/public/menu-37121', '2022-11-03 13:28:15', 'User 1:Super Admin', '2022-11-03 13:28:51', 'User 1:Super Admin', NULL),
(12, 'Associate Menu-1', 'https://github.com/RohitSharma0077/homelane-task', '2022-11-04 06:20:29', 'User 33:Admin', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(57, '2014_10_12_000000_create_users_table', 1),
(58, '2014_10_12_100000_create_password_resets_table', 1),
(59, '2019_08_19_000000_create_failed_jobs_table', 1),
(60, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(61, '2022_10_18_185232_products', 1),
(62, '2022_10_18_185248_categories', 1),
(63, '2022_11_03_083231_roles', 1),
(64, '2022_11_03_083342_menus', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_values` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Role values are comma separated values of Menu IDs',
  `created_on` timestamp NULL DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `modified_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `role_values`, `created_on`, `created_by`, `modified_on`, `modified_by`, `deleted_at`) VALUES
(1, 'Super Admin', '1,2,3', '2022-11-03 10:00:48', 'Super Admin', '2022-11-04 01:04:59', 'User 1:Super Admin', NULL),
(2, 'Admin', '7,8', '2022-11-03 10:00:48', 'Super Admin', '2022-11-04 06:30:41', 'User 33:Admin', NULL),
(3, 'Sub Admin', NULL, '2022-11-03 10:00:48', 'Super Admin', NULL, NULL, NULL),
(4, 'Executive', '9,10', '2022-11-03 10:00:48', 'Super Admin', '2022-11-04 01:00:45', 'User 1:Super Admin', NULL),
(5, 'Associate', '11, 12', '2022-11-03 10:00:48', 'Super Admin', NULL, NULL, NULL),
(6, 'Viewer', '1,8,2,3', '2022-11-04 01:01:35', 'User 1:Super Admin', NULL, NULL, '2022-11-03 17:12:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` bigint(20) UNSIGNED NOT NULL COMMENT 'Default Roles are SuperAdmin, Admin, SubAdmin,Executive, Associate',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_on` timestamp NULL DEFAULT NULL,
  `modified_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `role`, `email`, `email_verified_at`, `password`, `remember_token`, `created_on`, `created_by`, `modified_on`, `modified_by`, `deleted_at`) VALUES
(1, 'User', '1', 1, 'superadmin@gmail.com', NULL, '$2y$10$/w18cNrZV5VXc3WVfnIk5O185nfDw7LZxznyf1kwA2XfQA0XT/eY.', NULL, '2022-11-03 10:02:42', 'User 1-Super Admin', '2022-11-03 10:51:08', 'User 1-Super Admin', NULL),
(4, 'User', '33', 2, 'admin@gmail.com', NULL, '$2y$10$NTaGrvQOjSP2uk.kZXome.wb06D6IcGvTmGLdtgUz/U1GWARv7gDe', NULL, '2022-11-03 10:34:14', 'User 1-Super Admin', '2022-11-03 11:00:22', 'User 1-Super Admin', NULL),
(5, 'user', '44', 3, 'subadmin@gmail.com', NULL, '$2y$10$xV2hlX06wpjYExghhMRPX.p7sW2CKo3DyUXijLOlp8.VyrTu/g5gO', NULL, '2022-11-04 11:03:28', 'Super', NULL, NULL, NULL),
(6, 'Executive', '666', 4, 'executive@gmail.com', NULL, '$2y$10$8/iGtZyZCePfcUUGpgJ3S.6KQ.C0LOX.DjfDNke/Jbf5O1O2q/61G', NULL, '2022-11-03 18:30:00', 'Admin', '2022-11-08 14:53:22', 'User 1:Super Admin', NULL),
(7, 'User', '143', 5, 'associate@gmail.com', NULL, '$2y$10$tzuNsa163ATCawN9pTm02OQ4LW33usM2bVLFJ1gZuZdyDpmk/dR..', NULL, '2022-11-03 13:29:54', 'User 1:Super Admin', '2022-11-03 13:30:37', 'User 1:Super Admin', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menus_menu_name_unique` (`menu_name`),
  ADD UNIQUE KEY `menus_menu_url_unique` (`menu_URL`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
