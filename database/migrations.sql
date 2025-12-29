-- =============================================
-- TAYAFLY - TÜM VERİTABANI TABLOLARI
-- Bu SQL dosyasını phpMyAdmin'den çalıştırın
-- =============================================

-- 1. Users Tablosu
CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Password Reset Tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Sessions Tablosu
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id` bigint unsigned DEFAULT NULL,
    `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `user_agent` text COLLATE utf8mb4_unicode_ci,
    `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `last_activity` int NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Cache Tablosu
CREATE TABLE IF NOT EXISTS `cache` (
    `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `expiration` int NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Cache Locks Tablosu
CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `expiration` int NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Jobs Tablosu
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `attempts` tinyint unsigned NOT NULL,
    `reserved_at` int unsigned DEFAULT NULL,
    `available_at` int unsigned NOT NULL,
    `created_at` int unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Job Batches Tablosu
CREATE TABLE IF NOT EXISTS `job_batches` (
    `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `total_jobs` int NOT NULL,
    `pending_jobs` int NOT NULL,
    `failed_jobs` int NOT NULL,
    `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `options` mediumtext COLLATE utf8mb4_unicode_ci,
    `cancelled_at` int DEFAULT NULL,
    `created_at` int NOT NULL,
    `finished_at` int DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Failed Jobs Tablosu
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Migrations Tablosu (Laravel'in takip için kullandığı)
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch` int NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Categories Tablosu
CREATE TABLE IF NOT EXISTS `categories` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `meta_description` text COLLATE utf8mb4_unicode_ci,
    `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `parent_id` bigint unsigned NOT NULL DEFAULT '0',
    `sort_order` int NOT NULL DEFAULT '0',
    `show_in_top_menu` tinyint(1) NOT NULL DEFAULT '0',
    `show_in_footer_menu` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Posts Tablosu
CREATE TABLE IF NOT EXISTS `posts` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `short_description` text COLLATE utf8mb4_unicode_ci,
    `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tags` json DEFAULT NULL,
    `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `meta_description` text COLLATE utf8mb4_unicode_ci,
    `is_published` tinyint(1) NOT NULL DEFAULT '1',
    `comments_enabled` tinyint(1) NOT NULL DEFAULT '0',
    `user_id` bigint unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `posts_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Post-Category İlişki Tablosu
CREATE TABLE IF NOT EXISTS `post_category` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `post_id` bigint unsigned NOT NULL,
    `category_id` bigint unsigned NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `post_category_post_id_category_id_unique` (`post_id`,`category_id`),
    KEY `post_category_post_id_index` (`post_id`),
    KEY `post_category_category_id_index` (`category_id`),
    CONSTRAINT `post_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
    CONSTRAINT `post_category_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Settings Tablosu
CREATE TABLE IF NOT EXISTS `settings` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `favicon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `top_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/',
    `site_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `site_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `facebook_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `twitter_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `instagram_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `youtube_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `google_verification_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `analytics_code` text COLLATE utf8mb4_unicode_ci,
    `google_map` text COLLATE utf8mb4_unicode_ci,
    `bank_account_info` text COLLATE utf8mb4_unicode_ci,
    `bank_transfer_enabled` tinyint(1) NOT NULL DEFAULT '0',
    `cash_on_delivery_card_enabled` tinyint(1) NOT NULL DEFAULT '0',
    `cash_on_delivery_cash_enabled` tinyint(1) NOT NULL DEFAULT '0',
    `online_payment_enabled` tinyint(1) NOT NULL DEFAULT '0',
    `free_shipping_limit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `shipping_cost` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `discount_threshold` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `discount_type` tinyint(1) NOT NULL DEFAULT '0',
    `discount_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `top_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `order_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `credit_card_selection` tinyint(1) NOT NULL DEFAULT '0',
    `cash_on_delivery_shipping_cost` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `top_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `top_text_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `top_background_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- VARSAYILAN VERİLER
-- =============================================

-- Varsayılan Ayarlar
INSERT INTO `settings` (`id`, `site_title`, `site_description`, `email`, `phone`, `address`, `top_link`, `created_at`, `updated_at`) VALUES
(1, 'Tayafly', 'Tayafly Blog Sitesi', 'info@tayafly.com', '0555 555 5555', 'İstanbul, Türkiye', '/', NOW(), NOW())
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Migration kayıtları (Laravel'in bilmesi için)
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_12_01_141810_create_posts_table', 1),
('2025_12_03_121849_add_additional_fields_to_posts_table', 1),
('2025_12_03_130246_create_categories_table', 1),
('2025_12_03_135740_create_settings_table', 1),
('2025_12_12_141210_create_post_category_table', 1);

-- =============================================
-- TAMAMLANDI
-- =============================================

