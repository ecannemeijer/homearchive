-- ============================================
-- PRICE COMPARISON FEATURE - DATABASE UPDATE
-- Add offers and savings_recommendations tables
-- ============================================

-- Offers tabel voor prijsvergelijking
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

-- Savings recommendations tabel
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
-- EXAMPLE OFFERS DATA (OPTIONAL)
-- ============================================

-- Streaming aanbiedingen
INSERT INTO offers (provider, plan_name, price, frequency, category, description, url, conditions, is_active) VALUES
('Netflix', 'Basis Abonnement', 7.99, 'monthly', 'Streaming', 'HD kwaliteit, 2 schermen tegelijk', 'https://www.netflix.com/nl/', 'Alleen voor nieuwe klanten', 1),
('Disney+', 'Standaard', 8.99, 'monthly', 'Streaming', 'Alle Disney, Marvel, Star Wars content', 'https://www.disneyplus.com/', NULL, 1),
('Amazon Prime Video', 'Prime Membership', 6.99, 'monthly', 'Streaming', 'Inclusief gratis verzending', 'https://www.primevideo.com/', NULL, 1),
('Videoland', 'Plus pakket', 9.99, 'monthly', 'Streaming', 'Nederlandse content + films', 'https://www.videoland.com/', NULL, 1);

-- Software aanbiedingen
INSERT INTO offers (provider, plan_name, price, frequency, category, description, url, conditions, is_active) VALUES
('Microsoft 365', 'Persoonlijk', 69.00, 'yearly', 'Software', 'Word, Excel, PowerPoint, 1TB OneDrive', 'https://www.microsoft.com/nl-nl/microsoft-365', NULL, 1),
('Adobe Creative Cloud', 'Fotografie', 11.99, 'monthly', 'Software', 'Photoshop + Lightroom', 'https://www.adobe.com/', NULL, 1),
('Dropbox', 'Plus', 9.99, 'monthly', 'Software', '2TB cloud opslag', 'https://www.dropbox.com/', NULL, 1);

-- Verzekering aanbiedingen
INSERT INTO offers (provider, plan_name, price, frequency, category, description, url, conditions, is_active) VALUES
('OHRA', 'Zorgverzekering Basis', 119.00, 'monthly', 'Verzekering', 'Basisverzekering + gratis tandarts', 'https://www.ohra.nl/', 'Bij overstap voor 1 februari', 1),
('Zilveren Kruis', 'Basis Verzekering', 125.00, 'monthly', 'Verzekering', 'Standaard dekking', 'https://www.zilverenkruis.nl/', NULL, 1),
('VGZ', 'Goed Gedekt', 122.50, 'monthly', 'Verzekering', 'Basisverzekering met service', 'https://www.vgz.nl/', NULL, 1);

-- Sport aanbiedingen
INSERT INTO offers (provider, plan_name, price, frequency, category, description, url, conditions, is_active) VALUES
('Basic-Fit', 'Premium', 24.99, 'monthly', 'Sport', 'Onbeperkt naar alle vestigingen', 'https://www.basic-fit.com/', 'Alleen eerste maand korting', 1),
('TrainMore', 'All-in', 29.95, 'monthly', 'Sport', 'Inclusief groepslessen en sauna', 'https://www.trainmore.com/', NULL, 1),
('Fit For Free', 'Standaard', 19.95, 'monthly', 'Sport', 'Toegang tot alle clubs', 'https://www.fitforfree.nl/', NULL, 1);

-- Internet/Telecom aanbiedingen
INSERT INTO offers (provider, plan_name, price, frequency, category, description, url, conditions, is_active) VALUES
('Ziggo', 'Internet 500', 45.00, 'monthly', 'Overig', '500 Mbps download', 'https://www.ziggo.nl/', '12 maanden contract', 1),
('KPN', 'Internet Compleet', 52.50, 'monthly', 'Overig', '500 Mbps + TV', 'https://www.kpn.com/', NULL, 1),
('T-Mobile', 'Onbeperkt Internet', 25.00, 'monthly', 'Overig', 'Onbeperkt 4G/5G data', 'https://www.t-mobile.nl/', 'Sim-only', 1);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if tables were created successfully
SELECT 'Offers table created' as status, COUNT(*) as offer_count FROM offers;
SELECT 'Savings recommendations table created' as status, COUNT(*) as recommendation_count FROM savings_recommendations;
