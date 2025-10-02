<?php
require_once __DIR__ . '/../models/EventMessage.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Registration.php';

/**
 * Controller per la gestione dei messaggi di servizio agli eventi
 */
class MessageController {
    private $conn;
    private $eventMessage;
    private $event;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->eventMessage = new EventMessage($this->conn);
        $this->event = new Event($this->conn);
    }

    // Pagina principale messaggi per l'utente
    public function index() {
        // Verifica autenticazione
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $messages = [];
        
        try {
            // Se è un organizzatore, mostra i messaggi dei suoi eventi
            if ($_SESSION['user_type'] === 'organizer') {
                $messages = $this->eventMessage->getMessagesByOrganizer($user_id);
            } else {
                // Se è un partecipante, mostra i messaggi degli eventi a cui è iscritto
                $messages = $this->eventMessage->getMessagesForParticipant($user_id);
            }
        } catch (Exception $e) {
            $messages = [];
        }

        // Includi la vista
        include '../app/views/messages/index.php';
    }

    // Mostra form per inviare messaggio
    public function compose($event_id) {
        // Verifica autenticazione organizzatore
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        // Verifica che l'organizzatore possa gestire questo evento
        if (!$this->eventMessage->canSendMessage($_SESSION['user_id'], $event_id)) {
            header('Location: /organizer/dashboard');
            exit;
        }

        // Ottieni dettagli evento
        $event = $this->event->getById($event_id);
        if (!$event) {
            header('Location: /404');
            exit;
        }

        // Conta iscritti confermati
        $registration = new Registration($this->conn);
        $participants_count = $registration->countConfirmedParticipants($event_id);

        // Ottieni messaggi precedenti
        $previous_messages = $this->eventMessage->getEventMessages($event_id);

        $pageTitle = "Invia Messaggio - " . $event['titolo'];
        require_once __DIR__ . '/../views/messages/compose.php';
    }

    // Invia messaggio via AJAX
    public function send() {
        header('Content-Type: application/json');

        // Verifica autenticazione
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
            exit;
        }

        // Valida input
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
            exit;
        }

        $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        $subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS));
        $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS));

        // Validazioni
        if (!$event_id || empty($subject) || empty($message)) {
            echo json_encode([
                'success' => false, 
                'error' => 'Tutti i campi sono obbligatori'
            ]);
            exit;
        }

        if (strlen($subject) > 200) {
            echo json_encode([
                'success' => false, 
                'error' => 'Oggetto troppo lungo (max 200 caratteri)'
            ]);
            exit;
        }

        if (strlen($message) > 5000) {
            echo json_encode([
                'success' => false, 
                'error' => 'Messaggio troppo lungo (max 5000 caratteri)'
            ]);
            exit;
        }

        // Verifica autorizzazione evento
        if (!$this->eventMessage->canSendMessage($_SESSION['user_id'], $event_id)) {
            echo json_encode([
                'success' => false, 
                'error' => 'Non sei autorizzato a inviare messaggi per questo evento'
            ]);
            exit;
        }

        // Invia messaggio
        $result = $this->eventMessage->sendToEventParticipants(
            $event_id, 
            $_SESSION['user_id'], 
            $subject, 
            $message
        );

        echo json_encode($result);
    }

    // Visualizza dettagli messaggio inviato
    public function view($message_id) {
        // Verifica autenticazione organizzatore
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        // Ottieni dettagli messaggio
        $query = "SELECT em.*, e.titolo as event_title, e.id as event_id
                 FROM event_messages em
                 JOIN events e ON em.event_id = e.id
                 WHERE em.id = :message_id AND em.organizer_id = :organizer_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_id', $message_id);
        $stmt->bindParam(':organizer_id', $_SESSION['user_id']);
        $stmt->execute();

        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$message) {
            header('Location: /404');
            exit;
        }

        // Ottieni destinatari
        $recipients = $this->eventMessage->getMessageRecipients($message_id);

        $pageTitle = "Dettagli Messaggio - " . $message['subject'];
        require_once __DIR__ . '/../views/messages/view.php';
    }

    // API per ottenere messaggi evento
    public function getEventMessages($event_id) {
        header('Content-Type: application/json');

        // Verifica autenticazione
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
            exit;
        }

        // Verifica autorizzazione evento
        if (!$this->eventMessage->canSendMessage($_SESSION['user_id'], $event_id)) {
            echo json_encode(['success' => false, 'error' => 'Non autorizzato per questo evento']);
            exit;
        }

        $messages = $this->eventMessage->getEventMessages($event_id);
        echo json_encode(['success' => true, 'messages' => $messages]);
    }

    // API per statistiche messaggi
    public function getStats() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
            exit;
        }

        $stats = $this->eventMessage->getOrganizerStats($_SESSION['user_id']);
        echo json_encode(['success' => true, 'stats' => $stats]);
    }

    // Segna messaggio come aperto (tracking)
    public function markOpened($message_id) {
        if (!isset($_SESSION['user_id'])) {
            exit; // Tracking silenzioso
        }

        $this->eventMessage->markAsOpened($message_id, $_SESSION['user_id']);
        
        // Ritorna pixel di tracking trasparente
        header('Content-Type: image/gif');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // GIF trasparente 1x1 pixel
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    }

    // Preview email template (per debug)
    public function previewEmail($event_id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        if (!$this->eventMessage->canSendMessage($_SESSION['user_id'], $event_id)) {
            header('Location: /404');
            exit;
        }

        // Dati di esempio per preview
        $participant = [
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'email' => 'mario.rossi@example.com'
        ];

        $subject = $_GET['subject'] ?? 'Oggetto del messaggio';
        $message = $_GET['message'] ?? 'Contenuto del messaggio di esempio...';

        // Ottieni evento
        $event = $this->event->getById($event_id);
        
        // Mostra template email
        echo $this->buildEmailTemplate($participant, $subject, $message, $event);
    }

    // Helper per costruire template email (duplicato da EventMessage per preview)
    private function buildEmailTemplate($participant, $subject, $message, $event) {
        $template = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . htmlspecialchars($subject) . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
                .event-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .button { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>SportEvents</h1>
                    <p>Comunicazione di servizio</p>
                </div>
                
                <div class="content">
                    <h2>Ciao ' . htmlspecialchars($participant['nome']) . '!</h2>
                    
                    <div class="event-info">
                        <h3>📅 ' . htmlspecialchars($event['titolo']) . '</h3>
                        <p><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($event['data_evento'])) . '</p>
                        <p><strong>Luogo:</strong> ' . htmlspecialchars($event['luogo_partenza']) . '</p>
                    </div>
                    
                    <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        ' . nl2br(htmlspecialchars($message)) . '
                    </div>
                    
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="http://localhost:8080/events/' . $event['id'] . '" class="button">
                            Visualizza Evento
                        </a>
                    </div>
                </div>
                
                <div class="footer">
                    <p>Hai ricevuto questa email perché sei iscritto all\'evento "' . htmlspecialchars($event['titolo']) . '"</p>
                    <p>SportEvents - La piattaforma per eventi sportivi</p>
                    <p style="margin-top: 20px;">
                        <img src="/messages/track/' . ($this->id ?? 0) . '" width="1" height="1" style="display:none;" alt="">
                    </p>
                </div>
            </div>
        </body>
        </html>';

        return $template;
    }
}
?>
