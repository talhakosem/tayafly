-- =============================================
-- DESTINATIONS (Destinasyonlar) TABLOLARI
-- Bu SQL dosyasını phpMyAdmin'den çalıştırın
-- =============================================

-- 1. Destinations Tablosu
CREATE TABLE IF NOT EXISTS `destinations` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `meta_description` text COLLATE utf8mb4_unicode_ci,
    `sort_order` int NOT NULL DEFAULT '0',
    `is_active` tinyint(1) NOT NULL DEFAULT '1',
    `show_in_menu` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `destinations_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Post-Destination İlişki Tablosu
CREATE TABLE IF NOT EXISTS `post_destination` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `post_id` bigint unsigned NOT NULL,
    `destination_id` bigint unsigned NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `post_destination_post_id_destination_id_unique` (`post_id`,`destination_id`),
    KEY `post_destination_post_id_index` (`post_id`),
    KEY `post_destination_destination_id_index` (`destination_id`),
    CONSTRAINT `post_destination_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `post_destination_destination_id_foreign` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration kaydı (Laravel'in bilmesi için)
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_29_000001_create_destinations_table', 2);

-- =============================================
-- TAMAMLANDI
-- =============================================

