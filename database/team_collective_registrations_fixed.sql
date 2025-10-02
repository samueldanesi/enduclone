-- Sistema Iscrizioni Collettive CORRETTO
-- Separazione netta tra iscrizioni individuali e collettive

-- Tabella principale per iscrizioni collettive
CREATE TABLE IF NOT EXISTS team_collective_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    event_id INT NOT NULL,
    responsible_user_id INT NOT NULL,      -- Solo il team leader è utente registrato
    
    -- Dati responsabile (possono differire dall'utente registrato)
    responsible_name VARCHAR(255) NOT NULL,
    responsible_email VARCHAR(255) NOT NULL,
    responsible_phone VARCHAR(20),
    
    -- File e partecipanti
    excel_filename VARCHAR(255),           -- Nome file originale
    excel_file_path VARCHAR(500),          -- Percorso salvato su server
    total_participants INT DEFAULT 0,      -- Numero totale partecipanti
    
    -- Calcoli economici
    base_price_per_person DECIMAL(8,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    discounted_price_per_person DECIMAL(8,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    
    -- Pagamento
    payment_method ENUM('card', 'bank_transfer', 'invoice') DEFAULT 'card',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_transaction_id VARCHAR(255) NULL,
    payment_date DATETIME NULL,
    
    -- Stato iscrizione
    status ENUM('draft', 'submitted', 'confirmed', 'cancelled') DEFAULT 'draft',
    
    -- Note e metadati
    notes TEXT NULL,
    admin_notes TEXT NULL,
    
    -- Timestamp
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    confirmed_at DATETIME NULL,
    
    -- Indici e relazioni
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (responsible_user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_team_event (team_id, event_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella partecipanti (dati da Excel, NO utenti registrati)
CREATE TABLE IF NOT EXISTS team_collective_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collective_registration_id INT NOT NULL,
    
    -- Dati partecipante da Excel
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(255),                    -- Opzionale
    data_nascita DATE,
    sesso ENUM('M', 'F', 'A') NULL,       -- A = Altro
    codice_fiscale VARCHAR(16),
    telefono VARCHAR(20),
    cellulare VARCHAR(20),
    
    -- Dati aggiuntivi
    citta VARCHAR(100),
    provincia VARCHAR(2),
    nazionalita VARCHAR(2) DEFAULT 'IT',
    
    -- Dati sportivi
    categoria_agonistica VARCHAR(50),
    tessera_federale VARCHAR(50),
    società_appartenenza VARCHAR(255),
    
    -- Metadati
    row_number INT,                        -- Riga originale nel file Excel
    notes TEXT NULL,
    
    -- Stato partecipante
    status ENUM('active', 'withdrawn', 'disqualified') DEFAULT 'active',
    
    -- Timestamp
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Relazioni
    FOREIGN KEY (collective_registration_id) REFERENCES team_collective_registrations(id) ON DELETE CASCADE,
    
    INDEX idx_collective_reg (collective_registration_id),
    INDEX idx_name (cognome, nome),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella per tracciare sconti applicati automaticamente
CREATE TABLE IF NOT EXISTS collective_discount_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    min_participants INT NOT NULL,
    max_participants INT NULL,             -- NULL = senza limite superiore
    discount_percentage DECIMAL(5,2) NOT NULL,
    description VARCHAR(255),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_participants_range (min_participants, max_participants),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserimento regole sconto di default
INSERT INTO collective_discount_rules (min_participants, max_participants, discount_percentage, description) VALUES
(5, 9, 5.00, 'Sconto 5% per gruppi da 5 a 9 partecipanti'),
(10, 19, 10.00, 'Sconto 10% per gruppi da 10 a 19 partecipanti'),
(20, 49, 15.00, 'Sconto 15% per gruppi da 20 a 49 partecipanti'),
(50, NULL, 20.00, 'Sconto 20% per gruppi da 50 partecipanti in su');

-- Vista per statistiche rapide
CREATE VIEW team_collective_stats AS
SELECT 
    tcr.team_id,
    t.nome as team_name,
    COUNT(tcr.id) as total_registrations,
    SUM(tcr.total_participants) as total_participants,
    SUM(CASE WHEN tcr.payment_status = 'paid' THEN tcr.total_amount ELSE 0 END) as total_revenue,
    AVG(tcr.discount_percentage) as avg_discount,
    COUNT(CASE WHEN tcr.status = 'confirmed' THEN 1 END) as confirmed_registrations
FROM team_collective_registrations tcr
JOIN teams t ON tcr.team_id = t.id
GROUP BY tcr.team_id, t.nome;