<?php
/**
 * Modello Receipt per la gestione delle ricevute
 */
class Receipt {
    private $conn;
    private $table = 'receipts';

    public $id;
    public $user_id;
    public $registration_id;
    public $receipt_number;
    public $amount;
    public $pdf_file;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
        $this->ensureTableExists();
    }

    // Verifica e crea la tabella receipts se non esiste
    private function ensureTableExists() {
        try {
            $check = $this->conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'receipts' LIMIT 1");
            $check->execute();
            if ($check->fetchColumn()) return; // tabella esiste
            
            // Crea tabella receipts
            $sql = "CREATE TABLE IF NOT EXISTS receipts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                registration_id INT NOT NULL,
                receipt_number VARCHAR(50) UNIQUE NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(50) DEFAULT 'carta',
                payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                pdf_file VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_registration (registration_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $this->conn->exec($sql);
        } catch (Exception $e) {
            // Fallback silenzioso: se la creazione fallisce, le query restituiranno array vuoti
        }
    }

    // Crea nuova ricevuta
    public function create() {
        try {
            // Genera numero ricevuta univoco
            $this->receipt_number = $this->generateReceiptNumber();

            $query = "INSERT INTO " . $this->table . " 
                     SET user_id=:user_id, registration_id=:registration_id, 
                         receipt_number=:receipt_number, amount=:amount";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':registration_id', $this->registration_id);
            $stmt->bindParam(':receipt_number', $this->receipt_number);
            $stmt->bindParam(':amount', $this->amount);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                $this->generatePDF();
                return true;
            }
            return false;
        } catch (Exception $e) {
            // Fallback silenzioso se tabella manca o errore
            return false;
        }
    }

    // Genera numero ricevuta univoco
    private function generateReceiptNumber() {
        $year = date('Y');
        $month = date('m');
        
        // Trova l'ultimo numero della serie per questo mese
        $query = "SELECT receipt_number FROM " . $this->table . " 
                 WHERE receipt_number LIKE :pattern 
                 ORDER BY receipt_number DESC LIMIT 1";
        
        $pattern = "SE-{$year}{$month}-%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pattern', $pattern);
        $stmt->execute();
        
        $last_receipt = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($last_receipt) {
            // Estrai il numero sequenziale
            $parts = explode('-', $last_receipt['receipt_number']);
            $last_number = intval($parts[2]);
            $next_number = $last_number + 1;
        } else {
            $next_number = 1;
        }
        
        return "SE-{$year}{$month}-" . str_pad($next_number, 4, '0', STR_PAD_LEFT);
    }

    // Genera PDF della ricevuta
    private function generatePDF() {
        // Per semplicità, creo un file HTML che può essere convertito in PDF
        $upload_dir = UPLOAD_PATH . 'receipts/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = 'receipt_' . $this->receipt_number . '.html';
        $file_path = $upload_dir . $filename;

        // Ottieni dati per la ricevuta
    $query = "SELECT r.*, u.nome, u.cognome, u.email, u.telefono,
             e.titolo as event_title, e.data_evento, e.luogo as luogo_partenza
         FROM " . $this->table . " rec
         JOIN registrations r ON rec.registration_id = r.id
         JOIN users u ON rec.user_id = u.id
         JOIN events e ON r.evento_id = e.id
         WHERE rec.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $html = $this->generateReceiptHTML($data);
            file_put_contents($file_path, $html);
            
            // Aggiorna record con path del file
            $this->pdf_file = 'receipts/' . $filename;
            $update_query = "UPDATE " . $this->table . " SET pdf_file=:pdf_file WHERE id=:id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':pdf_file', $this->pdf_file);
            $update_stmt->bindParam(':id', $this->id);
            $update_stmt->execute();
        }
    }

    // Genera HTML della ricevuta
    private function generateReceiptHTML($data) {
        $html = '<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ricevuta ' . $this->receipt_number . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 24px; font-weight: bold; color: #2563eb; }
        .receipt-info { margin-bottom: 30px; }
        .customer-info, .event-info { margin-bottom: 20px; }
        .amount-section { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .total { font-size: 18px; font-weight: bold; color: #2563eb; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SportEvents</div>
        <div>Piattaforma Eventi Sportivi</div>
    </div>

    <div class="receipt-info">
        <h2>RICEVUTA DI PAGAMENTO</h2>
        <p><strong>Numero:</strong> ' . $this->receipt_number . '</p>
        <p><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($data['data_registrazione'])) . '</p>
    </div>

    <div class="customer-info">
        <h3>Dati Cliente</h3>
        <p><strong>Nome:</strong> ' . htmlspecialchars($data['nome'] . ' ' . $data['cognome']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($data['email']) . '</p>
        <p><strong>Telefono:</strong> ' . htmlspecialchars($data['telefono']) . '</p>
    </div>

    <div class="event-info">
        <h3>Evento</h3>
        <p><strong>Titolo:</strong> ' . htmlspecialchars($data['event_title']) . '</p>
        <p><strong>Data Evento:</strong> ' . date('d/m/Y H:i', strtotime($data['data_evento'])) . '</p>
        <p><strong>Luogo:</strong> ' . htmlspecialchars($data['luogo_partenza']) . '</p>
    </div>

    <div class="amount-section">
        <p><strong>Metodo di Pagamento:</strong> ' . ucfirst($data['metodo_pagamento']) . '</p>
        <p class="total"><strong>Importo Totale: €' . number_format($this->amount, 2) . '</strong></p>
    </div>

    <div class="footer">
        <p>SportEvents - Ricevuta generata automaticamente</p>
        <p>Per assistenza: info@sportevents.com</p>
    </div>
</body>
</html>';

        return $html;
    }

    // Ottieni ricevute dell'utente
    public function getUserReceipts($user_id) {
        try {
            $query = "SELECT rec.*, e.titolo as event_title
                 FROM " . $this->table . " rec
                 JOIN registrations r ON rec.registration_id = r.id
                 JOIN events e ON r.evento_id = e.id
                 WHERE rec.user_id = :user_id
                 ORDER BY rec.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Tabella non esiste o errore query: ritorna array vuoto
            return [];
        }
    }

    // Leggi singola ricevuta
    public function readOne() {
    $query = "SELECT rec.*, u.nome, u.cognome, u.email,
             e.titolo as event_title, e.data_evento
         FROM " . $this->table . " rec
         JOIN registrations r ON rec.registration_id = r.id
         JOIN users u ON rec.user_id = u.id
         JOIN events e ON r.evento_id = e.id
         WHERE rec.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->user_id = $row['user_id'];
            $this->registration_id = $row['registration_id'];
            $this->receipt_number = $row['receipt_number'];
            $this->amount = $row['amount'];
            $this->payment_method = $row['payment_method'];
            $this->payment_date = $row['payment_date'];
            $this->pdf_file = $row['pdf_file'];
            $this->created_at = $row['created_at'];
            
            return $row;
        }
        return false;
    }
}
?>
