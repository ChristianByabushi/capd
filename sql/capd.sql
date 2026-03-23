-- CAPD Database Schema
-- Run this in phpMyAdmin or: mysql -u root capd_db < sql/capd.sql

CREATE DATABASE IF NOT EXISTS capd_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE capd_db;

-- Languages supported: fr, en, sw
-- Admin users
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150),
    role ENUM('superadmin','admin','editor') DEFAULT 'editor',
    is_active TINYINT(1) DEFAULT 1,
    created_by INT DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site settings (email config, social links, address, etc.)
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Members
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    position_fr VARCHAR(150),
    position_en VARCHAR(150),
    position_sw VARCHAR(150),
    bio_fr TEXT,
    bio_en TEXT,
    bio_sw TEXT,
    photo VARCHAR(255),
    organ ENUM('conseil_administration','comite_gestion','comite_controle','secretariat_executif','membre') DEFAULT 'membre',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Partners
CREATE TABLE partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    logo VARCHAR(255),
    website VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- Activities / Projects
CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) NOT NULL UNIQUE,
    title_fr VARCHAR(255),
    title_en VARCHAR(255),
    title_sw VARCHAR(255),
    description_fr TEXT,
    description_en TEXT,
    description_sw TEXT,
    objectives_fr TEXT,
    objectives_en TEXT,
    objectives_sw TEXT,
    department VARCHAR(100),
    date_start DATE,
    date_end DATE,
    cover_image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    status ENUM('ongoing','completed','planned') DEFAULT 'ongoing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activity media (images/videos)
CREATE TABLE activity_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    media_type ENUM('image','video') DEFAULT 'image',
    file_path VARCHAR(255),
    video_url VARCHAR(255),
    caption VARCHAR(255),
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);

-- Blog / Communiqués
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) NOT NULL UNIQUE,
    title_fr VARCHAR(255),
    title_en VARCHAR(255),
    title_sw VARCHAR(255),
    content_fr LONGTEXT,
    content_en LONGTEXT,
    content_sw LONGTEXT,
    excerpt_fr TEXT,
    excerpt_en TEXT,
    excerpt_sw TEXT,
    cover_image VARCHAR(255),
    category ENUM('news','communique','report') DEFAULT 'news',
    is_published TINYINT(1) DEFAULT 0,
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Formations / Training centers
CREATE TABLE centres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) NOT NULL UNIQUE,
    name_fr VARCHAR(255),
    name_en VARCHAR(255),
    name_sw VARCHAR(255),
    description_fr TEXT,
    description_en TEXT,
    description_sw TEXT,
    domain ENUM('genre','entrepreneuriat','education','sante','environnement','paix') DEFAULT 'education',
    cover_image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0
);

-- Learner feedbacks
CREATE TABLE feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    centre_id INT,
    learner_name VARCHAR(150),
    feedback_text TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (centre_id) REFERENCES centres(id) ON DELETE SET NULL
);

-- Donations via Airtel Money
CREATE TABLE donations (
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

-- Contact messages
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(150),
    sender_email VARCHAR(150),
    subject VARCHAR(255),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hero slides (homepage)
CREATE TABLE hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    title_fr VARCHAR(255),
    title_en VARCHAR(255),
    title_sw VARCHAR(255),
    subtitle_fr TEXT,
    subtitle_en TEXT,
    subtitle_sw TEXT,
    btn1_label_fr VARCHAR(100),
    btn1_label_en VARCHAR(100),
    btn1_label_sw VARCHAR(100),
    btn1_url VARCHAR(255),
    btn2_label_fr VARCHAR(100),
    btn2_label_en VARCHAR(100),
    btn2_label_sw VARCHAR(100),
    btn2_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- Stats (editable counters on homepage)
CREATE TABLE stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label_fr VARCHAR(100),
    label_en VARCHAR(100),
    label_sw VARCHAR(100),
    value VARCHAR(50),
    icon VARCHAR(50),
    display_order INT DEFAULT 0
);

-- Default admin user (password: Admin@2026 — change immediately)
INSERT INTO admin_users (username, password, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'superadmin');

-- Default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'CAPD ASBL'),
('site_email', 'contact@capd.org'),
('smtp_host', 'localhost'),
('smtp_port', '587'),
('smtp_user', ''),
('smtp_pass', ''),
('smtp_from', 'contact@capd.org'),
('address_fr', 'Kinshasa, République Démocratique du Congo'),
('address_en', 'Kinshasa, Democratic Republic of Congo'),
('address_sw', 'Kinshasa, Jamhuri ya Kidemokrasia ya Kongo'),
('phone', '+243 000 000 000'),
('facebook', ''),
('twitter', ''),
('youtube', ''),
('airtel_money_number', '+243 000 000 000'),
('airtel_money_name', 'CAPD ASBL'),
('instagram', ''),
('linkedin', ''),
('tiktok', ''),
('default_lang', 'fr');

-- Default stats
INSERT INTO stats (label_fr, label_en, label_sw, value, icon, display_order) VALUES
('Membres actifs', 'Active members', 'Wanachama wanaofanya kazi', '46', 'fa-users', 1),
('Projets réalisés', 'Projects completed', 'Miradi iliyokamilika', '20+', 'fa-project-diagram', 2),
('Bénéficiaires', 'Beneficiaries', 'Wanufaika', '5000+', 'fa-hands-helping', 3),
('Années d\'expérience', 'Years of experience', 'Miaka ya uzoefu', '10+', 'fa-calendar-check', 4);
