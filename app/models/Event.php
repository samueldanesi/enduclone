<?php
/**
 * Modello Event per la gestione degli eventi sportivi
 */
class Event {
    private $conn;
    private $table = 'events';
    private $col = null; // mappa colonne dinamica

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

    // Rileva e memorizza la mappatura colonne a runtime (compatibilità schemi diversi)
    private function resolveColumns() {
        if ($this->col !== null) return; // già risolto

        // Default: schema ITA (complete_schema.sql)
        $this->col = [
            'ev_id' => 'id',
            'ev_org' => 'organizzatore_id',
            'ev_place' => 'luogo',
            'ev_city' => 'citta',
            'ev_price' => 'prezzo',
            'ev_cap' => 'posti_disponibili',
            'ev_dist' => 'lunghezza_km',
            'ev_status' => 'stato',
            'ev_cat' => 'categoria_id',
            'ev_sport' => null,
            'reg_id' => 'id',
            'reg_event' => 'evento_id',
            'reg_user' => 'utente_id',
            'reg_status' => 'stato',
            'reg_date' => 'data_iscrizione',
            'user_id' => 'id',
            'user_nome' => 'nome',
            'user_cognome' => 'cognome',
        ];

        // Adatta a possibili varianti legacy se davvero presenti
        if ($this->columnExists('events','event_id')) $this->col['ev_id'] = 'event_id';
        if ($this->columnExists('events','organizer_id')) $this->col['ev_org'] = 'organizer_id';
        if ($this->columnExists('events','luogo_partenza')) $this->col['ev_place'] = 'luogo_partenza';
        if ($this->columnExists('events','prezzo_base')) $this->col['ev_price'] = 'prezzo_base';
        // Città opzionale: citta (ita) o city (en)
        if ($this->columnExists('events','citta')) {
            $this->col['ev_city'] = 'citta';
        } elseif ($this->columnExists('events','city')) {
            $this->col['ev_city'] = 'city';
        } else {
            $this->col['ev_city'] = null;
        }
        // Capacità: prova moderne (capienza_massima), poi alternative (max_partecipanti)
        if ($this->columnExists('events','capienza_massima')) {
            $this->col['ev_cap'] = 'capienza_massima';
        } elseif ($this->columnExists('events','max_partecipanti')) {
            $this->col['ev_cap'] = 'max_partecipanti';
        }
        if ($this->columnExists('events','distanza_km')) $this->col['ev_dist'] = 'distanza_km';
        // Sport opzionale, usa 'sport' se esiste, altrimenti prova 'disciplina' o 'tipo_sport'
        if ($this->columnExists('events','sport')) {
            $this->col['ev_sport'] = 'sport';
        } elseif ($this->columnExists('events','disciplina')) {
            $this->col['ev_sport'] = 'disciplina';
        } elseif ($this->columnExists('events','tipo_sport')) {
            $this->col['ev_sport'] = 'tipo_sport';
        } else {
            $this->col['ev_sport'] = null;
        }
        // Categoria: supporta sia categoria (varchar) che categoria_id (int)
        if ($this->columnExists('events','categoria')) {
            $this->col['ev_cat'] = 'categoria';
        } elseif ($this->columnExists('events','categoria_id')) {
            $this->col['ev_cat'] = 'categoria_id';
        } else {
            $this->col['ev_cat'] = null;
        }
        // Se nessuna delle due colonne distanza esiste, disattiva il campo distanza
        if (!$this->columnExists('events', $this->col['ev_dist'])) {
            $this->col['ev_dist'] = null;
        }
        if ($this->columnExists('events','status')) $this->col['ev_status'] = 'status';

        if ($this->columnExists('registrations','registration_id')) $this->col['reg_id'] = 'registration_id';
        if ($this->columnExists('registrations','event_id')) $this->col['reg_event'] = 'event_id';
        if ($this->columnExists('registrations','user_id')) $this->col['reg_user'] = 'user_id';
        if ($this->columnExists('registrations','status')) $this->col['reg_status'] = 'status';
        if ($this->columnExists('registrations','data_registrazione')) $this->col['reg_date'] = 'data_registrazione';

        if ($this->columnExists('users','user_id')) $this->col['user_id'] = 'user_id';
        if ($this->columnExists('users','first_name')) $this->col['user_nome'] = 'first_name';
        if ($this->columnExists('users','last_name')) $this->col['user_cognome'] = 'last_name';
    }

