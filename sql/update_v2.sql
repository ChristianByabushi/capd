-- Run this in phpMyAdmin on capd_db if you already imported capd.sql
USE capd_db;

CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(150),
    donor_email VARCHAR(150),
    phone VARCHAR(30) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'CDF',
    transaction_ref VARCHAR(100),
    motivation TEXT,
    is_anonymous TINYINT(1) DEFAULT 0,
    status ENUM('pending','confirmed','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('airtel_money_number', '+243 000 000 000'),
('airtel_money_name', 'CAPD ASBL'),
('instagram', ''),
('linkedin', ''),
('tiktok', '');

-- v3: YouTube embed support + favicon setting
ALTER TABLE posts       ADD COLUMN IF NOT EXISTS youtube_url VARCHAR(255) DEFAULT NULL AFTER cover_image;
ALTER TABLE activities  ADD COLUMN IF NOT EXISTS youtube_url VARCHAR(255) DEFAULT NULL AFTER cover_image;
ALTER TABLE centres     ADD COLUMN IF NOT EXISTS youtube_url VARCHAR(255) DEFAULT NULL AFTER cover_image;
ALTER TABLE hero_slides ADD COLUMN IF NOT EXISTS youtube_url VARCHAR(255) DEFAULT NULL AFTER image;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('favicon', '');
