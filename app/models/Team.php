<?php
/**
 * Modello Team per la gestione delle squadre e società sportive
 */
class Team {
    private $conn;
    private $table = 'teams';

    public $id;
    public $nome;
    public $codice_fiscale;
    public $partita_iva;
    public $tipo;
    public $indirizzo;
    public $citta;
    public $provincia;
    public $cap;
    public $telefono;
    public $email;
    public $responsabile_nome;
    public $responsabile_cognome;
    public $responsabile_email;
    public $responsabile_telefono;
    public $referente_nome;
    public $referente_cognome;
    public $codice_affiliazione;
    public $logo;
    public $sito_web;
    public $note;
    public $status;
    public $categoria_eventi;
    public $visibilita;
    public $descrizione;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function columnExists($table, $column) {
        try {
            $stmt = $this->conn->prepare("SHOW COLUMNS FROM `{$table}` LIKE :col");
            $stmt->bindValue(':col', $column);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // Trova team per ID
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crea nuovo team
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                 SET nome_team=:nome_team, descrizione=:descrizione, privacy=:privacy,
                     captain_id=:captain_id, evento_id=:evento_id, data_creazione=NOW()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome_team', $data['nome']);
        $stmt->bindParam(':descrizione', $data['descrizione']);
        
        $privacy = $data['tipo'];
        $stmt->bindParam(':privacy', $privacy);
        
        $leader_id = $data['leader_id'];
        $stmt->bindParam(':captain_id', $leader_id);
        
        $evento_id = $data['evento_id'] ?? null;
        $stmt->bindParam(':evento_id', $evento_id);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Aggiorna team
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                 SET nome=:nome, descrizione=:descrizione, updated_at=NOW()
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':descrizione', $data['descrizione']);
        
        return $stmt->execute();
    }



