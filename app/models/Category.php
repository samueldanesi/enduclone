<?php

class Category {
    private $conn;
    private $table = 'categories';

    public $categoria_id;
    public $nome_categoria;
    public $descrizione;
    public $icona;
    public $colore;
    public $attiva;
    public $data_creazione;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ottieni tutte le categorie attive
    public function getAllActive() {
        $query = "SELECT categoria_id, nome_categoria, descrizione, icona, colore 
                  FROM " . $this->table . " 
                  WHERE attiva = 1 
                  ORDER BY nome_categoria";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Ottieni categoria per ID
    public function readOne($id) {
        $query = "SELECT categoria_id, nome_categoria, descrizione, icona, colore 
                  FROM " . $this->table . " 
                  WHERE categoria_id = :id AND attiva = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->categoria_id = $row['categoria_id'];
            $this->nome_categoria = $row['nome_categoria'];
            $this->descrizione = $row['descrizione'];
            $this->icona = $row['icona'];
            $this->colore = $row['colore'];
            return true;
        }

        return false;
    }

    // Crea nuova categoria
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (nome_categoria, descrizione, icona, colore, attiva) 
                  VALUES (:nome_categoria, :descrizione, :icona, :colore, :attiva)";

        $stmt = $this->conn->prepare($query);

        // Sanitizza i dati
        $this->nome_categoria = htmlspecialchars(strip_tags($this->nome_categoria));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        $this->icona = htmlspecialchars(strip_tags($this->icona));
        $this->colore = htmlspecialchars(strip_tags($this->colore));

        // Binding parametri
        $stmt->bindParam(':nome_categoria', $this->nome_categoria);
        $stmt->bindParam(':descrizione', $this->descrizione);
        $stmt->bindParam(':icona', $this->icona);
        $stmt->bindParam(':colore', $this->colore);
        $stmt->bindParam(':attiva', $this->attiva);

        if ($stmt->execute()) {
            $this->categoria_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }
}