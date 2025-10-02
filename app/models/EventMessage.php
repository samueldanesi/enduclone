<?php
/**
 * Modello EventMessage per la gestione dei messaggi di servizio agli iscritti
 */
class EventMessage {
    private $conn;
    private $table = 'event_messages';

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

    // Crea nuovo messaggio
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 (event_id, organizer_id, title, message, sent_at) 
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
            $participants_query = "SELECT r.registration_id, r.user_id, u.email, u.nome, u.cognome
                                  FROM registrations r
                                  JOIN users u ON r.user_id = u.user_id
                                  WHERE r.event_id = :event_id 
                                  AND r.stato = 'confermata'
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
            $notification_query = "INSERT INTO user_notifications 
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
        $query = "SELECT em.*, u.nome as organizer_name, u.cognome as organizer_surname,
                        (SELECT COUNT(*) FROM message_recipients mr WHERE mr.message_id = em.id AND mr.delivery_status = 'sent') as sent_count,
                        (SELECT COUNT(*) FROM message_recipients mr WHERE mr.message_id = em.id AND mr.delivery_status = 'failed') as failed_count
                 FROM " . $this->table . " em
                 LEFT JOIN users u ON em.organizer_id = u.user_id
                 WHERE em.event_id = :event_id
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
        $event_query = "SELECT id, titolo, data_evento, luogo_partenza FROM events WHERE id = :event_id";
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
                        <a href="http://localhost:8080/events/' . $event['id'] . '" class="button">
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
        $query = "SELECT COUNT(*) as count FROM events 
                 WHERE event_id = :event_id AND organizer_id = :organizer_id";
        
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
        $query = "SELECT em.*, e.titolo as evento_nome,
                         COUNT(DISTINCT r.user_id) as destinatari_count,
                         COUNT(DISTINCT mr.user_id) as visualizzazioni
                 FROM " . $this->table . " em
                 LEFT JOIN events e ON em.evento_id = e.event_id
                 LEFT JOIN registrations r ON e.event_id = r.event_id
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
        $query = "SELECT DISTINCT em.*, e.titolo as evento_nome,
                         u.nome as organizer_nome, u.cognome as organizer_cognome,
                         mr.read_at as letto_il
                 FROM " . $this->table . " em
                 LEFT JOIN events e ON em.evento_id = e.event_id
                 LEFT JOIN registrations r ON e.event_id = r.event_id
                 LEFT JOIN users u ON em.organizer_id = u.user_id
                 LEFT JOIN message_reads mr ON em.id = mr.message_id AND mr.user_id = :user_id
                 WHERE r.user_id = :user_id2
                 ORDER BY em.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_id2', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
