-- Fix Jobs Table Schema
-- Add missing columns to jobs table

USE article;

-- Rename user_id to buyer_id if it exists and buyer_id doesn't
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'user_id');
SET @buyer_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'buyer_id');

SET @sql = IF(@col_exists > 0 AND @buyer_exists = 0, 
    'ALTER TABLE jobs CHANGE user_id buyer_id BIGINT UNSIGNED NOT NULL DEFAULT 0', 
    'SELECT "user_id column does not exist or buyer_id already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Rename sub_category_id to subcategory_id if it exists
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'sub_category_id');
SET @subcat_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'subcategory_id');

SET @sql = IF(@col_exists > 0 AND @subcat_exists = 0, 
    'ALTER TABLE jobs CHANGE sub_category_id subcategory_id BIGINT UNSIGNED DEFAULT 0', 
    'SELECT "sub_category_id column does not exist or subcategory_id already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add missing columns
ALTER TABLE jobs 
    ADD COLUMN IF NOT EXISTS slug VARCHAR(255) NULL AFTER title,
    ADD COLUMN IF NOT EXISTS custom_budget TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 =>Enable ,0 => Disabled' AFTER budget,
    ADD COLUMN IF NOT EXISTS project_scope TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Large => 1, Medium=>2, Small->3' AFTER description,
    ADD COLUMN IF NOT EXISTS job_longevity TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'job_longevity: 3 to 6 months=>4, 1 to 3 months=>3,Less than 1 month=>2 , Less than 1 Week=>1' AFTER project_scope,
    ADD COLUMN IF NOT EXISTS skill_level TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Pro Level=>1, Expert=>2, Intermediate=>3, Entry=>4' AFTER job_longevity,
    ADD COLUMN IF NOT EXISTS questions TEXT NULL AFTER skill_level,
    ADD COLUMN IF NOT EXISTS skill_ids TEXT NULL AFTER questions,
    ADD COLUMN IF NOT EXISTS deadline DATE NULL AFTER skill_ids,
    ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER is_approved,
    ADD COLUMN IF NOT EXISTS interviews INT(11) NOT NULL DEFAULT 0 COMMENT 'total_interview count of this job , against bids' AFTER rejection_reason;

-- Ensure buyer_id exists
SET @buyer_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'buyer_id');
SET @sql = IF(@buyer_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN buyer_id BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER id', 
    'SELECT "buyer_id already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure subcategory_id exists
SET @subcat_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'subcategory_id');
SET @sql = IF(@subcat_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN subcategory_id BIGINT UNSIGNED DEFAULT 0 AFTER category_id', 
    'SELECT "subcategory_id already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create subcategories table if it doesn't exist
CREATE TABLE IF NOT EXISTS subcategories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
    name VARCHAR(40) NULL,
    image VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create skills table if it doesn't exist
CREATE TABLE IF NOT EXISTS skills (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(40) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create job_skills table if it doesn't exist
CREATE TABLE IF NOT EXISTS job_skills (
    id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    job_id BIGINT NOT NULL DEFAULT 0,
    skill_id BIGINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
