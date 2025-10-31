<?php
/**
 * Controller per la gestione degli eventi
 */
require_once __DIR__ . '/../models/Registration.php';
require_once __DIR__ . '/../models/Category.php';

class EventController {
    private $db;
    private $event;
    private $category;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->event = new Event($this->db);
        $this->category = new Category($this->db);
    }
    
    // Helper per rilevare nome colonna corretto nello schema
    private function detectColumn($table, $candidates) {
        static $cache = [];
        $cacheKey = $table . '_' . implode('_', $candidates);
        
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        
        try {
            $stmt = $this->db->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS 
                                        WHERE TABLE_SCHEMA = DATABASE() 
                                        AND TABLE_NAME = ? 
                                        AND COLUMN_NAME IN (" . implode(',', array_fill(0, count($candidates), '?')) . ")");
            $params = array_merge([$table], $candidates);
            $stmt->execute($params);
            $found = $stmt->fetchColumn();
            
            if ($found) {
                $cache[$cacheKey] = $found;
                return $found;
            }
            
            // Default al primo candidato
            $cache[$cacheKey] = $candidates[0];
            return $candidates[0];
        } catch (Exception $e) {
            return $candidates[0];
        }
    }

    // Mostra tutti gli eventi (homepage)
    public function index() {
        $filters = [];
        
        // Raccogli filtri dalla query string
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $filters['search'] = trim($_GET['search']);
        }
        
        if (isset($_GET['sport']) && !empty($_GET['sport'])) {
            $filters['sport'] = $_GET['sport'];
        }
        
        if (isset($_GET['città']) && !empty($_GET['città'])) {
            $filters['città'] = $_GET['città'];
        }
        
        if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
            $filters['categoria'] = $_GET['categoria'];
        }
        
        if (isset($_GET['luogo']) && !empty($_GET['luogo'])) {
            $filters['luogo'] = $_GET['luogo'];
        }
        
        if (isset($_GET['data_da']) && !empty($_GET['data_da'])) {
            $filters['data_da'] = $_GET['data_da'];
        }
        
        if (isset($_GET['data_a']) && !empty($_GET['data_a'])) {
            $filters['data_a'] = $_GET['data_a'];
        }
        
        // Ordinamento
        $sort = $_GET['sort'] ?? 'date';
        $filters['sort'] = $sort;

        try {
            $stmt = $this->event->readAll($filters);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $ex) {
            // Fallback di emergenza se readAll fallisce
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                @error_log('[ERROR] EventController::index readAll failed: ' . $ex->getMessage());
            }
            
            // Rileva schema delle colonne
            $evOrg = $this->detectColumn('events', ['organizer_id', 'organizzatore_id']);
            $evCap = $this->detectColumn('events', ['max_partecipanti', 'capienza_massima']);
            $evDist = $this->detectColumn('events', ['distanza_km', 'lunghezza_km']);
            $evCat = $this->detectColumn('events', ['categoria_id', 'categoria']);
            $evPrice = $this->detectColumn('events', ['prezzo_base', 'prezzo']);
            $evPlace = $this->detectColumn('events', ['luogo_partenza', 'luogo']);
            
            // Query fallback schema-aware
            $fallbackSql = "SELECT 
                    id AS event_id,
                    `{$evOrg}` AS organizer_id,
                    titolo,
                    descrizione,
                    data_evento,
                    `{$evPlace}` AS luogo_partenza,
                    " . ($evCat ? "`{$evCat}` AS categoria_id," : "NULL AS categoria_id,") . "
                    `{$evPrice}` AS prezzo_base,
                    `{$evCap}` AS max_partecipanti,
                    " . ($evDist ? "`{$evDist}` AS distanza_km," : "NULL AS distanza_km,") . "
                    immagine
                FROM events
                WHERE data_evento >= NOW()
                ORDER BY data_evento ASC";
            $stmt = $this->db->prepare($fallbackSql);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        
        // Completa dati mancanti (organizer_name) se non già presenti
        foreach ($events as &$event) {
            if (empty($event['organizer_name'])) {
                $orgQuery = "SELECT nome, cognome FROM users WHERE id = ?";
                $orgStmt = $this->db->prepare($orgQuery);
                $orgStmt->execute([$event['organizer_id']]);
                $organizer = $orgStmt->fetch(PDO::FETCH_ASSOC);
                $event['organizer_name'] = ($organizer ? $organizer['nome'] . ' ' . $organizer['cognome'] : 'Organizzatore');
            }
        }

        // Calcola registrations_count per ogni evento (se non già incluso dal model)
        foreach ($events as &$ev) {
            if (!isset($ev['registrations_count'])) {
                $stmtC = $this->db->prepare("SELECT COUNT(*) AS c FROM registrations WHERE event_id = ? AND status IN ('confermata','confirmed')");
                $stmtC->execute([$ev['event_id'] ?? $ev['id'] ?? null]);
                $rowC = $stmtC->fetch(PDO::FETCH_ASSOC) ?: ['c' => 0];
                $ev['registrations_count'] = (int)$rowC['c'];
            }
        }

        // Carica vista
        include __DIR__ . '/../views/events/index.php';
    }

    // Mostra dettaglio evento
    public function show($id) {
        $this->event->id = $id;
        $event = $this->event->readOne();
        
        if (!$event) {
            http_response_code(404);
            include __DIR__ . '/../views/404.php';
            return;
        }

        // Verifica se l'utente è già iscritto e crea istanza Registration
        $user_registered = false;
        $registration = new Registration($this->db);
        if (isset($_SESSION['user_id'])) {
            $user_registered = $registration->isUserRegistered($_SESSION['user_id'], $id);
        }

        // Passa la connessione database e l'istanza registration alla vista
        $db = $this->db;
        
        include __DIR__ . '/../views/events/show.php';
    }

    // Form creazione evento (solo organizzatori)
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            // Carica le categorie per il form con fallback se la tabella non esiste
            $categories = [];
            try {
                $categories_stmt = $this->category->getAllActive();
                if ($categories_stmt) {
                    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                }
            } catch (Throwable $catEx) {
                if (defined('DEBUG_MODE') && DEBUG_MODE) {
                    @error_log('[WARN] Category list unavailable: ' . $catEx->getMessage());
                }
                // Lascia $categories vuoto: la vista mostrerà le opzioni di default
            }
            
            include __DIR__ . '/../views/organizer/create.php';
        }
    }

    // Salva nuovo evento
    private function store() {
        // Validazione dati
        $errors = $this->validateEventData($_POST);
        
        if (empty($errors)) {
            $this->event->organizer_id = $_SESSION['user_id'];
            $this->event->titolo = $_POST['titolo'];
            $this->event->descrizione = $_POST['descrizione'];
            $this->event->data_evento = $_POST['data_evento'];
            $this->event->luogo_partenza = $_POST['luogo_partenza'];
            $this->event->citta = $_POST['citta'] ?? 'Milano'; // Default se non fornita
            $this->event->categoria_id = (int)($_POST['categoria_id'] ?? $_POST['categoria'] ?? 1);
            $this->event->sport = $_POST['sport'];
            $this->event->prezzo_base = (float)($_POST['prezzo_base'] ?? 0);
            $this->event->max_partecipanti = (int)($_POST['max_partecipanti'] ?? $_POST['capienza_massima'] ?? 100);
            $this->event->distanza_km = !empty($_POST['distanza_km']) ? (float)$_POST['distanza_km'] : (!empty($_POST['lunghezza_km']) ? (float)$_POST['lunghezza_km'] : null);
            // Mappa status a valori ENUM validi
            $status_input = $_POST['status'] ?? 'pubblicato';
            $valid_stati = ['bozza', 'pubblicato', 'chiuso', 'annullato'];
            $this->event->status = in_array($status_input, $valid_stati) ? $status_input : 'pubblicato';

            if ($this->event->create()) {
                // Upload immagine se presente
                if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === 0) {
                    $this->event->uploadImage($_FILES['immagine']);
                }
                
                // Upload GPX se presente
                if (isset($_FILES['file_gpx']) && $_FILES['file_gpx']['error'] === 0) {
                    $this->event->uploadGPX($_FILES['file_gpx']);
                }

                $_SESSION['success'] = 'Evento creato con successo!';
                header('Location: /organizer');
                exit;
            } else {
                $errors[] = 'Errore durante la creazione dell\'evento';
            }
        }

        // Ricarica form con errori
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        include __DIR__ . '/../views/organizer/create.php';
    }

    // Form modifica evento
    public function edit($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        $this->event->id = $id;
        $event_data = $this->event->readOne();
        
        if (!$event_data || $event_data['organizer_id'] != $_SESSION['user_id']) {
            http_response_code(404);
            include __DIR__ . '/../views/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            // Prepara le variabili per la vista
            $event = (object) $event_data;
            $db = $this->db;
            include __DIR__ . '/../views/events/edit.php';
        }
    }

    // Aggiorna evento
    private function update($id) {
        $errors = $this->validateEventData($_POST);
        
        if (empty($errors)) {
            $this->event->id = $id;
            $this->event->organizer_id = $_SESSION['user_id'];
            $this->event->titolo = $_POST['titolo'];
            $this->event->descrizione = $_POST['descrizione'];
            $this->event->data_evento = $_POST['data_evento'];
            $this->event->luogo_partenza = $_POST['luogo_partenza'];
            $this->event->citta = $_POST['citta'] ?? 'Milano'; // Default se non fornita
            $this->event->categoria_id = (int)($_POST['categoria_id'] ?? $_POST['categoria'] ?? 1);
            $this->event->sport = $_POST['sport'];
            $this->event->prezzo_base = (float)($_POST['prezzo_base'] ?? 0);
            $this->event->max_partecipanti = (int)($_POST['max_partecipanti'] ?? $_POST['capienza_massima'] ?? 100);
            $this->event->distanza_km = !empty($_POST['distanza_km']) ? (float)$_POST['distanza_km'] : (!empty($_POST['lunghezza_km']) ? (float)$_POST['lunghezza_km'] : null);
            
            // Mappa status a valori ENUM validi
            $status_input = $_POST['status'] ?? 'bozza';
            $valid_stati = ['bozza', 'pubblicato', 'chiuso', 'annullato'];
            $this->event->status = in_array($status_input, $valid_stati) ? $status_input : 'bozza';

            if ($this->event->update()) {
                // Upload immagine se presente
                if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === 0) {
                    $this->event->uploadImage($_FILES['immagine']);
                }
                
                // Upload GPX se presente
                if (isset($_FILES['file_gpx']) && $_FILES['file_gpx']['error'] === 0) {
                    $this->event->uploadGPX($_FILES['file_gpx']);
                }

                $_SESSION['success'] = 'Evento aggiornato con successo!';
                header('Location: /organizer');
                exit;
            } else {
                $errors[] = 'Errore durante l\'aggiornamento dell\'evento';
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        $event_data = $this->event->readOne();
        include __DIR__ . '/../views/events/edit.php';
    }

    // Elimina evento
    public function delete($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            http_response_code(403);
            return;
        }

        $this->event->id = $id;
        $this->event->organizer_id = $_SESSION['user_id'];
        
        if ($this->event->delete()) {
            $_SESSION['success'] = 'Evento eliminato con successo!';
        } else {
            $_SESSION['error'] = 'Errore durante l\'eliminazione dell\'evento';
        }

        header('Location: /organizer');
        exit;
    }

    // Dashboard organizzatore
    public function organizerDashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        $stmt = $this->event->getByOrganizer($_SESSION['user_id']);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/organizer/dashboard.php';
    }

    // Statistiche evento
    public function statistics($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            http_response_code(403);
            return;
        }

        $this->event->id = $id;
        $event_data = $this->event->readOne();
        
        if (!$event_data || $event_data['organizer_id'] != $_SESSION['user_id']) {
            http_response_code(404);
            return;
        }

        $statistics = $this->event->getEventStatistics();
        
        // Aggiungi statistiche base
        $registration = new Registration($this->db);
        $registrationStats = $registration->getEventRegistrationStats($id);
        $statistics = array_merge($statistics, $registrationStats);
        $statistics['total_registrations'] = $registrationStats['total_confirmed'];
        
        // Ottieni iscrizioni recenti
        $recent_registrations = $registration->getEventRegistrations($id);
        
        include __DIR__ . '/../views/events/statistics.php';
    }

    // Download lista iscritti (Excel)
    public function downloadRegistrations($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            http_response_code(403);
            return;
        }

        $this->event->id = $id;
        $event_data = $this->event->readOne();
        
        if (!$event_data || $event_data['organizer_id'] != $_SESSION['user_id']) {
            http_response_code(404);
            return;
        }

        // Ottieni lista iscritti
        $registration = new Registration($this->db);
        $registrations = $registration->getEventRegistrations($id);

        // Genera CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="iscritti_evento_' . $id . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header CSV
        fputcsv($output, [
            'ID', 'Nome', 'Cognome', 'Email', 'Telefono', 'Data Nascita', 
            'Sesso', 'Prezzo Pagato', 'Data Iscrizione', 'Status'
        ]);

        // Dati
        foreach ($registrations as $reg) {
            fputcsv($output, [
                $reg['id'],
                $reg['nome'],
                $reg['cognome'],
                $reg['email'],
                $reg['cellulare'],
                $reg['data_nascita'],
                $reg['sesso'],
                $reg['prezzo_pagato'],
                $reg['created_at'],
                $reg['status']
            ]);
        }

        fclose($output);
        exit;
    }

    // Validazione dati evento
    private function validateEventData($data) {
        $errors = [];

        if (empty($data['titolo'])) {
            $errors[] = 'Il titolo è obbligatorio';
        }

        if (empty($data['descrizione'])) {
            $errors[] = 'La descrizione è obbligatoria';
        }

        if (empty($data['data_evento'])) {
            $errors[] = 'La data dell\'evento è obbligatoria';
        } elseif (strtotime($data['data_evento']) <= time()) {
            $errors[] = 'La data dell\'evento deve essere futura';
        }

        if (empty($data['luogo_partenza'])) {
            $errors[] = 'Il luogo di partenza è obbligatorio';
        }

        if (empty($data['sport'])) {
            $errors[] = 'Lo sport è obbligatorio';
        }

        if (empty($data['prezzo_base']) || !is_numeric($data['prezzo_base']) || $data['prezzo_base'] < 0) {
            $errors[] = 'Il prezzo deve essere un numero valido';
        }

        $max_part = $data['max_partecipanti'] ?? $data['capienza_massima'] ?? null;
        if (empty($max_part) || !is_numeric($max_part) || $max_part <= 0) {
            $errors[] = 'Il numero massimo di partecipanti deve essere un numero positivo';
        }

        return $errors;
    }

    // API per ricerca eventi (AJAX)
    public function apiSearch() {
        header('Content-Type: application/json');
        
        $filters = [];
        if (isset($_GET['q'])) {
            $filters['search'] = $_GET['q'];
        }
        
        $stmt = $this->event->readAll($filters);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $events]);
    }
}
?>
