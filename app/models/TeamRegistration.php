<?php
/**
 * Modello TeamRegistration per gestire le iscrizioni collettive dei team
 */
class TeamRegistration {
    private $conn;
    private $table = 'team_registrations';

    public $id;
    public $team_id;
    public $event_id;
    public $responsabile_nome;
    public $responsabile_email;
    public $responsabile_telefono;
    public $numero_partecipanti;
    public $quota_individuale;
    public $sconto_percentuale;
    public $quota_totale;
    public $note;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crea nuova iscrizione collettiva
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET team_id=:team_id, event_id=:event_id, 
                     responsabile_nome=:responsabile_nome, responsabile_email=:responsabile_email,
                     responsabile_telefono=:responsabile_telefono, numero_partecipanti=:numero_partecipanti,
                     quota_individuale=:quota_individuale, sconto_percentuale=:sconto_percentuale,
                     quota_totale=:quota_totale, note=:note, status=:status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':team_id', $this->team_id);
        $stmt->bindParam(':event_id', $this->event_id);
        $stmt->bindParam(':responsabile_nome', $this->responsabile_nome);
        $stmt->bindParam(':responsabile_email', $this->responsabile_email);
        $stmt->bindParam(':responsabile_telefono', $this->responsabile_telefono);
        $stmt->bindParam(':numero_partecipanti', $this->numero_partecipanti);
        $stmt->bindParam(':quota_individuale', $this->quota_individuale);
        $stmt->bindParam(':sconto_percentuale', $this->sconto_percentuale);
        $stmt->bindParam(':quota_totale', $this->quota_totale);
        $stmt->bindParam(':note', $this->note);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Iscrizione collettiva da Excel
    public function createFromExcel($team_id, $event_id, $participants_data, $responsabile_data) {
        // Inizia transazione
        $this->conn->beginTransaction();

        try {
            // Ottieni dati evento per calcolare quota
            $event_query = "SELECT quota FROM events WHERE id = :event_id";
            $event_stmt = $this->conn->prepare($event_query);
            $event_stmt->bindParam(':event_id', $event_id);
            $event_stmt->execute();
            $event = $event_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                throw new Exception("Evento non trovato");
            }

            $quota_individuale = $event['quota'];
            $numero_partecipanti = count($participants_data);
            
            // Calcola sconto in base al numero di partecipanti
            $sconto_percentuale = $this->calculateGroupDiscount($numero_partecipanti);
            $quota_totale = $quota_individuale * $numero_partecipanti * (1 - $sconto_percentuale / 100);

            // Crea iscrizione collettiva
            $this->team_id = $team_id;
            $this->event_id = $event_id;
            $this->responsabile_nome = $responsabile_data['nome'];
            $this->responsabile_email = $responsabile_data['email'];
            $this->responsabile_telefono = $responsabile_data['telefono'] ?? '';
            $this->numero_partecipanti = $numero_partecipanti;
            $this->quota_individuale = $quota_individuale;
            $this->sconto_percentuale = $sconto_percentuale;
            $this->quota_totale = $quota_totale;
            $this->note = "Iscrizione collettiva da Excel";
            $this->status = 'pending';

            if (!$this->create()) {
                throw new Exception("Errore nella creazione dell'iscrizione collettiva");
            }

            $team_registration_id = $this->id;

            // Crea iscrizioni individuali per ogni partecipante
            foreach ($participants_data as $participant) {
                // Verifica o crea utente
                $user_id = $this->getOrCreateUser($participant);
                
                // Crea iscrizione individuale
                $registration_id = $this->createIndividualRegistration($user_id, $event_id, $quota_individuale, $team_registration_id);
                
                // Collega all'iscrizione collettiva
                $this->linkToTeamRegistration($team_registration_id, $registration_id, $user_id);
            }

            $this->conn->commit();
            return $team_registration_id;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Calcola sconto di gruppo
    private function calculateGroupDiscount($numero_partecipanti) {
        if ($numero_partecipanti >= 50) return 20;      // 20% per 50+ partecipanti
        if ($numero_partecipanti >= 25) return 15;      // 15% per 25-49 partecipanti
        if ($numero_partecipanti >= 15) return 10;      // 10% per 15-24 partecipanti
        if ($numero_partecipanti >= 10) return 5;       // 5% per 10-14 partecipanti
        return 0;                                       // Nessuno sconto sotto i 10
    }

    // Ottieni o crea utente da dati Excel
    private function getOrCreateUser($participant_data) {
        // Cerca utente esistente per email
        $user_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $user_stmt = $this->conn->prepare($user_query);
        $user_stmt->bindParam(':email', $participant_data['email']);
        $user_stmt->execute();
        $existing_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_user) {
            return $existing_user['id'];
        }

        // Crea nuovo utente
        $insert_user = "INSERT INTO users 
                       SET nome=:nome, cognome=:cognome, email=:email, 
                           data_nascita=:data_nascita, sesso=:sesso, 
                           codice_fiscale=:codice_fiscale, telefono=:telefono,
                           cellulare=:cellulare, user_type='participant',
                           status='active', created_at=NOW()";

        $user_stmt = $this->conn->prepare($insert_user);
        $user_stmt->bindParam(':nome', $participant_data['nome']);
        $user_stmt->bindParam(':cognome', $participant_data['cognome']);
        $user_stmt->bindParam(':email', $participant_data['email']);
        $user_stmt->bindParam(':data_nascita', $participant_data['data_nascita']);
        $user_stmt->bindParam(':sesso', $participant_data['sesso']);
        $user_stmt->bindParam(':codice_fiscale', $participant_data['codice_fiscale']);
        $user_stmt->bindParam(':telefono', $participant_data['telefono'] ?? '');
        $user_stmt->bindParam(':cellulare', $participant_data['cellulare'] ?? '');

        if ($user_stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        throw new Exception("Errore nella creazione dell'utente: " . $participant_data['email']);
    }

    // Crea iscrizione individuale
    private function createIndividualRegistration($user_id, $event_id, $quota, $team_registration_id) {
        $reg_query = "INSERT INTO registrations 
                     SET user_id=:user_id, event_id=:event_id, quota=:quota,
                         team_registration_id=:team_registration_id,
                         status='confirmed', created_at=NOW()";

        $reg_stmt = $this->conn->prepare($reg_query);
        $reg_stmt->bindParam(':user_id', $user_id);
        $reg_stmt->bindParam(':event_id', $event_id);
        $reg_stmt->bindParam(':quota', $quota);
        $reg_stmt->bindParam(':team_registration_id', $team_registration_id);

        if ($reg_stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        throw new Exception("Errore nella creazione dell'iscrizione per user_id: " . $user_id);
    }

    // Collega iscrizione alla registrazione di team
    private function linkToTeamRegistration($team_registration_id, $registration_id, $user_id) {
        $link_query = "INSERT INTO team_registration_members 
                      SET team_registration_id=:team_registration_id, 
                          registration_id=:registration_id, user_id=:user_id";

        $link_stmt = $this->conn->prepare($link_query);
        $link_stmt->bindParam(':team_registration_id', $team_registration_id);
        $link_stmt->bindParam(':registration_id', $registration_id);
        $link_stmt->bindParam(':user_id', $user_id);

        return $link_stmt->execute();
    }

    // Ottieni iscrizioni collettive di un team
    public function getTeamRegistrations($team_id) {
        $query = "SELECT tr.*, e.titolo as evento_nome, e.data_evento as evento_data,
                        e.luogo_partenza as evento_location, t.nome as team_nome
                 FROM " . $this->table . " tr
                 JOIN events e ON tr.event_id = e.id
                 JOIN teams t ON tr.team_id = t.id
                 WHERE tr.team_id = :team_id
                 ORDER BY tr.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni dettagli partecipanti di un'iscrizione collettiva
    public function getRegistrationMembers($team_registration_id) {
        $query = "SELECT u.nome, u.cognome, u.email, u.data_nascita, u.sesso,
                        r.status as registration_status, r.created_at as iscritto_il
                 FROM team_registration_members trm
                 JOIN users u ON trm.user_id = u.id
                 JOIN registrations r ON trm.registration_id = r.id
                 WHERE trm.team_registration_id = :team_registration_id
                 ORDER BY u.cognome, u.nome";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_registration_id', $team_registration_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Valida file Excel
    public function validateExcelData($excel_data) {
        $errors = [];
        $required_fields = ['nome', 'cognome', 'email', 'data_nascita', 'sesso'];

        foreach ($excel_data as $row_index => $row) {
            $row_errors = [];
            
            // Verifica campi obbligatori
            foreach ($required_fields as $field) {
                if (empty($row[$field])) {
                    $row_errors[] = "Campo '{$field}' mancante";
                }
            }

            // Valida email
            if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $row_errors[] = "Email non valida";
            }

            // Valida data nascita
            if (!empty($row['data_nascita'])) {
                $date = DateTime::createFromFormat('Y-m-d', $row['data_nascita']);
                if (!$date || $date->format('Y-m-d') !== $row['data_nascita']) {
                    $row_errors[] = "Data nascita non valida (formato: YYYY-MM-DD)";
                }
            }

            // Valida sesso
            if (!empty($row['sesso']) && !in_array(strtoupper($row['sesso']), ['M', 'F'])) {
                $row_errors[] = "Sesso deve essere M o F";
            }

            if (!empty($row_errors)) {
                $errors["Riga " . ($row_index + 2)] = $row_errors; // +2 perché Excel inizia da 1 e la prima è intestazione
            }
        }

        return $errors;
    }

    // Aggiorna status iscrizione collettiva
    public function updateStatus($team_registration_id, $status) {
        $query = "UPDATE " . $this->table . " SET status=:status WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $team_registration_id);
        
        return $stmt->execute();
    }

    // Ottieni statistiche iscrizioni collettive
    public function getStats($team_id = null) {
        $query = "SELECT 
                    COUNT(*) as totale_iscrizioni,
                    SUM(numero_partecipanti) as totale_partecipanti,
                    SUM(quota_totale) as totale_incassi,
                    AVG(sconto_percentuale) as sconto_medio
                 FROM " . $this->table;
        
        if ($team_id) {
            $query .= " WHERE team_id = :team_id";
        }

        $stmt = $this->conn->prepare($query);
        if ($team_id) {
            $stmt->bindParam(':team_id', $team_id);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
