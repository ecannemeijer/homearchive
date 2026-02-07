-- ============================================
-- PRODUCTION DATABASE SCHEMA
-- Subscription/Insurance Management System
-- ============================================
-- IMPORTANT: This schema creates a shared data model where all users
-- access the same data. Individual user isolation is NOT implemented.
-- All subscriptions, passwords, documents belong to system user (id=6).
--
-- Login credentials after import:
--   Email: admin@example.com
--   Password: admin123
-- ============================================

-- Disable foreign key checks for table creation
SET FOREIGN_KEY_CHECKS=0;

-- Tabel voor gebruikers
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor abonnementen en verzekeringen (shared data, all belong to system user id=6)
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL DEFAULT 6,
    name VARCHAR(255) NOT NULL,
    type ENUM('subscription', 'insurance') NOT NULL,
    category VARCHAR(100),
    cost DECIMAL(10, 2) NOT NULL,
    frequency ENUM('monthly', 'yearly') NOT NULL DEFAULT 'monthly',
    billing_date INT,
    start_date DATE,
    end_date DATE,
    is_monthly_cancelable TINYINT DEFAULT 0,
    username VARCHAR(255),
    password_encrypted VARCHAR(255),
    website_url VARCHAR(500),
    notes TEXT,
    is_active TINYINT DEFAULT 1,
    renewal_reminder INT DEFAULT 7,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_active (is_active),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor dokumenten uploads (shared data, all belong to system user id=6)
CREATE TABLE IF NOT EXISTS documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_id INT NOT NULL,
    user_id INT NOT NULL DEFAULT 6,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    file_path VARCHAR(500),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_subscription_id (subscription_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor wachtwoord vault (shared data, all belong to system user id=6)
CREATE TABLE IF NOT EXISTS passwords (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL DEFAULT 6,
    title VARCHAR(255) NOT NULL,
    username VARCHAR(255),
    password_encrypted VARCHAR(255) NOT NULL,
    website_url VARCHAR(500),
    notes TEXT,
    tags VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor notificaties
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subscription_id INT,
    type VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    message TEXT,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor categorieën/tags (shared across all users)
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL DEFAULT 6,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category_name (name),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor maandelijkse kosten tracking (shared data, all belong to system user id=6)
CREATE TABLE IF NOT EXISTS monthly_costs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL DEFAULT 6,
    year INT NOT NULL,
    month INT NOT NULL,
    total_cost DECIMAL(10, 2),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_month (year, month),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor prijsvergelijking aanbiedingen
CREATE TABLE IF NOT EXISTS offers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provider VARCHAR(191) NOT NULL COMMENT 'Aanbieder naam (bijv. Ziggo, VGZ)',
    plan_name VARCHAR(255) NOT NULL COMMENT 'Plan/product naam',
    price DECIMAL(10, 2) NOT NULL COMMENT 'Prijs',
    frequency ENUM('monthly', 'yearly') NOT NULL DEFAULT 'monthly',
    category VARCHAR(100) DEFAULT NULL COMMENT 'Categorie (streaming, internet, verzekering, etc.)',
    description TEXT DEFAULT NULL COMMENT 'Beschrijving van het aanbod',
    url VARCHAR(1024) DEFAULT NULL COMMENT 'Link naar aanbieding',
    features JSON DEFAULT NULL COMMENT 'Extra features (snelheid, dekking, etc.)',
    conditions TEXT DEFAULT NULL COMMENT 'Voorwaarden',
    is_active TINYINT DEFAULT 1 COMMENT 'Actief aanbod?',
    last_checked DATETIME DEFAULT NULL COMMENT 'Laatste keer gecontroleerd',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel voor besparingsaanbevelingen
CREATE TABLE IF NOT EXISTS savings_recommendations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_id INT NOT NULL,
    offer_id INT NOT NULL,
    monthly_savings DECIMAL(10, 2) NOT NULL,
    yearly_savings DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    notes TEXT DEFAULT NULL,
    recommended_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at DATETIME DEFAULT NULL,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE CASCADE,
    INDEX idx_subscription (subscription_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT USERS
-- ============================================

-- System user for shared data (all subscriptions, passwords, etc. belong to this user)
-- CRITICAL: Must exist before any data tables can reference it (id=6)
INSERT INTO users (id, name, email, password, is_admin) VALUES (6, 'System', 'system@example.com', '', 0) ON DUPLICATE KEY UPDATE name='System', is_admin=0;

-- Default admin user
-- Email: admin@example.com
-- Password: admin123 (bcrypt hash: $2y$10$SHWiFzOIWMfkgfgJcXz93eptOE5e648shifWZrHHR94FC.JvUJQJy)
INSERT INTO users (name, email, password, is_admin) VALUES ('Administrator', 'admin@example.com', '$2y$10$SHWiFzOIWMfkgfgJcXz93eptOE5e648shifWZrHHR94FC.JvUJQJy', 1) ON DUPLICATE KEY UPDATE email='admin@example.com', is_admin=1;

-- ============================================
-- DEFAULT CATEGORIES
-- ============================================

INSERT INTO categories (user_id, name, color) VALUES 
(6, 'Streaming', '#FF6B6B'),
(6, 'Software', '#4ECDC4'),
(6, 'Verzekering', '#45B7D1'),
(6, 'Sport', '#FFA07A'),
(6, 'Gezondheid', '#98D8C8'),
(6, 'Overig', '#95A5A6')
ON DUPLICATE KEY UPDATE color=VALUES(color);

-- ============================================
-- Re-enable foreign key checks
-- ============================================
SET FOREIGN_KEY_CHECKS=1;
