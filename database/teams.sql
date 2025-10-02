-- Tabella per gestire le squadre/società sportive
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    codice_fiscale VARCHAR(16) UNIQUE,
    partita_iva VARCHAR(11),
    tipo ENUM('societa', 'team', 'gruppo') DEFAULT 'team',
    indirizzo TEXT,
    citta VARCHAR(100),
    provincia VARCHAR(2),
    cap VARCHAR(5),
    telefono VARCHAR(20),
    email VARCHAR(255),
    responsabile_nome VARCHAR(100),
    responsabile_cognome VARCHAR(100),
    responsabile_email VARCHAR(255),
    responsabile_telefono VARCHAR(20),
    logo VARCHAR(255),
    sito_web VARCHAR(255),
    note TEXT,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_team_status (status),
    INDEX idx_team_name (nome),
    INDEX idx_team_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella per collegare utenti ai team
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    ruolo ENUM('manager', 'captain', 'member', 'guest') DEFAULT 'member',
    numero_tessera VARCHAR(50),
    data_tessera DATE,
    scadenza_tessera DATE,
    note TEXT,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    invited_by INT,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_team_user (team_id, user_id),
    INDEX idx_team_members_status (status),
    INDEX idx_team_members_role (ruolo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella per iscrizioni collettive
CREATE TABLE IF NOT EXISTS team_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    event_id INT NOT NULL,
    manager_id INT NOT NULL,
    nome_squadra VARCHAR(255),
    numero_partecipanti INT DEFAULT 0,
    quota_totale DECIMAL(10,2) DEFAULT 0.00,
    quota_per_persona DECIMAL(8,2) DEFAULT 0.00,
    sconto_percentuale DECIMAL(5,2) DEFAULT 0.00,
    sconto_fisso DECIMAL(8,2) DEFAULT 0.00,
    pagamento_centralizzato BOOLEAN DEFAULT TRUE,
    metodo_pagamento ENUM('card', 'transfer', 'invoice', 'cash') DEFAULT 'card',
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    note TEXT,
    file_excel VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_team_registrations_status (status),
    INDEX idx_team_registrations_event (event_id),
    INDEX idx_team_registrations_team (team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella per collegare le iscrizioni individuali alle iscrizioni di squadra
CREATE TABLE IF NOT EXISTS team_registration_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_registration_id INT NOT NULL,
    registration_id INT NOT NULL,
    user_id INT NOT NULL,
    posizione_squadra INT,
    categoria_squadra VARCHAR(50),
    ruolo_squadra ENUM('captain', 'member', 'reserve') DEFAULT 'member',
    note TEXT,
    
    FOREIGN KEY (team_registration_id) REFERENCES team_registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_team_reg_user (team_registration_id, user_id),
    INDEX idx_team_reg_members_position (posizione_squadra)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
