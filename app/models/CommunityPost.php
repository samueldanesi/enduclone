<?php
/**
 * Modello per i post della community
 */
class CommunityPost {
    private $conn;
    private $table = 'community_posts';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Crea un nuovo post
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                 (user_id, event_id, type, title, content, media_url, media_caption) 
                 VALUES (:user_id, :event_id, :type, :title, :content, :media_url, :media_caption)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':event_id', $data['event_id']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':media_url', $data['media_url']);
        $stmt->bindParam(':media_caption', $data['media_caption']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        throw new Exception("Errore nella creazione del post");
    }

    /**
     * Get feed posts UNIVERSALI (senza evento specifico)
     */
    public function getUniversalFeed($limit = 10, $offset = 0) {
        $query = "SELECT p.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome
                 FROM {$this->table} p
                 LEFT JOIN users u ON p.user_id = u.user_id
                 WHERE p.event_id IS NULL
                 ORDER BY p.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get feed posts per EVENTO SPECIFICO
     */
    public function getEventFeed($event_id, $limit = 10, $offset = 0) {
        $query = "SELECT p.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome,
                        e.titolo as event_title
                 FROM {$this->table} p
                 LEFT JOIN users u ON p.user_id = u.user_id
                 LEFT JOIN events e ON p.event_id = e.id
                 WHERE p.event_id = :event_id
                 ORDER BY p.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get post singolo con dettagli
     */
    public function getById($id) {
        $query = "SELECT p.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome,
                        e.titolo as event_title
                 FROM {$this->table} p
                 LEFT JOIN users u ON p.user_id = u.user_id
                 LEFT JOIN events e ON p.event_id = e.id
                 WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Toggle like su un post
     */
    public function toggleLike($user_id, $target_type, $target_id) {
        // Verifica se esiste già il like
        $checkQuery = "SELECT id FROM community_likes 
                      WHERE user_id = :user_id 
                      AND target_type = :target_type 
                      AND target_id = :target_id";
        
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':target_type', $target_type);
        $stmt->bindParam(':target_id', $target_id);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            // Rimuovi like esistente
            $deleteQuery = "DELETE FROM community_likes 
                           WHERE user_id = :user_id 
                           AND target_type = :target_type 
                           AND target_id = :target_id";
            
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':target_type', $target_type);
            $stmt->bindParam(':target_id', $target_id);
            $stmt->execute();
            
            $action = 'unliked';
        } else {
            // Aggiungi nuovo like
            $insertQuery = "INSERT INTO community_likes (user_id, target_type, target_id) 
                           VALUES (:user_id, :target_type, :target_id)";
            
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':target_type', $target_type);
            $stmt->bindParam(':target_id', $target_id);
            $stmt->execute();
            
            $action = 'liked';
        }
        
        // Get nuovo conteggio likes
        $countQuery = "SELECT likes_count FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($countQuery);
        $stmt->bindParam(':id', $target_id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return [
            'success' => true,
            'action' => $action,
            'likes_count' => $result['likes_count']
        ];
    }

    /**
     * Verifica se l'utente ha messo like
     */
    public function hasUserLiked($post_id, $user_id) {
        $query = "SELECT id FROM community_likes 
                 WHERE user_id = :user_id 
                 AND target_type = 'post' 
                 AND target_id = :post_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        
        return $stmt->fetch() !== false;
    }

    /**
     * Get posts di un utente
     */
    public function getByUser($user_id, $limit = 10, $offset = 0) {
        $query = "SELECT p.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome,
                        e.titolo as event_title
                 FROM {$this->table} p
                 LEFT JOIN users u ON p.user_id = u.user_id
                 LEFT JOIN events e ON p.event_id = e.id
                 WHERE p.user_id = :user_id
                 ORDER BY p.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aggiorna post
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET title = :title, 
                     content = :content, 
                     media_caption = :media_caption,
                     visibility = :visibility,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':media_caption', $data['media_caption']);
        $stmt->bindParam(':visibility', $data['visibility']);
        
        return $stmt->execute();
    }

    /**
     * Elimina post (soft delete)
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>