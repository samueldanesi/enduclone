<?php
/**
 * Modello TeamMessage per gestire la chat dei team
 */
class TeamMessage {
    private $conn;
    private $table = 'team_messages';

    public $id;
    public $team_id;
    public $user_id;
    public $message;
    public $message_type;
    public $event_id;
    public $parent_message_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Invia messaggio normale in chat
    public function sendMessage() {
        $query = "INSERT INTO " . $this->table . " 
                 SET team_id=:team_id, user_id=:user_id, message=:message, 
                     message_type=:message_type, event_id=:event_id, 
                     parent_message_id=:parent_message_id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':team_id', $this->team_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':message_type', $this->message_type);
        $stmt->bindParam(':event_id', $this->event_id);
        $stmt->bindParam(':parent_message_id', $this->parent_message_id);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // TODO: Implementare notifiche quando la struttura sarà aggiornata
            // $this->createTeamNotifications();
            
            return true;
        }
        return false;
    }

    // Ottieni messaggi del team con paginazione
    public function getTeamMessages($team_id, $limit = 50, $offset = 0) {
        $query = "SELECT tm.*, u.nome, u.cognome, u.email,
                        CONCAT(u.nome, ' ', u.cognome) as nome_utente,
                        e.titolo as nome_evento, e.data_evento as evento_data
                 FROM " . $this->table . " tm
                 JOIN users u ON tm.user_id = u.id
                 LEFT JOIN events e ON tm.event_id = e.id
                 WHERE tm.team_id = :team_id
                 ORDER BY tm.created_at DESC
                 LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Invia richiesta evento
    public function sendEventRequest($team_id, $admin_id, $event_id, $message, $target_participants, $deadline) {
        $this->conn->beginTransaction();
        
        try {
            // 1. Crea messaggio in chat
            $this->team_id = $team_id;
            $this->user_id = $admin_id;
            $this->message = $message;
            $this->message_type = 'richiesta_evento';
            $this->event_id = $event_id;
            
            if (!$this->sendMessage()) {
                throw new Exception("Errore nell'invio del messaggio");
            }

            // 2. Crea richiesta evento
            $request_query = "INSERT INTO team_event_requests 
                             SET team_id=:team_id, admin_id=:admin_id, event_id=:event_id,
                                 message_id=:message_id, target_participants=:target_participants,
                                 deadline=:deadline";

            $request_stmt = $this->conn->prepare($request_query);
            $request_stmt->bindParam(':team_id', $team_id);
            $request_stmt->bindParam(':admin_id', $admin_id);
            $request_stmt->bindParam(':event_id', $event_id);
            $request_stmt->bindParam(':message_id', $this->id);
            $request_stmt->bindParam(':target_participants', $target_participants);
            $request_stmt->bindParam(':deadline', $deadline);

            if (!$request_stmt->execute()) {
                throw new Exception("Errore nella creazione della richiesta evento");
            }

            $this->conn->commit();
            return $this->conn->lastInsertId(); // ID della richiesta

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Risposta a richiesta evento
    public function respondToEventRequest($request_id, $user_id, $status, $notes = '') {
        // Verifica che l'utente sia membro del team
        $verify_query = "SELECT ter.team_id FROM team_event_requests ter
                        JOIN team_members tm ON ter.team_id = tm.team_id
                        WHERE ter.id = :request_id AND tm.user_id = :user_id AND tm.attivo = 1";
        
        $verify_stmt = $this->conn->prepare($verify_query);
        $verify_stmt->bindParam(':request_id', $request_id);
        $verify_stmt->bindParam(':user_id', $user_id);
        $verify_stmt->execute();

        if (!$verify_stmt->fetch()) {
            throw new Exception("Non sei autorizzato a rispondere a questa richiesta");
        }

        // Inserisci o aggiorna risposta
        $response_query = "INSERT INTO team_event_participations 
                          SET request_id=:request_id, user_id=:user_id, status=:status, notes=:notes
                          ON DUPLICATE KEY UPDATE status=:status2, notes=:notes2, responded_at=NOW()";

        $response_stmt = $this->conn->prepare($response_query);
        $response_stmt->bindParam(':request_id', $request_id);
        $response_stmt->bindParam(':user_id', $user_id);
        $response_stmt->bindParam(':status', $status);
        $response_stmt->bindParam(':notes', $notes);
        $response_stmt->bindParam(':status2', $status);
        $response_stmt->bindParam(':notes2', $notes);

        if ($response_stmt->execute()) {
            // Aggiorna contatore nella richiesta
            $this->updateRequestCounter($request_id);
            
            // Invia messaggio di risposta in chat
            $this->sendResponseMessage($request_id, $user_id, $status);
            
            return true;
        }
        return false;
    }

    // Aggiorna contatore partecipanti nella richiesta
    private function updateRequestCounter($request_id) {
        $update_query = "UPDATE team_event_requests 
                        SET current_participants = (
                            SELECT COUNT(*) FROM team_event_participations 
                            WHERE request_id = :request_id AND status = 'confermato'
                        )
                        WHERE id = :request_id2";

        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bindParam(':request_id', $request_id);
        $update_stmt->bindParam(':request_id2', $request_id);
        $update_stmt->execute();

        // Verifica se raggiunto target per generare codice sconto
        $this->checkDiscountGeneration($request_id);
    }

    // Verifica se generare codice sconto
    private function checkDiscountGeneration($request_id) {
        $check_query = "SELECT * FROM team_event_requests 
                       WHERE id = :request_id AND current_participants >= target_participants 
                       AND status = 'aperta' AND discount_code IS NULL";

        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':request_id', $request_id);
        $check_stmt->execute();

        $request = $check_stmt->fetch(PDO::FETCH_ASSOC);
        if ($request) {
            $this->generateDiscountCode($request);
        }
    }

    // Genera codice sconto automatico
    private function generateDiscountCode($request) {
        // Calcola sconto in base al numero di partecipanti
        $participants = $request['current_participants'];
        $discount_percentage = $this->calculateDiscount($participants);
        
        if ($discount_percentage > 0) {
            $code = 'TEAM-' . $request['team_id'] . '-' . date('Ymd') . '-' . $participants;
            
            // Crea codice sconto
            $discount_query = "INSERT INTO discount_codes 
                              SET code=:code, event_id=:event_id, team_id=:team_id,
                                  team_request_id=:request_id, discount_percentage=:discount,
                                  max_uses=:max_uses, max_team_uses=:max_team_uses,
                                  valid_from=NOW(), valid_until=:deadline, status='active'";

            $discount_stmt = $this->conn->prepare($discount_query);
            $discount_stmt->bindParam(':code', $code);
            $discount_stmt->bindParam(':event_id', $request['event_id']);
            $discount_stmt->bindParam(':team_id', $request['team_id']);
            $discount_stmt->bindParam(':request_id', $request['id']);
            $discount_stmt->bindParam(':discount', $discount_percentage);
            $discount_stmt->bindParam(':max_uses', $participants);
            $discount_stmt->bindParam(':max_team_uses', $participants);
            $discount_stmt->bindParam(':deadline', $request['deadline']);

            if ($discount_stmt->execute()) {
                // Aggiorna richiesta con codice generato
                $update_query = "UPDATE team_event_requests 
                                SET discount_code=:code, discount_percentage=:discount, status='completata'
                                WHERE id=:request_id";

                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(':code', $code);
                $update_stmt->bindParam(':discount', $discount_percentage);
                $update_stmt->bindParam(':request_id', $request['id']);
                $update_stmt->execute();

                // Invia notifica di successo al team
                $this->notifyDiscountGenerated($request, $code, $discount_percentage);
            }
        }
    }

    // Calcola sconto in base ai partecipanti
    private function calculateDiscount($participants) {
        if ($participants >= 50) return 20;
        if ($participants >= 25) return 15;
        if ($participants >= 15) return 10;
        if ($participants >= 10) return 5;
        return 0;
    }

    // Invia messaggio di risposta
    private function sendResponseMessage($request_id, $user_id, $status) {
        $user_query = "SELECT nome, cognome FROM users WHERE id = :user_id";
        $user_stmt = $this->conn->prepare($user_query);
        $user_stmt->bindParam(':user_id', $user_id);
        $user_stmt->execute();
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

        $status_text = [
            'confermato' => '✅ Ci sarò!',
            'interessato' => '🤔 Forse...',
            'ritirato' => '❌ Non posso'
        ];

        $response_message = $user['nome'] . ' ' . $user['cognome'] . ': ' . $status_text[$status];

        $request_query = "SELECT team_id, message_id FROM team_event_requests WHERE id = :request_id";
        $request_stmt = $this->conn->prepare($request_query);
        $request_stmt->bindParam(':request_id', $request_id);
        $request_stmt->execute();
        $request = $request_stmt->fetch(PDO::FETCH_ASSOC);

        $this->team_id = $request['team_id'];
        $this->user_id = $user_id;
        $this->message = $response_message;
        $this->message_type = 'risposta_evento';
        $this->parent_message_id = $request['message_id'];
        $this->event_id = null;

        $this->sendMessage();
    }

    // Notifica generazione codice sconto
    private function notifyDiscountGenerated($request, $code, $discount) {
        $success_message = "🎉 OBIETTIVO RAGGIUNTO! 🎉\n" .
                          "Codice sconto generato: {$code}\n" .
                          "Sconto: {$discount}%\n" .
                          "Partecipanti: {$request['current_participants']}\n" .
                          "Utilizza il codice per iscriverti all'evento!";

        $this->team_id = $request['team_id'];
        $this->user_id = $request['admin_id'];
        $this->message = $success_message;
        $this->message_type = 'normale';
        $this->event_id = $request['event_id'];
        $this->parent_message_id = $request['message_id'];

        $this->sendMessage();
    }

    // Crea notifiche per membri del team
    private function createTeamNotifications() {
        if ($this->message_type === 'normale') {
            // Solo per messaggi normali, non spam per ogni risposta
            $members_query = "SELECT user_id FROM team_members 
                             WHERE team_id = :team_id AND attivo = 1 AND user_id != :sender_id";
            
            $members_stmt = $this->conn->prepare($members_query);
            $members_stmt->bindParam(':team_id', $this->team_id);
            $members_stmt->bindParam(':sender_id', $this->user_id);
            $members_stmt->execute();

            while ($member = $members_stmt->fetch(PDO::FETCH_ASSOC)) {
                $notification_query = "INSERT INTO user_notifications 
                                      SET user_id=:user_id, type='team_message', 
                                          title='Nuovo messaggio nel team',
                                          message='Hai un nuovo messaggio nel team',
                                          team_id=:team_id, team_message_id=:message_id";

                $notification_stmt = $this->conn->prepare($notification_query);
                $notification_stmt->bindParam(':user_id', $member['user_id']);
                $notification_stmt->bindParam(':team_id', $this->team_id);
                $notification_stmt->bindParam(':message_id', $this->id);
                $notification_stmt->execute();
            }
        }
    }

    // Ottieni statistiche richiesta evento
    public function getRequestStats($request_id) {
        $stats_query = "SELECT ter.*, 
                               COUNT(CASE WHEN tep.status = 'confermato' THEN 1 END) as confermati,
                               COUNT(CASE WHEN tep.status = 'interessato' THEN 1 END) as interessati,
                               COUNT(CASE WHEN tep.status = 'ritirato' THEN 1 END) as ritirati
                       FROM team_event_requests ter
                       LEFT JOIN team_event_participations tep ON ter.id = tep.request_id
                       WHERE ter.id = :request_id
                       GROUP BY ter.id";

        $stats_stmt = $this->conn->prepare($stats_query);
        $stats_stmt->bindParam(':request_id', $request_id);
        $stats_stmt->execute();

        return $stats_stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Elimina messaggio (solo autore o admin team)
    public function deleteMessage($message_id, $user_id) {
        $auth_query = "SELECT tm.user_id, tm.team_id,
                              (SELECT COUNT(*) FROM team_members 
                               WHERE team_id = tm.team_id AND user_id = :user_id 
                               AND ruolo IN ('admin', 'captain') AND attivo = 1) as is_admin
                      FROM " . $this->table . " tm
                      WHERE tm.id = :message_id";

        $auth_stmt = $this->conn->prepare($auth_query);
        $auth_stmt->bindParam(':message_id', $message_id);
        $auth_stmt->bindParam(':user_id', $user_id);
        $auth_stmt->execute();

        $auth = $auth_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$auth || ($auth['user_id'] != $user_id && $auth['is_admin'] == 0)) {
            return false; // Non autorizzato
        }

        $delete_query = "DELETE FROM " . $this->table . " WHERE id = :message_id";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bindParam(':message_id', $message_id);
        
        return $delete_stmt->execute();
    }
}
?>
