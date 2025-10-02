<?php
/**
 * Modello per Iscrizioni Collettive
 * Sistema CORRETTO e LOGICO per gestire team che si iscrivono agli eventi
 */
class CollectiveRegistration {
    private $conn;
    
    // Tabelle
    private $table = 'team_collective_registrations';
    private $participants_table = 'team_collective_participants';
    private $discount_rules_table = 'collective_discount_rules';
    
    // Proprietà
    public $id;
    public $team_id;
    public $event_id;
    public $responsible_user_id;
    public $responsible_name;
    public $responsible_email;
    public $responsible_phone;
    public $excel_filename;
    public $excel_file_path;
    public $total_participants;
    public $base_price_per_person;
    public $discount_percentage;
    public $discounted_price_per_person;
    public $total_amount;
    public $payment_method;
    public $payment_status;
    public $status;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Crea nuova iscrizione collettiva da Excel
     */
    public function createFromExcel($team_id, $event_id, $responsible_user_id, $excel_file, $responsible_data) {
        $this->conn->beginTransaction();
        
        try {
            // 1. Ottieni dati evento per prezzo base
            $event = $this->getEventDetails($event_id);
            if (!$event) {
                throw new Exception("Evento non trovato");
            }
            
            // 2. Processa file Excel e ottieni partecipanti
            $participants_data = $this->processExcelFile($excel_file);
            if (empty($participants_data)) {
                throw new Exception("Nessun partecipante valido trovato nel file Excel");
            }
            
            // 3. Calcola prezzi e sconti
            $pricing = $this->calculatePricing(count($participants_data), $event['prezzo_base']);
            
            // 4. Salva file Excel
            $excel_info = $this->saveExcelFile($excel_file);
            
            // 5. Crea record principale iscrizione collettiva
            $collective_id = $this->createMainRegistration([
                'team_id' => $team_id,
                'event_id' => $event_id,
                'responsible_user_id' => $responsible_user_id,
                'responsible_name' => $responsible_data['name'],
                'responsible_email' => $responsible_data['email'],
                'responsible_phone' => $responsible_data['phone'] ?? '',
                'excel_filename' => $excel_info['original_name'],
                'excel_file_path' => $excel_info['saved_path'],
                'total_participants' => count($participants_data),
                'base_price_per_person' => $event['prezzo_base'],
                'discount_percentage' => $pricing['discount_percentage'],
                'discounted_price_per_person' => $pricing['price_per_person'],
                'total_amount' => $pricing['total_amount'],
                'notes' => $responsible_data['notes'] ?? ''
            ]);
            
            // 6. Salva partecipanti
            $this->saveParticipants($collective_id, $participants_data);
            
            $this->conn->commit();
            return $collective_id;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    /**
     * Ottieni dettagli evento
     */
    private function getEventDetails($event_id) {
        $query = "SELECT id, titolo, prezzo_base, data_evento, luogo_partenza, capienza_massima 
                 FROM events WHERE id = :event_id AND status = 'published'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Processa file Excel/CSV
     */
    private function processExcelFile($excel_file) {
        $participants = [];
        $file_extension = strtolower(pathinfo($excel_file['name'], PATHINFO_EXTENSION));
        
        if ($file_extension === 'csv') {
            $participants = $this->parseCsvFile($excel_file['tmp_name']);
        } elseif (in_array($file_extension, ['xlsx', 'xls'])) {
            // TODO: Implementare parser Excel con PhpSpreadsheet quando disponibile
            throw new Exception("File Excel non ancora supportati. Utilizzare formato CSV.");
        } else {
            throw new Exception("Formato file non supportato. Utilizzare CSV o Excel.");
        }
        
        return $this->validateAndCleanParticipants($participants);
    }
    
    /**
     * Parse file CSV
     */
    private function parseCsvFile($file_path) {
        $participants = [];
        
        if (($handle = fopen($file_path, 'r')) === FALSE) {
            throw new Exception("Impossibile aprire il file CSV");
        }
        
        // Leggi header (prima riga)
        $headers = fgetcsv($handle, 1000, ",", '"', '\\');
        if (!$headers) {
            fclose($handle);
            throw new Exception("File CSV vuoto o corrotto");
        }
        
        // Mappa header ai campi
        $field_mapping = $this->getFieldMapping();
        $header_map = $this->mapHeaders($headers, $field_mapping);
        
        // Leggi dati
        $row_number = 2; // Inizia dalla riga 2 (dopo header)
        while (($row_data = fgetcsv($handle, 1000, ",", '"', '\\')) !== FALSE) {
            if ($this->isEmptyRow($row_data)) {
                $row_number++;
                continue;
            }
            
            $participant = ['row_number' => $row_number];
            
            // Mappa dati usando header_map
            foreach ($header_map as $csv_index => $db_field) {
                if (isset($row_data[$csv_index]) && $row_data[$csv_index] !== null) {
                    $participant[$db_field] = trim((string)$row_data[$csv_index]);
                }
            }
            
            $participants[] = $participant;
            $row_number++;
        }
        
        fclose($handle);
        return $participants;
    }
    
    /**
     * Mapping campi CSV → Database (semplificato)
     */
    private function getFieldMapping() {
        return [
            'nome' => ['nome', 'name', 'first_name', 'firstname'],
            'cognome' => ['cognome', 'surname', 'last_name', 'lastname', 'family_name'],
            'email' => ['email', 'e-mail', 'mail', 'posta', 'email (opzionale)'],
            'telefono' => ['telefono', 'phone', 'tel', 'cellulare', 'mobile', 'cell', 'telefono (opzionale)']
        ];
    }
    
    /**
     * Mappa header CSV ai campi database
     */
    private function mapHeaders($headers, $field_mapping) {
        $header_map = [];
        
        foreach ($headers as $index => $header) {
            $header_clean = strtolower(trim((string)$header));
            
            foreach ($field_mapping as $db_field => $variants) {
                foreach ($variants as $variant) {
                    if ($header_clean === $variant) {
                        $header_map[$index] = $db_field;
                        break 2;
                    }
                }
            }
        }
        
        return $header_map;
    }
    
    /**
     * Verifica se riga è vuota
     */
    private function isEmptyRow($row_data) {
        return empty(array_filter($row_data, function($value) {
            return !empty(trim((string)$value));
        }));
    }
    
    /**
     * Valida e pulisce dati partecipanti (solo nome e cognome obbligatori)
     */
    private function validateAndCleanParticipants($participants) {
        $validated = [];
        $errors = [];
        
        foreach ($participants as $index => $participant) {
            $validation_errors = [];
            
            // Solo nome e cognome sono obbligatori
            if (empty($participant['nome'])) {
                $validation_errors[] = "Nome mancante";
            }
            if (empty($participant['cognome'])) {
                $validation_errors[] = "Cognome mancante";
            }
            
            // Valida email solo se presente
            if (!empty($participant['email']) && !filter_var($participant['email'], FILTER_VALIDATE_EMAIL)) {
                $validation_errors[] = "Email non valida: " . $participant['email'];
            }
            
            // Pulisce il telefono se presente
            if (!empty($participant['telefono'])) {
                $participant['cellulare'] = $participant['telefono']; // Usa telefono come cellulare
            }
            
            if (!empty($validation_errors)) {
                $errors["Riga {$participant['row_number']}"] = $validation_errors;
            } else {
                $validated[] = $participant;
            }
        }
        
        if (!empty($errors)) {
            throw new Exception("Errori di validazione: " . json_encode($errors, JSON_PRETTY_PRINT));
        }
        
        return $validated;
    }
    
    /**
     * Parse flessibile delle date
     */
    private function parseDate($date_string) {
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $date_string);
            if ($date && $date->format($format) === $date_string) {
                return $date->format('Y-m-d');
            }
        }
        
        return null;
    }
    
    /**
     * Calcola prezzi e sconti automatici
     */
    private function calculatePricing($participant_count, $base_price) {
        // Ottieni regola sconto applicabile
        $discount_rule = $this->getApplicableDiscount($participant_count);
        
        $discount_percentage = $discount_rule ? $discount_rule['discount_percentage'] : 0;
        $price_per_person = $base_price * (1 - $discount_percentage / 100);
        $total_amount = $price_per_person * $participant_count;
        
        return [
            'discount_percentage' => $discount_percentage,
            'price_per_person' => $price_per_person,
            'total_amount' => $total_amount,
            'discount_rule' => $discount_rule
        ];
    }
    
    /**
     * Ottieni regola sconto applicabile
     */
    private function getApplicableDiscount($participant_count) {
        $query = "SELECT * FROM {$this->discount_rules_table} 
                 WHERE active = 1 
                 AND min_participants <= :count 
                 AND (max_participants IS NULL OR max_participants >= :count)
                 ORDER BY discount_percentage DESC 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':count', $participant_count);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Salva file Excel sul server
     */
    private function saveExcelFile($excel_file) {
        $upload_dir = __DIR__ . '/../../uploads/collective_registrations/';
        
        // Crea directory se non esistente
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $original_name = $excel_file['name'];
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $safe_filename = 'collective_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $extension;
        $full_path = $upload_dir . $safe_filename;
        
        if (!move_uploaded_file($excel_file['tmp_name'], $full_path)) {
            throw new Exception("Errore nel salvare il file Excel");
        }
        
        return [
            'original_name' => $original_name,
            'saved_path' => 'uploads/collective_registrations/' . $safe_filename
        ];
    }
    
    /**
     * Crea record principale iscrizione collettiva
     */
    private function createMainRegistration($data) {
        $query = "INSERT INTO {$this->table} SET 
                 team_id = :team_id,
                 event_id = :event_id,
                 responsible_user_id = :responsible_user_id,
                 responsible_name = :responsible_name,
                 responsible_email = :responsible_email,
                 responsible_phone = :responsible_phone,
                 excel_filename = :excel_filename,
                 excel_file_path = :excel_file_path,
                 total_participants = :total_participants,
                 base_price_per_person = :base_price_per_person,
                 discount_percentage = :discount_percentage,
                 discounted_price_per_person = :discounted_price_per_person,
                 total_amount = :total_amount,
                 notes = :notes,
                 status = 'submitted'";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind solo i parametri che esistono nella query
        $params = [
            'team_id' => $data['team_id'],
            'event_id' => $data['event_id'],
            'responsible_user_id' => $data['responsible_user_id'],
            'responsible_name' => $data['responsible_name'],
            'responsible_email' => $data['responsible_email'],
            'responsible_phone' => $data['responsible_phone'] ?? '',
            'excel_filename' => $data['excel_filename'],
            'excel_file_path' => $data['excel_file_path'],
            'total_participants' => $data['total_participants'],
            'base_price_per_person' => $data['base_price_per_person'],
            'discount_percentage' => $data['discount_percentage'],
            'discounted_price_per_person' => $data['discounted_price_per_person'],
            'total_amount' => $data['total_amount'],
            'notes' => $data['notes'] ?? ''
        ];
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        throw new Exception("Errore nella creazione dell'iscrizione collettiva");
    }
    
    /**
     * Salva partecipanti
     */
    private function saveParticipants($collective_id, $participants_data) {
        $query = "INSERT INTO {$this->participants_table} SET
                 collective_registration_id = :collective_id,
                 nome = :nome,
                 cognome = :cognome,
                 email = :email,
                 data_nascita = :data_nascita,
                 sesso = :sesso,
                 codice_fiscale = :codice_fiscale,
                 telefono = :telefono,
                 cellulare = :cellulare,
                 citta = :citta,
                 provincia = :provincia,
                 categoria_agonistica = :categoria_agonistica,
                 tessera_federale = :tessera_federale,
                 società_appartenenza = :società_appartenenza,
                 row_number = :row_number";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($participants_data as $participant) {
            $stmt->bindParam(':collective_id', $collective_id);
            
            // Bind tutti i campi
            $fields = ['nome', 'cognome', 'email', 'data_nascita', 'sesso', 'codice_fiscale', 
                      'telefono', 'cellulare', 'citta', 'provincia', 'categoria_agonistica', 
                      'tessera_federale', 'società_appartenenza', 'row_number'];
            
            foreach ($fields as $field) {
                $value = $participant[$field] ?? null;
                $stmt->bindValue(':' . $field, $value);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Errore nel salvare partecipante: " . $participant['nome'] . ' ' . $participant['cognome']);
            }
        }
    }
    
