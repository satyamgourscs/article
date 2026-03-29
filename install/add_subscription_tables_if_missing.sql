-- Run against database `article` (or your app DB) if `plans` is missing.
-- Safe to run once; re-running skips seed when `plans` already has rows.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `plans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `price` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `duration_days` int(10) UNSIGNED NOT NULL DEFAULT 365,
  `job_apply_limit` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `job_view_limit` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `job_post_limit` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `listing_visible_jobs` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `jobs_applied_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `jobs_viewed_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `jobs_posted_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_subscriptions_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `user_subscriptions_plan_id_foreign` (`plan_id`),
  CONSTRAINT `user_subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `buyer_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `jobs_applied_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `jobs_viewed_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `jobs_posted_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `buyer_subscriptions_buyer_id_is_active_index` (`buyer_id`,`is_active`),
  KEY `buyer_subscriptions_plan_id_foreign` (`plan_id`),
  CONSTRAINT `buyer_subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `plans` (`name`, `type`, `price`, `duration_days`, `job_apply_limit`, `job_view_limit`, `job_post_limit`, `listing_visible_jobs`, `is_active`, `created_at`, `updated_at`)
SELECT s.`name`, s.`type`, s.`price`, s.`duration_days`, s.`job_apply_limit`, s.`job_view_limit`, s.`job_post_limit`, s.`listing_visible_jobs`, s.`is_active`, s.`created_at`, s.`updated_at`
FROM (
  SELECT 'Free' AS `name`, 'student' AS `type`, 0.00000000 AS `price`, 365 AS `duration_days`, 5 AS `job_apply_limit`, 10 AS `job_view_limit`, 0 AS `job_post_limit`, 2 AS `listing_visible_jobs`, 1 AS `is_active`, NOW() AS `created_at`, NOW() AS `updated_at`
  UNION ALL SELECT 'Pro', 'student', 29.99000000, 30, 999999, 999999, 0, 999999, 1, NOW(), NOW()
  UNION ALL SELECT 'Free', 'company', 0.00000000, 365, 0, 0, 2, 999999, 1, NOW(), NOW()
  UNION ALL SELECT 'Pro', 'company', 99.99000000, 30, 0, 0, 999999, 999999, 1, NOW(), NOW()
) AS s
WHERE (SELECT COUNT(*) FROM `plans`) = 0;
