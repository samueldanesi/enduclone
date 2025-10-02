<?php
/**
 * Modello UserNotification per la gestione delle notifiche utente
 */
class UserNotification {
    private $conn;
    private $table = 'user_notifications';

    public $id;
    public $user_id;
    public $message_id;
    public $event_id;
    public $subject;
    public $message;
    public $is_read;
    public $read_at;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ottieni notifiche per utente
    public function getUserNotifications($user_id, $limit = 20, $offset = 0) {
        $query = "SELECT n.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
                        e.organizer_id, u.nome as organizer_name, u.cognome as organizer_surname,
                        em.title as message_title, em.message as message_content
                 FROM " . $this->table . " n
                 JOIN events e ON n.event_id = e.event_id
                 LEFT JOIN event_messages em ON n.message_id = em.id
                 LEFT JOIN users u ON e.organizer_id = u.user_id
                 WHERE n.user_id = :user_id
                 ORDER BY n.created_at DESC
                 LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Conta notifiche non lette
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE user_id = :user_id AND is_read = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // Marca notifica come letta
    public function markAsRead($notification_id, $user_id) {
        $query = "UPDATE " . $this->table . " 
                 SET is_read = TRUE, read_at = NOW() 
                 WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Marca tutte le notifiche come lette
    public function markAllAsRead($user_id) {
        $query = "UPDATE " . $this->table . " 
                 SET is_read = TRUE, read_at = NOW() 
                 WHERE user_id = :user_id AND is_read = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Elimina notifica
    public function delete($notification_id, $user_id) {
        $query = "DELETE FROM " . $this->table . " 
                 WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Ottieni statistiche notifiche utente
    public function getUserStats($user_id) {
        $query = "SELECT 
                    COUNT(*) as total_notifications,
                    COUNT(CASE WHEN is_read = TRUE THEN 1 END) as read_notifications,
                    COUNT(CASE WHEN is_read = FALSE THEN 1 END) as unread_notifications,
                    COUNT(DISTINCT event_id) as events_with_messages,
                    MIN(created_at) as first_notification,
                    MAX(created_at) as last_notification
                 FROM " . $this->table . "
                 WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ottieni notifiche recenti (ultime 24 ore)
    public function getRecentNotifications($user_id, $hours = 24) {
        $query = "SELECT n.*, e.titolo as event_title, e.data_evento,
                        em.organizer_id, u.nome as organizer_name, u.cognome as organizer_surname
                 FROM " . $this->table . " n
                 JOIN events e ON n.event_id = e.id
                 JOIN event_messages em ON n.message_id = em.id
                 JOIN users u ON em.organizer_id = u.id
                 WHERE n.user_id = :user_id 
                 AND n.created_at >= DATE_SUB(NOW(), INTERVAL :hours HOUR)
                 ORDER BY n.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni notifiche per evento specifico
    public function getEventNotifications($user_id, $event_id) {
        $query = "SELECT n.*, e.titolo as event_title, e.data_evento,
                        em.organizer_id, u.nome as organizer_name, u.cognome as organizer_surname
                 FROM " . $this->table . " n
                 JOIN events e ON n.event_id = e.id
                 JOIN event_messages em ON n.message_id = em.id
                 JOIN users u ON em.organizer_id = u.id
                 WHERE n.user_id = :user_id AND n.event_id = :event_id
                 ORDER BY n.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cleanup vecchie notifiche (oltre X giorni)
    public function cleanupOldNotifications($days = 90) {
        $query = "DELETE FROM " . $this->table . " 
                 WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Ottieni dettagli singola notifica
    public function getNotification($notification_id, $user_id) {
        $query = "SELECT n.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
                        em.organizer_id, u.nome as organizer_name, u.cognome as organizer_surname
                 FROM " . $this->table . " n
                 JOIN events e ON n.event_id = e.id
                 JOIN event_messages em ON n.message_id = em.id
                 JOIN users u ON em.organizer_id = u.id
                 WHERE n.id = :id AND n.user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
