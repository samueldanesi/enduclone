-- Database: sportevents_db
-- Creazione tabelle per l'applicazione SportEvents

-- Tabella utenti
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    data_nascita DATE,
    sesso ENUM('M', 'F', 'altro') NOT NULL,
    cellulare VARCHAR(20),
    user_type ENUM('participant', 'organizer', 'admin') DEFAULT 'participant',
    certificato_medico VARCHAR(255) NULL,
    tessera_affiliazione VARCHAR(255) NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_user_type (user_type),
    INDEX idx_status (status)
);

-- Tabella eventi
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    titolo VARCHAR(255) NOT NULL,
    descrizione TEXT,
    data_evento DATETIME NOT NULL,
    luogo_partenza VARCHAR(255) NOT NULL,
    categoria VARCHAR(100),
    sport VARCHAR(100) NOT NULL,
    disciplina VARCHAR(100),
    prezzo_base DECIMAL(8,2) DEFAULT 0.00,
    capienza_massima INT NOT NULL,
    immagine VARCHAR(255) NULL,
    file_gpx VARCHAR(255) NULL,
    altimetria INT NULL,
    lunghezza_km DECIMAL(6,2) NULL,
    status ENUM('draft', 'published', 'closed', 'completed', 'cancelled') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_organizer (organizer_id),
    INDEX idx_data_evento (data_evento),
    INDEX idx_sport (sport),
    INDEX idx_status (status),
    INDEX idx_categoria (categoria)
);

-- Tabella iscrizioni
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    prezzo_pagato DECIMAL(8,2) NOT NULL,
    metodo_pagamento VARCHAR(50),
    transaction_id VARCHAR(255),
    codice_sconto VARCHAR(50) NULL,
    sconto_applicato DECIMAL(8,2) DEFAULT 0.00,
    status ENUM('pending', 'confirmed', 'cancelled', 'refunded') DEFAULT 'pending',
    pettorale_numero INT NULL,
    note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_event (user_id, event_id),
    INDEX idx_user (user_id),
    INDEX idx_event (event_id),
    INDEX idx_status (status)
);

-- Tabella categorie di prezzo (price steps)
CREATE TABLE IF NOT EXISTS price_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    prezzo DECIMAL(8,2) NOT NULL,
    data_inizio DATETIME NOT NULL,
    data_fine DATETIME NOT NULL,
    capienza_massima INT NULL,
    attiva BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event (event_id),
    INDEX idx_date_range (data_inizio, data_fine)
);

-- Tabella codici sconto
CREATE TABLE IF NOT EXISTS discount_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codice VARCHAR(50) UNIQUE NOT NULL,
    event_id INT NULL, -- NULL = valido per tutti gli eventi
    tipo ENUM('percentage', 'fixed') NOT NULL,
    valore DECIMAL(8,2) NOT NULL,
    utilizzi_massimi INT DEFAULT 1,
    utilizzi_attuali INT DEFAULT 0,
    data_inizio DATETIME NOT NULL,
    data_fine DATETIME NOT NULL,
    attivo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_codice (codice),
    INDEX idx_event (event_id),
    INDEX idx_date_range (data_inizio, data_fine)
);

-- Tabella squadre/società
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    telefono VARCHAR(20),
    indirizzo TEXT,
    referente_nome VARCHAR(100),
    referente_cognome VARCHAR(100),
    codice_affiliazione VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nome (nome),
    INDEX idx_status (status)
);

-- Tabella membri squadre
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    ruolo ENUM('member', 'captain', 'admin') DEFAULT 'member',
    data_iscrizione DATE NOT NULL,
    attivo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_team_user (team_id, user_id),
    INDEX idx_team (team_id),
    INDEX idx_user (user_id)
);

-- Tabella risultati
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    position_overall INT,
    position_category INT,
    tempo_finale TIME,
    categoria_risultato VARCHAR(100),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_user (event_id, user_id),
    INDEX idx_event (event_id),
    INDEX idx_user (user_id),
    INDEX idx_position (position_overall)
);

-- Tabella foto/media
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NULL, -- Se associata a un partecipante specifico
    tipo ENUM('photo', 'video', 'document') NOT NULL,
    filename VARCHAR(255) NOT NULL,
    path VARCHAR(500) NOT NULL,
    caption TEXT NULL,
    pubblico BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event (event_id),
    INDEX idx_user (user_id),
    INDEX idx_tipo (tipo)
);

-- Tabella recensioni
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    titolo VARCHAR(255),
    commento TEXT,
    approvato BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_user_review (event_id, user_id),
    INDEX idx_event (event_id),
    INDEX idx_user (user_id),
    INDEX idx_rating (rating)
);

-- Tabella messaggi di servizio
CREATE TABLE IF NOT EXISTS service_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    oggetto VARCHAR(255) NOT NULL,
    messaggio TEXT NOT NULL,
    inviato_da INT NOT NULL,
    data_invio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    destinatari_count INT DEFAULT 0,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (inviato_da) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_event (event_id),
    INDEX idx_data_invio (data_invio)
);

