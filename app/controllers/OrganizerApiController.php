<?php
/**
 * Controller per le API dell'organizzatore (export/import, stats real-time)
 */
class OrganizerApiController {
    private $db;
    private $registration;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->registration = new Registration($this->db);
        $this->user = new User($this->db);
    }

    // Esporta Excel degli iscritti
    public function exportExcel($event_id) {
        // Verifica che l'utente sia organizzatore dell'evento
        if (!$this->isEventOwner($event_id)) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorizzato']);
            return;
        }

        // Ottieni dati iscritti
        $query = "SELECT 
                    u.nome, u.cognome, u.email, u.cellulare, u.data_nascita, u.sesso,
                    r.created_at as data_iscrizione, r.prezzo_pagato, r.metodo_pagamento,
                    r.team_name, r.note
                 FROM registrations r
                 JOIN users u ON r.user_id = u.id
                 WHERE r.event_id = :event_id AND r.status = 'confirmed'
                 ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Headers per download Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="iscritti_evento_' . $event_id . '_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        // Genera Excel usando una libreria semplice
        $this->generateExcelFromArray($registrations);
    }

    // Importa Excel con iscrizioni di gruppo
    public function importExcel() {
        if (!isset($_FILES['excel_file']) || !isset($_POST['event_id'])) {
            echo json_encode(['success' => false, 'message' => 'File o event_id mancanti']);
            return;
        }

        $event_id = $_POST['event_id'];
        $pettorale_fee = floatval($_POST['pettorale_fee'] ?? 5.00);
        $group_discount = intval($_POST['group_discount'] ?? 0);

        // Verifica autorizzazione
        if (!$this->isEventOwner($event_id)) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            return;
        }

        try {
            // Processa file Excel/CSV
            $data = $this->parseExcelFile($_FILES['excel_file']);
            
            $imported = 0;
            $errors = [];
            $total_revenue = 0;

            foreach ($data as $row_index => $row) {
                try {
                    // Valida dati richiesti
                    $validation = $this->validateImportRow($row);
                    if (!$validation['valid']) {
                        $errors[] = "Riga " . ($row_index + 2) . ": " . $validation['message'];
                        continue;
                    }

                    // Crea o trova utente
                    $user_id = $this->createOrFindUser($row);
                    if (!$user_id) {
                        $errors[] = "Riga " . ($row_index + 2) . ": Impossibile creare utente";
                        continue;
                    }

                    // Verifica se già iscritto
                    if ($this->registration->isUserRegistered($user_id, $event_id)) {
                        $errors[] = "Riga " . ($row_index + 2) . ": Utente già iscritto";
                        continue;
                    }

                    // Calcola prezzo (con sconto gruppo e costo pettorale)
                    $event_price = $this->getEventPrice($event_id);
                    $final_price = $event_price + $pettorale_fee;
                    
                    if ($group_discount > 0) {
                        $final_price = $final_price * (1 - $group_discount / 100);
                    }

                    // Crea iscrizione
                    $this->registration->user_id = $user_id;
                    $this->registration->event_id = $event_id;
                    $this->registration->prezzo_pagato = $final_price;
                    $this->registration->metodo_pagamento = 'import_excel';
                    $this->registration->status = 'confirmed';
                    $this->registration->team_name = $row['squadra'] ?? null;
                    $this->registration->note = 'Importato da Excel - Pettorale: €' . $pettorale_fee;

                    if ($this->registration->create()) {
                        $imported++;
                        $total_revenue += $final_price;
                    } else {
                        $errors[] = "Riga " . ($row_index + 2) . ": Errore creazione iscrizione";
                    }

                } catch (Exception $e) {
                    $errors[] = "Riga " . ($row_index + 2) . ": " . $e->getMessage();
                }
            }

            echo json_encode([
                'success' => true,
                'imported' => $imported,
                'errors' => count($errors),
                'error_details' => $errors,
                'total_revenue' => round($total_revenue, 2)
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Stats in tempo reale
    public function realtimeStats($event_id) {
        if (!$this->isEventOwner($event_id)) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorizzato']);
            return;
        }

        $query = "SELECT 
                    COUNT(*) as total_registrations,
                    SUM(prezzo_pagato) as total_revenue,
                    AVG(prezzo_pagato) as avg_price
                 FROM registrations 
                 WHERE event_id = :event_id AND status = 'confirmed'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    // Verifica se l'utente è proprietario dell'evento
    private function isEventOwner($event_id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            return false;
        }

        $query = "SELECT COUNT(*) as count FROM events WHERE id = :event_id AND organizer_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    // Parsa file Excel/CSV
    private function parseExcelFile($file) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($extension === 'csv') {
            return $this->parseCSV($file['tmp_name']);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            // Per semplicità, converti in CSV o usa una libreria come PhpSpreadsheet
            throw new Exception('File Excel non ancora supportato. Usa CSV per ora.');
        } else {
            throw new Exception('Formato file non supportato');
        }
    }

    // Parsa CSV
    private function parseCSV($file_path) {
        $data = [];
        $header = null;
        
        if (($handle = fopen($file_path, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!$header) {
                    // Prima riga come header
                    $header = array_map('strtolower', $row);
                    $header = array_map('trim', $header);
                } else {
                    // Combina header con valori
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        
        return $data;
    }

    // Valida riga importazione
    private function validateImportRow($row) {
        $required = ['nome', 'cognome', 'email', 'data_nascita', 'sesso'];
        
        foreach ($required as $field) {
            if (empty($row[$field])) {
                return ['valid' => false, 'message' => "Campo obbligatorio mancante: $field"];
            }
        }

        // Valida email
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Email non valida'];
        }

        // Valida data nascita
        if (!strtotime($row['data_nascita'])) {
            return ['valid' => false, 'message' => 'Data nascita non valida'];
        }

        // Valida sesso
        if (!in_array(strtoupper($row['sesso']), ['M', 'F'])) {
            return ['valid' => false, 'message' => 'Sesso deve essere M o F'];
        }

        return ['valid' => true];
    }

    // Crea o trova utente
    private function createOrFindUser($row) {
        // Cerca utente esistente per email
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $row['email']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        }

        // Crea nuovo utente
        $this->user->nome = $row['nome'];
        $this->user->cognome = $row['cognome'];
        $this->user->email = $row['email'];
        $this->user->password = 'imported_' . uniqid(); // Password temporanea
        $this->user->data_nascita = $row['data_nascita'];
        $this->user->sesso = strtoupper($row['sesso']);
        $this->user->cellulare = $row['cellulare'] ?? null;
        $this->user->user_type = 'participant';

        if ($this->user->register()) {
            return $this->user->id;
        }

        return false;
    }

    // Ottieni prezzo evento
    private function getEventPrice($event_id) {
        $query = "SELECT prezzo_base FROM events WHERE id = :event_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['prezzo_base'] ?? 0;
    }

    // Genera Excel semplice (CSV)
    private function generateExcelFromArray($data) {
        if (empty($data)) {
            echo "Nessun dato da esportare";
            return;
        }

        // Headers
        $headers = [
            'Nome', 'Cognome', 'Email', 'Cellulare', 'Data Nascita', 'Sesso',
            'Data Iscrizione', 'Prezzo Pagato', 'Metodo Pagamento', 'Squadra', 'Note'
        ];

        // Output CSV
        $output = fopen('php://output', 'w');
        
        // Intestazioni
        fputcsv($output, $headers);
        
        // Dati
        foreach ($data as $row) {
            fputcsv($output, [
                $row['nome'],
                $row['cognome'],
                $row['email'],
                $row['cellulare'],
                $row['data_nascita'],
                $row['sesso'],
                date('d/m/Y H:i', strtotime($row['data_iscrizione'])),
                '€' . number_format($row['prezzo_pagato'], 2),
                $row['metodo_pagamento'],
                $row['team_name'],
                $row['note']
            ]);
        }
        
        fclose($output);
    }
}
?>
