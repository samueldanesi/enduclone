<?php
/**
 * Modello per gli ordini dei prodotti
 */
class ProductOrder {
    private $conn;
    private $table = 'product_orders';

    public $id;
    public $user_id;
    public $prodotto_id;
    public $evento_id;
    public $quantita;
    public $taglia;
    public $colore;
    public $prezzo_unitario;
    public $totale;
    public $status; // 'pending', 'confirmed', 'shipped', 'delivered', 'cancelled'
    public $note_ordine;
    public $indirizzo_spedizione;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crea nuovo ordine
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                 SET user_id=:user_id, prodotto_id=:prodotto_id, evento_id=:evento_id,
                     quantita=:quantita, taglia=:taglia, colore=:colore,
                     prezzo_unitario=:prezzo_unitario, totale=:totale,
                     status=:status, note_ordine=:note_ordine, 
                     indirizzo_spedizione=:indirizzo_spedizione, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':prodotto_id', $this->prodotto_id);
        $stmt->bindParam(':evento_id', $this->evento_id);
        $stmt->bindParam(':quantita', $this->quantita);
        $stmt->bindParam(':taglia', $this->taglia);
        $stmt->bindParam(':colore', $this->colore);
        $stmt->bindParam(':prezzo_unitario', $this->prezzo_unitario);
        $stmt->bindParam(':totale', $this->totale);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':note_ordine', $this->note_ordine);
        $stmt->bindParam(':indirizzo_spedizione', $this->indirizzo_spedizione);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Ottieni ordini utente
    public function getUserOrders($user_id) {
        $query = "SELECT o.*, p.nome as prodotto_nome, p.immagine,
                         e.titolo as evento_nome, e.data_evento,
                         u.nome as organizer_nome, u.cognome as organizer_cognome
                 FROM " . $this->table . " o
                 LEFT JOIN event_products p ON o.prodotto_id = p.id
                 LEFT JOIN events e ON o.evento_id = e.event_id
                 LEFT JOIN users u ON p.organizer_id = u.user_id
                 WHERE o.user_id = :user_id
                 ORDER BY o.data_ordine DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni ordini organizzatore
    public function getOrganizerOrders($organizer_id) {
        $query = "SELECT o.*, p.nome as prodotto_nome,
                         e.titolo as evento_nome,
                         u.nome as cliente_nome, u.cognome as cliente_cognome, u.email as cliente_email
                 FROM " . $this->table . " o
                 LEFT JOIN event_products p ON o.prodotto_id = p.id
                 LEFT JOIN events e ON o.evento_id = e.event_id
                 LEFT JOIN users u ON o.user_id = u.user_id
                 WHERE p.organizer_id = :organizer_id
                 ORDER BY o.data_ordine DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aggiorna status ordine
    public function updateStatus($status) {
        $query = "UPDATE " . $this->table . " 
                 SET status = :status, updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    // Leggi singolo ordine
    public function readOne() {
        $query = "SELECT o.*, p.nome as prodotto_nome, p.immagine, p.descrizione,
                         e.titolo as evento_nome, e.data_evento, e.luogo_partenza,
                         u.nome as organizer_nome, u.cognome as organizer_cognome,
                         u.email as organizer_email
                 FROM " . $this->table . " o
                 LEFT JOIN event_products p ON o.prodotto_id = p.id
                 LEFT JOIN events e ON o.evento_id = e.event_id
                 LEFT JOIN users u ON p.organizer_id = u.user_id
                 WHERE o.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Statistiche ordini
    public function getOrderStats($user_id = null, $organizer_id = null) {
        if ($user_id) {
            $query = "SELECT 
                        COUNT(o.id) as ordini_totali,
                        SUM(o.totale) as spesa_totale,
                        COUNT(CASE WHEN o.status = 'delivered' THEN 1 END) as ordini_completati
                     FROM " . $this->table . " o
                     WHERE o.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
        } else if ($organizer_id) {
            $query = "SELECT 
                        COUNT(o.id) as ordini_totali,
                        SUM(o.totale) as fatturato_totale,
                        COUNT(CASE WHEN o.status = 'pending' THEN 1 END) as ordini_pending,
                        COUNT(CASE WHEN o.status = 'delivered' THEN 1 END) as ordini_completati
                     FROM " . $this->table . " o
                     LEFT JOIN event_products p ON o.prodotto_id = p.id
                     WHERE p.organizer_id = :organizer_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':organizer_id', $organizer_id);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>