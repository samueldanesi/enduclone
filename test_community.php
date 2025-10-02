<?php
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "✅ Connessione database OK\n\n";
    
    // Verifica tabelle community
    $tables = [
        'community_posts',
        'community_comments', 
        'community_likes',
        'event_results',
        'event_reviews',
        'review_helpful',
        'event_gallery'
    ];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabella $table: EXISTS\n";
        } else {
            echo "❌ Tabella $table: MISSING - Creazione in corso...\n";
            
            // Crea tabella mancante
            switch ($table) {
                case 'community_posts':
                    $sql = "CREATE TABLE community_posts (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        user_id INT NOT NULL,
                        event_id INT NULL,
                        type ENUM('general', 'event_experience', 'event_photo', 'event_video') DEFAULT 'general',
                        title VARCHAR(255) NULL,
                        content TEXT NOT NULL,
                        media_url VARCHAR(500) NULL,
                        media_caption VARCHAR(255) NULL,
                        likes_count INT DEFAULT 0,
                        comments_count INT DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
                    )";
                    break;
                    
                case 'community_comments':
                    $sql = "CREATE TABLE community_comments (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        post_id INT NOT NULL,
                        user_id INT NOT NULL,
                        parent_id INT NULL,
                        content TEXT NOT NULL,
                        likes_count INT DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (parent_id) REFERENCES community_comments(id) ON DELETE CASCADE
                    )";
                    break;
                    
                case 'community_likes':
                    $sql = "CREATE TABLE community_likes (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        user_id INT NOT NULL,
                        target_type ENUM('post', 'comment') NOT NULL,
                        target_id INT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE KEY unique_like (user_id, target_type, target_id),
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                    )";
                    break;
                    
                case 'event_results':
                    $sql = "CREATE TABLE event_results (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        event_id INT NOT NULL,
                        user_id INT NOT NULL,
                        position INT NOT NULL,
                        finish_time TIME NOT NULL,
                        pace VARCHAR(10) NULL,
                        category VARCHAR(50) NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        UNIQUE KEY unique_result (event_id, user_id)
                    )";
                    break;
                    
                case 'event_reviews':
                    $sql = "CREATE TABLE event_reviews (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        event_id INT NOT NULL,
                        user_id INT NOT NULL,
                        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                        review TEXT NOT NULL,
                        helpful_count INT DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        UNIQUE KEY unique_review (event_id, user_id)
                    )";
                    break;
                    
                case 'review_helpful':
                    $sql = "CREATE TABLE review_helpful (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        review_id INT NOT NULL,
                        user_id INT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (review_id) REFERENCES event_reviews(id) ON DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        UNIQUE KEY unique_helpful (review_id, user_id)
                    )";
                    break;
                    
                case 'event_gallery':
                    $sql = "CREATE TABLE event_gallery (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        event_id INT NOT NULL,
                        user_id INT NOT NULL,
                        media_url VARCHAR(500) NOT NULL,
                        media_type ENUM('photo', 'video') DEFAULT 'photo',
                        caption VARCHAR(255) NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                    )";
                    break;
            }
            
            if (isset($sql)) {
                $db->exec($sql);
                echo "   ✅ Tabella $table creata con successo\n";
            }
        }
    }
    
    echo "\n🎉 Setup Community completato!\n";
    
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
}
?>