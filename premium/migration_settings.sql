-- Migration: site_settings table for admin-controlled themes
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default theme setting
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
    ('active_theme', 'ramadan')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
