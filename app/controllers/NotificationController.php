<?php
require_once __DIR__ . '/../models/UserNotification.php';

/**
 * Controller per la gestione delle notifiche utente
 */
class NotificationController {
    private $conn;
    private $notification;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->notification = new UserNotification($this->conn);
    }

    // Mostra tutte le notifiche dell'utente
    public function index() {
        // Verifica autenticazione
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Ottieni notifiche
        $notifications = $this->notification->getUserNotifications($_SESSION['user_id'], $limit, $offset);
        
        // Ottieni statistiche
        $stats = $this->notification->getUserStats($_SESSION['user_id']);
        
        // Conta totale per paginazione
        $total_notifications = $stats['total_notifications'] ?? 0;
        $total_pages = ceil($total_notifications / $limit);

        $pageTitle = "Le Mie Notifiche - SportEvents";
        require_once __DIR__ . '/../views/notifications/index.php';
    }

    // Visualizza singola notifica
    public function show($notification_id) {
        // Verifica autenticazione
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Ottieni notifica
        $notification = $this->notification->getNotification($notification_id, $_SESSION['user_id']);
        
        if (!$notification) {
            header('Location: /404');
            exit;
        }

        // Marca come letta
        $this->notification->markAsRead($notification_id, $_SESSION['user_id']);

        $pageTitle = $notification['subject'] . " - SportEvents";
        require_once __DIR__ . '/../views/notifications/show.php';
    }

    // API per contare notifiche non lette
    public function getUnreadCount() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $count = $this->notification->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
    }

    // Marca tutte come lette via AJAX
    public function markAllRead() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
            exit;
        }

        $success = $this->notification->markAllAsRead($_SESSION['user_id']);
        echo json_encode(['success' => $success]);
    }

    // Marca singola come letta via AJAX
    public function markRead($notification_id) {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
            exit;
        }

        $success = $this->notification->markAsRead($notification_id, $_SESSION['user_id']);
        echo json_encode(['success' => $success]);
    }

    // Elimina notifica via AJAX
    public function delete($notification_id) {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
            exit;
        }

        $success = $this->notification->delete($notification_id, $_SESSION['user_id']);
        echo json_encode(['success' => $success]);
    }

    // API per notifiche recenti (dropdown)
    public function getRecent() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['notifications' => []]);
            exit;
        }

        $notifications = $this->notification->getRecentNotifications($_SESSION['user_id'], 24);
        $unread_count = $this->notification->getUnreadCount($_SESSION['user_id']);

        echo json_encode([
            'notifications' => $notifications,
            'unread_count' => $unread_count
        ]);
    }

    // Widget notifiche per header
    public function widget() {
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        $unread_count = $this->notification->getUnreadCount($_SESSION['user_id']);
        $recent_notifications = $this->notification->getRecentNotifications($_SESSION['user_id'], 5);

        return [
            'unread_count' => $unread_count,
            'recent_notifications' => $recent_notifications
        ];
    }
}
?>
