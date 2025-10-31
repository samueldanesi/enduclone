<?php
/**
 * Modello Registration per la gestione delle iscrizioni agli eventi
 */
class Registration {
    private $conn;
    private $table = 'registrations';

    public $registration_id;
    public $user_id;
    public $event_id;
    public $created_at; // sportevents_db
    public $status; // 'pending','confirmed','cancelled','refunded'
    public $prezzo_pagato;
    public $totale_pagato;
    public $metodo_pagamento; // opzionale
    public $note;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crea nuova iscrizione
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET user_id=:user_id, event_id=:event_id, prezzo_pagato=:prezzo_pagato,
                     status=:status, note=:note, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':event_id', $this->event_id);
        $stmt->bindParam(':prezzo_pagato', $this->prezzo_pagato);
    $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':note', $this->note);

        if ($stmt->execute()) {
            $this->registration_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Verifica se l'utente è già iscritto all'evento
    public function isUserRegistered($user_id, $event_id) {
    $query = "SELECT id FROM " . $this->table . " 
        WHERE user_id = :user_id AND event_id = :event_id 
        AND status IN ('confermata','confirmed')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Ottieni iscrizioni dell'utente
    public function getUserRegistrations($user_id) {
    $query = "SELECT r.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
             e.categoria_id
         FROM " . $this->table . " r
         JOIN events e ON r.event_id = e.id
         WHERE r.user_id = :user_id
         ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt;
    }

    // Ottieni iscrizioni dell'evento (per organizzatori)
    public function getEventRegistrations($event_id) {
    $query = "SELECT r.*, u.nome, u.cognome, u.email, u.cellulare, 
             u.data_nascita, u.sesso
         FROM " . $this->table . " r
         JOIN users u ON r.user_id = u.id
         WHERE r.event_id = :event_id AND r.status IN ('confermata','confirmed')
         ORDER BY r.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Conta partecipanti confermati per evento
    public function countConfirmedParticipants($event_id) {
    $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
        WHERE event_id = :event_id AND status IN ('confermata','confirmed')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // Aggiorna status iscrizione
    public function updateStatus($registration_id, $new_status) {
    $query = "UPDATE " . $this->table . " 
        SET status=:status, updated_at=NOW() 
        WHERE id=:id";

        $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $registration_id);

        return $stmt->execute();
    }

    // Cancella iscrizione
    public function cancel($user_id, $event_id) {
    $query = "UPDATE " . $this->table . " 
        SET status='cancelled', updated_at=NOW() 
        WHERE user_id=:user_id AND event_id=:event_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);

        return $stmt->execute();
    }

    // Ottieni dettagli iscrizione
    public function getRegistrationDetails($user_id, $event_id) {
    $query = "SELECT r.*, e.titolo as event_title, e.data_evento, e.prezzo_base,
             u.nome, u.cognome, u.email
         FROM " . $this->table . " r
         JOIN events e ON r.event_id = e.id
         JOIN users u ON r.user_id = u.id
         WHERE r.user_id = :user_id AND r.event_id = :event_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Statistiche iscrizioni per evento
    public function getEventRegistrationStats($event_id) {
        $stats = [];

        // Totale iscritti confermati
    $query = "SELECT COUNT(*) as total_confirmed
        FROM " . $this->table . " 
        WHERE event_id = :event_id AND status IN ('confermata','confirmed')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $stats['total_confirmed'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_confirmed'];

        // Totale incassi
    $query = "SELECT SUM(prezzo_pagato) as total_revenue
        FROM " . $this->table . " 
        WHERE event_id = :event_id AND status IN ('confermata','confirmed')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

        // Iscrizioni per giorno (ultimi 30 giorni)
                $query = "SELECT DATE(created_at) as date, COUNT(*) as count
                                 FROM " . $this->table . " 
                                 WHERE event_id = :event_id 
                                     AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                     AND status IN ('confermata','confirmed')
                                 GROUP BY DATE(created_at)
                                 ORDER BY date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $stats['daily_registrations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    // Verifica disponibilità posti
    public function checkEventAvailability($event_id) {
    $query = "SELECT e.capienza_massima AS max_partecipanti, COUNT(r.id) as current_registrations
        FROM events e
        LEFT JOIN " . $this->table . " r ON e.id = r.event_id AND r.status IN ('confermata','confirmed')
        WHERE e.id = :event_id
        GROUP BY e.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'available_spots' => $result['max_partecipanti'] - $result['current_registrations'],
                'total_capacity' => $result['max_partecipanti'],
                'current_registrations' => $result['current_registrations']
            ];
        }
        return false;
    }
}
?>
