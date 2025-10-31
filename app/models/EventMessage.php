<?php
/**
 * Modello EventMessage per la gestione dei messaggi di servizio agli iscritti
 */
class EventMessage {
    private $conn;
    private $table = 'event_messages';
    private $col = null; // mappa colonne dinamica

    public $id;
    public $event_id;
    public $organizer_id;
    public $subject;
    public $message;
    public $sent_at;
    public $recipients_count;
    public $delivery_status;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function resolveColumns() {
        if ($this->col !== null) return;
        $this->col = [
            'events_id' => 'id',
            'events_org' => 'organizer_id',
            'em_event' => 'event_id',
            'users_id' => 'id',
            'reg_event' => 'event_id',
            'reg_user' => 'user_id',
            'reg_status' => 'status',
            'reg_id' => 'id',
            'notifications_table' => 'notifications'
        ];

        // events
        if ($this->columnExists('events','event_id')) $this->col['events_id'] = 'event_id';
        if ($this->columnExists('events','organizzatore_id')) $this->col['events_org'] = 'organizzatore_id';
        // event_messages
        if ($this->columnExists('event_messages','evento_id')) $this->col['em_event'] = 'evento_id';
        // users
        if ($this->columnExists('users','user_id')) $this->col['users_id'] = 'user_id';
        // registrations
        if ($this->columnExists('registrations','registration_id')) $this->col['reg_id'] = 'registration_id';
        if ($this->columnExists('registrations','evento_id')) $this->col['reg_event'] = 'evento_id';
        if ($this->columnExists('registrations','utente_id')) $this->col['reg_user'] = 'utente_id';
        if ($this->columnExists('registrations','stato')) $this->col['reg_status'] = 'stato';
        // notifications table fallback
        if (!$this->tableExists('notifications') && $this->tableExists('user_notifications')) {
            $this->col['notifications_table'] = 'user_notifications';
        }
    }

    private function tableExists($table) {
        try {
            $stmt = $this->conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1");
            $stmt->bindValue(':t', $table);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (Exception $e) { return false; }
    }

    private function columnExists($table, $column) {
        try {
            $sql = "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :tbl AND column_name = :col";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':tbl', $table);
            $stmt->bindValue(':col', $column);
            $stmt->execute();
            return ((int)$stmt->fetchColumn()) > 0;
        } catch (Exception $e) { return false; }
    }

