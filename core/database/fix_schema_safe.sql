-- Fix Jobs Table Schema - Safe Version
USE article;

-- Add slug column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'slug');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN slug VARCHAR(255) NULL AFTER title', 
    'SELECT "slug already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add custom_budget column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'custom_budget');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN custom_budget TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''1 =>Enable ,0 => Disabled'' AFTER budget', 
    'SELECT "custom_budget already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add project_scope column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'project_scope');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN project_scope TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''Large => 1, Medium=>2, Small->3'' AFTER description', 
    'SELECT "project_scope already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add job_longevity column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'job_longevity');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN job_longevity TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''job_longevity: 3 to 6 months=>4, 1 to 3 months=>3,Less than 1 month=>2 , Less than 1 Week=>1'' AFTER project_scope', 
    'SELECT "job_longevity already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add skill_level column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'skill_level');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN skill_level TINYINT(1) NOT NULL DEFAULT 0 COMMENT ''Pro Level=>1, Expert=>2, Intermediate=>3, Entry=>4'' AFTER job_longevity', 
    'SELECT "skill_level already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add questions column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'questions');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN questions TEXT NULL AFTER skill_level', 
    'SELECT "questions already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add skill_ids column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'skill_ids');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN skill_ids TEXT NULL AFTER questions', 
    'SELECT "skill_ids already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add deadline column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'deadline');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN deadline DATE NULL AFTER skill_ids', 
    'SELECT "deadline already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add rejection_reason column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'rejection_reason');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN rejection_reason TEXT NULL AFTER is_approved', 
    'SELECT "rejection_reason already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add interviews column if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'article' AND TABLE_NAME = 'jobs' AND COLUMN_NAME = 'interviews');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE jobs ADD COLUMN interviews INT(11) NOT NULL DEFAULT 0 COMMENT ''total_interview count of this job , against bids'' AFTER rejection_reason', 
    'SELECT "interviews already exists"');
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
