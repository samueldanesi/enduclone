<?php
/**
 * Modello Event per la gestione degli eventi sportivi
 */
class Event {
    private $conn;
    private $table = 'events';

    public $id;
    public $organizer_id;
    public $titolo;
    public $descrizione;
    public $data_evento;
    public $luogo_partenza;
    public $citta;
    public $categoria_id;
    public $sport;
    public $prezzo_base;
    public $max_partecipanti;
    public $immagine;
    public $file_gpx;
    public $distanza_km;
    public $status; // 'bozza', 'pubblicato', 'chiuso', 'annullato'
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crea nuovo evento
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET organizer_id=:organizer_id, titolo=:titolo, descrizione=:descrizione,
                     data_evento=:data_evento, luogo_partenza=:luogo_partenza, citta=:citta,
                     categoria_id=:categoria_id, sport=:sport, prezzo_base=:prezzo_base,
                     max_partecipanti=:max_partecipanti, distanza_km=:distanza_km,
                     stato=:stato, data_creazione=NOW()";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':organizer_id', $this->organizer_id);
        $stmt->bindParam(':titolo', $this->titolo);
        $stmt->bindParam(':descrizione', $this->descrizione);
        $stmt->bindParam(':data_evento', $this->data_evento);
        $stmt->bindParam(':luogo_partenza', $this->luogo_partenza);
        $stmt->bindParam(':citta', $this->citta);
        $stmt->bindParam(':categoria_id', $this->categoria_id);
        $stmt->bindParam(':sport', $this->sport);
        $stmt->bindParam(':prezzo_base', $this->prezzo_base);
        $stmt->bindParam(':max_partecipanti', $this->max_partecipanti);
        $stmt->bindParam(':distanza_km', $this->distanza_km);
        $stmt->bindParam(':stato', $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Leggi tutti gli eventi (pubblici)
    public function readAll($filters = []) {
        $query = "SELECT e.*, u.nome as organizer_name, u.cognome as organizer_surname,
                         COUNT(r.registration_id) as registrations_count
                 FROM " . $this->table . " e
                 LEFT JOIN users u ON e.organizer_id = u.user_id
                 LEFT JOIN registrations r ON e.event_id = r.event_id AND r.stato = 'confermata'
                 WHERE e.stato = 'pubblicato' AND e.data_evento >= CURDATE()";

        // Filtri
        $params = [];
        
        // Filtro ricerca generale
        if (!empty($filters['search'])) {
            $query .= " AND (e.titolo LIKE :search OR e.descrizione LIKE :search OR e.luogo_partenza LIKE :search OR u.nome LIKE :search OR u.cognome LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['sport'])) {
            $query .= " AND e.sport = :sport";
            $params[':sport'] = $filters['sport'];
        }
        
        if (!empty($filters['categoria'])) {
            $query .= " AND e.categoria_id = :categoria_id";
            $params[':categoria_id'] = $filters['categoria'];
        }
        
        if (!empty($filters['luogo'])) {
            $query .= " AND e.luogo_partenza LIKE :luogo";
            $params[':luogo'] = '%' . $filters['luogo'] . '%';
        }
        
        if (!empty($filters['città'])) {
            $query .= " AND e.luogo_partenza LIKE :citta";
            $params[':citta'] = '%' . $filters['città'] . '%';
        }
        
        if (!empty($filters['data_da'])) {
            $query .= " AND e.data_evento >= :data_da";
            $params[':data_da'] = $filters['data_da'];
        }
        
        if (!empty($filters['data_a'])) {
            $query .= " AND e.data_evento <= :data_a";
            $params[':data_a'] = $filters['data_a'];
        }

        // Ordinamento
        $sortBy = $filters['sort'] ?? 'date';
        switch ($sortBy) {
            case 'price':
                $query .= " GROUP BY e.event_id ORDER BY e.prezzo_base ASC";
                break;
            case 'name':
                $query .= " GROUP BY e.event_id ORDER BY e.titolo ASC";
                break;
            case 'popularity':
                $query .= " GROUP BY e.event_id ORDER BY registrations_count DESC";
                break;
            default:
                $query .= " GROUP BY e.event_id ORDER BY e.data_evento ASC";
        }

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt;
    }

    // Leggi singolo evento
    public function readOne() {
        $query = "SELECT e.*, u.nome as organizer_name, u.cognome as organizer_surname,
                         COUNT(r.registration_id) as registrations_count
                 FROM " . $this->table . " e
                 LEFT JOIN users u ON e.organizer_id = u.user_id
                 LEFT JOIN registrations r ON e.event_id = r.event_id AND r.stato = 'confermata'
                 WHERE e.event_id = :id
                 GROUP BY e.event_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->organizer_id = $row['organizer_id'];
            $this->titolo = $row['titolo'];
            $this->descrizione = $row['descrizione'];
            $this->data_evento = $row['data_evento'];
            $this->luogo_partenza = $row['luogo_partenza'];
            $this->citta = $row['citta'] ?? null;
            $this->categoria_id = $row['categoria_id'] ?? null;
            $this->sport = $row['sport'] ?? null;
            $this->prezzo_base = $row['prezzo_base'] ?? 0;
            $this->max_partecipanti = $row['max_partecipanti'] ?? null;
            $this->immagine = $row['immagine'] ?? null;
            $this->distanza_km = $row['distanza_km'] ?? null;
            $this->status = $row['stato'] ?? 'bozza';
            $this->created_at = $row['created_at'] ?? null;
            $this->updated_at = $row['updated_at'] ?? null;
            
            return $row;
        }
        return false;
    }

