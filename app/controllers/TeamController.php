<?php
require_once '../app/models/Team.php';
require_once '../app/models/Event.php';
require_once '../app/models/User.php';
require_once '../app/models/CollectiveRegistration.php';

/**
 * TeamController - Gestione completa e logica dei team
 * 
 * Sezioni:
 * 1. GESTIONE BASE - Visualizzazione, creazione, modifica team
 * 2. GESTIONE MEMBRI - Inviti, richieste, espulsioni  
 * 3. ISCRIZIONI COLLETTIVE - Sistema unificato con form rapido e CSV
 * 4. STATISTICHE E REPORT - Dashboard team con dati completi
 */
class TeamController {
    private $db;
    private $team;
    private $event;
    private $user;
    private $collectiveRegistration;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->team = new Team($this->db);
        $this->event = new Event($this->db);
        $this->user = new User($this->db);
        $this->collectiveRegistration = new CollectiveRegistration($this->db);
    }

    // ==========================================
    // 1. GESTIONE BASE TEAM
    // ==========================================

    /**
     * Lista team con filtri e ricerca
     */
    public function index() {
        $this->requireAuth();
        
        $filters = [
            'search' => $_GET['search'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'visibilita' => $_GET['visibilita'] ?? ''
        ];
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $teams = $this->team->search($filters['search'], $filters['tipo'], $limit, $offset);
        $user_teams = $this->team->getUserTeams($_SESSION['user_id']);
        
        // Arricchisci dati team
        foreach ($teams as &$team) {
            $team['members_count'] = $this->getTeamMembersCount($team['id']);
            $team['is_member'] = $this->isUserMember($_SESSION['user_id'], $team['id']);
            $team['can_join'] = $this->canUserJoin($_SESSION['user_id'], $team['id']);
        }

        include '../app/views/teams/index.php';
    }

    /**
     * Visualizza dettagli team singolo
     */
    public function view($team_id) {
        $this->requireAuth();
        
        $team = $this->team->findById($team_id);
        if (!$team) {
            $_SESSION['error'] = "Team non trovato";
            header('Location: /teams');
            exit();
        }

        // Per ora consideriamo tutti gli utenti membri come potenziali manager
        $is_member = $this->isUserMember($_SESSION['user_id'], $team_id);
        $is_leader = $is_member; // Temporaneo: tutti i membri possono gestire
        $can_manage = $is_member;
        
        // Dati per la dashboard
        $members = $this->getTeamMembers($team_id);
        $stats = $this->getTeamStats($team_id);
        $recent_registrations = $this->getRecentCollectiveRegistrations($team_id);
        $pending_requests = $can_manage ? $this->getPendingJoinRequests($team_id) : [];

        include '../app/views/teams/view.php';
    }

    /**
     * Mostra la pagina di creazione di un nuovo team
     */
    public function create() {
        // Verifica che l'utente sia loggato
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        require_once __DIR__ . '/../views/teams/create.php';
    }

    /**
     * Salva un nuovo team nel database
     */
    public function store() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nome' => $_POST['nome'] ?? '',
                'descrizione' => $_POST['descrizione'] ?? '',
                'categoria' => $_POST['categoria'] ?? '',
                'livello' => $_POST['livello'] ?? 'intermedio',
                'max_membri' => $_POST['max_membri'] ?? 10,
                'creator_id' => $_SESSION['user_id']
            ];

            if ($this->team->create($data)) {
                header('Location: /teams?success=1');
            } else {
                header('Location: /teams/create?error=1');
            }
            exit;
        }
    }    /**
     * Form modifica team (solo per leader)
     */
    public function edit($team_id) {
        $this->requireAuth();
        
        $team = $this->team->findById($team_id);
        if (!$team || $team['leader_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Non autorizzato";
            header('Location: /teams');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditTeam($team_id);
        } else {
            include '../app/views/teams/edit.php';
        }
    }

    // ==========================================
    // 2. GESTIONE MEMBRI
    // ==========================================

    /**
     * Gestione membri del team (solo leader)
     */
    public function manageMembers($team_id) {
        $this->requireAuth();
        $this->requireTeamLeader($team_id);
        
        $team = $this->team->findById($team_id);
        $members = $this->getTeamMembers($team_id);
        $pending_requests = $this->getPendingJoinRequests($team_id);
        
        include '../app/views/teams/manage_members.php';
    }

    /**
     * Richiesta di adesione al team
     */
    public function requestJoin() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /teams');
            exit();
        }

        $team_id = $_POST['team_id'] ?? null;
        $message = $_POST['message'] ?? '';

        if (!$team_id || $this->isUserMember($_SESSION['user_id'], $team_id)) {
            $_SESSION['error'] = "Richiesta non valida";
            header('Location: /teams');
            exit();
        }

        try {
            $this->team->createJoinRequest($_SESSION['user_id'], $team_id, $message);
            $_SESSION['success'] = "Richiesta di adesione inviata";
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nell'invio della richiesta";
        }

        header('Location: /teams/view/' . $team_id);
    }

    /**
     * Approva/rifiuta richieste di adesione
     */
    public function handleJoinRequest() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /teams');
            exit();
        }

        $request_id = $_POST['request_id'] ?? null;
        $action = $_POST['action'] ?? null; // 'approve' or 'reject'
        
        if (!$request_id || !in_array($action, ['approve', 'reject'])) {
            $_SESSION['error'] = "Azione non valida";
            header('Location: /teams');
            exit();
        }

        try {
            $request = $this->team->getJoinRequest($request_id);
            $this->requireTeamLeader($request['team_id']);
            
            if ($action === 'approve') {
                $this->team->approveJoinRequest($request_id);
                $_SESSION['success'] = "Richiesta approvata";
            } else {
                $this->team->rejectJoinRequest($request_id);
                $_SESSION['success'] = "Richiesta rifiutata";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nell'elaborazione della richiesta";
        }

        header('Location: /teams/manage-members/' . $request['team_id']);
    }

    /**
     * Rimuovi membro dal team
     */
    public function removeMember() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /teams');
            exit();
        }

        $team_id = $_POST['team_id'] ?? null;
        $user_id = $_POST['user_id'] ?? null;

        $this->requireTeamLeader($team_id);

        try {
            $this->team->removeMember($team_id, $user_id);
            $_SESSION['success'] = "Membro rimosso dal team";
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nella rimozione del membro";
        }

        header('Location: /teams/manage-members/' . $team_id);
    }

    // ==========================================
    // 3. ISCRIZIONI COLLETTIVE - SISTEMA UNIFICATO
    
    /**
     * Unisciti a un team (gestisce richiesta POST)
     */
    public function join() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /teams');
            exit();
        }

        $team_id = $_POST['team_id'] ?? null;
        
        if (!$team_id || !is_numeric($team_id)) {
            $_SESSION['error'] = "Team non valido";
            header('Location: /teams');
            exit();
        }

        try {
            $this->team->createJoinRequest($_SESSION['user_id'], $team_id);
            $_SESSION['success'] = "Richiesta di adesione inviata";
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nell'invio della richiesta";
        }

        header('Location: /teams/view/' . $team_id);
    }
    
    /**
     * Chat del team
     */
    public function chat($team_id) {
        $this->requireAuth();
        
        if (!$this->isUserMember($_SESSION['user_id'], $team_id)) {
            $_SESSION['error'] = "Non sei membro di questo team";
            header('Location: /teams');
            exit();
        }
        
        $team = $this->team->findById($team_id);
        
        // Qui dovrebbe esserci la logica per la chat
        // Per ora reindirizza alla vista del team
        $_SESSION['info'] = "Funzionalità chat in sviluppo";
        header('Location: /teams/view/' . $team_id);
    }

    // ==========================================

    /**
     * Dashboard iscrizioni collettive
     */
    public function collectiveRegistrations($team_id) {
        $this->requireAuth();
        // Temporaneamente commentato per testing
        // $this->requireTeamLeader($team_id);
        
        $team = $this->team->findById($team_id);
        $registrations = $this->collectiveRegistration->getByTeam($team_id);
        $available_events = $this->event->getAvailable();
        
        include '../app/views/teams/collective_dashboard.php';
    }

    /**
     * Form iscrizione rapida (inserimento manuale)
     */
    public function quickRegistration($team_id) {
        $this->requireAuth();
        $this->requireTeamLeader($team_id);
        
        $team = $this->team->findById($team_id);
        $events = $this->event->getAvailable();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleQuickRegistration($team_id);
        } else {
            include '../app/views/teams/quick_registration.php';
        }
    }

    /**
     * Upload iscrizione CSV
     */
    public function csvRegistration($team_id) {
        $this->requireAuth();
        $this->requireTeamLeader($team_id);
        
        $team = $this->team->findById($team_id);
        $events = $this->event->getAvailable();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCsvRegistration($team_id);
        } else {
            include '../app/views/teams/csv_registration.php';
        }
    }

    /**
     * Download template CSV
     */
    public function downloadCsvTemplate() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="template_iscrizione_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        
        fputcsv($output, ['Nome', 'Cognome', 'Email', 'Telefono'], ',', '"', '\\');
        fputcsv($output, ['Mario', 'Rossi', 'mario@email.com', '333-1234567'], ',', '"', '\\');
        fputcsv($output, ['Anna', 'Verdi', '', '333-7654321'], ',', '"', '\\');
        fputcsv($output, ['Luigi', 'Bianchi', 'luigi@email.com', ''], ',', '"', '\\');
        
        fclose($output);
        exit();
    }

    /**
     * Checkout unificato per iscrizioni collettive
     */
    public function checkoutCollective($registration_id) {
        $this->requireAuth();
        
        $registration = $this->collectiveRegistration->findById($registration_id);
        if (!$registration || !$this->canAccessRegistration($registration)) {
            $_SESSION['error'] = "Iscrizione non trovata";
            header('Location: /teams');
            exit();
        }

        include '../app/views/teams/checkout_collective.php';
    }

    /**
     * Conferma pagamento iscrizione collettiva
     */
    public function confirmPayment($registration_id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /teams');
            exit();
        }

        try {
            $registration = $this->collectiveRegistration->findById($registration_id);
            if (!$registration || !$this->canAccessRegistration($registration)) {
                throw new Exception("Iscrizione non valida");
            }

            $this->collectiveRegistration->markAsPaid($registration_id);
            
            $_SESSION['success'] = "Pagamento completato! Riceverai conferma via email.";
            header('Location: /teams/view/' . $registration['team_id']);
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nel pagamento: " . $e->getMessage();
            header('Location: /teams/checkout-collective/' . $registration_id);
        }
    }

    // ==========================================
    // 4. STATISTICHE E REPORT
    // ==========================================

    /**
     * Dashboard statistiche team
     */
    public function stats($team_id) {
        $this->requireAuth();
        $this->requireTeamMember($team_id);
        
        $team = $this->team->findById($team_id);
        $stats = $this->getDetailedTeamStats($team_id);
        
        include '../app/views/teams/stats.php';
    }

    /**
     * Pagina ricerca team
     */
    public function search() {
        $this->requireAuth();
        
        $search = $_GET['search'] ?? '';
        $sport = $_GET['sport'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $teams = $this->team->search($search, $sport, $limit, $offset);
        
        include '../app/views/teams/search.php';
    }

    /**
     * API ricerca team (per autocomplete)
     */
    public function searchApi() {
        $this->requireAuth();
        
        $search = $_GET['q'] ?? '';
        $limit = min(20, (int)($_GET['limit'] ?? 10));
        
        $teams = $this->team->search($search, '', $limit, 0);
        
        header('Content-Type: application/json');
        echo json_encode($teams);
    }

    // ==========================================
    // METODI PRIVATI DI SUPPORTO
    // ==========================================

    private function requireAuth() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    }

    private function requireTeamLeader($team_id) {
        $team = $this->team->findById($team_id);
        if (!$team) {
            $_SESSION['error'] = "Team non trovato";
            header('Location: /teams');
            exit();
        }
        
        // Verifica se l'utente è membro del team
        if (!$this->isUserMember($_SESSION['user_id'], $team_id)) {
            $_SESSION['error'] = "Devi essere membro del team per accedere a questa sezione";
            header('Location: /teams');
            exit();
        }
    }

    private function requireTeamMember($team_id) {
        if (!$this->isUserMember($_SESSION['user_id'], $team_id)) {
            $_SESSION['error'] = "Devi essere membro del team per accedere";
            header('Location: /teams');
            exit();
        }
    }

    private function isUserMember($user_id, $team_id) {
        return $this->team->isUserMember($user_id, $team_id);
    }

    private function canUserJoin($user_id, $team_id) {
        return !$this->isUserMember($user_id, $team_id) && 
               !$this->team->hasPendingRequest($user_id, $team_id);
    }

    private function canAccessRegistration($registration) {
        $team = $this->team->findById($registration['team_id']);
        return $team && (
            $team['leader_id'] == $_SESSION['user_id'] ||
            $this->isUserMember($_SESSION['user_id'], $registration['team_id'])
        );
    }

    private function getTeamMembers($team_id) {
        return $this->team->getMembers($team_id);
    }

    private function getTeamMembersCount($team_id) {
        return $this->team->getMembersCount($team_id);
    }

    private function getPendingJoinRequests($team_id) {
        return $this->team->getPendingRequests($team_id);
    }

    private function getTeamStats($team_id) {
        return [
            'membri_attivi' => $this->getTeamMembersCount($team_id),
            'iscrizioni_eventi' => $this->collectiveRegistration->countByTeam($team_id),
            'eventi_completati' => $this->collectiveRegistration->countCompletedByTeam($team_id),
            'totale_partecipanti' => $this->collectiveRegistration->getTotalParticipantsByTeam($team_id)
        ];
    }

    private function getDetailedTeamStats($team_id) {
        // Statistiche dettagliate per dashboard
        return [
            'basic' => $this->getTeamStats($team_id),
            'monthly_registrations' => $this->collectiveRegistration->getMonthlyStats($team_id),
            'event_breakdown' => $this->collectiveRegistration->getEventBreakdown($team_id),
            'financial_summary' => $this->collectiveRegistration->getFinancialSummary($team_id)
        ];
    }

    private function getRecentCollectiveRegistrations($team_id, $limit = 5) {
        return $this->collectiveRegistration->getRecentByTeam($team_id, $limit);
    }

    private function handleCreateTeam() {
        $nome = trim($_POST['nome'] ?? '');
        $descrizione = trim($_POST['descrizione'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        
        if (empty($nome)) {
            $_SESSION['error'] = "Il nome del team è obbligatorio";
            return;
        }

        try {
            $team_id = $this->team->create([
                'nome' => $nome,
                'descrizione' => $descrizione,
                'tipo' => $tipo,
                'leader_id' => $_SESSION['user_id']
            ]);
            
            $_SESSION['success'] = "Team creato con successo!";
            header('Location: /teams/view/' . $team_id);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nella creazione del team";
        }
    }

    private function handleEditTeam($team_id) {
        $nome = trim($_POST['nome'] ?? '');
        $descrizione = trim($_POST['descrizione'] ?? '');
        
        if (empty($nome)) {
            $_SESSION['error'] = "Il nome del team è obbligatorio";
            return;
        }

        try {
            $this->team->update($team_id, [
                'nome' => $nome,
                'descrizione' => $descrizione
            ]);
            
            $_SESSION['success'] = "Team aggiornato con successo!";
            header('Location: /teams/view/' . $team_id);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nell'aggiornamento del team";
        }
    }

    private function handleQuickRegistration($team_id) {
        $event_id = $_POST['event_id'] ?? null;
        $participants = $_POST['participants'] ?? [];
        $notes = $_POST['notes'] ?? '';

        if (!$event_id || empty($participants)) {
            $_SESSION['error'] = "Dati mancanti per l'iscrizione";
            return;
        }

        try {
            // Usa il sistema esistente CollectiveRegistration
            $registration_id = $this->collectiveRegistration->createQuickRegistration($team_id, $event_id, $participants, $notes);
            
            $_SESSION['success'] = "Iscrizione creata! Procedi al pagamento.";
            header('Location: /teams/checkout-collective/' . $registration_id);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nella creazione dell'iscrizione: " . $e->getMessage();
        }
    }

    private function handleCsvRegistration($team_id) {
        $event_id = $_POST['event_id'] ?? null;
        $notes = $_POST['notes'] ?? '';

        if (!$event_id || !isset($_FILES['csv_file'])) {
            $_SESSION['error'] = "Seleziona evento e file CSV";
            return;
        }

        try {
            // Usa il sistema esistente CollectiveRegistration  
            $registration_id = $this->collectiveRegistration->createFromExcel($team_id, $event_id, $_FILES['csv_file'], $notes);
            
            $_SESSION['success'] = "Iscrizione creata da CSV! Procedi al pagamento.";
            header('Location: /teams/checkout-collective/' . $registration_id);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Errore nell'elaborazione del CSV: " . $e->getMessage();
        }
    }
}