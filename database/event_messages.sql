-- Tabella per messaggi di servizio agli iscritti
CREATE TABLE IF NOT EXISTS event_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    organizer_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recipients_count INT DEFAULT 0,
    delivery_status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_event_id (event_id),
    INDEX idx_organizer_id (organizer_id),
    INDEX idx_sent_at (sent_at)
);

-- Tabella per tracciare l'invio a singoli utenti
CREATE TABLE IF NOT EXISTS message_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_status ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
    opened_at TIMESTAMP NULL,
    
    FOREIGN KEY (message_id) REFERENCES event_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_message_user (message_id, user_id),
    INDEX idx_message_id (message_id),
    INDEX idx_user_id (user_id),
    INDEX idx_delivery_status (delivery_status)
);
