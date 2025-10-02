<?php
/**
 * Modello per le recensioni degli eventi
 */
class EventReview {
    private $conn;
    private $table = 'event_reviews';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Crea una nuova recensione
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                 (event_id, user_id, rating, title, content, photos, verified_participation) 
                 VALUES (:event_id, :user_id, :rating, :title, :content, :photos, :verified_participation)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':event_id', $data['event_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':rating', $data['rating']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':photos', $data['photos']);
        $stmt->bindParam(':verified_participation', $data['verified_participation']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        throw new Exception("Errore nella creazione della recensione");
    }

    /**
     * Get recensioni di un evento
     */
    public function getByEvent($event_id, $limit = null, $offset = 0) {
        $limitClause = $limit ? "LIMIT :limit OFFSET :offset" : "";
        
        $query = "SELECT r.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome,
                        u.avatar as user_avatar
                 FROM {$this->table} r
                 LEFT JOIN users u ON r.user_id = u.id
                 WHERE r.event_id = :event_id AND r.status = 'active'
                 ORDER BY r.verified_participation DESC, r.created_at DESC
                 {$limitClause}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistiche recensioni di un evento
     */
    public function getEventStats($event_id) {
        $query = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1,
                    SUM(CASE WHEN verified_participation = 1 THEN 1 ELSE 0 END) as verified_reviews
                 FROM {$this->table} 
                 WHERE event_id = :event_id AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica se l'utente può recensire l'evento
     */
    public function canUserReview($user_id, $event_id) {
        // Verifica se ha già recensito
        $query = "SELECT id FROM {$this->table} 
                 WHERE user_id = :user_id AND event_id = :event_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return false; // Già recensito
        }
        
        // Verifica se l'evento è concluso
        $eventQuery = "SELECT status, data_evento FROM events WHERE id = :event_id";
        $stmt = $this->conn->prepare($eventQuery);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $event = $stmt->fetch();
        
        if (!$event || $event['status'] !== 'completed') {
            return false; // Evento non completato
        }
        
        return true;
    }

    /**
     * Verifica se l'utente ha partecipato all'evento
     */
    public function hasUserParticipated($user_id, $event_id) {
        $query = "SELECT id FROM registrations 
                 WHERE user_id = :user_id 
                 AND event_id = :event_id 
                 AND status IN ('confirmed', 'completed')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetch() !== false;
    }

    /**
     * Aggiorna recensione
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET rating = :rating, 
                     title = :title, 
                     content = :content, 
                     photos = :photos,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':rating', $data['rating']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':photos', $data['photos']);
        
        return $stmt->execute();
    }

    /**
     * Elimina recensione
     */
    public function delete($id) {
        $query = "UPDATE {$this->table} SET status = 'deleted' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Segna recensione come utile
     */
    public function markHelpful($review_id, $user_id) {
        // Verifica se già segnata come utile
        $checkQuery = "SELECT id FROM review_helpful 
                      WHERE review_id = :review_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':review_id', $review_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return false; // Già segnata
        }
        
        // Inserisci voto utile
        $insertQuery = "INSERT INTO review_helpful (review_id, user_id) 
                       VALUES (:review_id, :user_id)";
        
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bindParam(':review_id', $review_id);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            // Aggiorna contatore
            $updateQuery = "UPDATE {$this->table} 
                           SET helpful_count = helpful_count + 1 
                           WHERE id = :review_id";
            
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bindParam(':review_id', $review_id);
            $stmt->execute();
            
            return true;
        }
        
        return false;
    }
}
?>