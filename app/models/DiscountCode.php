<?php
/**
 * Modello DiscountCode per la gestione dei codici sconto
 */
class DiscountCode {
    private $conn;
    private $table = 'discount_codes';

    public $id;
    public $codice;
    public $event_id;
    public $tipo;
    public $valore;
    public $utilizzi_massimi;
    public $utilizzi_attuali;
    public $data_inizio;
    public $data_fine;
    public $attivo;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Verifica validità del codice
    public function validateCode($code, $user_id, $event_id, $amount) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE codice = :code 
                 AND attivo = 1 
                 AND data_inizio <= NOW() 
                 AND (data_fine IS NULL OR data_fine >= NOW())
                 AND (event_id IS NULL OR event_id = :event_id)
                 AND (utilizzi_massimi IS NULL OR utilizzi_attuali < utilizzi_massimi)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $discount = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifica se l'utente ha già usato questo codice
            $usage_query = "SELECT COUNT(*) as usage_count 
                           FROM discount_usage du
                           JOIN registrations r ON du.registration_id = r.id
                           WHERE du.discount_code_id = :discount_id 
                           AND r.user_id = :user_id";
            
            $usage_stmt = $this->conn->prepare($usage_query);
            $usage_stmt->bindParam(':discount_id', $discount['id']);
            $usage_stmt->bindParam(':user_id', $user_id);
            $usage_stmt->execute();
            
            $usage = $usage_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usage['usage_count'] > 0) {
                return [
                    'valid' => false,
                    'message' => 'Hai già utilizzato questo codice sconto'
                ];
            }

            // Calcola sconto
            $discount_amount = $this->calculateDiscount($discount, $amount);
            
            if ($discount_amount > 0) {
                return [
                    'valid' => true,
                    'discount_id' => $discount['id'],
                    'code' => $discount['codice'],
                    'type' => $discount['tipo'],
                    'discount_amount' => $discount_amount,
                    'final_amount' => max(0, $amount - $discount_amount),
                    'message' => $this->getDiscountMessage($discount, $discount_amount)
                ];
            }
        }

        return [
            'valid' => false,
            'message' => 'Codice sconto non valido o scaduto'
        ];
    }

    // Calcola l'importo dello sconto
    private function calculateDiscount($discount, $amount) {
        switch ($discount['tipo']) {
            case 'percentage':
                return ($amount * $discount['valore']) / 100;
            
            case 'fixed':
                return min($discount['valore'], $amount);
            
            default:
                return 0;
        }
    }

    // Genera message dello sconto
    private function getDiscountMessage($discount, $discount_amount) {
        switch ($discount['tipo']) {
            case 'percentage':
                return "Sconto {$discount['valore']}% applicato (-€" . number_format($discount_amount, 2) . ")";
            
            case 'fixed':
                return "Sconto fisso di €" . number_format($discount_amount, 2) . " applicato";
            
            default:
                return "Sconto applicato";
        }
    }

    // Applica il codice sconto (incrementa utilizzi)
    public function applyDiscount($discount_id, $user_id, $registration_id, $original_amount, $discount_amount, $final_amount) {
        try {
            $this->conn->beginTransaction();

            // Incrementa utilizzi del codice
            $update_query = "UPDATE " . $this->table . " 
                           SET utilizzi_attuali = utilizzi_attuali + 1 
                           WHERE id = :discount_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':discount_id', $discount_id);
            $update_stmt->execute();

            // Registra l'utilizzo
            $usage_query = "INSERT INTO discount_usage 
                           (discount_code_id, user_id, registration_id, original_amount, discount_amount, final_amount) 
                           VALUES (:discount_id, :user_id, :registration_id, :original_amount, :discount_amount, :final_amount)";
            $usage_stmt = $this->conn->prepare($usage_query);
            $usage_stmt->bindParam(':discount_id', $discount_id);
            $usage_stmt->bindParam(':user_id', $user_id);
            $usage_stmt->bindParam(':registration_id', $registration_id);
            $usage_stmt->bindParam(':original_amount', $original_amount);
            $usage_stmt->bindParam(':discount_amount', $discount_amount);
            $usage_stmt->bindParam(':final_amount', $final_amount);
            $usage_stmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Ottieni tutti i codici attivi
    public function getActiveCodes($event_id = null) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE attivo = 1 
                 AND data_inizio <= NOW() 
                 AND (data_fine IS NULL OR data_fine >= NOW())";
        
        if ($event_id) {
            $query .= " AND (event_id IS NULL OR event_id = :event_id)";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($event_id) {
            $stmt->bindParam(':event_id', $event_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Statistiche utilizzo codice
    public function getUsageStats($discount_id) {
        $query = "SELECT 
                    COUNT(*) as total_uses,
                    SUM(discount_amount) as total_discount_given,
                    AVG(discount_amount) as avg_discount,
                    MIN(used_at) as first_use,
                    MAX(used_at) as last_use
                 FROM discount_usage 
                 WHERE discount_code_id = :discount_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':discount_id', $discount_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // API per validazione codice in tempo reale
    public function apiValidateCode($code, $user_id, $event_id, $amount) {
        $result = $this->validateCode($code, $user_id, $event_id, $amount);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
?>
