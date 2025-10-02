<?php
/**
 * Modello GpxFile per la gestione dei file GPX degli eventi
 */
class GpxFile {
    private $conn;
    private $table = 'gpx_files';

    public $id;
    public $event_id;
    public $filename;
    public $original_name;
    public $file_path;
    public $file_size;
    public $download_count;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Upload file GPX
    public function upload($file, $event_id) {
        $upload_dir = __DIR__ . '/../../uploads/gpx/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validazione file
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($file_extension) !== 'gpx') {
            return ['success' => false, 'message' => 'Solo file GPX sono permessi'];
        }

        // Validazione dimensione (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File troppo grande (max 5MB)'];
        }

        // Nome file sicuro
        $filename = 'gpx_' . $event_id . '_' . time() . '.gpx';
        $file_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Salva nel database
            $query = "INSERT INTO " . $this->table . " 
                     (event_id, filename, original_name, file_path, file_size) 
                     VALUES (:event_id, :filename, :original_name, :file_path, :file_size)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':filename', $filename);
            $stmt->bindParam(':original_name', $file['name']);
            $stmt->bindParam(':file_path', $file_path);
            $stmt->bindParam(':file_size', $file['size']);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return ['success' => true, 'message' => 'File GPX caricato con successo'];
            }
        }

        return ['success' => false, 'message' => 'Errore durante il caricamento'];
    }

    // Ottieni file GPX per evento
    public function getByEventId($event_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE event_id = :event_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni singolo file GPX
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->event_id = $row['event_id'];
            $this->filename = $row['filename'];
            $this->original_name = $row['original_name'];
            $this->file_path = $row['file_path'];
            $this->file_size = $row['file_size'];
            $this->download_count = $row['download_count'];
            return true;
        }
        return false;
    }

    // Incrementa contatore download
    public function incrementDownloadCount() {
        $query = "UPDATE " . $this->table . " SET download_count = download_count + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Elimina file GPX
    public function delete() {
        // Elimina file fisico
        if (file_exists($this->file_path)) {
            unlink($this->file_path);
        }

        // Elimina record dal database
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Verifica se utente può scaricare (deve essere iscritto E aver pagato)
    public function canUserDownload($user_id) {
        $query = "SELECT r.*, rec.id as receipt_id
                 FROM registrations r
                 LEFT JOIN receipts rec ON r.id = rec.registration_id
                 WHERE r.user_id = :user_id 
                 AND r.event_id = :event_id 
                 AND r.status = 'confirmed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $this->event_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Utente deve essere iscritto e aver pagato
        if ($result && $result['prezzo_pagato'] > 0) {
            return [
                'can_download' => true,
                'registration_id' => $result['id'],
                'amount_paid' => $result['prezzo_pagato'],
                'payment_method' => $result['metodo_pagamento']
            ];
        }
        
        return [
            'can_download' => false,
            'reason' => $result ? 'Evento gratuito - GPX non disponibile' : 'Non sei iscritto o pagamento non completato'
        ];
    }

    // Statistiche download
    public function getDownloadStats($event_id) {
        $query = "SELECT 
                    COUNT(*) as total_files,
                    SUM(download_count) as total_downloads
                 FROM " . $this->table . " 
                 WHERE event_id = :event_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