    private function columnExists($table, $column) {
        try {
            // Primo tentativo: information_schema (più portabile)
            $sql = "SELECT COUNT(*) AS cnt
                    FROM information_schema.columns
                    WHERE table_schema = DATABASE()
                      AND table_name = :tbl
                      AND column_name = :col";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':tbl', $table);
            $stmt->bindValue(':col', $column);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (($row['cnt'] ?? 0) > 0) return true;

            // Fallback: SHOW COLUMNS
            $stmt2 = $this->conn->prepare("SHOW COLUMNS FROM `".$table."` LIKE :col");
            $stmt2->bindValue(':col', $column);
            $stmt2->execute();
            return $stmt2->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // Ritorna i valori enum consentiti per una colonna, se la colonna è di tipo ENUM
    private function getEnumValues($table, $column) {
        try {
            $sql = "SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :tbl AND column_name = :col";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':tbl', $table);
            $stmt->bindValue(':col', $column);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || empty($row['COLUMN_TYPE'])) return [];
            $type = $row['COLUMN_TYPE']; // es: enum('draft','published','closed','cancelled')
            // Estrai tutte le occorrenze tra apici singoli, gestendo eventuali escape
            if (preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $type, $m)) {
                // $m[1] contiene i valori senza gli apici esterni; rimuovi backslash di escape
                return array_map(function($v){
                    return stripslashes($v);
                }, $m[1]);
            }
            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    // Crea nuovo evento (schema-agnostico)
    public function create() {
        $this->resolveColumns();
        $evOrg = $this->col['ev_org'];
        $evPlace = $this->col['ev_place'];
        $evCity = $this->col['ev_city'];
        $evPrice = $this->col['ev_price'];
        $evCap = $this->col['ev_cap'];
        $evDist = $this->col['ev_dist'];
    $evStatus = $this->col['ev_status'];
        $evCat = $this->col['ev_cat'];
    $evSport = $this->col['ev_sport'];

        $query = "INSERT INTO " . $this->table . " 
                 SET `$evOrg`=:organizer_id, titolo=:titolo, descrizione=:descrizione,
                     data_evento=:data_evento, `$evPlace`=:luogo";

        if ($evCity) {
            $query .= ", `$evCity`=:citta";
        }
        if ($evCat) {
            $query .= ", `$evCat`=:categoria_id";
        }
        $query .= ", `$evPrice`=:prezzo, `$evCap`=:max_partecipanti";
        if ($evSport) {
            $query .= ", `$evSport`=:sport";
        }

        if ($evDist) {
            $query .= ", `$evDist`=:distanza_km";
        }

        // Prepara status solo se compatibile con l'ENUM (se presente)
        $statusParam = null;
        if ($evStatus) {
            $allowed = $this->getEnumValues('events', $evStatus);
            $candidate = $this->status;
            if (!empty($allowed)) {
                // Mappa IT <-> EN se necessario
                $it2en = [
                    'bozza' => 'draft',
                    'pubblicato' => 'published',
                    'chiuso' => 'closed',
                    'annullato' => 'cancelled',
                ];
                $en2it = array_flip($it2en);
                if (!in_array($candidate, $allowed, true)) {
                    if (isset($it2en[$candidate]) && in_array($it2en[$candidate], $allowed, true)) {
                        $candidate = $it2en[$candidate];
                    } elseif (isset($en2it[$candidate]) && in_array($en2it[$candidate], $allowed, true)) {
                        $candidate = $en2it[$candidate];
                    } else {
                        $candidate = null; // non compatibile con ENUM, evita di settare la colonna
                    }
                }
            }
            $statusParam = $candidate; // se allowed vuoto, salva valore così com'è
            if ($statusParam !== null) {
                $query .= ", `$evStatus`=:stato";
            }
        }

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':organizer_id', $this->organizer_id);
        $stmt->bindParam(':titolo', $this->titolo);
        $stmt->bindParam(':descrizione', $this->descrizione);
        $stmt->bindParam(':data_evento', $this->data_evento);
        $stmt->bindParam(':luogo', $this->luogo_partenza);
        if ($evCity) {
            $stmt->bindParam(':citta', $this->citta);
        }
        if ($evCat) {
            $stmt->bindParam(':categoria_id', $this->categoria_id);
        }
        $stmt->bindParam(':prezzo', $this->prezzo_base);
        $stmt->bindParam(':max_partecipanti', $this->max_partecipanti);
        if ($evDist) {
            $stmt->bindParam(':distanza_km', $this->distanza_km);
        }
        if ($evSport) {
            $sportVal = $this->sport;
            if ($sportVal === null || $sportVal === '') { $sportVal = 'altro'; }
            $stmt->bindValue(':sport', $sportVal);
        }
        if ($evStatus && $statusParam !== null) {
            $stmt->bindParam(':stato', $statusParam);
        }

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Leggi tutti gli eventi (pubblici) - schema-agnostico
    public function readAll($filters = []) {
        $this->resolveColumns();
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            @error_log('[DEBUG] Event::readAll colmap=' . json_encode($this->col));
            @error_log('[DEBUG] Event::readAll filters=' . json_encode($filters));
        }

        $evId = $this->col['ev_id'];
        $evOrg = $this->col['ev_org'];
        $evPlace = $this->col['ev_place'];
        $evPrice = $this->col['ev_price'];
        $evCap = $this->col['ev_cap'];
    $evDist = $this->col['ev_dist'];
    $evSport = $this->col['ev_sport'];
        $evCat = $this->col['ev_cat'];
        $evStatus = $this->col['ev_status'];
        $regId = $this->col['reg_id'];
        $regEvent = $this->col['reg_event'];
        $regStatus = $this->col['reg_status'];
        $userId = $this->col['user_id'];
        $userNome = $this->col['user_nome'];
        $userCognome = $this->col['user_cognome'];

          $statusFilter = $regStatus ? " AND r1.`$regStatus` IN ('confermata','confirmed')" : "";

        // Costruzione selezione distanza in modo sicuro
    $selectDist = $evDist ? "e.`$evDist` AS distanza_km," : "NULL AS distanza_km,";
    $selectSport = $evSport ? "e.`$evSport` AS sport," : "NULL AS sport,";

      $query = "SELECT 
                    e.`$evId` AS event_id,
                    e.`$evOrg` AS organizer_id,
                    e.titolo,
                    e.descrizione,
                    e.data_evento,
                    e.`$evPlace` AS luogo_partenza,
              " . ($evCat ? "e.`$evCat`" : "NULL") . " AS categoria_id,
                    e.`$evPrice` AS prezzo_base,
              e.`$evCap` AS max_partecipanti,
          $selectDist
              e.immagine,
          $selectSport
                    u.`$userNome` AS organizer_name,
                    u.`$userCognome` AS organizer_surname,
                          (SELECT COUNT(*) FROM registrations r1 WHERE r1.`$regEvent` = e.`$evId`" . $statusFilter . ") AS registrations_count
                 FROM events e
                      LEFT JOIN users u ON e.`$evOrg` = u.`$userId`
                 WHERE e.data_evento >= NOW()";
        
        // Filtra solo eventi pubblicati (non bozze) se la colonna status esiste
        if ($evStatus) {
            $query .= " AND (e.`$evStatus` = 'published' OR e.`$evStatus` = 'pubblicato')";
        }

        // Filtri
        $params = [];
        
        // Filtro ricerca generale
        if (!empty($filters['search'])) {
            $query .= " AND (e.titolo LIKE :search OR e.descrizione LIKE :search OR e.`$evPlace` LIKE :search OR u.`$userNome` LIKE :search OR u.`$userCognome` LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Nota: la colonna "sport" non è presente nello schema completo; filtro ignorato se presente
        
        if (!empty($filters['categoria'])) {
            $query .= " AND e.categoria_id = :categoria_id";
            $params[':categoria_id'] = $filters['categoria'];
        }
        
        if (!empty($filters['luogo'])) {
            $query .= " AND e.`$evPlace` LIKE :luogo";
            $params[':luogo'] = '%' . $filters['luogo'] . '%';
        }
        
        if (!empty($filters['città'])) {
            $query .= " AND e.`$evPlace` LIKE :citta";
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
                $query .= " ORDER BY e.`$evPrice` ASC";
                break;
            case 'name':
                $query .= " ORDER BY e.titolo ASC";
                break;
            case 'popularity':
                $query .= " ORDER BY registrations_count DESC";
                break;
            default:
                $query .= " ORDER BY e.data_evento ASC";
        }

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt;
    }

    // Leggi singolo evento (schema-agnostico)
    public function readOne() {
        $this->resolveColumns();
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            @error_log('[DEBUG] Event::readOne colmap=' . json_encode($this->col) . ' id=' . $this->id);
        }

        $evId = $this->col['ev_id'];
        $evOrg = $this->col['ev_org'];
        $evPlace = $this->col['ev_place'];
        $evPrice = $this->col['ev_price'];
        $evCap = $this->col['ev_cap'];
        $evDist = $this->col['ev_dist'];
        $evCat = $this->col['ev_cat'];
        $evSport = $this->col['ev_sport'];
        $regId = $this->col['reg_id'];
        $regEvent = $this->col['reg_event'];
        $regStatus = $this->col['reg_status'];
        $userId = $this->col['user_id'];
        $userNome = $this->col['user_nome'];
        $userCognome = $this->col['user_cognome'];
          $statusFilter = $regStatus ? " AND r1.`$regStatus` IN ('confermata','confirmed')" : "";

    $selectDist = $evDist ? "e.`$evDist` AS distanza_km," : "NULL AS distanza_km,";
    $selectSport = $evSport ? "e.`$evSport` AS sport," : "NULL AS sport,";
      $query = "SELECT 
                    e.`$evId` AS event_id,
                    e.`$evOrg` AS organizer_id,
                    e.titolo,
                    e.descrizione,
                    e.data_evento,
                    e.`$evPlace` AS luogo_partenza,
              " . ($evCat ? "e.`$evCat`" : "NULL") . " AS categoria_id,
                    e.`$evPrice` AS prezzo_base,
              e.`$evCap` AS max_partecipanti,
          $selectDist
              e.immagine,
          $selectSport
                    u.`$userNome` AS organizer_name,
                    u.`$userCognome` AS organizer_surname,
                          (SELECT COUNT(*) FROM registrations r1 WHERE r1.`$regEvent` = e.`$evId`" . $statusFilter . ") AS registrations_count
                 FROM events e
                      LEFT JOIN users u ON e.`$evOrg` = u.`$userId`
                 WHERE e.`$evId` = :id
                      LIMIT 1";

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
            $this->citta = $row['citta'] ?? null; // opzionale nello schema completo
            $this->categoria_id = $row['categoria_id'] ?? null;
            $this->sport = $row['sport'] ?? null; // opzionale
            $this->prezzo_base = $row['prezzo_base'] ?? 0;
            $this->max_partecipanti = $row['max_partecipanti'] ?? null;
            $this->immagine = $row['immagine'] ?? null;
            $this->distanza_km = $row['distanza_km'] ?? null;
            $this->status = $row['stato'] ?? 'pubblicato'; // opzionale
            $this->created_at = $row['created_at'] ?? null;
            $this->updated_at = $row['updated_at'] ?? null;
            
            return $row;
        }
        return false;
    }

    // Aggiorna evento (schema-agnostico)
    public function update() {
        $this->resolveColumns();
        $evId = $this->col['ev_id'];
        $evOrg = $this->col['ev_org'];
        $evPlace = $this->col['ev_place'];
        $evCity = $this->col['ev_city'];
        $evPrice = $this->col['ev_price'];
        $evCap = $this->col['ev_cap'];
        $evDist = $this->col['ev_dist'];
    $evStatus = $this->col['ev_status'];
        $evCat = $this->col['ev_cat'];
    $evSport = $this->col['ev_sport'];

        $query = "UPDATE " . $this->table . " 
                 SET titolo=:titolo, descrizione=:descrizione, data_evento=:data_evento,
                     `$evPlace`=:luogo";
        if ($evCity) {
            $query .= ", `$evCity`=:citta";
        }
        if ($evCat) {
            $query .= ", `$evCat`=:categoria_id";
        }
        $query .= ", `$evPrice`=:prezzo, `$evCap`=:max_partecipanti, updated_at=NOW()";
        if ($evSport) {
            $query .= ", `$evSport`=:sport";
        }
        if ($evDist) {
            $query .= ", `$evDist`=:distanza_km";
        }
        // Prepara status solo se compatibile con l'ENUM (se presente)
        $statusParam = null;
        if ($evStatus) {
            $allowed = $this->getEnumValues('events', $evStatus);
            $candidate = $this->status;
            if (!empty($allowed)) {
                $it2en = [
                    'bozza' => 'draft',
                    'pubblicato' => 'published',
                    'chiuso' => 'closed',
                    'annullato' => 'cancelled',
                ];
                $en2it = array_flip($it2en);
                if (!in_array($candidate, $allowed, true)) {
                    if (isset($it2en[$candidate]) && in_array($it2en[$candidate], $allowed, true)) {
                        $candidate = $it2en[$candidate];
                    } elseif (isset($en2it[$candidate]) && in_array($en2it[$candidate], $allowed, true)) {
                        $candidate = $en2it[$candidate];
                    } else {
                        $candidate = null;
                    }
                }
            }
            $statusParam = $candidate;
            if ($statusParam !== null) {
                $query .= ", `$evStatus`=:stato";
            }
        }
        $query .= " WHERE `$evId`=:id AND `$evOrg`=:organizer_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':titolo', $this->titolo);
        $stmt->bindParam(':descrizione', $this->descrizione);
        $stmt->bindParam(':data_evento', $this->data_evento);
        $stmt->bindParam(':luogo', $this->luogo_partenza);
        if ($evCity) {
            $stmt->bindParam(':citta', $this->citta);
        }
        if ($evCat) {
            $stmt->bindParam(':categoria_id', $this->categoria_id);
        }
        $stmt->bindParam(':prezzo', $this->prezzo_base);
        $stmt->bindParam(':max_partecipanti', $this->max_partecipanti);
        if ($evDist) {
            $stmt->bindParam(':distanza_km', $this->distanza_km);
        }
        if ($evSport) {
            $sportVal = $this->sport;
            if ($sportVal === null || $sportVal === '') { $sportVal = 'altro'; }
            $stmt->bindValue(':sport', $sportVal);
        }
        if ($evStatus && $statusParam !== null) {
            $stmt->bindParam(':stato', $statusParam);
        }
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':organizer_id', $this->organizer_id);

        return $stmt->execute();
    }

    // Elimina evento
    public function delete() {
        $this->resolveColumns();
        $evId = $this->col['ev_id'];
        $evOrg = $this->col['ev_org'];
        $query = "DELETE FROM " . $this->table . " WHERE `$evId`=:id AND `$evOrg`=:organizer_id";
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
        $this->resolveColumns();
        $regEvent = $this->col['reg_event'];
        $regStatus = $this->col['reg_status'];
        $evCap = $this->col['ev_cap'];
        $evId = $this->col['ev_id'];

    $statusCond = $regStatus ? " AND r.`$regStatus` IN ('confermata','confirmed')" : "";
    $query = "SELECT 
            (SELECT COUNT(*) FROM registrations r WHERE r.`$regEvent` = :event_id".$statusCond.") AS registrations,
            (SELECT `$evCap` FROM events WHERE `$evId` = :event_id) AS capacity";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $current = (int)($row['registrations'] ?? 0);
        $cap = (int)($row['capacity'] ?? 0);
        return ($cap === 0) ? true : ($current < $cap);
    }

    // Ottieni evento per ID
    public function getById($event_id) {
        $this->resolveColumns();
        $evId = $this->col['ev_id'];
        $query = "SELECT * FROM " . $this->table . " WHERE `$evId` = :event_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ottieni eventi dell'organizzatore
    public function getByOrganizer($organizer_id) {
        $this->resolveColumns();
        $evId = $this->col['ev_id'];
        $evOrg = $this->col['ev_org'];
        $regEvent = $this->col['reg_event'];
        $regStatus = $this->col['reg_status'];
        $statusCond = $regStatus ? " AND r.`$regStatus` IN ('confermata','confirmed')" : "";
        $query = "SELECT e.*,
                         (SELECT COUNT(*) FROM registrations r WHERE r.`$regEvent` = e.`$evId`".$statusCond.") AS registrations_count
                  FROM " . $this->table . " e
                  WHERE e.`$evOrg` = :organizer_id
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
    $this->resolveColumns();
    $regEvent = $this->col['reg_event'];
    $regUser = $this->col['reg_user'];
    $regStatus = $this->col['reg_status'];
    $userId = $this->col['user_id'];
    $statusCond = $regStatus ? " AND r.`$regStatus` IN ('confermata','confirmed')" : "";

    $query = "SELECT u.sesso, COUNT(*) as count
         FROM registrations r
         JOIN users u ON r.`$regUser` = u.`$userId`
         WHERE r.`$regEvent` = :event_id".$statusCond." 
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
                 JOIN users u ON r.`$regUser` = u.`$userId`
                 WHERE r.`$regEvent` = :event_id".$statusCond." 
                 GROUP BY age_group";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $this->id);
        $stmt->execute();
        $stats['by_age'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Totale incassi
    $query = "SELECT SUM(prezzo_pagato) as total_revenue
        FROM registrations r
        WHERE r.`$regEvent` = :event_id".$statusCond;
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $this->id);
        $stmt->execute();
        $stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
        
        return $stats;
    }

    // Cambia status evento
    public function changeStatus($new_status) {
        $this->resolveColumns();
        $evId = $this->col['ev_id'];
        $evOrg = $this->col['ev_org'];
        $evStatus = $this->col['ev_status'];
        if (!$evStatus) return false; // nessuna colonna status da aggiornare
        $query = "UPDATE " . $this->table . " SET `$evStatus`=:status, updated_at=NOW() WHERE `$evId`=:id AND `$evOrg`=:organizer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':organizer_id', $this->organizer_id);
        return $stmt->execute();
    }

    // Ottieni eventi disponibili per iscrizione
    public function getAvailable() {
        $this->resolveColumns();
        $evStatus = $this->col['ev_status'];
        $query = "SELECT * FROM " . $this->table . " WHERE data_evento > NOW()";
        if ($evStatus) {
            $query .= " AND `$evStatus` IN ('pubblicato','published')";
        }
        $query .= " ORDER BY data_evento ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