    // Crea nuovo messaggio
    public function create() {
        $this->resolveColumns();
        $emEvent = $this->col['em_event'];
        $orgCol = $this->columnExists($this->table, 'organizzatore_id') ? 'organizzatore_id' : 'organizer_id';
        $query = "INSERT INTO " . $this->table . " 
                 (`$emEvent`, `$orgCol`, title, message, sent_at) 
                 VALUES (:event_id, :organizer_id, :title, :message, NOW())";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':event_id', $this->event_id);
        $stmt->bindParam(':organizer_id', $this->organizer_id);
        $stmt->bindParam(':title', $this->subject);
        $stmt->bindParam(':message', $this->message);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Invia messaggio a tutti gli iscritti dell'evento
    public function sendToEventParticipants($event_id, $organizer_id, $subject, $message) {
        try {
            $this->conn->beginTransaction();

            // Ottieni tutti gli iscritti confermati dell'evento
            $this->resolveColumns();
            $regId = $this->col['reg_id'];
            $regEvent = $this->col['reg_event'];
            $regUser = $this->col['reg_user'];
            $regStatus = $this->col['reg_status'];
            $userId = $this->col['users_id'];
            $statusCond = $regStatus ? " AND r.`$regStatus` IN ('confermata','confirmed')" : '';
            $participants_query = "SELECT r.`$regId` AS registration_id, r.`$regUser` AS user_id, u.email, u.nome, u.cognome
                                  FROM registrations r
                                  JOIN users u ON r.`$regUser` = u.`$userId`
                                  WHERE r.`$regEvent` = :event_id" . $statusCond . "
                                  ORDER BY u.nome, u.cognome";
            
            $participants_stmt = $this->conn->prepare($participants_query);
            $participants_stmt->bindParam(':event_id', $event_id);
            $participants_stmt->execute();
            $participants = $participants_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($participants)) {
                throw new Exception('Nessun iscritto trovato per questo evento');
            }

            // Crea il messaggio principale
            $this->event_id = $event_id;
            $this->organizer_id = $organizer_id;
            $this->subject = $subject;
            $this->message = $message;
            $this->recipients_count = count($participants);

            if (!$this->create()) {
                throw new Exception('Errore nella creazione del messaggio');
            }

            // Crea record per ogni destinatario
            $recipient_query = "INSERT INTO message_recipients 
                               (message_id, recipient_id, user_id, registration_id, email, delivery_status) 
                               VALUES (:message_id, :recipient_id, :user_id, :registration_id, :email, 'pending')";
            
            $recipient_stmt = $this->conn->prepare($recipient_query);
            
            // Crea anche notifiche interne per i partecipanti
            $notificationsTable = $this->col['notifications_table'];
            $notification_query = "INSERT INTO `$notificationsTable` 
                                  (user_id, message_id, event_id, subject, message) 
                                  VALUES (:user_id, :message_id, :event_id, :subject, :message)";
            
            $notification_stmt = $this->conn->prepare($notification_query);
            
            $sent_count = 0;
            foreach ($participants as $participant) {
                $recipient_stmt->bindParam(':message_id', $this->id);
                $recipient_stmt->bindParam(':recipient_id', $participant['user_id']);
                $recipient_stmt->bindParam(':user_id', $participant['user_id']);
                $recipient_stmt->bindParam(':registration_id', $participant['registration_id']);
                $recipient_stmt->bindParam(':email', $participant['email']);
                
                if ($recipient_stmt->execute()) {
                    // Crea notifica interna
                    $notification_stmt->bindParam(':user_id', $participant['user_id']);
                    $notification_stmt->bindParam(':message_id', $this->id);
                    $notification_stmt->bindParam(':event_id', $event_id);
                    $notification_stmt->bindParam(':subject', $subject);
                    $notification_stmt->bindParam(':message', $message);
                    $notification_stmt->execute();
                    
                    // Simula invio email (in produzione userebbe un servizio email reale)
                    $email_sent = $this->sendEmail($participant, $subject, $message, $event_id);
                    
                    if ($email_sent) {
                        $this->updateRecipientStatus($this->id, $participant['user_id'], 'sent');
                        $sent_count++;
                    } else {
                        $this->updateRecipientStatus($this->id, $participant['user_id'], 'failed');
                    }
                }
            }

            // Aggiorna stato messaggio principale
            $status = $sent_count > 0 ? 'sent' : 'failed';
            $this->updateMessageStatus($this->id, $status);

            $this->conn->commit();

            return [
                'success' => true,
                'message_id' => $this->id,
                'total_recipients' => count($participants),
                'sent_count' => $sent_count,
                'failed_count' => count($participants) - $sent_count
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // Ottieni messaggi di un evento
    public function getEventMessages($event_id) {
        $this->resolveColumns();
        $emEvent = $this->col['em_event'];
        $userId = $this->col['users_id'];
        $query = "SELECT em.*, u.nome as organizer_name, u.cognome as organizer_surname,
                        (SELECT COUNT(*) FROM message_recipients mr WHERE mr.message_id = em.id AND mr.delivery_status = 'sent') as sent_count,
                        (SELECT COUNT(*) FROM message_recipients mr WHERE mr.message_id = em.id AND mr.delivery_status = 'failed') as failed_count
                 FROM " . $this->table . " em
                 LEFT JOIN users u ON em.organizer_id = u.`$userId`
                 WHERE em.`$emEvent` = :event_id
                 ORDER BY em.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni dettagli destinatari di un messaggio
    public function getMessageRecipients($message_id) {
        $query = "SELECT mr.*, u.nome, u.cognome, u.email
                 FROM message_recipients mr
                 JOIN users u ON mr.user_id = u.user_id
                 WHERE mr.message_id = :message_id
                 ORDER BY u.nome, u.cognome";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_id', $message_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni statistiche messaggi per organizzatore
    public function getOrganizerStats($organizer_id) {
        $query = "SELECT 
                    COUNT(*) as total_messages,
                    SUM(recipients_count) as total_recipients,
                    COUNT(CASE WHEN delivery_status = 'sent' THEN 1 END) as sent_messages,
                    COUNT(CASE WHEN delivery_status = 'failed' THEN 1 END) as failed_messages
                 FROM " . $this->table . "
                 WHERE organizer_id = :organizer_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Simula invio email (in produzione integrare con servizio email reale)
    private function sendEmail($participant, $subject, $message, $event_id) {
        // Ottieni dettagli evento
    $this->resolveColumns();
    $evId = $this->col['events_id'];
    $event_query = "SELECT `$evId` AS id, titolo, data_evento, luogo_partenza FROM events WHERE `$evId` = :event_id";
        $event_stmt = $this->conn->prepare($event_query);
        $event_stmt->bindParam(':event_id', $event_id);
        $event_stmt->execute();
        $event = $event_stmt->fetch(PDO::FETCH_ASSOC);

        // Componi email HTML
        $email_html = $this->buildEmailTemplate($participant, $subject, $message, $event);
        
        // Simula invio (in produzione usare PHPMailer, SendGrid, etc.)
        $to = $participant['email'];
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SportEvents <noreply@sportevents.com>" . "\r\n";
        
        // Per ora simula sempre successo (95% success rate)
        return (rand(1, 100) <= 95);
        
        // In produzione:
        // return mail($to, $subject, $email_html, $headers);
    }

    // Template email HTML
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
                        <a href="' . (defined('BASE_URL') ? BASE_URL : '') . '/events/' . $event['id'] . '" class="button">
                            Visualizza Evento
                        </a>
                    </div>
                </div>
                
                <div class="footer">
                    <p>Hai ricevuto questa email perché sei iscritto all\'evento "' . htmlspecialchars($event['titolo']) . '"</p>
                    <p>SportEvents - La piattaforma per eventi sportivi</p>
                </div>
            </div>
        </body>
        </html>';

        return $template;
    }

    // Aggiorna stato destinatario
    private function updateRecipientStatus($message_id, $user_id, $status) {
        $query = "UPDATE message_recipients 
                 SET delivery_status = :status, sent_at = NOW() 
                 WHERE message_id = :message_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':message_id', $message_id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    // Aggiorna stato messaggio principale
    private function updateMessageStatus($message_id, $status) {
        $query = "UPDATE " . $this->table . " 
                 SET delivery_status = :status 
                 WHERE id = :message_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':message_id', $message_id);
        
        return $stmt->execute();
    }

    // Verifica se l'organizzatore può inviare messaggi per questo evento
    public function canSendMessage($organizer_id, $event_id) {
        $this->resolveColumns();
        $evId = $this->col['events_id'];
        $evOrg = $this->col['events_org'];
        $query = "SELECT COUNT(*) as count FROM events 
                 WHERE `$evId` = :event_id AND `$evOrg` = :organizer_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    // Marca messaggio come letto (tracking aperture)
    public function markAsOpened($message_id, $user_id) {
        $query = "UPDATE message_recipients 
                 SET opened_at = NOW() 
                 WHERE message_id = :message_id AND user_id = :user_id AND opened_at IS NULL";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_id', $message_id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    // Ottieni messaggi per organizzatore
    public function getMessagesByOrganizer($organizer_id) {
    $this->resolveColumns();
    $emEvent = $this->col['em_event'];
    $evId = $this->col['events_id'];
    $regEvent = $this->col['reg_event'];
    $query = "SELECT em.*, e.titolo as evento_nome,
                         COUNT(DISTINCT r.user_id) as destinatari_count,
                         COUNT(DISTINCT mr.user_id) as visualizzazioni
                 FROM " . $this->table . " em
         LEFT JOIN events e ON em.`$emEvent` = e.`$evId`
         LEFT JOIN registrations r ON e.`$evId` = r.`$regEvent`
                 LEFT JOIN message_reads mr ON em.id = mr.message_id
                 WHERE em.organizer_id = :organizer_id
                 GROUP BY em.id
                 ORDER BY em.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni messaggi per partecipante
    public function getMessagesForParticipant($user_id) {
    $this->resolveColumns();
    $userId = $this->col['users_id'];
    $emEvent = $this->col['em_event'];
    $evId = $this->col['events_id'];
    $regEvent = $this->col['reg_event'];
    $query = "SELECT DISTINCT em.*, e.titolo as evento_nome,
                         u.nome as organizer_nome, u.cognome as organizer_cognome,
                         mr.read_at as letto_il
                 FROM " . $this->table . " em
         LEFT JOIN events e ON em.`$emEvent` = e.`$evId`
         LEFT JOIN registrations r ON e.`$evId` = r.`$regEvent`
         LEFT JOIN users u ON em.organizer_id = u.`$userId`
                 LEFT JOIN message_reads mr ON em.id = mr.message_id AND mr.user_id = :user_id
         WHERE r.`$userId` = :user_id2
                 ORDER BY em.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_id2', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
