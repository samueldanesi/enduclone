<?php

class Category {
    private $conn;
    private $table = 'categories';

    public $id;
    public $nome;
    public $descrizione;
    public $attiva;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function tableExists($table) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t");
            $stmt->bindValue(':t', $table);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['cnt'] ?? 0) > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    // Ottieni tutte le categorie attive
    public function getAllActive() {
        if (!$this->tableExists($this->table)) {
            // Nessuna tabella: restituisci un array vuoto per permettere fallback nella vista
            return null;
        }

        $query = "SELECT id AS categoria_id, nome AS nome_categoria, descrizione 
                  FROM " . $this->table . " 
                  WHERE attiva = 1 
                  ORDER BY nome";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Ottieni categoria per ID
    public function readOne($id) {
        if (!$this->tableExists($this->table)) {
            return false;
        }
        $query = "SELECT id, nome, descrizione 
                  FROM " . $this->table . " 
                  WHERE id = :id AND attiva = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->descrizione = $row['descrizione'];
            return true;
        }

        return false;
    }

    // Crea nuova categoria
    public function create() {
        if (!$this->tableExists($this->table)) {
            return false;
        }
        $query = "INSERT INTO " . $this->table . " 
                  (nome, descrizione, attiva) 
                  VALUES (:nome, :descrizione, :attiva)";

        $stmt = $this->conn->prepare($query);

        // Sanitizza i dati
    $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));

        // Binding parametri
    $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':descrizione', $this->descrizione);
        $stmt->bindParam(':attiva', $this->attiva);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }
}