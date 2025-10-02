<?php
/**
 * Controller per la gestione della Community e contenuti social
 */
class CommunityController {
    private $conn;
    private $communityPost;
    private $communityComment;
    private $eventResult;
    private $eventReview;

    public function __construct($db) {
        $this->conn = $db;
        require_once '../app/models/CommunityPost.php';
        require_once '../app/models/CommunityComment.php';
        require_once '../app/models/EventResult.php';
        require_once '../app/models/EventReview.php';
        
        $this->communityPost = new CommunityPost($db);
        $this->communityComment = new CommunityComment($db);
        $this->eventResult = new EventResult($db);
        $this->eventReview = new EventReview($db);
    }

    /**
     * Homepage della community - Menu principale con 3 sezioni
     */
    public function index() {
        $this->requireAuth();
        
        // Statistiche utente per dashboard
        $userStats = $this->getUserStats($_SESSION['user_id']);
        
        include '../app/views/community/index.php';
    }

    /**
     * Community Universale - Chat generale
     */
    public function universal() {
        $this->requireAuth();
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get feed posts SOLO UNIVERSALI (event_id = NULL)
        $posts = $this->communityPost->getUniversalFeed($limit, $offset);
        
        // Aggiungi commenti recenti per ogni post
        foreach ($posts as &$post) {
            $post['comments'] = $this->communityComment->getByPost($post['id'], 3);
            $post['user_has_liked'] = $this->communityPost->hasUserLiked($post['id'], $_SESSION['user_id']);
        }
        
        include '../app/views/community/universal.php';
    }

    /**
     * Lista eventi dell'utente con le loro community
     */
    public function events() {
        $this->requireAuth();
        
        // Get eventi a cui l'utente è iscritto
        $userEvents = $this->getUserEvents($_SESSION['user_id']);
        
        include '../app/views/community/events.php';
    }

    /**
     * Sezione risultati e classifiche
     */
    public function results() {
        $this->requireAuth();
        
        // Get risultati dell'utente
                $userResults = $this->getUserEventResults($_SESSION['user_id']);
        
        // Get tutti i risultati degli eventi partecipati
        $eventResults = $this->getUserEventResults($_SESSION['user_id']);
        
        include '../app/views/community/results.php';
    }    /**
     * Feed specifico per evento - SOLO PER PARTECIPANTI
     */
    public function eventFeed($event_id) {
        $this->requireAuth();
        
        // Verifica che l'utente sia iscritto all'evento
        if (!$this->isUserRegisteredForEvent($_SESSION['user_id'], $event_id)) {
            $_SESSION['error'] = 'Devi essere iscritto a questo evento per accedere alla sua community!';
            header('Location: /community');
            exit();
        }
        
        // Get event details
        require_once '../app/models/Event.php';
        $eventModel = new Event($this->conn);
        $eventModel->id = $event_id;
        $event = $eventModel->readOne();
        
        if (!$event) {
            $_SESSION['error'] = 'Evento non trovato';
            header('Location: /community');
            exit();
        }
        
        // Get event-specific content SOLO PER QUESTO EVENTO
        $posts = $this->communityPost->getEventFeed($event_id, 20);
        $results = $this->eventResult->getByEvent($event_id);
        $reviews = $this->eventReview->getByEvent($event_id);
        $photos = $this->getEventPhotos($event_id);
        
        // Aggiungi commenti per ogni post
        foreach ($posts as &$post) {
            $post['comments'] = $this->communityComment->getByPost($post['id'], 3);
            $post['user_has_liked'] = $this->communityPost->hasUserLiked($post['id'], $_SESSION['user_id']);
        }
        
        include '../app/views/community/event_feed.php';
    }

