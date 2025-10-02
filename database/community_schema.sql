-- Schema per la sezione Community e Post-gara
-- Eseguire questo file per creare le tabelle necessarie

-- Tabella per i post della community
CREATE TABLE IF NOT EXISTS community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NULL, -- NULL = post universale, NOT NULL = post specifico evento
    type ENUM('text', 'photo', 'video', 'result') DEFAULT 'text',
    title VARCHAR(255) NULL,
    content TEXT NOT NULL,
    media_url VARCHAR(500) NULL, -- path per foto/video
    media_caption TEXT NULL,
    visibility ENUM('public', 'participants', 'private') DEFAULT 'public',
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    status ENUM('active', 'hidden', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_event_id (event_id),
    INDEX idx_created_at (created_at),
    INDEX idx_status (status),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Tabella per i commenti ai post
CREATE TABLE IF NOT EXISTS community_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_comment_id INT NULL, -- per risposte ai commenti
    content TEXT NOT NULL,
    media_url VARCHAR(500) NULL, -- per allegati nei commenti
    likes_count INT DEFAULT 0,
    status ENUM('active', 'hidden', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_post_id (post_id),
    INDEX idx_user_id (user_id),
    INDEX idx_parent_comment (parent_comment_id),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_comment_id) REFERENCES community_comments(id) ON DELETE CASCADE
);

-- Tabella per i likes ai post e commenti
CREATE TABLE IF NOT EXISTS community_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    target_type ENUM('post', 'comment') NOT NULL,
    target_id INT NOT NULL, -- post_id o comment_id
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_like (user_id, target_type, target_id),
    INDEX idx_target (target_type, target_id),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabella per le classifiche degli eventi
CREATE TABLE IF NOT EXISTS event_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_id INT NULL, -- collegamento alla registrazione
    position INT NOT NULL,
    category VARCHAR(100) NULL, -- categoria di gara (M/F, fasce età, etc.)
    time_result VARCHAR(20) NULL, -- tempo di gara (formato HH:MM:SS)
    distance_km DECIMAL(8,3) NULL,
    pace VARCHAR(10) NULL, -- passo medio
    points INT NULL, -- punti assegnati
    notes TEXT NULL,
    verified BOOLEAN DEFAULT FALSE, -- risultato verificato dall'organizzatore
    source VARCHAR(100) NULL, -- sistema di cronometraggio utilizzato
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_event_id (event_id),
    INDEX idx_user_id (user_id),
    INDEX idx_position (position),
    INDEX idx_category (category),
    INDEX idx_verified (verified),
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabella per le recensioni degli eventi
CREATE TABLE IF NOT EXISTS event_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    photos TEXT NULL, -- JSON array di foto
    verified_participation BOOLEAN DEFAULT FALSE, -- utente ha partecipato
    helpful_count INT DEFAULT 0,
    status ENUM('active', 'hidden', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_review (event_id, user_id),
    INDEX idx_event_id (event_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabella per segnalare recensioni utili
CREATE TABLE IF NOT EXISTS review_helpful (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_helpful (review_id, user_id),
    
    FOREIGN KEY (review_id) REFERENCES event_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabella per le foto degli eventi (galleria)
CREATE TABLE IF NOT EXISTS event_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL, -- chi ha caricato la foto
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    caption TEXT NULL,
    participants_tagged TEXT NULL, -- JSON array di user_id taggati
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE, -- foto in evidenza
    status ENUM('active', 'pending', 'hidden', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_event_id (event_id),
    INDEX idx_user_id (user_id),
    INDEX idx_featured (featured),
    INDEX idx_status (status),
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Inserimento di alcuni dati di esempio
INSERT IGNORE INTO community_posts (user_id, event_id, type, title, content, visibility) VALUES
(1, NULL, 'text', 'Benvenuti nella community!', 'Questo è il primo post della community SportEvents. Qui potete condividere le vostre esperienze sportive!', 'public'),
(1, 1, 'text', 'Preparazione Maratona', 'Come state preparando la maratona di Milano? Condividete i vostri programmi di allenamento!', 'public');

-- Trigger per aggiornare automaticamente i contatori
DELIMITER $$

CREATE TRIGGER update_post_comments_count 
AFTER INSERT ON community_comments
FOR EACH ROW
BEGIN
    UPDATE community_posts 
    SET comments_count = comments_count + 1 
    WHERE id = NEW.post_id;
END$$

CREATE TRIGGER update_post_likes_count 
AFTER INSERT ON community_likes
FOR EACH ROW
BEGIN
    IF NEW.target_type = 'post' THEN
        UPDATE community_posts 
        SET likes_count = likes_count + 1 
        WHERE id = NEW.target_id;
    END IF;
END$$

CREATE TRIGGER decrease_post_likes_count 
AFTER DELETE ON community_likes
FOR EACH ROW
BEGIN
    IF OLD.target_type = 'post' THEN
        UPDATE community_posts 
        SET likes_count = likes_count - 1 
        WHERE id = OLD.target_id;
    END IF;
END$$

DELIMITER ;

-- Indici per migliorare le performance
CREATE INDEX idx_posts_timeline ON community_posts(event_id, created_at DESC, status);
CREATE INDEX idx_comments_thread ON community_comments(post_id, parent_comment_id, created_at);
CREATE INDEX idx_results_ranking ON event_results(event_id, category, position);