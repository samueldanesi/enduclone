<?php
/**
 * API Controller per le notifiche
 */
class NotificationApiController {
    private $conn;
    private $notification;

    public function __construct($db) {
        $this->conn = $db;
        require_once __DIR__ . '/../models/UserNotification.php';
        $this->notification = new UserNotification($db);
    }

    /**
     * Conta notifiche non lette per utente corrente
     */
    public function count() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            return;
        }

        try {
            $count = $this->notification->getUnreadCount($_SESSION['user_id']);
            echo json_encode(['count' => (int)$count]);
        } catch (Exception $e) {
            echo json_encode(['count' => 0]);
        }
    }

    /**
     * Ottieni lista notifiche per utente
     */
    public function list() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['notifications' => []]);
            return;
        }

        try {
            $limit = min(20, (int)($_GET['limit'] ?? 10));
            $notifications = $this->notification->getUserNotifications($_SESSION['user_id'], $limit);
            echo json_encode(['notifications' => $notifications]);
        } catch (Exception $e) {
            echo json_encode(['notifications' => []]);
        }
    }

    /**
     * Segna notifica come letta
     */
    public function markAsRead() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            return;
        }

        $notificationId = $_POST['notification_id'] ?? null;
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'ID notifica mancante']);
            return;
        }

        try {
            $result = $this->notification->markAsRead($notificationId, $_SESSION['user_id']);
            echo json_encode(['success' => $result]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Errore server']);
        }
    }

    /**
     * Segna tutte le notifiche come lette
     */
    public function markAllAsRead() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            return;
        }

        try {
            $result = $this->notification->markAllAsRead($_SESSION['user_id']);
            echo json_encode(['success' => $result]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Errore server']);
        }
    }
}
?>