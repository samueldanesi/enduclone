-- Tabella per codici sconto e promozioni
CREATE TABLE IF NOT EXISTS discount_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    type ENUM('percentage', 'fixed', 'free') NOT NULL DEFAULT 'percentage',
    value DECIMAL(8,2) NOT NULL, -- 5.00 per 5%, 10.00 per €10 fisso
    min_amount DECIMAL(8,2) DEFAULT 0, -- Importo minimo per applicare sconto
    max_uses INT DEFAULT NULL, -- Usi massimi totali (NULL = illimitato)
    used_count INT DEFAULT 0, -- Contatore usi attuali
    max_uses_per_user INT DEFAULT 1, -- Usi per utente
    valid_from DATETIME DEFAULT CURRENT_TIMESTAMP,
    valid_to DATETIME DEFAULT NULL, -- NULL = senza scadenza
    applicable_to ENUM('all', 'specific_events', 'categories') DEFAULT 'all',
    event_ids JSON DEFAULT NULL, -- IDs eventi specifici se applicable_to = 'specific_events'
    categories JSON DEFAULT NULL, -- Categorie se applicable_to = 'categories'
    created_by INT, -- ID organizzatore che ha creato il codice
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_code (code),
    INDEX idx_status (status),
    INDEX idx_valid_dates (valid_from, valid_to)
);

-- Tabella per tracciare utilizzi dei codici
CREATE TABLE IF NOT EXISTS discount_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discount_code_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_id INT NOT NULL,
    original_amount DECIMAL(8,2) NOT NULL,
    discount_amount DECIMAL(8,2) NOT NULL,
    final_amount DECIMAL(8,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (discount_code_id) REFERENCES discount_codes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_code (discount_code_id, user_id, registration_id),
    INDEX idx_code_user (discount_code_id, user_id),
    INDEX idx_used_at (used_at)
);

-- Inserisci codice promozionale di benvenuto
INSERT INTO discount_codes (
    code, 
    description, 
    type, 
    value, 
    min_amount,
    max_uses,
    max_uses_per_user,
    valid_to,
    status
) VALUES (
    'SPORT5',
    'Sconto benvenuto 5% su tutti gli eventi',
    'percentage',
    5.00,
    0.00,
    NULL, -- Illimitato
    1, -- Una volta per utente
    '2025-12-31 23:59:59',
    'active'
);

-- Inserisci altri codici di esempio
INSERT INTO discount_codes (
    code, 
    description, 
    type, 
    value, 
    min_amount,
    max_uses,
    max_uses_per_user,
    valid_to,
    status
) VALUES 
('EARLY10', 'Early Bird - 10€ di sconto', 'fixed', 10.00, 30.00, 100, 1, '2025-11-30 23:59:59', 'active'),
('RUNNER15', 'Sconto Runner 15%', 'percentage', 15.00, 25.00, 50, 1, '2025-12-31 23:59:59', 'active'),
('FREERUN', 'Corsa gratuita', 'free', 0.00, 0.00, 20, 1, '2025-10-31 23:59:59', 'active');
