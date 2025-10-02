<?php
/**
 * Modello per i commenti della community
 */
class CommunityComment {
    private $conn;
    private $table = 'community_comments';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Crea un nuovo commento
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                 (post_id, user_id, parent_comment_id, content, media_url) 
                 VALUES (:post_id, :user_id, :parent_comment_id, :content, :media_url)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':post_id', $data['post_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':parent_comment_id', $data['parent_comment_id']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':media_url', $data['media_url'] ?? null);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        throw new Exception("Errore nella creazione del commento");
    }

    /**
     * Get commenti di un post
     */
    public function getByPost($post_id, $limit = null) {
        $limitClause = $limit ? "LIMIT :limit" : "";
        
        $query = "SELECT c.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome
                 FROM {$this->table} c
                 LEFT JOIN users u ON c.user_id = u.id
                 WHERE c.post_id = :post_id 
                 AND c.parent_comment_id IS NULL
                 ORDER BY c.created_at ASC
                 {$limitClause}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get risposte per ogni commento
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getReplies($comment['id']);
            $comment['user_has_liked'] = $this->hasUserLiked($comment['id'], $_SESSION['user_id'] ?? 0);
        }
        
        return $comments;
    }

    /**
     * Get risposte a un commento
     */
    public function getReplies($parent_comment_id, $limit = 10) {
        $query = "SELECT c.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome,
                        u.avatar as user_avatar
                 FROM {$this->table} c
                 LEFT JOIN users u ON c.user_id = u.id
                 WHERE c.parent_comment_id = :parent_comment_id 
                 AND c.status = 'active'
                 ORDER BY c.created_at ASC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':parent_comment_id', $parent_comment_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        
        $stmt->execute();
        $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Aggiungi info sui like per ogni risposta
        foreach ($replies as &$reply) {
            $reply['user_has_liked'] = $this->hasUserLiked($reply['id'], $_SESSION['user_id'] ?? 0);
        }
        
        return $replies;
    }

    /**
     * Get commento singolo
     */
    public function getById($id) {
        $query = "SELECT c.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome,
                        u.avatar as user_avatar
                 FROM {$this->table} c
                 LEFT JOIN users u ON c.user_id = u.id
                 WHERE c.id = :id AND c.status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($comment) {
            $comment['user_has_liked'] = $this->hasUserLiked($id, $_SESSION['user_id'] ?? 0);
        }
        
        return $comment;
    }

    /**
     * Verifica se l'utente ha messo like al commento
     */
    public function hasUserLiked($comment_id, $user_id) {
        $query = "SELECT id FROM community_likes 
                 WHERE user_id = :user_id 
                 AND target_type = 'comment' 
                 AND target_id = :comment_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':comment_id', $comment_id);
        $stmt->execute();
        
        return $stmt->fetch() !== false;
    }

    /**
     * Aggiorna commento
     */
    public function update($id, $content) {
        $query = "UPDATE {$this->table} 
                 SET content = :content, 
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':content', $content);
        
        return $stmt->execute();
    }

    /**
     * Elimina commento (soft delete)
     */
    public function delete($id) {
        $query = "UPDATE {$this->table} SET status = 'deleted' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Conta commenti di un post
     */
    public function countByPost($post_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                 WHERE post_id = :post_id AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }
}
?>