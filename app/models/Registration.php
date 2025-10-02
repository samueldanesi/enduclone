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
    public $data_registrazione;
    public $stato; // 'pending', 'confermata', 'pagata', 'annullata'
    public $prezzo_pagato;
    public $totale_pagato;
    public $metodo_pagamento; // 'contanti', 'carta', 'bonifico', 'paypal', 'stripe'
    public $note;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crea nuova iscrizione
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET user_id=:user_id, event_id=:event_id, prezzo_pagato=:prezzo_pagato,
                     totale_pagato=:totale_pagato, metodo_pagamento=:metodo_pagamento, 
                     stato=:stato, note=:note, data_registrazione=NOW()";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':event_id', $this->event_id);
        $stmt->bindParam(':prezzo_pagato', $this->prezzo_pagato);
        $stmt->bindParam(':totale_pagato', $this->totale_pagato);
        $stmt->bindParam(':metodo_pagamento', $this->metodo_pagamento);
        $stmt->bindParam(':stato', $this->stato);
        $stmt->bindParam(':note', $this->note);

        if ($stmt->execute()) {
            $this->registration_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Verifica se l'utente è già iscritto all'evento
    public function isUserRegistered($user_id, $event_id) {
        $query = "SELECT registration_id FROM " . $this->table . " 
                 WHERE user_id = :user_id AND event_id = :event_id 
                 AND (stato IN ('pending', 'confermata', 'pagata') OR stato IS NULL OR stato = '')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Ottieni iscrizioni dell'utente
    public function getUserRegistrations($user_id) {
        $query = "SELECT r.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
                         e.sport, e.categoria_id
                 FROM " . $this->table . " r
                 JOIN events e ON r.event_id = e.event_id
                 WHERE r.user_id = :user_id
                 ORDER BY r.data_registrazione DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt;
    }

    // Ottieni iscrizioni dell'evento (per organizzatori)
    public function getEventRegistrations($event_id) {
        $query = "SELECT r.*, u.nome, u.cognome, u.email, u.telefono, 
                         u.data_nascita, u.sesso
                 FROM " . $this->table . " r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.event_id = :event_id AND r.stato = 'confermata'
                 ORDER BY r.data_registrazione ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Conta partecipanti confermati per evento
    public function countConfirmedParticipants($event_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE event_id = :event_id AND stato = 'confermata'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // Aggiorna status iscrizione
    public function updateStatus($registration_id, $new_status) {
        $query = "UPDATE " . $this->table . " 
                 SET stato=:stato, updated_at=NOW() 
                 WHERE registration_id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stato', $new_status);
        $stmt->bindParam(':id', $registration_id);

        return $stmt->execute();
    }

    // Cancella iscrizione
    public function cancel($user_id, $event_id) {
        $query = "UPDATE " . $this->table . " 
                 SET stato='annullata', updated_at=NOW() 
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
                 JOIN events e ON r.event_id = e.event_id
                 JOIN users u ON r.user_id = u.user_id
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
                 WHERE event_id = :event_id AND stato = 'confermata'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $stats['total_confirmed'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_confirmed'];

        // Totale incassi
        $query = "SELECT SUM(prezzo_pagato) as total_revenue
                 FROM " . $this->table . " 
                 WHERE event_id = :event_id AND stato = 'confermata'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

        // Iscrizioni per giorno (ultimi 30 giorni)
        $query = "SELECT DATE(data_registrazione) as date, COUNT(*) as count
                 FROM " . $this->table . " 
                 WHERE event_id = :event_id 
                   AND data_registrazione >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                   AND stato = 'confermata'
                 GROUP BY DATE(data_registrazione)
                 ORDER BY date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $stats['daily_registrations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    // Verifica disponibilità posti
    public function checkEventAvailability($event_id) {
        $query = "SELECT e.max_partecipanti, COUNT(r.registration_id) as current_registrations
                 FROM events e
                 LEFT JOIN " . $this->table . " r ON e.event_id = r.event_id AND r.stato = 'confermata'
                 WHERE e.event_id = :event_id
                 GROUP BY e.event_id";

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
