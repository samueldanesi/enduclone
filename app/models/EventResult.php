<?php
/**
 * Modello per i risultati degli eventi
 */
class EventResult {
    private $conn;
    private $table = 'event_results';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Inserisci risultato
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                 (event_id, user_id, registration_id, position, category, time_result, 
                  distance_km, pace, points, notes, verified, source) 
                 VALUES (:event_id, :user_id, :registration_id, :position, :category, 
                         :time_result, :distance_km, :pace, :points, :notes, :verified, :source)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':event_id', $data['event_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':registration_id', $data['registration_id']);
        $stmt->bindParam(':position', $data['position']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':time_result', $data['time_result']);
        $stmt->bindParam(':distance_km', $data['distance_km']);
        $stmt->bindParam(':pace', $data['pace']);
        $stmt->bindParam(':points', $data['points']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':verified', $data['verified'] ?? false);
        $stmt->bindParam(':source', $data['source']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        throw new Exception("Errore nell'inserimento del risultato");
    }

    /**
     * Get risultati di un evento
     */
    public function getByEvent($event_id, $limit = null, $category = null) {
        $whereClause = "r.event_id = :event_id";
        $limitClause = "";
        
        if ($category) {
            $whereClause .= " AND r.category = :category";
        }
        
        if ($limit) {
            $limitClause = "LIMIT :limit";
        }
        
        $query = "SELECT r.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome,
                        u.avatar as user_avatar,
                        reg.numero_pettorale
                 FROM {$this->table} r
                 LEFT JOIN users u ON r.user_id = u.id
                 LEFT JOIN registrations reg ON r.registration_id = reg.id
                 WHERE {$whereClause}
                 ORDER BY r.position ASC, r.time_result ASC
                 {$limitClause}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get categorie di un evento
     */
    public function getCategories($event_id) {
        $query = "SELECT DISTINCT category 
                 FROM {$this->table} 
                 WHERE event_id = :event_id 
                 AND category IS NOT NULL
                 ORDER BY category";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get risultato di un utente per un evento
     */
    public function getByUserAndEvent($user_id, $event_id) {
        $query = "SELECT r.*, 
                        u.nome as user_nome, 
                        u.cognome as user_cognome
                 FROM {$this->table} r
                 LEFT JOIN users u ON r.user_id = u.id
                 WHERE r.user_id = :user_id AND r.event_id = :event_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Aggiorna risultato
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET position = :position, 
                     category = :category, 
                     time_result = :time_result, 
                     distance_km = :distance_km, 
                     pace = :pace, 
                     points = :points, 
                     notes = :notes, 
                     verified = :verified, 
                     source = :source,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':position', $data['position']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':time_result', $data['time_result']);
        $stmt->bindParam(':distance_km', $data['distance_km']);
        $stmt->bindParam(':pace', $data['pace']);
        $stmt->bindParam(':points', $data['points']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':verified', $data['verified']);
        $stmt->bindParam(':source', $data['source']);
        
        return $stmt->execute();
    }

    /**
     * Importa risultati da CSV
     */
    public function importFromCsv($event_id, $csv_file, $source = 'CSV Import') {
        $results = [];
        $handle = fopen($csv_file, 'r');
        
        // Skip header row
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Assumiamo formato: Position, Bib, Name, Surname, Category, Time, Distance
            $result_data = [
                'event_id' => $event_id,
                'user_id' => $this->findUserByName($data[2], $data[3]), // Nome, Cognome
                'position' => (int)$data[0],
                'category' => $data[4] ?? null,
                'time_result' => $data[5] ?? null,
                'distance_km' => isset($data[6]) ? (float)$data[6] : null,
                'pace' => $this->calculatePace($data[5] ?? null, $data[6] ?? null),
                'verified' => true,
                'source' => $source
            ];
            
            if ($result_data['user_id']) {
                $this->create($result_data);
                $results[] = $result_data;
            }
        }
        
        fclose($handle);
        return $results;
    }

    /**
     * Trova utente per nome e cognome
     */
    private function findUserByName($nome, $cognome) {
        $query = "SELECT id FROM users 
                 WHERE nome = :nome AND cognome = :cognome 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cognome', $cognome);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }

    /**
     * Calcola passo medio
     */
    private function calculatePace($time, $distance) {
        if (!$time || !$distance || $distance == 0) {
            return null;
        }
        
        // Converti tempo in secondi
        $timeParts = explode(':', $time);
        $seconds = 0;
        
        if (count($timeParts) == 3) {
            $seconds = $timeParts[0] * 3600 + $timeParts[1] * 60 + $timeParts[2];
        } elseif (count($timeParts) == 2) {
            $seconds = $timeParts[0] * 60 + $timeParts[1];
        }
        
        // Calcola passo per km
        $paceSeconds = $seconds / $distance;
        $paceMinutes = floor($paceSeconds / 60);
        $paceSecondsRemainder = $paceSeconds % 60;
        
        return sprintf("%d:%02d", $paceMinutes, $paceSecondsRemainder);
    }
}
?>