-- Tabella add-on acquistabili
CREATE TABLE IF NOT EXISTS event_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT,
    prezzo DECIMAL(8,2) NOT NULL,
    quantita_disponibile INT NULL,
    tipo ENUM('physical', 'service', 'digital') DEFAULT 'physical',
    attivo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event (event_id),
    INDEX idx_attivo (attivo)
);

-- Tabella acquisti add-on
CREATE TABLE IF NOT EXISTS addon_purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    addon_id INT NOT NULL,
    quantita INT DEFAULT 1,
    prezzo_unitario DECIMAL(8,2) NOT NULL,
    prezzo_totale DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (addon_id) REFERENCES event_addons(id) ON DELETE CASCADE,
    INDEX idx_registration (registration_id),
    INDEX idx_addon (addon_id)
);

-- Tabella notifiche
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    titolo VARCHAR(255) NOT NULL,
    messaggio TEXT NOT NULL,
    event_id INT NULL,
    letta BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_letta (letta),
    INDEX idx_created_at (created_at)
);

-- Tabella sessioni (per gestire login persistenti)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_expires (expires_at)
);

-- Inserimento dati di esempio

-- Utente admin
INSERT INTO users (nome, cognome, email, password, data_nascita, sesso, cellulare, user_type, status, email_verified) VALUES
('Admin', 'Sistema', 'admin@sportevents.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1990-01-01', 'M', '+39 123 456 7890', 'admin', 'active', TRUE);

-- Organizzatore di esempio
INSERT INTO users (nome, cognome, email, password, data_nascita, sesso, cellulare, user_type, status, email_verified) VALUES
('Mario', 'Rossi', 'organizer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1985-03-15', 'M', '+39 345 678 9012', 'organizer', 'active', TRUE);

-- Partecipante di esempio
INSERT INTO users (nome, cognome, email, password, data_nascita, sesso, cellulare, user_type, status, email_verified) VALUES
('Giulia', 'Bianchi', 'participant@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1992-07-20', 'F', '+39 333 444 5555', 'participant', 'active', TRUE);

-- Eventi di esempio
INSERT INTO events (organizer_id, titolo, descrizione, data_evento, luogo_partenza, categoria, sport, disciplina, prezzo_base, capienza_massima, lunghezza_km, altimetria, status) VALUES
(2, 'Maratona di Milano', 'La classica maratona urbana di Milano attraverso i luoghi più belli della città', '2024-04-14 09:00:00', 'Castello Sforzesco, Milano', 'Maratona', 'running', 'strada', 45.00, 15000, 42.195, 50, 'published'),
(2, 'Gran Fondo delle Dolomiti', 'Granfondo ciclistica tra le meravigliose Dolomiti', '2024-07-15 08:00:00', 'Cortina d\'Ampezzo', 'Gran Fondo', 'cycling', 'strada', 80.00, 3000, 135.5, 2800, 'published'),
(2, 'Triathlon Lago di Garda', 'Triathlon olimpico sulle sponde del Lago di Garda', '2024-09-10 07:30:00', 'Riva del Garda', 'Olimpico', 'triathlon', 'olimpico', 120.00, 1500, 51.5, 400, 'published');

-- Categorie di prezzo
INSERT INTO price_categories (event_id, nome, prezzo, data_inizio, data_fine, capienza_massima) VALUES
(1, 'Early Bird', 35.00, '2024-01-01 00:00:00', '2024-02-29 23:59:59', 5000),
(1, 'Regular', 45.00, '2024-03-01 00:00:00', '2024-04-10 23:59:59', NULL),
(1, 'Last Minute', 55.00, '2024-04-11 00:00:00', '2024-04-13 23:59:59', NULL);

-- Codici sconto
INSERT INTO discount_codes (codice, event_id, tipo, valore, utilizzi_massimi, data_inizio, data_fine) VALUES
('WELCOME20', NULL, 'percentage', 20.00, 100, '2024-01-01 00:00:00', '2024-12-31 23:59:59'),
('TEAM50', 1, 'fixed', 50.00, 50, '2024-01-01 00:00:00', '2024-04-13 23:59:59');

-- Squadra di esempio
INSERT INTO teams (nome, email, telefono, referente_nome, referente_cognome, codice_affiliazione) VALUES
('ASD Running Club Milano', 'info@runningclubmilano.it', '+39 02 1234567', 'Luca', 'Verdi', 'RC001');

-- Membro squadra
INSERT INTO team_members (team_id, user_id, ruolo, data_iscrizione) VALUES
(1, 3, 'member', '2024-01-15');

-- Add-on per eventi
INSERT INTO event_addons (event_id, nome, descrizione, prezzo, quantita_disponibile, tipo) VALUES
(1, 'Maglia Tecnica', 'Maglia tecnica ufficiale dell\'evento', 25.00, 1000, 'physical'),
(1, 'Pacco Gara Premium', 'Pacco gara con prodotti premium', 15.00, 5000, 'physical'),
(2, 'Foto Professionali', 'Servizio fotografico durante la gara', 30.00, NULL, 'service');
