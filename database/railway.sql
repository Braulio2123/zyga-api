-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 04:48 PM
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
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `assistance_requests`
--

CREATE TABLE `assistance_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(26) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `pickup_address` varchar(255) DEFAULT NULL,
  `status` enum('created','assigned','in_progress','completed','cancelled') NOT NULL DEFAULT 'created',
  `cancel_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pickup_reference` varchar(255) DEFAULT NULL,
  `quoted_amount` decimal(10,2) DEFAULT NULL,
  `final_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `pricing_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pricing_breakdown`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assistance_requests`
--

INSERT INTO `assistance_requests` (`id`, `public_id`, `user_id`, `provider_id`, `service_id`, `vehicle_id`, `lat`, `lng`, `pickup_address`, `status`, `cancel_reason`, `created_at`, `updated_at`, `pickup_reference`, `quoted_amount`, `final_amount`, `payment_status`, `payment_method`, `pricing_breakdown`) VALUES
(1, 'ZYGAREQAVAILABLE0000000001', 1, NULL, 1, 1, 20.67360000, -103.34400000, 'Av. Juárez 123, Guadalajara, Jalisco', 'created', NULL, '2026-04-16 20:41:35', '2026-04-16 20:41:35', NULL, NULL, NULL, 'pending', NULL, NULL),
(2, 'ZYGAREQCOMPLETED0000000001', 1, 1, 2, 1, 20.67000000, -103.35000000, 'Av. México 2500, Guadalajara, Jalisco', 'completed', NULL, '2026-04-16 20:41:35', '2026-04-16 20:41:35', NULL, NULL, NULL, 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consent_types`
--

CREATE TABLE `consent_types` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `legal_documents`
--

CREATE TABLE `legal_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `consent_type_id` smallint(5) UNSIGNED NOT NULL,
  `version` varchar(50) NOT NULL,
  `published_at` datetime(3) NOT NULL,
  `content_hash` char(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_03_07_170000_create_users_table', 1),
(2, '2026_03_07_171916_create_status_domains_table', 1),
(3, '2026_03_07_171932_create_role_types_table', 1),
(4, '2026_03_07_172110_create_status_types_table', 1),
(5, '2026_03_07_172132_create_consent_types_table', 1),
(6, '2026_03_07_172145_create_legal_documents_table', 1),
(7, '2026_03_07_172259_create_services_table', 1),
(8, '2026_03_07_172318_create_vehicle_types_table', 1),
(9, '2026_03_07_172410_create_providers_table', 1),
(10, '2026_03_07_172424_create_provider_services_table', 1),
(11, '2026_03_07_172438_create_provider_schedules_table', 1),
(12, '2026_03_07_172930_create_vehicles_table', 1),
(13, '2026_03_07_172947_create_assistance_requests_table', 1),
(14, '2026_03_07_172959_create_service_requests_table', 1),
(15, '2026_03_07_173004_create_request_events_table', 1),
(16, '2026_03_07_173030_create_request_history_table', 1),
(17, '2026_03_07_173934_create_payments_table', 1),
(18, '2026_03_07_173946_create_payment_transactions_table', 1),
(19, '2026_03_07_173957_create_payment_method_types_table', 1),
(20, '2026_03_07_174625_create_user_roles_table', 1),
(21, '2026_03_07_174640_create_audit_logs_table', 1),
(22, '2026_03_07_174653_create_notifications_table', 1),
(23, '2026_03_07_174744_create_vehicle_documents_table', 1),
(24, '2026_03_07_174757_create_provider_documents_table', 1),
(25, '2026_03_07_180735_create_service_history_table', 1),
(26, '2026_03_07_180752_create_vehicles_services_table', 1),
(27, '2026_03_07_180804_create_service_feedbacks_table', 1),
(28, '2026_03_07_180916_create_user_addresses_table', 1),
(29, '2026_03_07_180930_create_transactions_table', 1),
(30, '2026_03_07_180940_create_transaction_details_table', 1),
(31, '2026_03_07_181054_create_service_images_table', 1),
(32, '2026_03_07_181106_create_provider_reviews_table', 1),
(33, '2026_03_07_181120_create_payment_methods_table', 1),
(34, '2026_03_07_181205_create_service_requests_feedback_table', 1),
(35, '2026_03_07_181219_create_transaction_logs_table', 1),
(36, '2026_03_07_181229_create_user_settings_table', 1),
(37, '2026_03_07_181347_create_user_activity_logs_table', 1),
(38, '2026_03_07_181358_create_user_sessions_table', 1),
(39, '2026_03_07_181412_create_password_resets_table', 1),
(40, '2026_03_07_181457_create_notifications_history_table', 1),
(41, '2026_03_07_181508_create_user_activities_table', 1),
(42, '2026_03_07_181520_create_user_permissions_table', 1),
(43, '2026_03_07_181557_create_user_notifications_table', 1),
(44, '2026_03_07_181606_create_transaction_types_table', 1),
(45, '2026_03_07_181614_create_subscription_plans_table', 1),
(46, '2026_03_07_181615_create_user_subscription_plans_table', 1),
(47, '2026_03_10_025130_create_personal_access_tokens_table', 1),
(48, '2026_04_08_000001_add_cancel_reason_to_assistance_requests_table', 1),
(49, '2026_04_15_090452_create_provider_locations_table', 1),
(50, '2026_04_16_060413_add_pickup_reference_to_assistance_requests_table', 1),
(51, '2026_04_16_095428_create_service_vehicle_rates_table', 1),
(52, '2026_04_16_095429_add_pricing_fields_to_assistance_requests_table', 1),
(53, '2026_04_16_101753_add_review_fields_to_payments_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'assistance_request', 'Tu solicitud de asistencia fue registrada correctamente.', 0, '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(2, 1, 'payment', 'Tu pago fue registrado correctamente.', 0, '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `notifications_history`
--

CREATE TABLE `notifications_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assistance_request_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `reference` varchar(120) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `validated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `assistance_request_id`, `amount`, `payment_method`, `reference`, `notes`, `transaction_id`, `status`, `validated_by`, `validated_at`, `created_at`, `updated_at`) VALUES
(1, 2, 850.00, 'card', NULL, NULL, 'TXN-ZYGA-DEMO-0001', 'completed', NULL, NULL, '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `method_details` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method_types`
--

