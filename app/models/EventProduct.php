<?php
/**
 * Modello per i prodotti/servizi aggiuntivi degli eventi
 */
class EventProduct {
    private $conn;
    private $table = 'event_products';

    public $id;
    public $evento_id;
    public $organizer_id;
    public $nome;
    public $descrizione;
    public $categoria; // 'abbigliamento', 'accessori', 'pacco_gara', 'foto', 'donazione', 'altro'
    public $prezzo;
    public $quantita_disponibile;
    public $quantita_venduta;
    public $immagine;
    public $taglia_disponibili; // JSON: ["XS", "S", "M", "L", "XL"]
    public $colori_disponibili; // JSON: ["rosso", "blu", "nero"]
    public $attivo;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crea nuovo prodotto
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                 (evento_id, nome, descrizione, categoria, prezzo, 
                  quantita_disponibile, quantita_venduta, immagine, 
                  organizer_id, attivo) 
                 VALUES (:evento_id, :nome, :descrizione, :categoria, :prezzo,
                        :quantita_disponibile, :quantita_venduta, :immagine,
                        :organizer_id, :attivo)";

        $params = [
            ':evento_id' => $data['event_id'],
            ':nome' => $data['nome'],
            ':descrizione' => $data['descrizione'] ?? '',
            ':categoria' => $data['categoria'],
            ':prezzo' => $data['prezzo'],
            ':quantita_disponibile' => $data['quantita_disponibile'],
            ':quantita_venduta' => $data['quantita_venduta'] ?? 0,
            ':immagine' => $data['immagine'] ?? '',
            ':organizer_id' => $data['organizer_id'],
            ':attivo' => $data['attivo'] ?? 1
        ];

        $stmt = $this->conn->prepare($query);
        if ($stmt->execute($params)) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Leggi tutti i prodotti pubblici (per lo shop)
    public function readAll($filters = []) {
    $query = "SELECT p.*, e.titolo as evento_nome, e.data_evento,
             u.nome as organizer_nome, u.cognome as organizer_cognome,
             (p.quantita_disponibile - p.quantita_venduta) as disponibili
         FROM " . $this->table . " p
         LEFT JOIN events e ON p.evento_id = e.id
         LEFT JOIN users u ON p.organizer_id = u.id
         WHERE p.attivo = 1";

        $params = [];
        
        // Filtro per categoria
        if (!empty($filters['categoria'])) {
            $query .= " AND p.categoria = :categoria";
            $params[':categoria'] = $filters['categoria'];
        }
        
        // Filtro per evento
        if (!empty($filters['event_id'])) {
            $query .= " AND p.evento_id = :event_id";
            $params[':event_id'] = $filters['event_id'];
        }
        
        // Ricerca per nome
        if (!empty($filters['search'])) {
            $query .= " AND (p.nome LIKE :search OR p.descrizione LIKE :search OR e.titolo LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Solo prodotti disponibili
        if (!empty($filters['disponibili'])) {
            $query .= " AND (p.quantita_disponibile - p.quantita_venduta) > 0";
        }

    $query .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    // Leggi prodotti di un organizzatore
    public function getByOrganizer($organizer_id) {
    $query = "SELECT p.*, e.titolo as evento_nome, e.data_evento,
             (p.quantita_disponibile - p.quantita_venduta) as disponibili,
             COUNT(o.id) as ordini_totali,
             SUM(o.quantita * o.prezzo_unitario) as fatturato_totale
         FROM " . $this->table . " p
         LEFT JOIN events e ON p.evento_id = e.id
         LEFT JOIN product_orders o ON p.id = o.prodotto_id
         WHERE p.organizer_id = :organizer_id
         GROUP BY p.id
         ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        return $stmt;
    }

    // Leggi singolo prodotto
    public function readOne() {
    $query = "SELECT p.*, e.titolo as evento_nome, e.data_evento, e.luogo_partenza,
             u.nome as organizer_nome, u.cognome as organizer_cognome,
             (p.quantita_disponibile - p.quantita_venduta) as disponibili
         FROM " . $this->table . " p
         LEFT JOIN events e ON p.evento_id = e.id
         LEFT JOIN users u ON p.organizer_id = u.id
         WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->evento_id = $row['evento_id'];
            $this->organizer_id = $row['organizer_id'];
            $this->nome = $row['nome'];
            $this->descrizione = $row['descrizione'];
            $this->categoria = $row['categoria'];
            $this->prezzo = $row['prezzo'];
            $this->quantita_disponibile = $row['quantita_disponibile'];
            $this->quantita_venduta = $row['quantita_venduta'];
            $this->immagine = $row['immagine'];
            $this->taglia_disponibili = $row['taglia_disponibili'];
            $this->colori_disponibili = $row['colori_disponibili'];
            $this->attivo = $row['attivo'];
            $this->created_at = $row['created_at'];
            
            return $row;
        }
        return false;
    }

    // Aggiorna prodotto
    public function update($id, $data) {
    $query = "UPDATE " . $this->table . " SET
         nome = :nome,
         descrizione = :descrizione,
         categoria = :categoria,
         prezzo = :prezzo,
         quantita_disponibile = :quantita_disponibile,
         attivo = :attivo
         WHERE id = :id";
                 
        $params = [
            ':id' => $id,
            ':nome' => $data['nome'],
            ':descrizione' => $data['descrizione'],
            ':prezzo' => $data['prezzo'],
            ':quantita_disponibile' => $data['quantita_disponibile'],
            ':categoria' => $data['categoria'],
            ':attivo' => $data['attivo'] ?? 1
        ];
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    // Elimina prodotto
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND organizer_id = :organizer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':organizer_id', $this->organizer_id);
        return $stmt->execute();
    }

    // Aggiorna quantità venduta
    public function updateQuantitaVenduta($quantita) {
        $query = "UPDATE " . $this->table . " 
                 SET quantita_venduta = quantita_venduta + :quantita 
                 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':quantita', $quantita);
        return $stmt->execute();
    }

    // Upload immagine prodotto
    public function uploadImage($file) {
        $uploadDir = '../uploads/products/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedTypes)) {
            return false;
        }

        $newFileName = 'product_' . $this->id . '_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Salva il percorso nel database
            $query = "UPDATE " . $this->table . " SET immagine = :immagine WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':immagine', $newFileName);
            $stmt->bindParam(':id', $this->id);
            return $stmt->execute();
        }

        return false;
    }

    // Ottieni statistiche prodotto
    public function getStats() {
          $query = "SELECT 
                          COUNT(o.id) as ordini_totali,
                          SUM(o.quantita) as pezzi_venduti,
                          SUM(o.quantita * o.prezzo_unitario) as fatturato,
                          NULL as rating_medio
                      FROM " . $this->table . " p
                      LEFT JOIN product_orders o ON p.id = o.prodotto_id
                      WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>