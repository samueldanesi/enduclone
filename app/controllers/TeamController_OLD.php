<?php
require_once '../app/models/Team.php';
require_once '../app/models/CollectiveRegistration.php';
require_once '../app/models/TeamMessage.php';
require_once '../app/models/Event.php';
require_once '../app/models/User.php';

/**
 * Controller per la gestione dei team e iscrizioni collettive + sistema sociale
 */
class TeamController {
    private $db;
    private $team;
    private $collectiveRegistration;
    private $teamMessage;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->team = new Team($this->db);
        $this->collectiveRegistration = new CollectiveRegistration($this->db);
        $this->teamMessage = new TeamMessage($this->db);
    }

    // Pagina principale team
    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $search = $_GET['search'] ?? '';
        $tipo = $_GET['tipo'] ?? '';
        $categoria_eventi = $_GET['categoria_eventi'] ?? '';
        $visibilita = $_GET['visibilita'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Carica i team con i filtri
        $teams = $this->team->search($search, $tipo, $limit, $offset, $categoria_eventi, $visibilita);
        $user_teams = $this->team->getUserTeams($_SESSION['user_id']);
        
        // Aggiungi informazioni aggiuntive per ogni team
        foreach ($teams as &$team) {
            $team['members_count'] = $this->getTeamMembersCount($team['id']);
            $team['can_join'] = $this->canUserJoinTeam($_SESSION['user_id'], $team['id']);
            $team['is_member'] = $this->isUserMember($_SESSION['user_id'], $team['id']);
            $team['is_admin'] = $this->isUserTeamAdmin($_SESSION['user_id'], $team['id']);
        }
        
        foreach ($user_teams as &$team) {
            $team['members_count'] = $this->getTeamMembersCount($team['id']);
            $team['is_admin'] = $this->isUserTeamAdmin($_SESSION['user_id'], $team['id']);
            $team['pending_requests'] = $this->getPendingJoinRequestsCount($team['id']);
        }

        // Passa i parametri di filtro alla view
        $filters = compact('search', 'tipo', 'categoria_eventi', 'visibilita');
        
        require_once '../app/views/teams/index.php';
    }

    // Crea nuovo team
    public function create() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'participant') {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->team->nome = trim($_POST['nome']);
            $this->team->codice_fiscale = trim($_POST['codice_fiscale'] ?? '');
            $this->team->partita_iva = trim($_POST['partita_iva'] ?? '');
            $this->team->tipo = $_POST['tipo'];
            $this->team->indirizzo = trim($_POST['indirizzo']);
            $this->team->citta = trim($_POST['citta']);
            $this->team->provincia = trim($_POST['provincia']);
            $this->team->cap = trim($_POST['cap']);
            $this->team->telefono = trim($_POST['telefono'] ?? '');
            $this->team->email = trim($_POST['email']);
            $this->team->responsabile_nome = trim($_POST['responsabile_nome']);
            $this->team->responsabile_cognome = trim($_POST['responsabile_cognome']);
            $this->team->responsabile_email = trim($_POST['responsabile_email']);
            $this->team->responsabile_telefono = trim($_POST['responsabile_telefono']);
            $this->team->sito_web = trim($_POST['sito_web'] ?? '');
            $this->team->note = trim($_POST['note'] ?? '');
            $this->team->status = 'active';

            if ($this->team->create()) {
                // Aggiungi il creatore come admin del team
                $this->team->addMember($this->team->id, $_SESSION['user_id'], 'admin');
                
                $_SESSION['success_message'] = 'Team creato con successo!';
                header('Location: /teams/view/' . $this->team->id);
                exit;
            } else {
                $error = 'Errore nella creazione del team';
            }
        }

        require_once '../app/views/teams/create.php';
    }

    // Visualizza team
    public function view($team_id) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $team = $this->team->getById($team_id);
        if (!$team) {
            header('Location: /teams');
            exit;
        }

        $members = $this->team->getMembers($team_id);
        $collective_registrations = $this->collectiveRegistration->getTeamCollectiveRegistrations($team_id);
        $stats = $this->team->getTeamStats($team_id);
        $can_manage = $this->team->canManage($team_id, $_SESSION['user_id']);

        require_once '../app/views/teams/view.php';
    }

    // Iscrizione collettiva - NUOVO SISTEMA LOGICO
    public function collectiveRegistration($team_id) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Verifica permessi
        if (!$this->team->canManage($team_id, $_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Non hai i permessi per gestire questo team';
            header('Location: /teams/view/' . $team_id);
            exit;
        }

        $team = $this->team->getById($team_id);
        if (!$team) {
            header('Location: /teams');
            exit;
        }

        // Ottieni eventi disponibili
        $event_model = new Event($this->db);
        $available_events = $event_model->readAll(['status' => 'published']);
        
        // Ottieni sconti disponibili per mostrare nella UI
        $available_discounts = $this->collectiveRegistration->getAvailableDiscounts();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCollectiveRegistrationSubmit($team_id);
            return;
        }

        require_once '../app/views/teams/collective_registration.php';
    }

    // Gestisce submit iscrizione collettiva - NUOVA LOGICA
    private function handleCollectiveRegistrationSubmit($team_id) {
        // Validazione base
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error_message'] = 'Errore nel caricamento del file Excel/CSV';
            header('Location: /teams/collective-registration/' . $team_id);
            exit;
        }

        $event_id = $_POST['event_id'] ?? 0;
        $responsible_name = trim($_POST['responsabile_nome'] ?? '');
        $responsible_email = trim($_POST['responsabile_email'] ?? '');
        $responsible_phone = trim($_POST['responsabile_telefono'] ?? '');
        $notes = trim($_POST['note'] ?? '');

        // Validazione campi obbligatori
        if (empty($event_id) || empty($responsible_name) || empty($responsible_email)) {
            $_SESSION['error_message'] = 'Tutti i campi obbligatori devono essere compilati';
            header('Location: /teams/collective-registration/' . $team_id);
            exit;
        }

        try {
            // Dati responsabile
            $responsible_data = [
                'name' => $responsible_name,
                'email' => $responsible_email,
                'phone' => $responsible_phone,
                'notes' => $notes
            ];

            // Crea iscrizione collettiva usando il NUOVO sistema
            $collective_id = $this->collectiveRegistration->createFromExcel(
                $team_id, 
                $event_id, 
                $_SESSION['user_id'], 
                $_FILES['excel_file'], 
                $responsible_data
            );
            
            $_SESSION['success_message'] = 'Iscrizione collettiva creata con successo!';
            header('Location: /teams/collective-details/' . $collective_id);
            exit;

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Errore: ' . $e->getMessage();
            header('Location: /teams/collective-registration/' . $team_id);
            exit;
        }
    }



    // Dettagli iscrizione collettiva - NUOVO SISTEMA
    public function collectiveDetails($collective_id) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Ottieni dettagli iscrizione collettiva
        $collective_registration = $this->collectiveRegistration->getCollectiveRegistrationDetails($collective_id);
        
        if (!$collective_registration) {
            $_SESSION['error_message'] = 'Iscrizione collettiva non trovata';
            header('Location: /teams');
            exit;
        }

        // Verifica permessi (solo membri del team possono vedere)
        if (!$this->team->canManage($collective_registration['team_id'], $_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Non hai i permessi per visualizzare questa iscrizione';
            header('Location: /teams');
            exit;
        }

        // Ottieni lista partecipanti
        $participants = $this->collectiveRegistration->getCollectiveParticipants($collective_id);
        
        // Ottieni dettagli team
        $team = $this->team->getById($collective_registration['team_id']);

        require_once '../app/views/teams/collective_details.php';
    }

    // API per ricerca team (AJAX)
    public function searchApi() {
        header('Content-Type: application/json');
        
        $search = $_GET['q'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        $teams = $this->team->search($search, '', $limit, 0);
        
        echo json_encode($teams);
    }

    // Download template CSV per iscrizioni collettive
    public function downloadTemplate() {
        // Genera template usando il nuovo sistema
        $template_data = $this->collectiveRegistration->generateCsvTemplate();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="template_iscrizione_collettiva_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // BOM per UTF-8 (aiuta Excel ad aprire correttamente i caratteri speciali)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Scrivi headers
        fputcsv($output, $template_data['headers'], ',', '"', '\\');
        
        // Scrivi dati di esempio
        foreach ($template_data['sample_data'] as $row) {
            fputcsv($output, $row, ',', '"', '\\');
        }
        
        fclose($output);
        exit;
    }

    // === METODI PER IL SISTEMA SOCIALE DEI TEAM ===
    
    /**
     * Visualizza la chat del team
     */
    public function chat() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $team_id = $_GET['team_id'] ?? 0;
        
        // Verifica che l'utente sia membro del team
        if (!$this->isUserMember($_SESSION['user_id'], $team_id)) {
            header('Location: /teams?error=not_member');
            exit;
        }
        
        // Carica team e messaggi
        $team = $this->team->getById($team_id);
        $messages = $this->teamMessage->getTeamMessages($team_id);
        
        require_once '../app/views/teams/chat.php';
    }
    
    /**
     * Invia messaggio nella chat del team
     */
    public function sendMessage() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            exit;
        }
        
        $team_id = $_POST['team_id'] ?? 0;
        $messaggio = trim($_POST['messaggio'] ?? '');
        
        if (empty($messaggio)) {
            echo json_encode(['success' => false, 'message' => 'Messaggio vuoto']);
            exit;
        }
        
        // Verifica che l'utente sia membro del team
        if (!$this->isUserMember($_SESSION['user_id'], $team_id)) {
            echo json_encode(['success' => false, 'message' => 'Non sei membro di questo team']);
            exit;
        }
        
        // Imposta i dati del messaggio
        $this->teamMessage->team_id = $team_id;
        $this->teamMessage->user_id = $_SESSION['user_id'];
        $this->teamMessage->message = $messaggio;
        $this->teamMessage->message_type = 'normale';
        $this->teamMessage->event_id = null;
        $this->teamMessage->parent_message_id = null;
        
        if ($this->teamMessage->sendMessage()) {
            echo json_encode(['success' => true, 'message' => 'Messaggio inviato']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore nell\'invio']);
        }
    }
    
    /**
     * Invia richiesta di evento nella chat
     */
    public function sendEventRequest() {
        session_start();
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            exit;
        }
        
        $team_id = $_POST['team_id'] ?? 0;
        $event_id = $_POST['event_id'] ?? 0;
        $messaggio = trim($_POST['messaggio'] ?? '');
        
        // Verifica che l'utente sia admin del team
        if (!$this->isUserTeamAdmin($_SESSION['user_id'], $team_id)) {
            echo json_encode(['success' => false, 'message' => 'Solo gli admin possono inviare richieste di eventi']);
            exit;
        }
        
        $result = $this->teamMessage->sendEventRequest($team_id, $_SESSION['user_id'], $event_id, $messaggio);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Richiesta evento inviata']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore nell\'invio della richiesta']);
        }
    }
    
    /**
     * Risponde ad una richiesta di evento
     */
    public function respondToEventRequest() {
        session_start();
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            exit;
        }
        
        $request_id = $_POST['request_id'] ?? 0;
        $response = $_POST['response'] ?? ''; // 'interested', 'confirmed'
        
        if (!in_array($response, ['interested', 'confirmed'])) {
            echo json_encode(['success' => false, 'message' => 'Risposta non valida']);
            exit;
        }
        
        $result = $this->teamMessage->respondToEventRequest($request_id, $_SESSION['user_id'], $response);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Risposta registrata']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore nella risposta']);
        }
    }
    
    /**
     * Cerca team pubblici per categoria
     */
    public function searchTeams() {
        $categoria = $_GET['categoria'] ?? null;
        $search = $_GET['search'] ?? '';
        
        $teams = Team::getPublicTeamsByCategory($this->db, $categoria);
        
        // Filtra per nome se c'è una ricerca
        if (!empty($search)) {
            $teams = array_filter($teams, function($team) use ($search) {
                return stripos($team['nome'], $search) !== false || 
                       stripos($team['descrizione'], $search) !== false;
            });
        }
        
        require_once '../app/views/teams/search.php';
    }
    
    /**
     * Unisciti ad un team pubblico
     */
    public function joinTeam() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            exit;
        }
        
        $team_id = $_POST['team_id'] ?? 0;
        
        // Verifica che il team sia pubblico
        $query = "SELECT visibilita FROM teams WHERE id = :team_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$team || $team['visibilita'] !== 'pubblico') {
            echo json_encode(['success' => false, 'message' => 'Team non pubblico']);
            exit;
        }
        
        // Aggiungi direttamente come membro
        $query = "INSERT INTO team_members (team_id, user_id, ruolo, data_iscrizione, attivo) 
                 VALUES (:team_id, :user_id, 'member', CURDATE(), 1)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Ti sei unito al team!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore nell\'unirsi al team']);
        }
    }
    
    /**
     * Richiedi di unirti ad un team
     */
    public function requestJoin() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
            exit;
        }
        
        $team_id = $_POST['team_id'] ?? 0;
        $messaggio = trim($_POST['messaggio'] ?? '');
        
        $this->team->id = $team_id;
        $result = $this->team->requestToJoin($_SESSION['user_id'], $messaggio);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Richiesta inviata']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore nell\'invio della richiesta']);
        }
    }
    
    /**
     * Gestisce le richieste di adesione (per admin team)
     */
    public function manageJoinRequests() {
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $team_id = $_GET['team_id'] ?? 0;
        
        // Verifica che l'utente sia admin del team
        if (!$this->isUserTeamAdmin($_SESSION['user_id'], $team_id)) {
            header('Location: /teams?error=not_admin');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = $_POST['request_id'] ?? 0;
            $action = $_POST['action'] ?? '';
            
            $this->team->id = $team_id;
            $result = $this->team->handleJoinRequest($request_id, $action);
            
            if ($result) {
                $message = $action === 'approved' ? 'Richiesta approvata' : 'Richiesta rifiutata';
                header("Location: /teams/manage-requests?team_id=$team_id&success=" . urlencode($message));
            } else {
                header("Location: /teams/manage-requests?team_id=$team_id&error=Errore nell'operazione");
            }
            exit;
        }
        
        // Carica richieste pendenti
        $this->team->id = $team_id;
        $team = $this->team->readOne();
        $requests = $this->team->getPendingJoinRequests();
        
        require_once '../app/views/teams/manage_requests.php';
    }
    
    /**
     * Verifica se l'utente è membro del team
     */
    private function isUserMember($user_id, $team_id) {
        // Prima controlla team_members
        $query = "SELECT id FROM team_members 
                 WHERE user_id = :user_id AND team_id = :team_id AND attivo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return true;
        }
        
        // Poi controlla team_join_requests approvate
        $query = "SELECT id FROM team_join_requests 
                 WHERE user_id = :user_id AND team_id = :team_id AND status = 'approved'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Verifica se l'utente è admin del team
     */
    private function isUserTeamAdmin($user_id, $team_id) {
        $query = "SELECT id FROM teams WHERE id = :team_id AND referente_nome = 
                 (SELECT CONCAT(nome, ' ', cognome) FROM users WHERE id = :user_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Conta i membri di un team
     */
    private function getTeamMembersCount($team_id) {
        // Prima conta da team_members
        $query = "SELECT COUNT(*) as count FROM team_members 
                 WHERE team_id = :team_id AND attivo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'] ?? 0;
        
        // Se non ci sono membri, conta dalle richieste approvate
        if ($count == 0) {
            $query = "SELECT COUNT(*) as count FROM team_join_requests 
                     WHERE team_id = :team_id AND status = 'approved'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':team_id', $team_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['count'] ?? 0;
        }
        
        return $count;
    }
    
    /**
     * Verifica se l'utente può unirsi al team
     */
    private function canUserJoinTeam($user_id, $team_id) {
        // Se è già membro, non può unirsi di nuovo
        if ($this->isUserMember($user_id, $team_id)) {
            return false;
        }
        
        // Verifica se ha già una richiesta pendente
        $query = "SELECT id FROM team_join_requests 
                 WHERE team_id = :team_id AND user_id = :user_id AND status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() == 0;
    }
    
    /**
     * Conta le richieste pendenti per un team
     */
    private function getPendingJoinRequestsCount($team_id) {
        $query = "SELECT COUNT(*) as count FROM team_join_requests 
                 WHERE team_id = :team_id AND status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Conferma pagamento iscrizione collettiva
     */
    public function confirmCollectivePayment($collective_id) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // Verifica permessi
        $collective_registration = $this->collectiveRegistration->getCollectiveRegistrationDetails($collective_id);
        if (!$collective_registration || !$this->team->canManage($collective_registration['team_id'], $_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Non autorizzato';
            header('Location: /teams');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_method = $_POST['payment_method'] ?? 'card';
            $transaction_id = $_POST['transaction_id'] ?? null;
            
            try {
                // Aggiorna status pagamento
                $this->collectiveRegistration->updatePaymentStatus($collective_id, 'paid', $transaction_id);
                
                // Conferma iscrizione
                $this->collectiveRegistration->confirmRegistration($collective_id);
                
                $_SESSION['success_message'] = 'Pagamento confermato! Iscrizione collettiva attivata.';
                header('Location: /teams/collective-details/' . $collective_id);
                exit;
                
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Errore: ' . $e->getMessage();
                header('Location: /teams/collective-details/' . $collective_id);
                exit;
            }
        }
        
        require_once '../app/views/teams/confirm_collective_payment.php';
    }

    // Form rapido per iscrizione collettiva
    public function quickCollective($team_id) {
        $team_data = $this->team->findById($team_id);
        
        if (!$team_data || $team_data['leader_id'] != $_SESSION['user_id']) {
            header('Location: /teams');
            exit();
        }

        include '../app/views/teams/quick_collective.php';
    }

    // Checkout rapido iscrizione collettiva
    public function quickCollectiveCheckout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /teams');
            exit();
        }

        $team_id = $_POST['team_id'] ?? null;
        $event_id = $_POST['event_id'] ?? null;
        $participants = $_POST['participants'] ?? [];
        $notes = $_POST['notes'] ?? '';

        if (!$team_id || !$event_id || empty($participants)) {
            $_SESSION['error'] = "Dati mancanti per l'iscrizione";
            header('Location: /teams');
            exit();
        }

        // Verifica che l'utente sia il leader del team
        $team_data = $this->team->findById($team_id);
        if (!$team_data || $team_data['leader_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Non autorizzato";
            header('Location: /teams');
            exit();
        }

        // Ottieni dati evento
        $event = $this->event->findById($event_id);
        if (!$event) {
            $_SESSION['error'] = "Evento non trovato";
            header('Location: /teams');
            exit();
        }

        // Calcola prezzi
        $participant_count = count($participants);
        $base_price = $event['prezzo_base'];
        
        // Calcola sconto
        $discount_percent = 0;
        if ($participant_count >= 50) $discount_percent = 20;
        elseif ($participant_count >= 30) $discount_percent = 15;
        elseif ($participant_count >= 20) $discount_percent = 12;
        elseif ($participant_count >= 15) $discount_percent = 10;
        elseif ($participant_count >= 10) $discount_percent = 8;
        elseif ($participant_count >= 5) $discount_percent = 5;

        $discounted_price = $base_price * (1 - $discount_percent / 100);
        $total_amount = $discounted_price * $participant_count;

        // Salva nella sessione per il checkout
        $_SESSION['quick_collective_data'] = [
            'team_id' => $team_id,
            'team_name' => $team_data['nome'],
            'event_id' => $event_id,
            'event_name' => $event['nome'],
            'participants' => $participants,
            'participant_count' => $participant_count,
            'base_price' => $base_price,
            'discount_percent' => $discount_percent,
            'discounted_price' => $discounted_price,
            'total_amount' => $total_amount,
            'notes' => $notes
        ];

        // Redirect al checkout
        header('Location: /teams/quick-checkout');
        exit();
    }

    // Pagina checkout rapido
    public function quickCheckout() {
        if (!isset($_SESSION['quick_collective_data'])) {
            header('Location: /teams');
            exit();
        }

        $data = $_SESSION['quick_collective_data'];
        include '../app/views/teams/quick_checkout.php';
    }

    // Conferma pagamento rapido
    public function confirmQuickPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['quick_collective_data'])) {
            header('Location: /teams');
            exit();
        }

        $data = $_SESSION['quick_collective_data'];

        try {
            $this->conn->beginTransaction();

            // Salva iscrizione collettiva diretta nel database
            $collective_id = $this->saveQuickCollectiveRegistration($data);

            $this->conn->commit();

            // Pulisci sessione
            unset($_SESSION['quick_collective_data']);

            $_SESSION['success'] = "Iscrizione collettiva completata con successo! Riceverai una ricevuta via email.";
            header('Location: /teams/view/' . $data['team_id']);
            exit();

        } catch (Exception $e) {
            $this->conn->rollback();
            $_SESSION['error'] = "Errore nel completare l'iscrizione: " . $e->getMessage();
            header('Location: /teams/quick-checkout');
            exit();
        }
    }

    // Salva iscrizione collettiva rapida
    private function saveQuickCollectiveRegistration($data) {
        // Genera ID ricevuta
        $receipt_id = 'QC-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Query per salvare iscrizione principale
        $query = "INSERT INTO team_collective_registrations SET 
                 team_id = :team_id,
                 event_id = :event_id,
                 responsible_user_id = :user_id,
                 responsible_name = :user_name,
                 responsible_email = :user_email,
                 total_participants = :total_participants,
                 base_price_per_person = :base_price,
                 discount_percentage = :discount_percent,
                 discounted_price_per_person = :discounted_price,
                 total_amount = :total_amount,
                 notes = :notes,
                 receipt_id = :receipt_id,
                 status = 'paid',
                 payment_method = 'quick_form',
                 created_at = NOW()";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'team_id' => $data['team_id'],
            'event_id' => $data['event_id'], 
            'user_id' => $_SESSION['user_id'],
            'user_name' => $_SESSION['name'] ?? 'Team Leader',
            'user_email' => $_SESSION['email'] ?? '',
            'total_participants' => $data['participant_count'],
            'base_price' => $data['base_price'],
            'discount_percent' => $data['discount_percent'],
            'discounted_price' => $data['discounted_price'],
            'total_amount' => $data['total_amount'],
            'notes' => $data['notes'],
            'receipt_id' => $receipt_id
        ]);

        $collective_id = $this->conn->lastInsertId();

        // Salva partecipanti
        foreach ($data['participants'] as $participant) {
            if (empty($participant['nome']) || empty($participant['cognome'])) continue;

            $query = "INSERT INTO team_collective_participants SET
                     collective_registration_id = :collective_id,
                     nome = :nome,
                     cognome = :cognome,
                     created_at = NOW()";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'collective_id' => $collective_id,
                'nome' => trim($participant['nome']),
                'cognome' => trim($participant['cognome'])
            ]);
        }

        return $collective_id;
    }
}
?>
