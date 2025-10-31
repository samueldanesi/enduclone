-- Create only the team_join_requests table that's needed for Team model
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