    // Aggiorna evento
    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET titolo=:titolo, descrizione=:descrizione, data_evento=:data_evento,
                     luogo_partenza=:luogo_partenza, citta=:citta, categoria_id=:categoria_id, sport=:sport,
                     prezzo_base=:prezzo_base, max_partecipanti=:max_partecipanti,
                     distanza_km=:distanza_km, stato=:stato, updated_at=NOW()
                 WHERE event_id=:id AND organizer_id=:organizer_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titolo', $this->titolo);
        $stmt->bindParam(':descrizione', $this->descrizione);
        $stmt->bindParam(':data_evento', $this->data_evento);
        $stmt->bindParam(':luogo_partenza', $this->luogo_partenza);
        $stmt->bindParam(':citta', $this->citta);
        $stmt->bindParam(':categoria_id', $this->categoria_id);
        $stmt->bindParam(':sport', $this->sport);
        $stmt->bindParam(':prezzo_base', $this->prezzo_base);
        $stmt->bindParam(':max_partecipanti', $this->max_partecipanti);
        $stmt->bindParam(':distanza_km', $this->distanza_km);
        $stmt->bindParam(':stato', $this->status);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':organizer_id', $this->organizer_id);

        return $stmt->execute();
    }

    // Elimina evento
    public function delete() {
        $query = "DELETE FROM " . $this->table . " 
                 WHERE id=:id AND organizer_id=:organizer_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':organizer_id', $this->organizer_id);

        return $stmt->execute();
    }

    // Upload immagine evento
    public function uploadImage($file) {
        $upload_dir = UPLOAD_PATH . 'events/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'event_' . $this->id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $this->immagine = 'events/' . $filename;
            
            // Aggiorna database
            $query = "UPDATE " . $this->table . " SET immagine=:immagine WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':immagine', $this->immagine);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        }
        return false;
    }

    // Upload file GPX
    public function uploadGPX($file) {
        $upload_dir = UPLOAD_PATH . 'gpx/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = 'track_' . $this->id . '_' . time() . '.gpx';
        $file_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $this->file_gpx = 'gpx/' . $filename;
            
            // Aggiorna database
            $query = "UPDATE " . $this->table . " SET file_gpx=:file_gpx WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':file_gpx', $this->file_gpx);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        }
        return false;
    }

    // Verifica disponibilità posti
    public function checkAvailability() {
        $query = "SELECT COUNT(*) as registrations 
                 FROM registrations 
                 WHERE event_id = :event_id AND stato = 'confermata'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $this->id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_registrations = $result['registrations'];
        
        return ($current_registrations < $this->max_partecipanti);
    }

    // Ottieni evento per ID
    public function getById($event_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE event_id = :event_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ottieni eventi dell'organizzatore
    public function getByOrganizer($organizer_id) {
        $query = "SELECT e.*, COUNT(r.registration_id) as registrations_count
                 FROM " . $this->table . " e
                 LEFT JOIN registrations r ON e.event_id = r.event_id AND r.stato = 'confermata'
                 WHERE e.organizer_id = :organizer_id
                 GROUP BY e.event_id
                 ORDER BY e.data_evento DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();

        return $stmt;
    }

    // Ottieni statistiche evento per organizzatore
    public function getEventStatistics() {
        $stats = [];
        
        // Iscrizioni per sesso
        $query = "SELECT u.sesso, COUNT(*) as count
                 FROM registrations r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.event_id = :event_id AND r.stato = 'confermata'
                 GROUP BY u.sesso";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $this->id);
        $stmt->execute();
        $stats['by_gender'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Iscrizioni per età
        $query = "SELECT 
                    CASE 
                        WHEN YEAR(CURDATE()) - YEAR(u.data_nascita) < 18 THEN 'Under 18'
                        WHEN YEAR(CURDATE()) - YEAR(u.data_nascita) BETWEEN 18 AND 30 THEN '18-30'
                        WHEN YEAR(CURDATE()) - YEAR(u.data_nascita) BETWEEN 31 AND 45 THEN '31-45'
                        WHEN YEAR(CURDATE()) - YEAR(u.data_nascita) BETWEEN 46 AND 60 THEN '46-60'
                        ELSE 'Over 60'
                    END as age_group,
                    COUNT(*) as count
                 FROM registrations r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.event_id = :event_id AND r.stato = 'confermata'
                 GROUP BY age_group";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $this->id);
        $stmt->execute();
        $stats['by_age'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Totale incassi
        $query = "SELECT SUM(prezzo_pagato) as total_revenue
                 FROM registrations
                 WHERE event_id = :event_id AND stato = 'confermata'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $this->id);
        $stmt->execute();
        $stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
        
        return $stats;
    }

    // Cambia status evento
    public function changeStatus($new_status) {
        $query = "UPDATE " . $this->table . " 
                 SET stato=:status, updated_at=NOW() 
                 WHERE event_id=:id AND organizer_id=:organizer_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':organizer_id', $this->organizer_id);

        return $stmt->execute();
    }

    // Ottieni eventi disponibili per iscrizione
    public function getAvailable() {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE stato = 'pubblicato' 
                 AND data_evento > NOW() 
                 ORDER BY data_evento ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