    // Ottieni team per ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ottieni tutti i team attivi
    public function getAllActive() {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE status = 'active' 
                 ORDER BY nome ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cerca team
    public function search($search_term = '', $tipo = '', $limit = 20, $offset = 0, $categoria_eventi = '', $visibilita = '') {
        $hasStatus = $this->columnExists('team_members', 'status');
        $memberActiveCond = $hasStatus ? "tm.status = 'active'" : "tm.attivo = 1";
        $query = "SELECT t.*, 
                        t.id as id,
                        t.id as team_id,
                        t.nome as nome,
                        COUNT(tm.user_id) as members_count
                   FROM " . $this->table . " t 
                   LEFT JOIN team_members tm ON t.id = tm.team_id AND {$memberActiveCond}
                   WHERE t.status = 'active'";
        $params = [];
        
        // Filtro per ricerca testuale
        if (!empty($search_term)) {
            $query .= " AND (t.nome_team LIKE :search 
                           OR t.referente_nome LIKE :search 
                           OR t.referente_cognome LIKE :search
                           OR t.descrizione LIKE :search 
                           OR t.citta LIKE :search)";
            $params[':search'] = '%' . $search_term . '%';
        }
        
        // Filtro per tipo team (se necessario)
        if (!empty($tipo)) {
            $query .= " AND t.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        
        // Filtro per categoria eventi
        if (!empty($categoria_eventi)) {
            $query .= " AND t.categoria_eventi LIKE :categoria";
            $params[':categoria'] = '%' . $categoria_eventi . '%';
        }
        
        // Filtro per visibilità
        if (!empty($visibilita)) {
            $query .= " AND t.visibilita = :visibilita";
            $params[':visibilita'] = $visibilita;
        }

        $query .= " GROUP BY t.id ORDER BY t.nome ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aggiungi membro al team
    public function addMember($team_id, $user_id, $ruolo = 'member', $invited_by = null) {
        $query = "INSERT INTO team_members 
                 SET team_id=:team_id, user_id=:user_id, ruolo=:ruolo, 
                     status='active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':ruolo', $ruolo);

        return $stmt->execute();
    }

    // Ottieni membri del team
    public function getMembers($team_id, $status = 'active') {
        $hasStatus = $this->columnExists('team_members', 'status');
        $hasJoinedAt = $this->columnExists('team_members', 'joined_at');
        $joinedCol = $hasJoinedAt ? 'tm.joined_at' : 'tm.data_iscrizione';
        $query = "SELECT tm.*, u.nome, u.cognome, u.email, u.cellulare, {$joinedCol} as joined_at
                   FROM team_members tm
                   JOIN users u ON tm.user_id = u.id
                   WHERE tm.team_id = :team_id";
        
        if ($status !== null) {
            if ($hasStatus) {
                $query .= " AND tm.status = :status";
            } else {
                // legacy boolean
                $query .= " AND tm.attivo = :attivo";
            }
        }
        
        $query .= " ORDER BY tm.ruolo DESC, u.nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        if ($status !== null) {
            if ($hasStatus) {
                $stmt->bindParam(':status', $status);
            } else {
                $attivo = ($status === 'active') ? 1 : 0;
                $stmt->bindParam(':attivo', $attivo, PDO::PARAM_INT);
            }
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Conta membri del team
    public function getMembersCount($team_id) {
    $hasStatus = $this->columnExists('team_members', 'status');
    $query = "SELECT COUNT(*) as count FROM team_members 
         WHERE team_id = :team_id AND " . ($hasStatus ? "status = 'active'" : "attivo = 1");
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    // Verifica se utente è membro del team
    public function isUserMember($user_id, $team_id) {
    $hasStatus = $this->columnExists('team_members', 'status');
    $query = "SELECT COUNT(*) as count FROM team_members 
         WHERE team_id = :team_id AND user_id = :user_id AND " . ($hasStatus ? "status = 'active'" : "attivo = 1");
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }

    // Verifica se utente ha richiesta di adesione pendente
    public function hasPendingRequest($user_id, $team_id) {
        $query = "SELECT COUNT(*) as count FROM team_join_requests 
                 WHERE team_id = :team_id AND user_id = :user_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }

    // Ottieni richieste di adesione pendenti
    public function getPendingRequests($team_id) {
    $query = "SELECT jr.*, u.nome, u.cognome, u.email 
         FROM team_join_requests jr
         JOIN users u ON jr.user_id = u.id
         WHERE jr.team_id = :team_id AND jr.status = 'pending'
         ORDER BY jr.data_richiesta DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica se utente è membro del team (metodo legacy)
    public function isMember($team_id, $user_id) {
        $query = "SELECT COUNT(*) as count FROM team_members 
                 WHERE team_id = :team_id AND user_id = :user_id AND status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Verifica se utente può gestire il team
    public function canManage($team_id, $user_id) {
        $hasStatus = $this->columnExists('team_members', 'status');
        $activeCond = $hasStatus ? "status = 'active'" : "attivo = 1";
        $query = "SELECT ruolo FROM team_members 
                 WHERE team_id = :team_id AND user_id = :user_id AND {$activeCond}";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && in_array($result['ruolo'], ['manager', 'captain']);
    }

    // Ottieni team di un utente
    public function getUserTeams($user_id) {
        $hasStatus = $this->columnExists('team_members', 'status');
        $hasJoinedAt = $this->columnExists('team_members', 'joined_at');
        $joinedCol = $hasJoinedAt ? 'tm.joined_at' : 'tm.data_iscrizione';
        $activeCond = $hasStatus ? "tm.status = 'active'" : "tm.attivo = 1";
        $query = "SELECT t.*, tm.ruolo, {$joinedCol} as joined_at
                 FROM " . $this->table . " t
                 JOIN team_members tm ON t.id = tm.team_id
                 WHERE tm.user_id = :user_id AND {$activeCond} AND t.status = 'active'
                 ORDER BY tm.ruolo DESC, t.nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Se non ci sono risultati, prova con team_join_requests approvate
        if (empty($results)) {
            $query = "SELECT t.*, jr.data_risposta as joined_at
                     FROM " . $this->table . " t
                     JOIN team_join_requests jr ON t.id = jr.team_id
                     WHERE jr.user_id = :user_id AND jr.status = 'approved' AND t.status = 'active'
                     ORDER BY t.nome ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $results;
    }

    // Statistiche team
    public function getTeamStats($team_id) {
    $hasStatus = $this->columnExists('team_members', 'status');
    $memberActiveCond = $hasStatus ? "status = 'active'" : "attivo = 1";
    $stats_query = "SELECT 
               (SELECT COUNT(*) FROM team_members WHERE team_id = :team_id AND {$memberActiveCond}) as membri_attivi,
                           (SELECT COUNT(*) FROM team_registrations WHERE team_id = :team_id2) as iscrizioni_eventi,
                           (SELECT COUNT(*) FROM team_registrations WHERE team_id = :team_id3 AND status = 'completed') as eventi_completati,
                           (SELECT COALESCE(SUM(quota_totale), 0) FROM team_registrations WHERE team_id = :team_id4 AND status IN ('confirmed', 'completed')) as totale_speso";

        $stmt = $this->conn->prepare($stats_query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':team_id2', $team_id);
        $stmt->bindParam(':team_id3', $team_id);
        $stmt->bindParam(':team_id4', $team_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Elimina team (soft delete)
    public function delete($team_id) {
        $query = "UPDATE " . $this->table . " SET status='inactive' WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $team_id);
        
        return $stmt->execute();
    }

    // Metodi per il sistema sociale dei team
    
    // Verifica se l'utente può unirsi al team
    public function canUserJoin($userId) {
        // Verifica se l'utente è già membro
        $query = "SELECT id FROM team_registrations 
                 WHERE team_id = :team_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $this->id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return false; // Già membro o con richiesta pending
        }
        
        // Se è pubblico, può unirsi direttamente
        if ($this->visibilita === 'pubblico') {
            return true;
        }
        
        return false; // Per team privati serve richiesta
    }
    
    // Richiedi di unirti al team
    public function requestToJoin($userId, $messaggio = '') {
        $query = "INSERT INTO team_join_requests 
                 SET team_id = :team_id, user_id = :user_id, messaggio = :message,
                     status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $this->id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':message', $messaggio);
        
        return $stmt->execute();
    }
    
    // Ottieni le richieste di adesione pendenti
    public function getPendingJoinRequests() {
        $query = "SELECT jr.*, u.nome, u.cognome, u.email 
                 FROM team_join_requests jr
                 JOIN users u ON jr.user_id = u.id
                 WHERE jr.team_id = :team_id AND jr.status = 'pending'
                 ORDER BY jr.data_richiesta DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $this->id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Approva/rifiuta richiesta di adesione
    public function handleJoinRequest($requestId, $action) {
        if (!in_array($action, ['approved', 'rejected'])) {
            return false;
        }
        
        $this->conn->beginTransaction();
        
        try {
            // Aggiorna lo status della richiesta
            $query = "UPDATE team_join_requests 
                     SET status = :status, data_risposta = NOW()
                     WHERE id = :id AND team_id = :team_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $action);
            $stmt->bindParam(':id', $requestId);
            $stmt->bindParam(':team_id', $this->id);
            $stmt->execute();
            
            if ($action === 'approved') {
                // Ottieni l'user_id dalla richiesta
                $query = "SELECT user_id FROM team_join_requests WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $requestId);
                $stmt->execute();
                $request = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($request) {
                    // Aggiungi l'utente al team
                    $query = "INSERT INTO team_registrations 
                             SET team_id = :team_id, user_id = :user_id, 
                                 data_iscrizione = NOW(), status = 'approved'";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':team_id', $this->id);
                    $stmt->bindParam(':user_id', $request['user_id']);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    // Ottieni i team pubblici per categoria
    public static function getPublicTeamsByCategory($conn, $categoria = null) {
        $whereClause = "WHERE visibilita = 'pubblico'";
        if ($categoria) {
            $whereClause .= " AND categoria_eventi = :categoria";
        }
        
        $query = "SELECT * FROM teams " . $whereClause . " ORDER BY nome ASC";
        $stmt = $conn->prepare($query);
        
        if ($categoria) {
            $stmt->bindParam(':categoria', $categoria);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // METODI PER GESTIONE RICHIESTE ADESIONE
    // ==========================================

    /**
     * Crea richiesta di adesione al team
     */
    public function createJoinRequest($user_id, $team_id, $message = '') {
        // Verifica che non ci sia già una richiesta pendente
        if ($this->hasPendingRequest($user_id, $team_id)) {
            throw new Exception("Hai già una richiesta pendente per questo team");
        }

        // Verifica che non sia già membro
        if ($this->isUserMember($user_id, $team_id)) {
            throw new Exception("Sei già membro di questo team");
        }

        $query = "INSERT INTO team_join_requests (team_id, user_id, message, status, created_at) 
                 VALUES (:team_id, :user_id, :message, 'pending', NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':message', $message);
        
        return $stmt->execute();
    }

    /**
     * Ottieni richiesta di adesione per ID
     */
    public function getJoinRequest($request_id) {
    $query = "SELECT jr.*, u.nome, u.cognome, u.email, t.nome as team_name
         FROM team_join_requests jr
         JOIN users u ON jr.user_id = u.id
         JOIN teams t ON jr.team_id = t.id
                 WHERE jr.id = :request_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Approva richiesta di adesione
     */
    public function approveJoinRequest($request_id) {
        $request = $this->getJoinRequest($request_id);
        if (!$request) {
            throw new Exception("Richiesta non trovata");
        }

        try {
            $this->conn->beginTransaction();

            // Aggiungi utente al team
            $query = "INSERT INTO team_members (team_id, user_id, ruolo, status) 
                     VALUES (:team_id, :user_id, 'member', 'active')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':team_id', $request['team_id']);
            $stmt->bindParam(':user_id', $request['user_id']);
            $stmt->execute();

            // Aggiorna stato richiesta
            $query = "UPDATE team_join_requests SET status = 'approved', updated_at = NOW() 
                     WHERE id = :request_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':request_id', $request_id);
            $stmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * Rifiuta richiesta di adesione
     */
    public function rejectJoinRequest($request_id) {
        $query = "UPDATE team_join_requests SET status = 'rejected', updated_at = NOW() 
                 WHERE id = :request_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':request_id', $request_id);
        
        return $stmt->execute();
    }

    /**
     * Rimuovi membro dal team
     */
    public function removeMember($team_id, $user_id) {
    $query = "UPDATE team_members SET status = 'inactive' 
                 WHERE team_id = :team_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
}
?>
