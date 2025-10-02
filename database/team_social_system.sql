-- Migrazione per il nuovo sistema Team Social
-- Fase 1: Database + Chat Base

-- Aggiorna tabella teams per supportare il sistema sociale
ALTER TABLE teams 
ADD COLUMN IF NOT EXISTS categoria_eventi VARCHAR(100) AFTER nome,
ADD COLUMN IF NOT EXISTS visibilita ENUM('pubblico', 'privato') DEFAULT 'pubblico' AFTER categoria_eventi,
ADD COLUMN IF NOT EXISTS descrizione TEXT AFTER visibilita;

-- Chat del team
CREATE TABLE team_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('normale', 'richiesta_evento', 'risposta_evento') DEFAULT 'normale',
    event_id INT NULL, -- Se è una richiesta evento
    parent_message_id INT NULL, -- Per risposte a richieste
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_message_id) REFERENCES team_messages(id) ON DELETE CASCADE,
    INDEX idx_team_created (team_id, created_at),
    INDEX idx_message_type (message_type),
    INDEX idx_event_messages (event_id, message_type)
);

-- Richieste eventi con partecipazioni
CREATE TABLE team_event_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT NOT NULL,
    admin_id INT NOT NULL,
    event_id INT NOT NULL,
    message_id INT NOT NULL, -- Collegamento al messaggio in chat
    target_participants INT NOT NULL DEFAULT 10,
    current_participants INT DEFAULT 0,
    deadline DATETIME NOT NULL,
    status ENUM('aperta', 'completata', 'scaduta', 'annullata') DEFAULT 'aperta',
    discount_code VARCHAR(20) NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES team_messages(id) ON DELETE CASCADE,
    INDEX idx_team_status (team_id, status),
    INDEX idx_deadline (deadline),
    INDEX idx_discount_code (discount_code)
);

-- Partecipazioni alle richieste
CREATE TABLE team_event_participations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('interessato', 'confermato', 'ritirato') DEFAULT 'interessato',
    responded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    FOREIGN KEY (request_id) REFERENCES team_event_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_request (request_id, user_id),
    INDEX idx_request_status (request_id, status)
);

-- Richieste di adesione ai team privati
CREATE TABLE team_join_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_team_request (team_id, user_id, status),
    INDEX idx_team_status (team_id, status),
    INDEX idx_user_requests (user_id, status)
);

-- Codici sconto team (estensione della tabella discount_codes esistente)
ALTER TABLE discount_codes 
ADD COLUMN team_id INT NULL AFTER event_id,
ADD COLUMN max_team_uses INT NULL AFTER max_uses,
ADD COLUMN team_request_id INT NULL AFTER team_id,
ADD FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
ADD FOREIGN KEY (team_request_id) REFERENCES team_event_requests(id) ON DELETE CASCADE;

-- Tracciamento uso codici per team
CREATE TABLE team_discount_uses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    discount_code_id INT NOT NULL,
    user_id INT NOT NULL,
    team_id INT NOT NULL,
    request_id INT NOT NULL,
    registration_id INT NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discount_code_id) REFERENCES discount_codes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES team_event_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_code_use (discount_code_id, user_id),
    INDEX idx_team_request (team_id, request_id)
);

-- Notifiche team (estensione sistema notifiche esistente)
ALTER TABLE user_notifications 
ADD COLUMN team_id INT NULL AFTER event_id,
ADD COLUMN team_message_id INT NULL AFTER team_id,
ADD COLUMN team_request_id INT NULL AFTER team_message_id,
ADD FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
ADD FOREIGN KEY (team_message_id) REFERENCES team_messages(id) ON DELETE CASCADE,
ADD FOREIGN KEY (team_request_id) REFERENCES team_event_requests(id) ON DELETE CASCADE;
