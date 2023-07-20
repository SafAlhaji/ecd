-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 10, 2021 at 02:09 PM
-- Server version: 5.7.33-0ubuntu0.18.04.1
-- PHP Version: 7.2.34-18+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edc`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu`
--

CREATE TABLE `admin_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_menu`
--

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 'Dashboard', 'fa-bar-chart', '/', NULL, NULL, '2020-11-20 03:11:58'),
(2, 0, 14, 'Users Mangement', 'fa-tasks', NULL, NULL, NULL, '2021-04-07 00:50:40'),
(3, 2, 15, 'Users', 'fa-users', 'auth/users', NULL, NULL, '2021-04-07 00:50:40'),
(4, 2, 16, 'Roles', 'fa-user', 'auth/roles', NULL, NULL, '2021-04-07 00:50:40'),
(5, 2, 17, 'Permission', 'fa-ban', 'auth/permissions', NULL, NULL, '2021-04-07 00:50:40'),
(13, 28, 28, 'Countries', 'fa-flag', '/countries', NULL, '2020-10-19 15:09:29', '2021-04-07 00:50:40'),
(14, 28, 29, 'Service Provider', 'fa-flag-o', '/service_provider', NULL, '2020-10-19 15:41:06', '2021-04-07 00:50:40'),
(15, 28, 30, 'Branches', 'fa-braille', '/branches', NULL, '2020-10-19 15:43:54', '2021-04-07 00:50:40'),
(16, 30, 3, 'Requests', 'fa-registered', 'requests', NULL, '2020-10-20 21:44:56', '2021-03-17 21:37:04'),
(17, 28, 23, 'Service Mangement', 'fa-server', NULL, NULL, '2020-10-20 21:45:57', '2021-04-07 00:50:40'),
(18, 17, 26, 'Service Type', 'fa-dollar', 'service_type', NULL, '2020-10-20 21:47:02', '2021-04-07 00:50:40'),
(19, 17, 25, 'Emabassy  Services', 'fa-ils', 'embassy_service', NULL, '2020-10-20 21:47:59', '2021-04-07 00:50:40'),
(20, 28, 27, 'Professions', 'fa-product-hunt', 'professions', NULL, '2020-10-22 00:38:03', '2021-04-07 00:50:40'),
(21, 28, 22, 'Organization Details', 'fa-bank', 'organization_details', NULL, '2020-10-27 23:39:09', '2021-04-07 00:50:40'),
(22, 0, 9, 'Batch Requests', 'fa-compress', 'request_batches', NULL, '2020-10-29 04:29:41', '2021-04-07 00:50:40'),
(23, 0, 11, 'SMS', 'fa-comment', NULL, 'sms_messages', '2020-11-05 05:05:55', '2021-04-07 00:50:40'),
(24, 23, 12, 'setup messages', 'fa-bars', '/sms_messages', 'sms_messages', '2020-11-05 05:06:45', '2021-04-07 00:50:40'),
(25, 23, 13, 'setup sms gateway', 'fa-bars', '/sms_gateway', NULL, '2020-11-05 05:07:09', '2021-04-07 00:50:40'),
(26, 0, 10, 'Report', 'fa-bars', '/request_report', NULL, '2020-11-09 00:14:42', '2021-04-07 00:50:40'),
(27, 0, 18, 'Menu Mangement', 'fa-medium', '/auth/menu', NULL, '2020-11-20 01:46:05', '2021-04-07 00:50:40'),
(28, 0, 19, 'Settings', 'fa-wrench', NULL, '*', '2020-11-20 03:09:30', '2021-04-07 00:50:40'),
(29, 30, 4, 'RequestType', 'fa-bars', 'request_type', NULL, '2021-03-17 21:35:55', '2021-03-17 21:37:04'),
(30, 0, 2, 'Requests Mangement', 'fa-bars', NULL, NULL, '2021-03-17 21:36:35', '2021-03-17 21:37:04'),
(31, 17, 24, 'General Service', 'fa-bars', 'general_service', NULL, '2021-03-25 03:42:35', '2021-04-07 00:50:40'),
(32, 28, 21, 'Tax Type', 'fa-bars', 'tax_type', NULL, '2021-03-26 00:13:45', '2021-04-07 00:50:40'),
(33, 28, 20, 'Setup Invoice', 'fa-newspaper-o', 'setup_invoice', NULL, '2021-03-31 02:23:34', '2021-04-07 00:50:40'),
(34, 0, 5, 'Transactions', 'fa-money', 'transactions', NULL, '2021-04-02 23:13:40', '2021-04-02 23:14:33'),
(35, 0, 8, 'Customers', 'fa-users', 'customers', NULL, '2021-04-07 00:19:26', '2021-04-07 00:50:40'),
(36, 34, 6, 'Received Voucher', 'fa-bars', 'trans_recived', NULL, '2021-04-07 00:49:37', '2021-04-08 02:14:23'),
(37, 34, 7, 'Payment Voucher', 'fa-bars', 'trans_pay', NULL, '2021-04-07 00:50:20', '2021-04-08 01:29:49'),
(38, 34, 0, 'List Transactions', 'fa-bars', 'transactions', NULL, '2021-04-08 00:19:26', '2021-04-08 01:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `admin_operation_log`
--

CREATE TABLE `admin_operation_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES
(1, 'All permission', '*', '', '*', NULL, NULL),
(2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL),
(3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL),
(4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL),
(5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL),
(6, 'Admin helpers', 'ext.helpers', '', '/helpers/*', '2020-10-18 10:57:49', '2020-10-18 10:57:49'),
(11, 'Full_Request_Access', 'requests', '', '/requests*', '2020-11-22 17:36:21', '2020-11-22 19:04:31'),
(13, 'Requests Filters', 'requests_filter', 'GET', '/requests*', '2020-11-22 17:38:30', '2020-11-22 17:38:30'),
(14, 'Requests Create', 'requests_create', 'GET,POST', '/requests/create', '2020-11-22 17:40:18', '2020-11-22 17:40:18'),
(15, 'batch action', 'batch_action', 'POST', '/_handle_action_', '2020-11-22 17:47:35', '2020-11-22 17:47:35'),
(16, 'update_requests', 'update_requests', 'POST,PUT', '/requests/*', '2020-11-22 17:48:45', '2020-11-23 16:16:55'),
(17, 'edit_requests', 'edit_requests', 'GET', '/requests/*/edit', '2020-11-22 17:50:34', '2020-11-22 17:57:29'),
(18, 'Full request batches', 'request_batches', '', '/request_batches*', '2020-11-22 17:52:54', '2020-11-22 17:52:54'),
(19, 'request_batches_excel', 'request_batches_excel', '', '/request_batches/excel*', '2020-11-22 17:53:49', '2020-11-22 17:53:49'),
(20, 'request_batches_pdf', 'request_batches_pdf', '', '/request_batches/print*', '2020-11-22 17:54:32', '2020-11-22 17:54:32'),
(21, 'update_request_status', 'update_request_status', '', '/requests/request_status*', '2020-11-22 17:55:09', '2020-11-22 17:55:09'),
(22, 'view_batch', 'view_batch', 'GET', '/request_batches/*', '2020-11-22 17:56:09', '2020-11-22 17:57:20'),
(23, 'update_requests_batch', 'update_requests_batch', 'POST', '/request_batches/*', '2020-11-22 17:57:12', '2020-11-22 17:57:12'),
(24, 'request_batches_filter', 'request_batches_filter', 'GET', '/request_batches*', '2020-11-22 17:59:08', '2020-11-22 17:59:08'),
(25, 'request_report_full_access', 'request_report_full_access', '', '/request_report*', '2020-11-22 17:59:48', '2020-11-22 17:59:48'),
(26, 'request_report_filter', 'request_report_filter', 'GET', '/request_report*', '2020-11-22 18:00:13', '2020-11-22 18:00:13'),
(27, 'request_report_full_report_excel', 'request_report_full_report_excel', '', '/request_report/full_report_excel', '2020-11-22 18:02:50', '2020-11-22 18:02:50'),
(28, 'request_report_pdf', 'request_report_pdf', '', '/request_report/full_report', '2020-11-22 18:04:20', '2020-11-22 18:04:20'),
(29, 'sms_messages_full', 'sms_messages_full', '', '/sms_messages*', '2020-11-22 18:05:07', '2020-11-22 18:05:07'),
(30, 'sms_gateway_full', 'sms_gateway_full', '', '/sms_gateway*', '2020-11-22 18:05:31', '2020-11-22 18:05:31'),
(31, 'get_customer_data', 'get_customer_data', 'GET', '/get_customer*', '2020-11-25 09:52:35', '2020-11-25 10:13:02'),
(32, 'get_servicedetails_types', 'get_servicedetails_types', 'GET', '/get_servicedetails_types*', '2020-11-25 10:19:38', '2020-11-25 10:19:38'),
(33, 'get_servicedetails_professions', 'get_servicedetails_professions', 'GET', '/get_servicedetails_professions*', '2020-11-25 10:20:13', '2020-11-25 10:20:13'),
(34, 'get_service', 'get_service', 'GET', '/get_service*', '2020-11-25 10:20:39', '2020-11-25 10:20:39');

-- --------------------------------------------------------

--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'administrator', '2020-10-18 10:28:05', '2020-10-18 10:28:05'),
(2, 'data entry', 'user_data_entry', '2020-11-20 01:57:02', '2020-11-20 01:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `admin_role_menu`
--

CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role_menu`
--

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL),
(1, 13, NULL, NULL),
(1, 14, NULL, NULL),
(1, 15, NULL, NULL),
(1, 18, NULL, NULL),
(1, 19, NULL, NULL),
(1, 20, NULL, NULL),
(1, 21, NULL, NULL),
(1, 25, NULL, NULL),
(1, 17, NULL, NULL),
(1, 27, NULL, NULL),
(1, 28, NULL, NULL),
(1, 22, NULL, NULL),
(1, 16, NULL, NULL),
(2, 16, NULL, NULL),
(1, 26, NULL, NULL),
(2, 26, NULL, NULL),
(1, 29, NULL, NULL),
(1, 30, NULL, NULL),
(1, 31, NULL, NULL),
(1, 32, NULL, NULL),
(1, 33, NULL, NULL),
(1, 34, NULL, NULL),
(1, 35, NULL, NULL),
(1, 36, NULL, NULL),
(1, 37, NULL, NULL),
(1, 38, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_role_permissions`
--

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role_permissions`
--

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(2, 2, NULL, NULL),
(2, 3, NULL, NULL),
(2, 13, NULL, NULL),
(2, 14, NULL, NULL),
(2, 16, NULL, NULL),
(2, 26, NULL, NULL),
(2, 28, NULL, NULL),
(2, 21, NULL, NULL),
(2, 31, NULL, NULL),
(2, 32, NULL, NULL),
(2, 33, NULL, NULL),
(2, 34, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_role_users`
--

CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role_users`
--

INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`, `branch_id`, `phone_number`, `email`) VALUES
(1, 'admin', '$2y$10$eCcA5ywc9NpxEDSKrleLaeEyoKMEFFxsjkeCIeGcRKBp2pVPPEwPK', 'Administrator', NULL, 'kshImsL4yJS2C2FEMaWvkPNzzoFgOCLRzpF98sdsocJvQ7y5HHiEhRqJguzq', '2020-10-18 10:28:04', '2021-02-06 09:03:41', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users_branches`
--

CREATE TABLE `admin_users_branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_user_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_permissions`
--

CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch_status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `bank_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batch_status`
--

CREATE TABLE `batch_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_revenue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `snl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requests_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches_embessies`
--

CREATE TABLE `branches_embessies` (
  `id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `embassy_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name_code`, `title`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'AF', 'Afghanistan', NULL, NULL, NULL),
(2, 'AL', 'Albania', NULL, NULL, NULL),
(3, 'DZ', 'Algeria', NULL, NULL, NULL),
(4, 'AS', 'American Samoa', NULL, NULL, NULL),
(5, 'AD', 'Andorra', NULL, NULL, NULL),
(6, 'AO', 'Angola', NULL, NULL, NULL),
(7, 'AI', 'Anguilla', NULL, NULL, NULL),
(8, 'AQ', 'Antarctica', NULL, NULL, NULL),
(9, 'AG', 'Antigua And Barbuda', NULL, NULL, NULL),
(10, 'AR', 'Argentina', NULL, NULL, NULL),
(11, 'AM', 'Armenia', NULL, NULL, NULL),
(12, 'AW', 'Aruba', NULL, NULL, NULL),
(13, 'AU', 'Australia', NULL, NULL, NULL),
(14, 'AT', 'Austria', NULL, NULL, NULL),
(15, 'AZ', 'Azerbaijan', NULL, NULL, NULL),
(16, 'BS', 'Bahamas The', NULL, NULL, NULL),
(17, 'BH', 'Bahrain', NULL, NULL, NULL),
(18, 'BD', 'Bangladesh', NULL, NULL, NULL),
(19, 'BB', 'Barbados', NULL, NULL, NULL),
(20, 'BY', 'Belarus', NULL, NULL, NULL),
(21, 'BE', 'Belgium', NULL, NULL, NULL),
(22, 'BZ', 'Belize', NULL, NULL, NULL),
(23, 'BJ', 'Benin', NULL, NULL, NULL),
(24, 'BM', 'Bermuda', NULL, NULL, NULL),
(25, 'BT', 'Bhutan', NULL, NULL, NULL),
(26, 'BO', 'Bolivia', NULL, NULL, NULL),
(27, 'BA', 'Bosnia and Herzegovina', NULL, NULL, NULL),
(28, 'BW', 'Botswana', NULL, NULL, NULL),
(29, 'BV', 'Bouvet Island', NULL, NULL, NULL),
(30, 'BR', 'Brazil', NULL, NULL, NULL),
(31, 'IO', 'British Indian Ocean Territory', NULL, NULL, NULL),
(32, 'BN', 'Brunei', NULL, NULL, NULL),
(33, 'BG', 'Bulgaria', NULL, NULL, NULL),
(34, 'BF', 'Burkina Faso', NULL, NULL, NULL),
(35, 'BI', 'Burundi', NULL, NULL, NULL),
(36, 'KH', 'Cambodia', NULL, NULL, NULL),
(37, 'CM', 'Cameroon', NULL, NULL, NULL),
(38, 'CA', 'Canada', NULL, NULL, NULL),
(39, 'CV', 'Cape Verde', NULL, NULL, NULL),
(40, 'KY', 'Cayman Islands', NULL, NULL, NULL),
(41, 'CF', 'Central African Republic', NULL, NULL, NULL),
(42, 'TD', 'Chad', NULL, NULL, NULL),
(43, 'CL', 'Chile', NULL, NULL, NULL),
(44, 'CN', 'China', NULL, NULL, NULL),
(45, 'CX', 'Christmas Island', NULL, NULL, NULL),
(46, 'CC', 'Cocos (Keeling) Islands', NULL, NULL, NULL),
(47, 'CO', 'Colombia', NULL, NULL, NULL),
(48, 'KM', 'Comoros', NULL, NULL, NULL),
(49, 'CG', 'Republic Of The Congo', NULL, NULL, NULL),
(50, 'CD', 'Democratic Republic Of The Congo', NULL, NULL, NULL),
(51, 'CK', 'Cook Islands', NULL, NULL, NULL),
(52, 'CR', 'Costa Rica', NULL, NULL, NULL),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', NULL, NULL, NULL),
(54, 'HR', 'Croatia (Hrvatska)', NULL, NULL, NULL),
(55, 'CU', 'Cuba', NULL, NULL, NULL),
(56, 'CY', 'Cyprus', NULL, NULL, NULL),
(57, 'CZ', 'Czech Republic', NULL, NULL, NULL),
(58, 'DK', 'Denmark', NULL, NULL, NULL),
(59, 'DJ', 'Djibouti', NULL, NULL, NULL),
(60, 'DM', 'Dominica', NULL, NULL, NULL),
(61, 'DO', 'Dominican Republic', NULL, NULL, NULL),
(62, 'TP', 'East Timor', NULL, NULL, NULL),
(63, 'EC', 'Ecuador', NULL, NULL, NULL),
(64, 'EG', 'Egypt', NULL, NULL, NULL),
(65, 'SV', 'El Salvador', NULL, NULL, NULL),
(66, 'GQ', 'Equatorial Guinea', NULL, NULL, NULL),
(67, 'ER', 'Eritrea', NULL, NULL, NULL),
(68, 'EE', 'Estonia', NULL, NULL, NULL),
(69, 'ET', 'Ethiopia', NULL, NULL, NULL),
(70, 'XA', 'External Territories of Australia', NULL, NULL, NULL),
(71, 'FK', 'Falkland Islands', NULL, NULL, NULL),
(72, 'FO', 'Faroe Islands', NULL, NULL, NULL),
(73, 'FJ', 'Fiji Islands', NULL, NULL, NULL),
(74, 'FI', 'Finland', NULL, NULL, NULL),
(75, 'FR', 'France', NULL, NULL, NULL),
(76, 'GF', 'French Guiana', NULL, NULL, NULL),
(77, 'PF', 'French Polynesia', NULL, NULL, NULL),
(78, 'TF', 'French Southern Territories', NULL, NULL, NULL),
(79, 'GA', 'Gabon', NULL, NULL, NULL),
(80, 'GM', 'Gambia The', NULL, NULL, NULL),
(81, 'GE', 'Georgia', NULL, NULL, NULL),
(82, 'DE', 'Germany', NULL, NULL, NULL),
(83, 'GH', 'Ghana', NULL, NULL, NULL),
(84, 'GI', 'Gibraltar', NULL, NULL, NULL),
(85, 'GR', 'Greece', NULL, NULL, NULL),
(86, 'GL', 'Greenland', NULL, NULL, NULL),
(87, 'GD', 'Grenada', NULL, NULL, NULL),
(88, 'GP', 'Guadeloupe', NULL, NULL, NULL),
(89, 'GU', 'Guam', NULL, NULL, NULL),
(90, 'GT', 'Guatemala', NULL, NULL, NULL),
(91, 'XU', 'Guernsey and Alderney', NULL, NULL, NULL),
(92, 'GN', 'Guinea', NULL, NULL, NULL),
(93, 'GW', 'Guinea-Bissau', NULL, NULL, NULL),
(94, 'GY', 'Guyana', NULL, NULL, NULL),
(95, 'HT', 'Haiti', NULL, NULL, NULL),
(96, 'HM', 'Heard and McDonald Islands', NULL, NULL, NULL),
(97, 'HN', 'Honduras', NULL, NULL, NULL),
(98, 'HK', 'Hong Kong S.A.R.', NULL, NULL, NULL),
(99, 'HU', 'Hungary', NULL, NULL, NULL),
(100, 'IS', 'Iceland', NULL, NULL, NULL),
(101, 'IN', 'India', NULL, NULL, NULL),
(102, 'ID', 'Indonesia', NULL, NULL, NULL),
(103, 'IR', 'Iran', NULL, NULL, NULL),
(104, 'IQ', 'Iraq', NULL, NULL, NULL),
(105, 'IE', 'Ireland', NULL, NULL, NULL),
(106, 'IL', 'Israel', NULL, NULL, NULL),
(107, 'IT', 'Italy', NULL, NULL, NULL),
(108, 'JM', 'Jamaica', NULL, NULL, NULL),
(109, 'JP', 'Japan', NULL, NULL, NULL),
(110, 'XJ', 'Jersey', NULL, NULL, NULL),
(111, 'JO', 'Jordan', NULL, NULL, NULL),
(112, 'KZ', 'Kazakhstan', NULL, NULL, NULL),
(113, 'KE', 'Kenya', NULL, NULL, NULL),
(114, 'KI', 'Kiribati', NULL, NULL, NULL),
(115, 'KP', 'Korea North', NULL, NULL, NULL),
(116, 'KR', 'Korea South', NULL, NULL, NULL),
(117, 'KW', 'Kuwait', NULL, NULL, NULL),
(118, 'KG', 'Kyrgyzstan', NULL, NULL, NULL),
(119, 'LA', 'Laos', NULL, NULL, NULL),
(120, 'LV', 'Latvia', NULL, NULL, NULL),
(121, 'LB', 'Lebanon', NULL, NULL, NULL),
(122, 'LS', 'Lesotho', NULL, NULL, NULL),
(123, 'LR', 'Liberia', NULL, NULL, NULL),
(124, 'LY', 'Libya', NULL, NULL, NULL),
(125, 'LI', 'Liechtenstein', NULL, NULL, NULL),
(126, 'LT', 'Lithuania', NULL, NULL, NULL),
(127, 'LU', 'Luxembourg', NULL, NULL, NULL),
(128, 'MO', 'Macau S.A.R.', NULL, NULL, NULL),
(129, 'MK', 'Macedonia', NULL, NULL, NULL),
(130, 'MG', 'Madagascar', NULL, NULL, NULL),
(131, 'MW', 'Malawi', NULL, NULL, NULL),
(132, 'MY', 'Malaysia', NULL, NULL, NULL),
(133, 'MV', 'Maldives', NULL, NULL, NULL),
(134, 'ML', 'Mali', NULL, NULL, NULL),
(135, 'MT', 'Malta', NULL, NULL, NULL),
(136, 'XM', 'Man (Isle of)', NULL, NULL, NULL),
(137, 'MH', 'Marshall Islands', NULL, NULL, NULL),
(138, 'MQ', 'Martinique', NULL, NULL, NULL),
(139, 'MR', 'Mauritania', NULL, NULL, NULL),
(140, 'MU', 'Mauritius', NULL, NULL, NULL),
(141, 'YT', 'Mayotte', NULL, NULL, NULL),
(142, 'MX', 'Mexico', NULL, NULL, NULL),
(143, 'FM', 'Micronesia', NULL, NULL, NULL),
(144, 'MD', 'Moldova', NULL, NULL, NULL),
(145, 'MC', 'Monaco', NULL, NULL, NULL),
(146, 'MN', 'Mongolia', NULL, NULL, NULL),
(147, 'MS', 'Montserrat', NULL, NULL, NULL),
(148, 'MA', 'Morocco', NULL, NULL, NULL),
(149, 'MZ', 'Mozambique', NULL, NULL, NULL),
(150, 'MM', 'Myanmar', NULL, NULL, NULL),
(151, 'NA', 'Namibia', NULL, NULL, NULL),
(152, 'NR', 'Nauru', NULL, NULL, NULL),
(153, 'NP', 'Nepal', NULL, NULL, NULL),
(154, 'AN', 'Netherlands Antilles', NULL, NULL, NULL),
(155, 'NL', 'Netherlands The', NULL, NULL, NULL),
(156, 'NC', 'New Caledonia', NULL, NULL, NULL),
(157, 'NZ', 'New Zealand', NULL, NULL, NULL),
(158, 'NI', 'Nicaragua', NULL, NULL, NULL),
(159, 'NE', 'Niger', NULL, NULL, NULL),
(160, 'NG', 'Nigeria', NULL, NULL, NULL),
(161, 'NU', 'Niue', NULL, NULL, NULL),
(162, 'NF', 'Norfolk Island', NULL, NULL, NULL),
(163, 'MP', 'Northern Mariana Islands', NULL, NULL, NULL),
(164, 'NO', 'Norway', NULL, NULL, NULL),
(165, 'OM', 'Oman', NULL, NULL, NULL),
(166, 'PK', 'Pakistan', NULL, NULL, NULL),
(167, 'PW', 'Palau', NULL, NULL, NULL),
(168, 'PS', 'Palestinian Territory Occupied', NULL, NULL, NULL),
(169, 'PA', 'Panama', NULL, NULL, NULL),
(170, 'PG', 'Papua new Guinea', NULL, NULL, NULL),
(171, 'PY', 'Paraguay', NULL, NULL, NULL),
(172, 'PE', 'Peru', NULL, NULL, NULL),
(173, 'PH', 'Philippines', NULL, NULL, NULL),
(174, 'PN', 'Pitcairn Island', NULL, NULL, NULL),
(175, 'PL', 'Poland', NULL, NULL, NULL),
(176, 'PT', 'Portugal', NULL, NULL, NULL),
(177, 'PR', 'Puerto Rico', NULL, NULL, NULL),
(178, 'QA', 'Qatar', NULL, NULL, NULL),
(179, 'RE', 'Reunion', NULL, NULL, NULL),
(180, 'RO', 'Romania', NULL, NULL, NULL),
(181, 'RU', 'Russia', NULL, NULL, NULL),
(182, 'RW', 'Rwanda', NULL, NULL, NULL),
(183, 'SH', 'Saint Helena', NULL, NULL, NULL),
(184, 'KN', 'Saint Kitts And Nevis', NULL, NULL, NULL),
(185, 'LC', 'Saint Lucia', NULL, NULL, NULL),
(186, 'PM', 'Saint Pierre and Miquelon', NULL, NULL, NULL),
(187, 'VC', 'Saint Vincent And The Grenadines', NULL, NULL, NULL),
(188, 'WS', 'Samoa', NULL, NULL, NULL),
(189, 'SM', 'San Marino', NULL, NULL, NULL),
(190, 'ST', 'Sao Tome and Principe', NULL, NULL, NULL),
(191, 'SA', 'Saudi Arabia', NULL, NULL, NULL),
(192, 'SN', 'Senegal', NULL, NULL, NULL),
(193, 'RS', 'Serbia', NULL, NULL, NULL),
(194, 'SC', 'Seychelles', NULL, NULL, NULL),
(195, 'SL', 'Sierra Leone', NULL, NULL, NULL),
(196, 'SG', 'Singapore', NULL, NULL, NULL),
(197, 'SK', 'Slovakia', NULL, NULL, NULL),
(198, 'SI', 'Slovenia', NULL, NULL, NULL),
(199, 'XG', 'Smaller Territories of the UK', NULL, NULL, NULL),
(200, 'SB', 'Solomon Islands', NULL, NULL, NULL),
(201, 'SO', 'Somalia', NULL, NULL, NULL),
(202, 'ZA', 'South Africa', NULL, NULL, NULL),
(203, 'GS', 'South Georgia', NULL, NULL, NULL),
(204, 'SS', 'South Sudan', NULL, NULL, NULL),
(205, 'ES', 'Spain', NULL, NULL, NULL),
(206, 'LK', 'Sri Lanka', NULL, NULL, NULL),
(207, 'SD', 'Sudan', NULL, NULL, NULL),
(208, 'SR', 'Suriname', NULL, NULL, NULL),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', NULL, NULL, NULL),
(210, 'SZ', 'Swaziland', NULL, NULL, NULL),
(211, 'SE', 'Sweden', NULL, NULL, NULL),
(212, 'CH', 'Switzerland', NULL, NULL, NULL),
(213, 'SY', 'Syria', NULL, NULL, NULL),
(214, 'TW', 'Taiwan', NULL, NULL, NULL),
(215, 'TJ', 'Tajikistan', NULL, NULL, NULL),
(216, 'TZ', 'Tanzania', NULL, NULL, NULL),
(217, 'TH', 'Thailand', NULL, NULL, NULL),
(218, 'TG', 'Togo', NULL, NULL, NULL),
(219, 'TK', 'Tokelau', NULL, NULL, NULL),
(220, 'TO', 'Tonga', NULL, NULL, NULL),
(221, 'TT', 'Trinidad And Tobago', NULL, NULL, NULL),
(222, 'TN', 'Tunisia', NULL, NULL, NULL),
(223, 'TR', 'Turkey', NULL, NULL, NULL),
(224, 'TM', 'Turkmenistan', NULL, NULL, NULL),
(225, 'TC', 'Turks And Caicos Islands', NULL, NULL, NULL),
(226, 'TV', 'Tuvalu', NULL, NULL, NULL),
(227, 'UG', 'Uganda', NULL, NULL, NULL),
(228, 'UA', 'Ukraine', NULL, NULL, NULL),
(229, 'AE', 'United Arab Emirates', NULL, NULL, NULL),
(230, 'GB', 'United Kingdom', NULL, NULL, NULL),
(231, 'US', 'United States', NULL, NULL, NULL),
(232, 'UM', 'United States Minor Outlying Islands', NULL, NULL, NULL),
(233, 'UY', 'Uruguay', NULL, NULL, NULL),
(234, 'UZ', 'Uzbekistan', NULL, NULL, NULL),
(235, 'VU', 'Vanuatu', NULL, NULL, NULL),
(236, 'VA', 'Vatican City State (Holy See)', NULL, NULL, NULL),
(237, 'VE', 'Venezuela', NULL, NULL, NULL),
(238, 'VN', 'Vietnam', NULL, NULL, NULL),
(239, 'VG', 'Virgin Islands (British)', NULL, NULL, NULL),
(240, 'VI', 'Virgin Islands (US)', NULL, NULL, NULL),
(241, 'WF', 'Wallis And Futuna Islands', NULL, NULL, NULL),
(242, 'EH', 'Western Sahara', NULL, NULL, NULL),
(243, 'YE', 'Yemen', NULL, NULL, NULL),
(244, 'YU', 'Yugoslavia', NULL, NULL, NULL),
(245, 'ZM', 'Zambia', NULL, NULL, NULL),
(246, 'ZW', 'Zimbabwe', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valid_passport_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `alt_phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profession_id` int(11) DEFAULT NULL,
  `snl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creidt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `embessies`
--

CREATE TABLE `embessies` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `snl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_setup`
--

CREATE TABLE `invoice_setup` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_setup`
--

INSERT INTO `invoice_setup` (`id`, `title`, `currency`, `created_at`, `updated_at`) VALUES
(1, '<blockquote>\r\n<p><span style=\"font-size:14px\"><strong>MAC Agency for Services, Travel and Tourism</strong></span></p>\r\n</blockquote>', 'جنيه', '2021-03-31 02:05:09', '2021-04-08 11:37:25');

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_01_04_173148_create_admin_tables', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2020_10_18_125948_edit_admin_users_table', 2),
(6, '2020_10_18_134302_create_branches_table', 3),
(7, '2020_10_18_134416_create_service_type_table', 4),
(8, '2020_10_18_134740_create_service_table', 5),
(9, '2020_10_18_135445_create_customer_table', 6),
(10, '2020_10_18_135626_create_embessies_table', 7),
(11, '2020_10_18_135753_create_countries_table', 8),
(12, '2020_10_18_140047_create_batch_status_table', 9),
(13, '2020_10_18_140127_create_request_status_table', 10),
(14, '2020_10_18_145712_create_batch_table', 11),
(15, '2020_10_18_150722_create_requests_table', 12),
(16, '2020_10_21_140322_create_profession_table', 13),
(17, '2020_10_21_140431_changes_in_customer_table', 13),
(18, '2020_10_21_150336_changes_in_service_table', 13),
(19, '2020_10_21_161453_rename_columns_in_service_table', 13),
(20, '2020_10_21_165639_create_branches_embessies_table', 14),
(21, '2020_10_21_212515_drop_some_columns_in_service_table', 15),
(22, '2020_10_21_212749_create_service_details_table', 15),
(23, '2020_10_22_162050_add_sln_to_many_tables', 16),
(24, '2020_10_22_164424_add_sln_to_customer_tables', 16),
(25, '2020_10_24_080342_drop_some_columns', 17),
(26, '2020_10_24_080848_drop_amount_service_amount_type', 17),
(27, '2020_10_24_094044_add_notes_to_requests_table', 18),
(28, '2020_10_25_211329_add_delivery_datetime_to_requests_table', 19),
(29, '2020_10_26_033004_add_columns_to_requests_table', 19),
(30, '2020_10_26_033647_add_charges_to_requests_table', 19),
(31, '2020_10_27_220440_add_request_datetime_to_requests_table', 20),
(32, '2020_10_27_224109_create_organization_details_table', 20),
(33, '2020_10_27_232748_add_activity_to_organization_details_table', 20),
(34, '2020_10_29_034629_add_embassy_serial_number_to_batch_table', 21),
(35, '2020_10_29_215955_add_embassy_serial_number_to_requests_table', 22),
(36, '2020_10_29_235150_changes_batch_table', 22),
(37, '2020_10_30_004429_add_months_config_organization_details', 22),
(38, '2020_10_31_233038_create_admin_users_branches_table', 23),
(39, '2020_11_01_011257_add_request_code_to_branches_table', 24),
(40, '2020_11_03_234354_add_renew_note_to_requests_table', 25),
(41, '2020_11_04_215232_add_batch_date_to_batch_table', 26),
(42, '2020_11_05_004221_create_sms_message_table', 26),
(43, '2020_11_05_011127_create_sms_gateway_table', 26),
(44, '2020_11_05_023334_add_secret_key_to_sms_gateway_table', 26),
(45, '2020_11_10_044812_add_sender_name', 27),
(46, '2020_11_21_132613_add_branch_id_to_batch_table', 28),
(47, '2021_03_17_011252_create_requests_types_table', 29),
(48, '2021_03_17_011637_create_transactions_history_table', 30),
(49, '2021_03_17_011806_create_payment_types_table', 31),
(50, '2021_03_17_011854_create_tax_history_table', 32),
(51, '2021_03_17_012438_create_tax_types_table', 33),
(53, '2021_03_31_040100_create_invoice_setup_table', 34);

-- --------------------------------------------------------

--
-- Table structure for table `organization_details`
--

CREATE TABLE `organization_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `activity_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month_config` int(11) DEFAULT NULL,
  `app_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organization_details`
--

INSERT INTO `organization_details` (`id`, `title`, `title_ar`, `phone_numbers`, `tax_number`, `email`, `logo_1`, `logo_2`, `url`, `address`, `created_at`, `updated_at`, `deleted_at`, `activity_title`, `activity_title_ar`, `month_config`, `app_name`) VALUES
(1, 'MAC Agency for Services, Travel and Tourism', 'وكالة ماك للخدمات والسفر والسياحة', '[\"0536725842\"]', '581', 'info@mac.com', 'images/8cc593a7bf1cdf19b1fd706746c717af.png', 'images/25a433633db146bf6fc6f08896a13f3a.png', 'https://www.mac.com', 'Sudan Khartoum', '2020-10-29 03:36:42', '2021-04-10 01:50:07', NULL, 'For general services, travel and tourism services', 'للخدمات العامة وخدمات السفر والسياحة', 6, 'Embassy Service');

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
-- Table structure for table `payment_types`
--

CREATE TABLE `payment_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` int(11) DEFAULT NULL COMMENT '0= in & 1 = out',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profession`
--

CREATE TABLE `profession` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `snl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `staff_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_status_id` int(11) DEFAULT NULL,
  `embassy_id` int(11) DEFAULT NULL,
  `qr_string` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `snl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `delivery_date_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type_id` int(11) DEFAULT NULL,
  `profession_id` int(11) DEFAULT NULL,
  `service_charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `embassy_charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_created_at` date DEFAULT NULL,
  `embassy_serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `renew_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_type_id` int(11) DEFAULT NULL,
  `payment_type_id` int(11) DEFAULT NULL,
  `payment_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status_id` int(11) DEFAULT '0' COMMENT '0 not paid 1 paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests_types`
--

CREATE TABLE `requests_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requests_types`
--

INSERT INTO `requests_types` (`id`, `title`, `created_at`, `updated_at`) VALUES
(1, 'خدمات سفارات', '2021-03-17 21:33:50', '2021-03-17 21:33:50'),
(2, 'خدمات عامه', '2021-03-17 21:33:58', '2021-03-17 21:33:58');

-- --------------------------------------------------------

--
-- Table structure for table `request_status`
--

CREATE TABLE `request_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `snl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_details`
--

CREATE TABLE `service_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `profession_id` int(11) DEFAULT NULL,
  `embassy_charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `snl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type_id` int(11) DEFAULT NULL,
  `amount_service_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_type_id` int(11) DEFAULT NULL,
  `is_tax_include` int(11) NOT NULL DEFAULT '0' COMMENT '0 = not include ,1 = include'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_type`
--

CREATE TABLE `service_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_type`
--

INSERT INTO `service_type` (`id`, `title`, `title_ar`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Out side', 'Out side', '2021-03-26 00:09:53', '2021-03-26 00:09:53', NULL),
(2, 'In side', 'In side', '2021-03-26 00:10:26', '2021-03-26 00:10:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sms_gateway`
--

CREATE TABLE `sms_gateway` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_parameter_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_parameter_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_parameter_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `secret_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_message`
--

CREATE TABLE `sms_message` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_other_lang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_history`
--

CREATE TABLE `tax_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_history_id` int(11) DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_types`
--

CREATE TABLE `tax_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'amount in percentage',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_types`
--

INSERT INTO `tax_types` (`id`, `title`, `amount`, `created_at`, `updated_at`) VALUES
(1, 'VAT15%', '15', '2021-03-25 23:53:35', '2021-03-25 23:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `transactions_history`
--

CREATE TABLE `transactions_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type_id` int(11) DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_type` int(11) DEFAULT NULL,
  `snl` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_string` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_operation_log_user_id_index` (`user_id`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_permissions_name_unique` (`name`),
  ADD UNIQUE KEY `admin_permissions_slug_unique` (`slug`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_roles_name_unique` (`name`),
  ADD UNIQUE KEY `admin_roles_slug_unique` (`slug`);

--
-- Indexes for table `admin_role_menu`
--
ALTER TABLE `admin_role_menu`
  ADD KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`);

--
-- Indexes for table `admin_role_permissions`
--
ALTER TABLE `admin_role_permissions`
  ADD KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`);

--
-- Indexes for table `admin_role_users`
--
ALTER TABLE `admin_role_users`
  ADD KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_username_unique` (`username`);

--
-- Indexes for table `admin_users_branches`
--
ALTER TABLE `admin_users_branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  ADD KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`);

--
-- Indexes for table `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `batch_status`
--
ALTER TABLE `batch_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches_embessies`
--
ALTER TABLE `branches_embessies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `embessies`
--
ALTER TABLE `embessies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_setup`
--
ALTER TABLE `invoice_setup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organization_details`
--
ALTER TABLE `organization_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profession`
--
ALTER TABLE `profession`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests_types`
--
ALTER TABLE `requests_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `request_status`
--
ALTER TABLE `request_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_details`
--
ALTER TABLE `service_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_type`
--
ALTER TABLE `service_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_gateway`
--
ALTER TABLE `sms_gateway`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_message`
--
ALTER TABLE `sms_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_history`
--
ALTER TABLE `tax_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_types`
--
ALTER TABLE `tax_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions_history`
--
ALTER TABLE `transactions_history`
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
-- AUTO_INCREMENT for table `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `admin_users_branches`
--
ALTER TABLE `admin_users_branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `batch`
--
ALTER TABLE `batch`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `batch_status`
--
ALTER TABLE `batch_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `branches_embessies`
--
ALTER TABLE `branches_embessies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;
--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `embessies`
--
ALTER TABLE `embessies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `invoice_setup`
--
ALTER TABLE `invoice_setup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT for table `organization_details`
--
ALTER TABLE `organization_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profession`
--
ALTER TABLE `profession`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `requests_types`
--
ALTER TABLE `requests_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `request_status`
--
ALTER TABLE `request_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_details`
--
ALTER TABLE `service_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_type`
--
ALTER TABLE `service_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `sms_gateway`
--
ALTER TABLE `sms_gateway`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sms_message`
--
ALTER TABLE `sms_message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tax_history`
--
ALTER TABLE `tax_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tax_types`
--
ALTER TABLE `tax_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `transactions_history`
--
ALTER TABLE `transactions_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
