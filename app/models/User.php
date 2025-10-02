<?php
/**
 * Modello User per la gestione degli utenti
 */
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $nome;
    public $cognome;
    public $email;
    public $password;
    public $data_nascita;
    public $sesso;
    public $cellulare;
    public $user_type; // 'participant', 'organizer', 'admin'
    public $certificato_medico;
    public $tipo_certificato;
    public $tessera_affiliazione;
    public $scadenza_certificato;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrazione nuovo utente
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                 SET nome=:nome, cognome=:cognome, email=:email, password=:password, 
                     data_nascita=:data_nascita, sesso=:sesso, telefono=:cellulare,
                     ruolo=:user_type";

        $stmt = $this->conn->prepare($query);

        // Hash della password
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind dei parametri
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':cognome', $this->cognome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':data_nascita', $this->data_nascita);
        $stmt->bindParam(':sesso', $this->sesso);
        $stmt->bindParam(':cellulare', $this->cellulare);
        // Mappatura user_type per il database
        $ruolo_map = [
            'participant' => 'atleta',
            'organizer' => 'organizzatore', 
            'admin' => 'admin'
        ];
        $ruolo_db = $ruolo_map[$this->user_type] ?? 'atleta';
        $stmt->bindParam(':user_type', $ruolo_db);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Login utente
    public function login($email, $password) {
        $query = "SELECT user_id as id, nome, cognome, email, password, ruolo as user_type, data_registrazione as created_at 
                 FROM " . $this->table . " 
                 WHERE email = :email AND attivo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->nome = $row['nome'];
                $this->cognome = $row['cognome'];
                $this->email = $row['email'];
                
                // Mappatura inversa ruolo database -> user_type
                $ruolo_map = [
                    'atleta' => 'participant',
                    'organizzatore' => 'organizer', 
                    'admin' => 'admin'
                ];
                $this->user_type = $ruolo_map[$row['user_type']] ?? 'participant';
                
                $this->created_at = $row['created_at'];
                return true;
            }
        }
        return false;
    }

    // Verifica se email esiste già
    public function emailExists() {
        $query = "SELECT user_id as id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Carica dati utente per ID
    public function readOne() {
        $query = "SELECT user_id as id, nome, cognome, email, data_nascita, sesso, telefono, ruolo as user_type, certificato_medico, data_registrazione as created_at 
                 FROM " . $this->table . " WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->nome = $row['nome'];
            $this->cognome = $row['cognome'];
            $this->email = $row['email'];
            $this->data_nascita = $row['data_nascita'] ?? null;
            $this->sesso = $row['sesso'] ?? null;
            $this->cellulare = $row['telefono'] ?? null;
            
            // Mappatura inversa ruolo database -> user_type
            $ruolo_map = [
                'atleta' => 'participant',
                'organizzatore' => 'organizer', 
                'admin' => 'admin'
            ];
            $this->user_type = $ruolo_map[$row['user_type']] ?? 'participant';
            $this->certificato_medico = $row['certificato_medico'] ?? null;
            $this->created_at = $row['created_at'];
            
            return true;
        }
        return false;
    }

    // Aggiorna profilo utente
    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET nome=:nome, cognome=:cognome, data_nascita=:data_nascita, 
                     sesso=:sesso, cellulare=:cellulare, updated_at=NOW()";
        
        // Aggiungi campi opzionali se presenti
        if ($this->certificato_medico) {
            $query .= ", certificato_medico=:certificato_medico";
        }
        if ($this->tessera_affiliazione) {
            $query .= ", tessera_affiliazione=:tessera_affiliazione";
        }
        
        $query .= " WHERE user_id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':cognome', $this->cognome);
        $stmt->bindParam(':data_nascita', $this->data_nascita);
        $stmt->bindParam(':sesso', $this->sesso);
        $stmt->bindParam(':cellulare', $this->cellulare);
        $stmt->bindParam(':id', $this->id);

        if ($this->certificato_medico) {
            $stmt->bindParam(':certificato_medico', $this->certificato_medico);
        }
        if ($this->tessera_affiliazione) {
            $stmt->bindParam(':tessera_affiliazione', $this->tessera_affiliazione);
        }

        return $stmt->execute();
    }



    // Ottieni statistiche utente
    public function getStatistics() {
        $stats = [];
        
        // Numero iscrizioni
        $query = "SELECT COUNT(*) as total_registrations FROM registrations WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->execute();
        $stats['total_registrations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_registrations'];
        
        // Eventi completati
        $query = "SELECT COUNT(*) as completed_events 
                 FROM registrations r 
                 JOIN events e ON r.event_id = e.event_id 
                 WHERE r.user_id = :user_id AND e.data_evento < NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->execute();
        $stats['completed_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['completed_events'];
        
        return $stats;
    }

    // Cambio password
    public function changePassword($old_password, $new_password) {
        // Verifica password attuale
        $query = "SELECT password FROM " . $this->table . " WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($old_password, $row['password'])) {
                // Aggiorna con nuova password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE " . $this->table . " SET password=:password WHERE user_id=:id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(':password', $new_password_hash);
                $update_stmt->bindParam(':id', $this->id);
                
                return $update_stmt->execute();
            }
        }
        return false;
    }

    // Upload certificato medico
    public function uploadCertificato($file, $tipo_certificato, $scadenza = null) {
        $upload_dir = __DIR__ . '/../../uploads/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
        
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            return false;
        }

        $filename = 'cert_' . $this->id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $this->certificato_medico = 'certificates/' . $filename;
            $this->tipo_certificato = $tipo_certificato;
            $this->scadenza_certificato = $scadenza;
            
            // Aggiorna database
            $query = "UPDATE " . $this->table . " 
                     SET certificato_medico=:certificato_medico
                     WHERE user_id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':certificato_medico', $this->certificato_medico);
            $stmt->bindParam(':tipo_certificato', $this->tipo_certificato);
            $stmt->bindParam(':scadenza_certificato', $this->scadenza_certificato);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        }
        return false;
    }

    // Upload tessera affiliazione
    public function uploadTessera($file) {
        $upload_dir = __DIR__ . '/../../uploads/cards/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
        
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            return false;
        }

        $filename = 'card_' . $this->id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $this->tessera_affiliazione = 'cards/' . $filename;
            
            // Aggiorna database
            $query = "UPDATE " . $this->table . " SET tessera_affiliazione=:tessera_affiliazione WHERE user_id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':tessera_affiliazione', $this->tessera_affiliazione);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        }
        return false;
    }

    // Controlla scadenza certificato medico
    public function isCertificatoScaduto() {
        if (!$this->scadenza_certificato) {
            return false;
        }
        
        $scadenza = new DateTime($this->scadenza_certificato);
        $oggi = new DateTime();
        
        return $scadenza < $oggi;
    }

    // Giorni rimanenti prima della scadenza certificato
    public function giorniScadenzaCertificato() {
        if (!$this->scadenza_certificato) {
            return null;
        }
        
        $scadenza = new DateTime($this->scadenza_certificato);
        $oggi = new DateTime();
        
        if ($scadenza < $oggi) {
            return 0;
        }
        
        return $oggi->diff($scadenza)->days;
    }

    // Ottieni storico iscrizioni utente
    public function getRegistrationHistory() {
        $query = "SELECT r.*, e.titolo as event_title, e.data_evento, e.luogo_partenza, 
                         e.sport, e.distanza_km, e.prezzo_base,
                         r.prezzo_pagato
                 FROM registrations r
                 JOIN events e ON r.event_id = e.event_id
                 WHERE r.user_id = :user_id
                 ORDER BY r.registration_id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