CREATE TABLE `payment_method_types` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_method_types`
--

INSERT INTO `payment_method_types` (`id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'cash', 'Efectivo', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(2, 'transfer', 'Transferencia', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(3, 'card', 'Tarjeta', 0, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(4, 'digital', 'Digital', 0, '2026-04-16 20:41:33', '2026-04-16 20:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `gateway` varchar(255) NOT NULL,
  `gateway_event_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `provider_kind` varchar(100) DEFAULT NULL,
  `status_id` int(10) UNSIGNED NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `user_id`, `display_name`, `provider_kind`, `status_id`, `is_verified`, `created_at`, `updated_at`) VALUES
(1, 2, 'Grúas Express GDL', 'grua', 1, 1, '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `provider_documents`
--

CREATE TABLE `provider_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `document_url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_documents`
--

INSERT INTO `provider_documents` (`id`, `provider_id`, `document_type`, `document_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'licencia', 'https://example.com/documentos/licencia-gdl.pdf', '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `provider_locations`
--

CREATE TABLE `provider_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `assistance_request_id` bigint(20) UNSIGNED NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `accuracy` decimal(8,2) DEFAULT NULL,
  `heading` decimal(8,2) DEFAULT NULL,
  `speed` decimal(8,2) DEFAULT NULL,
  `recorded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider_reviews`
--

CREATE TABLE `provider_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider_schedules`
--

CREATE TABLE `provider_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(3) UNSIGNED NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'America/Mexico_City',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_schedules`
--

INSERT INTO `provider_schedules` (`id`, `provider_id`, `day_of_week`, `start_time`, `end_time`, `timezone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '08:00:00', '18:00:00', 'America/Mexico_City', 1, '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `provider_services`
--

CREATE TABLE `provider_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_services`
--

INSERT INTO `provider_services` (`id`, `provider_id`, `service_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(2, 1, 1, '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(3, 1, 2, '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `request_events`
--

CREATE TABLE `request_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `event_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_events`
--

INSERT INTO `request_events` (`id`, `request_id`, `event_type`, `event_data`, `created_at`, `updated_at`) VALUES
(1, 1, 'request_created', '{\"source\":\"seeder\",\"status\":\"created\"}', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(2, 2, 'status_created', '{\"source\":\"seeder\",\"status\":\"created\",\"step\":1}', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(3, 2, 'status_assigned', '{\"source\":\"seeder\",\"status\":\"assigned\",\"step\":2}', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(4, 2, 'status_in_progress', '{\"source\":\"seeder\",\"status\":\"in_progress\",\"step\":3}', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(5, 2, 'status_completed', '{\"source\":\"seeder\",\"status\":\"completed\",\"step\":4}', '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `request_history`
--

CREATE TABLE `request_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_history`
--

INSERT INTO `request_history` (`id`, `request_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'created', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(2, 2, 'created', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(3, 2, 'assigned', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(4, 2, 'in_progress', '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(5, 2, 'completed', '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_types`
--

CREATE TABLE `role_types` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_types`
--

INSERT INTO `role_types` (`id`, `code`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'client', 'Cliente', 'Usuario que solicita servicios de asistencia vial.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(2, 'provider', 'Proveedor', 'Usuario proveedor que atiende solicitudes de asistencia.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(3, 'admin', 'Administrador', 'Usuario administrativo con acceso al panel de gestión.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `code`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'grua', 'Grúa', 'Servicio de arrastre o traslado del vehículo.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(2, 'paso_corriente', 'Paso de corriente', 'Asistencia por batería descargada.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(3, 'cambio_llanta', 'Cambio de llanta', 'Apoyo para reemplazar una llanta ponchada.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(4, 'envio_gasolina', 'Envío de gasolina', 'Suministro básico de combustible por emergencia.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(5, 'cerrajeria', 'Cerrajería vehicular', 'Apoyo en caso de llaves olvidadas dentro del vehículo o bloqueo.', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `service_feedbacks`
--

CREATE TABLE `service_feedbacks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_request_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_history`
--

CREATE TABLE `service_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_request_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_images`
--

CREATE TABLE `service_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assistance_request_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_requests_feedback`
--

CREATE TABLE `service_requests_feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_request_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_vehicle_rates`
--

CREATE TABLE `service_vehicle_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type_id` smallint(5) UNSIGNED NOT NULL,
  `base_amount` decimal(10,2) NOT NULL,
  `night_surcharge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `weekend_surcharge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_vehicle_rates`
--

INSERT INTO `service_vehicle_rates` (`id`, `service_id`, `vehicle_type_id`, `base_amount`, `night_surcharge`, `weekend_surcharge`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 900.00, 180.00, 120.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(2, 1, 2, 650.00, 130.00, 90.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(3, 1, 3, 1100.00, 220.00, 150.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(4, 1, 4, 1200.00, 240.00, 160.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(5, 1, 5, 1050.00, 210.00, 140.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(6, 2, 1, 280.00, 60.00, 40.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(7, 2, 2, 220.00, 50.00, 30.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(8, 2, 3, 320.00, 70.00, 50.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(9, 2, 4, 340.00, 75.00, 55.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(10, 2, 5, 330.00, 70.00, 50.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(11, 3, 1, 320.00, 70.00, 50.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(12, 3, 2, 260.00, 55.00, 35.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(13, 3, 3, 380.00, 80.00, 60.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(14, 3, 4, 400.00, 85.00, 65.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(15, 3, 5, 390.00, 80.00, 60.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(16, 4, 1, 260.00, 50.00, 35.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(17, 4, 2, 220.00, 45.00, 30.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(18, 4, 3, 300.00, 60.00, 45.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(19, 4, 4, 320.00, 65.00, 50.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(20, 4, 5, 310.00, 60.00, 45.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(21, 5, 1, 350.00, 80.00, 55.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(22, 5, 2, 300.00, 65.00, 45.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(23, 5, 3, 420.00, 90.00, 65.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(24, 5, 4, 440.00, 95.00, 70.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(25, 5, 5, 430.00, 90.00, 65.00, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `status_domains`
--

CREATE TABLE `status_domains` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status_domains`
--

INSERT INTO `status_domains` (`id`, `code`, `name`, `created_at`, `updated_at`) VALUES
(1, 'provider', 'Estatus de proveedor', '2026-04-16 20:41:33', '2026-04-16 20:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `status_types`
--

CREATE TABLE `status_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain_id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_terminal` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status_types`
--

INSERT INTO `status_types` (`id`, `domain_id`, `code`, `name`, `description`, `is_terminal`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'active', 'Activo', 'Proveedor activo y disponible para operar.', 0, 1, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(2, 1, 'pending', 'Pendiente', 'Proveedor pendiente de validación o configuración.', 0, 2, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(3, 1, 'suspended', 'Suspendido', 'Proveedor suspendido temporalmente.', 0, 3, 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_logs`
--

CREATE TABLE `transaction_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_types`
--

CREATE TABLE `transaction_types` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'client@zyga.com', '$2y$12$e/mrMIbrx.MQC6G2sILpLuP1VoCW7e7vjvPafx6yeOhGbzwWstVzC', '2026-04-16 20:41:34', '2026-04-16 20:41:34'),
(2, 'provider@zyga.com', '$2y$12$37p85hxt/P651VHMd9ZL4.IlMufZBZMSdQrY.ew32b2xOjc9wY9Ue', '2026-04-16 20:41:34', '2026-04-16 20:41:34'),
(3, 'admin@zyga.com', '$2y$12$K9eNFE.heOeZM5OYrmqPHOgl4.1ul8SXIp0LfB/VvSecsuhEAzbyu', '2026-04-16 20:41:34', '2026-04-16 20:41:34');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `zip_code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `address`, `city`, `state`, `country`, `zip_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'Av. Vallarta 1450', 'Guadalajara', 'Jalisco', 'México', '44100', '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` smallint(5) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-16 20:41:34', '2026-04-16 20:41:34'),
(2, 2, 2, '2026-04-16 20:41:35', '2026-04-16 20:41:35'),
(3, 3, 3, '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_subscription_plans`
--

CREATE TABLE `user_subscription_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_plan_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type_id` smallint(5) UNSIGNED NOT NULL,
  `plate` varchar(16) NOT NULL,
  `brand` varchar(60) NOT NULL,
  `model` varchar(60) NOT NULL,
  `year` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `user_id`, `vehicle_type_id`, `plate`, `brand`, `model`, `year`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'JAL457B', 'Nissan', 'Versa', 2020, '2026-04-16 20:41:35', '2026-04-16 20:41:35');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles_services`
--

CREATE TABLE `vehicles_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_documents`
--

CREATE TABLE `vehicle_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `document_url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

CREATE TABLE `vehicle_types` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_types`
--

INSERT INTO `vehicle_types` (`id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'auto', 'Automóvil', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(2, 'moto', 'Motocicleta', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(3, 'camioneta', 'Camioneta', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(4, 'pickup', 'Pickup', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33'),
(5, 'suv', 'SUV', 1, '2026-04-16 20:41:33', '2026-04-16 20:41:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assistance_requests`
--
ALTER TABLE `assistance_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assistance_requests_public_id_unique` (`public_id`),
  ADD KEY `assistance_requests_user_id_foreign` (`user_id`),
  ADD KEY `assistance_requests_provider_id_foreign` (`provider_id`),
  ADD KEY `assistance_requests_service_id_foreign` (`service_id`),
  ADD KEY `assistance_requests_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `consent_types`
--
ALTER TABLE `consent_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consent_types_code_unique` (`code`);

--
-- Indexes for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `legal_documents_consent_type_id_version_unique` (`consent_type_id`,`version`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `notifications_history`
--
ALTER TABLE `notifications_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_history_notification_id_foreign` (`notification_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_assistance_request_id_foreign` (`assistance_request_id`),
  ADD KEY `payments_validated_by_foreign` (`validated_by`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_methods_user_id_foreign` (`user_id`);

--
-- Indexes for table `payment_method_types`
--
ALTER TABLE `payment_method_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_method_types_code_unique` (`code`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_transactions_gateway_event_id_unique` (`gateway_event_id`),
  ADD KEY `payment_transactions_payment_id_foreign` (`payment_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `providers_user_id_foreign` (`user_id`),
  ADD KEY `providers_status_id_foreign` (`status_id`);

--
-- Indexes for table `provider_documents`
--
ALTER TABLE `provider_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_documents_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `provider_locations`
--
ALTER TABLE `provider_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_locations_provider_request_index` (`provider_id`,`assistance_request_id`),
  ADD KEY `provider_locations_request_recorded_index` (`assistance_request_id`,`recorded_at`);

--
-- Indexes for table `provider_reviews`
--
ALTER TABLE `provider_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_reviews_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `provider_schedules`
--
ALTER TABLE `provider_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_schedules_provider_id_day_of_week_unique` (`provider_id`,`day_of_week`);

--
-- Indexes for table `provider_services`
--
ALTER TABLE `provider_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_services_provider_id_foreign` (`provider_id`),
  ADD KEY `provider_services_service_id_foreign` (`service_id`);

--
-- Indexes for table `request_events`
--
ALTER TABLE `request_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_events_request_id_foreign` (`request_id`);

--
-- Indexes for table `request_history`
--
ALTER TABLE `request_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_history_request_id_foreign` (`request_id`);

--
-- Indexes for table `role_types`
--
ALTER TABLE `role_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_types_code_unique` (`code`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_code_unique` (`code`);

--
-- Indexes for table `service_feedbacks`
--
ALTER TABLE `service_feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_feedbacks_service_request_id_foreign` (`service_request_id`);

--
-- Indexes for table `service_history`
--
ALTER TABLE `service_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_history_service_request_id_foreign` (`service_request_id`);

--
-- Indexes for table `service_images`
--
ALTER TABLE `service_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_images_service_id_foreign` (`service_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_requests_assistance_request_id_foreign` (`assistance_request_id`),
  ADD KEY `service_requests_service_id_foreign` (`service_id`),
  ADD KEY `service_requests_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `service_requests_feedback`
--
ALTER TABLE `service_requests_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_requests_feedback_service_request_id_foreign` (`service_request_id`);

--
-- Indexes for table `service_vehicle_rates`
--
ALTER TABLE `service_vehicle_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `svc_vehicle_rate_unique` (`service_id`,`vehicle_type_id`),
  ADD KEY `service_vehicle_rates_vehicle_type_id_foreign` (`vehicle_type_id`);

--
-- Indexes for table `status_domains`
--
ALTER TABLE `status_domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_domains_code_unique` (`code`);

--
-- Indexes for table `status_types`
--
ALTER TABLE `status_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_types_domain_id_code_unique` (`domain_id`,`code`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_details_transaction_id_foreign` (`transaction_id`);

--
-- Indexes for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_logs_transaction_id_foreign` (`transaction_id`);

--
-- Indexes for table `transaction_types`
--
ALTER TABLE `transaction_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_activities_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_notifications_user_id_foreign` (`user_id`),
  ADD KEY `user_notifications_notification_id_foreign` (`notification_id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_permissions_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_roles_user_id_foreign` (`user_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_sessions_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_settings_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_subscription_plans`
--
ALTER TABLE `user_subscription_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_subscription_plans_user_id_foreign` (`user_id`),
  ADD KEY `user_subscription_plans_subscription_plan_id_foreign` (`subscription_plan_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_plate_unique` (`plate`),
  ADD KEY `vehicles_user_id_foreign` (`user_id`),
  ADD KEY `vehicles_vehicle_type_id_foreign` (`vehicle_type_id`);

--
-- Indexes for table `vehicles_services`
--
ALTER TABLE `vehicles_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicles_services_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `vehicles_services_service_id_foreign` (`service_id`);

--
-- Indexes for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_documents_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_types_code_unique` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assistance_requests`
--
ALTER TABLE `assistance_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consent_types`
--
ALTER TABLE `consent_types`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications_history`
--
ALTER TABLE `notifications_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_method_types`
--
ALTER TABLE `payment_method_types`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provider_documents`
--
ALTER TABLE `provider_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provider_locations`
--
ALTER TABLE `provider_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provider_reviews`
--
ALTER TABLE `provider_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provider_schedules`
--
ALTER TABLE `provider_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provider_services`
--
ALTER TABLE `provider_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `request_events`
--
ALTER TABLE `request_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `request_history`
--
ALTER TABLE `request_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_types`
--
ALTER TABLE `role_types`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_feedbacks`
--
ALTER TABLE `service_feedbacks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_history`
--
ALTER TABLE `service_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_images`
--
ALTER TABLE `service_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_requests_feedback`
--
ALTER TABLE `service_requests_feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_vehicle_rates`
--
ALTER TABLE `service_vehicle_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `status_domains`
--
ALTER TABLE `status_domains`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `status_types`
--
ALTER TABLE `status_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_types`
--
ALTER TABLE `transaction_types`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_subscription_plans`
--
ALTER TABLE `user_subscription_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicles_services`
--
ALTER TABLE `vehicles_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assistance_requests`
--
ALTER TABLE `assistance_requests`
  ADD CONSTRAINT `assistance_requests_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `assistance_requests_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `assistance_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assistance_requests_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD CONSTRAINT `legal_documents_consent_type_id_foreign` FOREIGN KEY (`consent_type_id`) REFERENCES `consent_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications_history`
--
ALTER TABLE `notifications_history`
  ADD CONSTRAINT `notifications_history_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_assistance_request_id_foreign` FOREIGN KEY (`assistance_request_id`) REFERENCES `assistance_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `providers`
--
ALTER TABLE `providers`
  ADD CONSTRAINT `providers_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `status_types` (`id`),
  ADD CONSTRAINT `providers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_documents`
--
ALTER TABLE `provider_documents`
  ADD CONSTRAINT `provider_documents_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_locations`
--
ALTER TABLE `provider_locations`
  ADD CONSTRAINT `provider_locations_assistance_request_id_foreign` FOREIGN KEY (`assistance_request_id`) REFERENCES `assistance_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `provider_locations_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_reviews`
--
ALTER TABLE `provider_reviews`
  ADD CONSTRAINT `provider_reviews_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_schedules`
--
ALTER TABLE `provider_schedules`
  ADD CONSTRAINT `provider_schedules_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_services`
--
ALTER TABLE `provider_services`
  ADD CONSTRAINT `provider_services_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `provider_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_events`
--
ALTER TABLE `request_events`
  ADD CONSTRAINT `request_events_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `assistance_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_history`
--
ALTER TABLE `request_history`
  ADD CONSTRAINT `request_history_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `assistance_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_feedbacks`
--
ALTER TABLE `service_feedbacks`
  ADD CONSTRAINT `service_feedbacks_service_request_id_foreign` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_history`
--
ALTER TABLE `service_history`
  ADD CONSTRAINT `service_history_service_request_id_foreign` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_images`
--
ALTER TABLE `service_images`
  ADD CONSTRAINT `service_images_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_assistance_request_id_foreign` FOREIGN KEY (`assistance_request_id`) REFERENCES `assistance_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_requests_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `service_requests_feedback`
--
ALTER TABLE `service_requests_feedback`
  ADD CONSTRAINT `service_requests_feedback_service_request_id_foreign` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_vehicle_rates`
--
ALTER TABLE `service_vehicle_rates`
  ADD CONSTRAINT `service_vehicle_rates_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_vehicle_rates_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `status_types`
--
ALTER TABLE `status_types`
  ADD CONSTRAINT `status_types_domain_id_foreign` FOREIGN KEY (`domain_id`) REFERENCES `status_domains` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD CONSTRAINT `transaction_logs_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `user_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role_types` (`id`),
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_subscription_plans`
--
ALTER TABLE `user_subscription_plans`
  ADD CONSTRAINT `user_subscription_plans_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_subscription_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicles_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`);

--
-- Constraints for table `vehicles_services`
--
ALTER TABLE `vehicles_services`
  ADD CONSTRAINT `vehicles_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicles_services_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD CONSTRAINT `vehicle_documents_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
