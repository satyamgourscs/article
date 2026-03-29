-- Create remaining critical pivot/related tables
USE article;

-- Create skill_user pivot table if it doesn't exist
CREATE TABLE IF NOT EXISTS skill_user (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    skill_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_skill (user_id, skill_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create education table if it doesn't exist
CREATE TABLE IF NOT EXISTS education (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    degree VARCHAR(255) NULL,
    institution VARCHAR(255) NULL,
    field_of_study VARCHAR(255) NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create portfolios table if it doesn't exist
CREATE TABLE IF NOT EXISTS portfolios (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    url VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
