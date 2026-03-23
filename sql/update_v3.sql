-- v3: Role system upgrade
-- Run this in phpMyAdmin on capd_db AFTER update_v2.sql
USE capd_db;

-- Add 'admin' role between superadmin and editor
ALTER TABLE admin_users
    MODIFY COLUMN role ENUM('superadmin','admin','editor') DEFAULT 'editor';

-- Track who created each user + last login
ALTER TABLE admin_users
    ADD COLUMN IF NOT EXISTS created_by INT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS last_login DATETIME DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1;

-- Promote the default user to superadmin (already is, just ensuring)
UPDATE admin_users SET role='superadmin' WHERE username='admin';