    /**
     * Crea un nuovo post
     */
    public function createPost() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreatePost();
        } else {
            $events = $this->getAvailableEvents();
            include '../app/views/community/create.php';
        }
    }

    /**
     * Gestisce la creazione del post
     */
    private function handleCreatePost() {
        $data = [
            'user_id' => $_SESSION['user_id'],
            'event_id' => !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null,
            'type' => $_POST['type'] ?? 'text',
            'title' => $_POST['title'] ?? null,
            'content' => $_POST['content'] ?? '',
            'visibility' => $_POST['visibility'] ?? 'public'
        ];

        // Gestione upload media
        if (!empty($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleMediaUpload($_FILES['media']);
            if ($uploadResult['success']) {
                $data['media_url'] = $uploadResult['path'];
                $data['media_caption'] = $_POST['media_caption'] ?? null;
            } else {
                $_SESSION['error'] = $uploadResult['error'];
                header('Location: /community/create');
                exit();
            }
        }

        try {
            $postId = $this->communityPost->create($data);
            $_SESSION['success'] = "Post pubblicato con successo!";
            
            $redirectUrl = $data['event_id'] 
                ? "/community/event/{$data['event_id']}" 
                : "/community";
            
            header("Location: $redirectUrl");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nella pubblicazione: " . $e->getMessage();
            header('Location: /community/create');
            exit();
        }
    }

    /**
     * Aggiungi commento a un post
     */
    public function addComment() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed');
        }

        $data = [
            'post_id' => (int)$_POST['post_id'],
            'user_id' => $_SESSION['user_id'],
            'content' => $_POST['content'] ?? '',
            'parent_comment_id' => !empty($_POST['parent_comment_id']) ? (int)$_POST['parent_comment_id'] : null
        ];

        if (empty($data['content'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Contenuto commento richiesto']);
            exit();
        }

        try {
            $commentId = $this->communityComment->create($data);
            
            // Return JSON per AJAX
            if (isset($_POST['ajax'])) {
                $comment = $this->communityComment->getById($commentId);
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'comment' => $comment]);
                exit();
            }
            
            $_SESSION['success'] = "Commento aggiunto!";
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/community'));
        } catch (Exception $e) {
            if (isset($_POST['ajax'])) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
            
            $_SESSION['error'] = "Errore nell'aggiunta del commento";
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/community'));
        }
    }

    /**
     * Toggle like su post o commento
     */
    public function toggleLike() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed');
        }

        $targetType = $_POST['target_type'] ?? ''; // 'post' o 'comment'
        $targetId = (int)($_POST['target_id'] ?? 0);
        $userId = $_SESSION['user_id'];

        try {
            $result = $this->communityPost->toggleLike($userId, $targetType, $targetId);
            
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Classifica evento
     */
    public function eventResults($event_id) {
        // Verifica evento
        require_once '../app/models/Event.php';
        $event = new Event($this->conn);
        $eventData = $event->getById($event_id);
        
        if (!$eventData) {
            $_SESSION['error'] = "Evento non trovato";
            header('Location: /community');
            exit();
        }
        
        $category = $_GET['category'] ?? null;
        $results = $this->eventResult->getByEvent($event_id, null, $category);
        $categories = $this->eventResult->getCategories($event_id);
        
        include '../app/views/community/event_results.php';
    }

    /**
     * Galleria foto evento
     */
    public function eventGallery($event_id) {
        require_once '../app/models/Event.php';
        $event = new Event($this->conn);
        $eventData = $event->getById($event_id);
        
        if (!$eventData) {
            $_SESSION['error'] = "Evento non trovato";
            header('Location: /community');
            exit();
        }
        
        require_once '../app/models/EventGallery.php';
        $gallery = new EventGallery($this->conn);
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $photos = $gallery->getByEvent($event_id, $limit, $offset);
        $totalPhotos = $gallery->countByEvent($event_id);
        
        include '../app/views/community/event_gallery.php';
    }

    /**
     * Upload media helper
     */
    private function handleMediaUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Tipo di file non supportato'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File troppo grande (max 10MB)'];
        }
        
        $uploadDir = '../uploads/community/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'path' => '/uploads/community/' . $filename];
        } else {
            return ['success' => false, 'error' => 'Errore nel caricamento del file'];
        }
    }

    /**
     * Get eventi disponibili per i post
     */
    private function getAvailableEvents() {
        require_once '../app/models/Event.php';
        $event = new Event($this->conn);
        return $event->readAll(['status' => 'published']); // Eventi pubblicati
    }

    /**
     * Verifica se l'utente è iscritto all'evento
     */
    private function isUserRegisteredForEvent($user_id, $event_id) {
        $query = "SELECT registration_id FROM registrations WHERE user_id = :user_id AND event_id = :event_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Get statistiche utente per dashboard
     */
    private function getUserStats($user_id) {
        $stats = [
            'posts_count' => 0,
            'events_count' => 0,
            'results_count' => 0
        ];
        
        // Conta post
        $query = "SELECT COUNT(*) as total FROM community_posts WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['posts_count'] = $result['total'];
        
        // Conta eventi partecipati
        $query = "SELECT COUNT(*) as total FROM registrations WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['events_count'] = $result['total'];
        
        // Conta risultati
        $query = "SELECT COUNT(*) as total FROM event_results WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['results_count'] = $result['total'];
        
        return $stats;
    }

    /**
     * Get eventi dell'utente
     */
    private function getUserEvents($user_id) {
        $query = "SELECT DISTINCT e.event_id, e.titolo as event_title, e.data_evento as event_date, e.sport as disciplina
                 FROM events e
                 INNER JOIN registrations r ON e.event_id = r.event_id
                 WHERE r.user_id = :user_id
                 ORDER BY e.data_evento DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get risultati eventi utente
     */
    private function getUserEventResults($user_id) {
        $query = "SELECT e.titolo as event_title, e.data_evento as event_date, 'N/A' as event_discipline, 
                        er.posizione as posizione, er.tempo_finale as tempo_finale, er.evento_id as evento_id
                 FROM event_results er
                 INNER JOIN events e ON er.evento_id = e.event_id
                 WHERE er.user_id = :user_id
                 ORDER BY e.data_evento DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Metodo per community evento specifico
    public function eventCommunity($eventId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $event = $this->getEventDetails($eventId);
        if (!$event) {
            header('Location: /community/events');
            exit;
        }

        $isRegistered = $this->isUserRegisteredForEvent($_SESSION['user_id'], $eventId);
        $eventPosts = $this->getEventPosts($eventId);
        $totalParticipants = $this->getEventParticipantsCount($eventId);

        require_once __DIR__ . '/../views/community/event.php';
    }

    /**
     * Get dettagli evento
     */
    private function getEventDetails($eventId) {
        $query = "SELECT * FROM events WHERE event_id = :event_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get post di un evento specifico
     */
    private function getEventPosts($eventId) {
        $query = "SELECT cp.*, u.nome as author_name,
                        0 as likes_count,
                        (SELECT COUNT(*) FROM community_comments cc WHERE cc.post_id = cp.id) as comments_count,
                        0 as user_liked
                 FROM community_posts cp
                 INNER JOIN users u ON cp.user_id = u.user_id
                 WHERE cp.event_id = :event_id
                 ORDER BY cp.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Aggiungi commenti per ogni post  
        foreach ($posts as &$post) {
            $post['comments'] = $this->getPostComments($post['id']);
        }
        
        return $posts;
    }

    /**
     * Get numero partecipanti evento
     */
    private function getEventParticipantsCount($eventId) {
        $query = "SELECT COUNT(*) as total FROM registrations WHERE event_id = :event_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }

    /**
     * Get commenti di un post
     */
    private function getPostComments($postId) {
        $query = "SELECT cc.*, u.nome as author_name
                 FROM community_comments cc
                 INNER JOIN users u ON cc.user_id = u.user_id
                 WHERE cc.post_id = :post_id
                 ORDER BY cc.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica autenticazione
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    }
}
?>