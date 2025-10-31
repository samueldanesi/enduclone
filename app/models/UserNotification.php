<?php
/**
 * Modello UserNotification per la gestione delle notifiche utente
 */
class UserNotification {
    private $conn;
    private $table = 'notifications';

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

    private function columnExists($table, $column) {
        try {
            $stmt = $this->conn->prepare("SHOW COLUMNS FROM `{$table}` LIKE :col");
            $stmt->bindValue(':col', $column);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // Ottieni notifiche per utente
    public function getUserNotifications($user_id, $limit = 20, $offset = 0) {
        $hasMessageId = $this->columnExists($this->table, 'message_id');
        $selectMsg = $hasMessageId 
            ? "em.subject as message_title, em.message as message_content"
            : "n.titolo as message_title, n.messaggio as message_content";

        $query = "SELECT n.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
                        e.organizer_id, u.nome as organizer_name, u.cognome as organizer_surname,
                        {$selectMsg}
                 FROM " . $this->table . " n
                 LEFT JOIN events e ON n.event_id = e.id
                 " . ($hasMessageId ? "LEFT JOIN event_messages em ON n.message_id = em.id" : "") . "
                 LEFT JOIN users u ON e.organizer_id = u.id
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
    $readCol = $this->columnExists($this->table, 'is_read') ? 'is_read' : ($this->columnExists($this->table, 'letta') ? 'letta' : null);
    $readCond = $readCol ? "$readCol = FALSE" : "1=1";
    $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
         WHERE user_id = :user_id AND {$readCond}";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // Marca notifica come letta
    public function markAsRead($notification_id, $user_id) {
    $readCol = $this->columnExists($this->table, 'is_read') ? 'is_read' : ($this->columnExists($this->table, 'letta') ? 'letta' : null);
    $hasReadAt = $this->columnExists($this->table, 'read_at');
    $set = $readCol ? "$readCol = TRUE" : "";
    if ($hasReadAt) { $set .= ($set ? ", " : "") . "read_at = NOW()"; }
    if (!$set) { $set = 'updated_at = NOW()'; }
    $query = "UPDATE " . $this->table . " SET {$set} WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Marca tutte le notifiche come lette
    public function markAllAsRead($user_id) {
    $readCol = $this->columnExists($this->table, 'is_read') ? 'is_read' : ($this->columnExists($this->table, 'letta') ? 'letta' : null);
    $hasReadAt = $this->columnExists($this->table, 'read_at');
    $set = $readCol ? "$readCol = TRUE" : "";
    if ($hasReadAt) { $set .= ($set ? ", " : "") . "read_at = NOW()"; }
    if (!$set) { $set = 'updated_at = NOW()'; }
    $whereUnread = $readCol ? "$readCol = FALSE" : "1=1";
    $query = "UPDATE " . $this->table . " SET {$set} WHERE user_id = :user_id AND {$whereUnread}";

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
          $readCol = $this->columnExists($this->table, 'is_read') ? 'is_read' : ($this->columnExists($this->table, 'letta') ? 'letta' : null);
          $readExpr = $readCol ? "COUNT(CASE WHEN {$readCol} = TRUE THEN 1 END)" : "0";
          $unreadExpr = $readCol ? "COUNT(CASE WHEN {$readCol} = FALSE THEN 1 END)" : "COUNT(*)";
          $query = "SELECT 
                          COUNT(*) as total_notifications,
                          {$readExpr} as read_notifications,
                          {$unreadExpr} as unread_notifications,
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
        $hasMessageId = $this->columnExists($this->table, 'message_id');
        $selectMsg = $hasMessageId 
            ? "em.subject as message_title, em.message as message_content"
            : "n.titolo as message_title, n.messaggio as message_content";
        $organizerJoin = $hasMessageId ? 'em.organizer_id' : 'e.organizer_id';
        $query = "SELECT n.*, e.titolo as event_title, e.data_evento,
                        {$selectMsg}, u.nome as organizer_name, u.cognome as organizer_surname
                 FROM " . $this->table . " n
                 LEFT JOIN events e ON n.event_id = e.id
                 " . ($hasMessageId ? "LEFT JOIN event_messages em ON n.message_id = em.id" : "") . "
                 LEFT JOIN users u ON {$organizerJoin} = u.id
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
        $hasMessageId = $this->columnExists($this->table, 'message_id');
        $selectMsg = $hasMessageId 
            ? "em.subject as message_title, em.message as message_content"
            : "n.titolo as message_title, n.messaggio as message_content";
        $organizerJoin = $hasMessageId ? 'em.organizer_id' : 'e.organizer_id';
        $query = "SELECT n.*, e.titolo as event_title, e.data_evento,
                        {$selectMsg}, u.nome as organizer_name, u.cognome as organizer_surname
                 FROM " . $this->table . " n
                 LEFT JOIN events e ON n.event_id = e.id
                 " . ($hasMessageId ? "LEFT JOIN event_messages em ON n.message_id = em.id" : "") . "
                 LEFT JOIN users u ON {$organizerJoin} = u.id
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
        $hasMessageId = $this->columnExists($this->table, 'message_id');
        $selectMsg = $hasMessageId 
            ? "em.subject as message_title, em.message as message_content"
            : "n.titolo as message_title, n.messaggio as message_content";
        $organizerJoin = $hasMessageId ? 'em.organizer_id' : 'e.organizer_id';
        $query = "SELECT n.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
                        {$selectMsg}, u.nome as organizer_name, u.cognome as organizer_surname
                 FROM " . $this->table . " n
                 LEFT JOIN events e ON n.event_id = e.id
                 " . ($hasMessageId ? "LEFT JOIN event_messages em ON n.message_id = em.id" : "") . "
                 LEFT JOIN users u ON {$organizerJoin} = u.id
                 WHERE n.id = :id AND n.user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notification_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
