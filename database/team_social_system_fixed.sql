-- Migrazione per il sistema sociale dei team SportEvents
-- Aggiorna le tabelle esistenti e crea le nuove per supportare il sistema sociale

-- Aggiorna tabella teams per supportare il sistema sociale
ALTER TABLE teams 
ADD COLUMN IF NOT EXISTS categoria_eventi VARCHAR(100) AFTER nome,
ADD COLUMN IF NOT EXISTS visibilita ENUM('pubblico', 'privato') DEFAULT 'pubblico' AFTER categoria_eventi,
ADD COLUMN IF NOT EXISTS descrizione TEXT AFTER visibilita;

-- Tabella per i messaggi dei team (chat)
CREATE TABLE IF NOT EXISTS team_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    tipo ENUM('messaggio', 'richiesta_evento') DEFAULT 'messaggio',
    messaggio TEXT NOT NULL,
    event_id INT NULL,
    data_invio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modificato BOOLEAN DEFAULT FALSE,
    data_modifica TIMESTAMP NULL,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_team_messages (team_id, data_invio),
    INDEX idx_user_messages (user_id, data_invio)
);

-- Tabella per le richieste di eventi dei team
CREATE TABLE IF NOT EXISTS team_event_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    event_id INT NOT NULL,
    admin_user_id INT NOT NULL,
    messaggio TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    discount_code VARCHAR(20) NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    data_richiesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_risposta TIMESTAMP NULL,
    scadenza_risposta TIMESTAMP NULL,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_team_requests (team_id, status),
    INDEX idx_event_requests (event_id, status),
    UNIQUE KEY unique_team_event_request (team_id, event_id, status)
);

-- Tabella per le partecipazioni agli eventi tramite team
CREATE TABLE IF NOT EXISTS team_event_participations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_id INT NULL,
    status ENUM('interested', 'confirmed', 'registered') DEFAULT 'interested',
    data_adesione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES team_event_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE SET NULL,
    INDEX idx_request_participations (request_id, status),
    INDEX idx_user_participations (user_id, status),
    UNIQUE KEY unique_user_request (request_id, user_id)
);

-- Tabella per le richieste di adesione ai team
CREATE TABLE IF NOT EXISTS team_join_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    messaggio TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    data_richiesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_risposta TIMESTAMP NULL,
    reviewed_by INT NULL,
    note_admin TEXT,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_team_request (team_id, user_id, status),
    INDEX idx_team_status (team_id, status),
    INDEX idx_user_requests (user_id, status)
);

-- Aggiorna tabella discount_codes per supportare codici generati dai team
ALTER TABLE discount_codes 
ADD COLUMN IF NOT EXISTS team_id INT AFTER event_id,
ADD COLUMN IF NOT EXISTS generato_automaticamente BOOLEAN DEFAULT FALSE AFTER utilizzi_attuali,
ADD COLUMN IF NOT EXISTS team_request_id INT NULL AFTER team_id;

-- Aggiungi foreign key per team_id (se non esiste già)
SET @exist := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
               WHERE CONSTRAINT_SCHEMA = DATABASE() 
               AND TABLE_NAME = 'discount_codes' 
               AND CONSTRAINT_NAME = 'discount_codes_ibfk_team');
               
SET @sql = IF(@exist = 0, 
    'ALTER TABLE discount_codes ADD FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE',
    'SELECT "Foreign key already exists"');
    
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tabella per tracciare l'uso dei codici sconto generati dinamicamente
CREATE TABLE IF NOT EXISTS team_discount_uses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    discount_code_id INT NOT NULL,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    data_utilizzo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_code_id) REFERENCES discount_codes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_use (team_id, discount_code_id, user_id, event_id)
);

-- Inserisci alcuni dati di esempio per testare il sistema
INSERT IGNORE INTO teams (nome, categoria_eventi, visibilita, descrizione, email, telefono, status) VALUES
('Runners Milano', 'running,trail', 'pubblico', 'Gruppo di appassionati di corsa a Milano', 'info@runnersmilano.it', '02123456789', 'active'),
('Bikers Roma', 'ciclismo,mtb', 'privato', 'Team ciclistico romano per eventi su strada e mountain bike', 'contact@bikersroma.it', '06987654321', 'active'),
('Triathlon Torino', 'triathlon,nuoto', 'pubblico', 'Società sportiva specializzata in triathlon', 'info@triathlontorino.it', '011555666777', 'active');

-- Commenti finali
-- La migrazione aggiunge:
-- 1. Supporto per categorie eventi e visibilità nei team
-- 2. Sistema di chat per i team
-- 3. Richieste di eventi con codici sconto automatici
-- 4. Gestione delle partecipazioni agli eventi tramite team
-- 5. Sistema di richieste di adesione ai team
-- 6. Tracciamento dell'uso dei codici sconto