    /**
     * Ottieni iscrizioni collettive di un team
     */
    public function getTeamCollectiveRegistrations($team_id) {
        $query = "SELECT cr.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
                        t.nome as team_name
                 FROM {$this->table} cr
                 JOIN events e ON cr.event_id = e.id
                 JOIN teams t ON cr.team_id = t.id
                 WHERE cr.team_id = :team_id
                 ORDER BY cr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ottieni dettagli iscrizione collettiva
     */
    public function getCollectiveRegistrationDetails($registration_id, $team_id = null) {
        $where_clause = "cr.id = :registration_id";
        $params = [':registration_id' => $registration_id];
        
        if ($team_id) {
            $where_clause .= " AND cr.team_id = :team_id";
            $params[':team_id'] = $team_id;
        }
        
        $query = "SELECT cr.*, e.titolo as event_title, e.data_evento, e.luogo_partenza,
                        e.prezzo_base as event_base_price, t.nome as team_name
                 FROM {$this->table} cr
                 JOIN events e ON cr.event_id = e.id
                 JOIN teams t ON cr.team_id = t.id
                 WHERE {$where_clause}";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ottieni partecipanti di un'iscrizione collettiva
     */
    public function getCollectiveParticipants($registration_id) {
        $query = "SELECT * FROM {$this->participants_table}
                 WHERE collective_registration_id = :registration_id
                 ORDER BY cognome, nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':registration_id', $registration_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Aggiorna status pagamento
     */
    public function updatePaymentStatus($registration_id, $status, $transaction_id = null) {
        $query = "UPDATE {$this->table} SET 
                 payment_status = :status,
                 payment_transaction_id = :transaction_id,
                 payment_date = CASE WHEN :status = 'paid' THEN NOW() ELSE payment_date END
                 WHERE id = :registration_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->bindParam(':registration_id', $registration_id);
        
        return $stmt->execute();
    }
    
    /**
     * Conferma iscrizione collettiva
     */
    public function confirmRegistration($registration_id) {
        $query = "UPDATE {$this->table} SET 
                 status = 'confirmed',
                 confirmed_at = NOW()
                 WHERE id = :registration_id AND payment_status = 'paid'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':registration_id', $registration_id);
        
        return $stmt->execute();
    }
    
    /**
     * Ottieni statistiche sconti disponibili
     */
    public function getAvailableDiscounts() {
        $query = "SELECT * FROM {$this->discount_rules_table} 
                 WHERE active = 1 
                 ORDER BY min_participants ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Genera template CSV semplificato
     */
    public function generateCsvTemplate() {
        $headers = [
            'Nome', 'Cognome', 'Email (opzionale)', 'Telefono (opzionale)'
        ];
        
        $sample_data = [
            ['Mario', 'Rossi', 'mario.rossi@email.com', '333-1234567'],
            ['Anna', 'Verdi', 'anna.verdi@email.com', '333-7654321'],
            ['Luigi', 'Bianchi', '', '333-9876543']
        ];
        
        return [
            'headers' => $headers,
            'sample_data' => $sample_data
        ];
    }

    // ==========================================
    // METODI PER IL NUOVO CONTROLLER
    // ==========================================

    /**
     * Ottieni iscrizioni collettive per team
     */
    public function getByTeam($team_id) {
        $query = "SELECT cr.*, e.titolo as event_name 
                 FROM {$this->table} cr
                 LEFT JOIN events e ON cr.event_id = e.id
                 WHERE cr.team_id = :team_id
                 ORDER BY cr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trova iscrizione per ID
     */
    public function findById($registration_id) {
        $query = "SELECT cr.*, e.titolo as event_name, t.nome as team_name
                 FROM {$this->table} cr
                 LEFT JOIN events e ON cr.event_id = e.id
                 LEFT JOIN teams t ON cr.team_id = t.id
                 WHERE cr.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $registration_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Segna iscrizione come pagata
     */
    public function markAsPaid($registration_id) {
        $query = "UPDATE {$this->table} SET status = 'paid', updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $registration_id);
        
        return $stmt->execute();
    }

    /**
     * Conta iscrizioni per team
     */
    public function countByTeam($team_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE team_id = :team_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Conta iscrizioni completate per team
     */
    public function countCompletedByTeam($team_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                 WHERE team_id = :team_id AND status = 'completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Totale partecipanti per team
     */
    public function getTotalParticipantsByTeam($team_id) {
        $query = "SELECT SUM(total_participants) as total FROM {$this->table} 
                 WHERE team_id = :team_id AND status != 'cancelled'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Iscrizioni recenti per team
     */
    public function getRecentByTeam($team_id, $limit = 5) {
        $query = "SELECT cr.*, e.titolo as event_name 
                 FROM {$this->table} cr
                 LEFT JOIN events e ON cr.event_id = e.id
                 WHERE cr.team_id = :team_id
                 ORDER BY cr.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Metodi placeholder per statistiche avanzate
    public function getMonthlyStats($team_id) {
        return []; // TODO: Implementare
    }

    public function getEventBreakdown($team_id) {
        return []; // TODO: Implementare
    }

    public function getFinancialSummary($team_id) {
        return []; // TODO: Implementare
    }

    /**
     * Crea iscrizione rapida da form
     */
    public function createQuickRegistration($team_id, $event_id, $participants, $notes = '') {
        // Placeholder - usa il sistema esistente createFromExcel simulando un file
        $temp_file = tempnam(sys_get_temp_dir(), 'quick_reg_');
        $handle = fopen($temp_file, 'w');
        
        fputcsv($handle, ['Nome', 'Cognome'], ',', '"', '\\');
        foreach ($participants as $p) {
            if (!empty($p['nome']) && !empty($p['cognome'])) {
                fputcsv($handle, [$p['nome'], $p['cognome']], ',', '"', '\\');
            }
        }
        fclose($handle);

        $fake_file = [
            'name' => 'quick_registration.csv',
            'tmp_name' => $temp_file,
            'size' => filesize($temp_file),
            'type' => 'text/csv'
        ];

        try {
            // Aggiungo responsible_user_id (utente corrente) e responsible_data vuoto
            $responsible_user_id = $_SESSION['user_id'] ?? 1;
            $responsible_data = [];
            $result = $this->createFromExcel($team_id, $event_id, $responsible_user_id, $fake_file, $responsible_data);
            unlink($temp_file);
            return $result;
        } catch (Exception $e) {
            unlink($temp_file);
            throw $e;
        }
    }
}
?>