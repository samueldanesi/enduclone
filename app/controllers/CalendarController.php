<?php
/**
 * Controller per la gestione del calendario personale
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Calendar.php';

class CalendarController {
    private $db;
    private $calendar;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->calendar = new Calendar($this->db);
    }

    // Mostra la vista calendario
    public function index() {
        AuthController::requireAuth();
        
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');
        
        // Validazione parametri
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            $year = date('Y');
            $month = date('m');
        }
        
        $events = $this->calendar->getUserMonthEvents($_SESSION['user_id'], $year, $month);
        $stats = $this->calendar->getCalendarStats($_SESSION['user_id']);
        $upcomingEvents = $this->calendar->getUpcomingEvents($_SESSION['user_id']);
        
        include __DIR__ . '/../views/calendar/index.php';
    }

    // API per ottenere eventi calendario (AJAX)
    public function getEvents() {
        AuthController::requireAuth();
        header('Content-Type: application/json');
        
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');
        
        $events = $this->calendar->getCalendarData($_SESSION['user_id'], $year, $month);
        echo json_encode($events);
        exit;
    }

    // Mostra form per nuovo evento
    public function create() {
        AuthController::requireAuth();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $time = $_GET['time'] ?? '09:00';
        
        include __DIR__ . '/../views/calendar/create.php';
    }

    // Salva nuovo evento
    public function store() {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metodo non consentito';
            header('Location: /calendar');
            exit;
        }

        $errors = $this->validateEventData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: /calendar/create');
            exit;
        }

        $this->calendar->user_id = $_SESSION['user_id'];
        $this->calendar->title = trim($_POST['title']);
        $this->calendar->description = trim($_POST['description'] ?? '');
        $this->calendar->event_type = $_POST['event_type'] ?? 'personal';
        $this->calendar->color = $_POST['color'] ?? '#3b82f6';
        $this->calendar->location = trim($_POST['location'] ?? '');
        $this->calendar->notification_minutes = (int)($_POST['notification_minutes'] ?? 30);
        
        // Gestione date e orari
        if (isset($_POST['is_all_day'])) {
            $this->calendar->is_all_day = true;
            $this->calendar->start_datetime = $_POST['start_date'] . ' 00:00:00';
            $this->calendar->end_datetime = $_POST['end_date'] ? $_POST['end_date'] . ' 23:59:59' : null;
        } else {
            $this->calendar->is_all_day = false;
            $this->calendar->start_datetime = $_POST['start_date'] . ' ' . $_POST['start_time'];
            $this->calendar->end_datetime = $_POST['end_date'] && $_POST['end_time'] ? 
                $_POST['end_date'] . ' ' . $_POST['end_time'] : null;
        }

        if ($this->calendar->create()) {
            $_SESSION['success'] = 'Evento aggiunto al calendario con successo';
        } else {
            $_SESSION['error'] = 'Errore durante la creazione dell\'evento';
        }

        header('Location: /calendar');
        exit;
    }

    // Mostra form modifica evento
    public function edit($id) {
        AuthController::requireAuth();
        
        if (!$this->calendar->getById($id, $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Evento non trovato';
            header('Location: /calendar');
            exit;
        }

        include __DIR__ . '/../views/calendar/edit.php';
    }

    // Aggiorna evento
    public function update($id) {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metodo non consentito';
            header('Location: /calendar');
            exit;
        }

        if (!$this->calendar->getById($id, $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Evento non trovato';
            header('Location: /calendar');
            exit;
        }

        $errors = $this->validateEventData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: /calendar/' . $id . '/edit');
            exit;
        }

        $this->calendar->title = trim($_POST['title']);
        $this->calendar->description = trim($_POST['description'] ?? '');
        $this->calendar->event_type = $_POST['event_type'] ?? 'personal';
        $this->calendar->color = $_POST['color'] ?? '#3b82f6';
        $this->calendar->location = trim($_POST['location'] ?? '');
        $this->calendar->notification_minutes = (int)($_POST['notification_minutes'] ?? 30);
        
        // Gestione date e orari
        if (isset($_POST['is_all_day'])) {
            $this->calendar->is_all_day = true;
            $this->calendar->start_datetime = $_POST['start_date'] . ' 00:00:00';
            $this->calendar->end_datetime = $_POST['end_date'] ? $_POST['end_date'] . ' 23:59:59' : null;
        } else {
            $this->calendar->is_all_day = false;
            $this->calendar->start_datetime = $_POST['start_date'] . ' ' . $_POST['start_time'];
            $this->calendar->end_datetime = $_POST['end_date'] && $_POST['end_time'] ? 
                $_POST['end_date'] . ' ' . $_POST['end_time'] : null;
        }

        if ($this->calendar->update()) {
            $_SESSION['success'] = 'Evento aggiornato con successo';
        } else {
            $_SESSION['error'] = 'Errore durante l\'aggiornamento dell\'evento';
        }

        header('Location: /calendar');
        exit;
    }

    // Elimina evento
    public function delete($id) {
        AuthController::requireAuth();
        
        if (!$this->calendar->getById($id, $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Evento non trovato';
            header('Location: /calendar');
            exit;
        }

        if ($this->calendar->delete()) {
            $_SESSION['success'] = 'Evento eliminato con successo';
        } else {
            $_SESSION['error'] = 'Errore durante l\'eliminazione dell\'evento';
        }

        header('Location: /calendar');
        exit;
    }

    // Vista dettagli evento
    public function show($id) {
        AuthController::requireAuth();
        
        if (!$this->calendar->getById($id, $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Evento non trovato';
            header('Location: /calendar');
            exit;
        }

        include __DIR__ . '/../views/calendar/show.php';
    }

    // Vista giornaliera
    public function day() {
        AuthController::requireAuth();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Validazione data
        if (!DateTime::createFromFormat('Y-m-d', $date)) {
            $date = date('Y-m-d');
        }
        
        $events = $this->calendar->getUserDayEvents($_SESSION['user_id'], $date);
        
        include __DIR__ . '/../views/calendar/day.php';
    }

    // Validazione dati evento
    private function validateEventData($data) {
        $errors = [];

        if (empty(trim($data['title'] ?? ''))) {
            $errors[] = 'Il titolo è obbligatorio';
        }

        if (empty($data['start_date']) || !DateTime::createFromFormat('Y-m-d', $data['start_date'])) {
            $errors[] = 'Data di inizio non valida';
        }

        if (!empty($data['end_date']) && !DateTime::createFromFormat('Y-m-d', $data['end_date'])) {
            $errors[] = 'Data di fine non valida';
        }

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $errors[] = 'La data di fine deve essere successiva alla data di inizio';
            }
        }

        if (!isset($data['is_all_day'])) {
            if (empty($data['start_time']) || !DateTime::createFromFormat('H:i', $data['start_time'])) {
                $errors[] = 'Orario di inizio non valido';
            }
        }

        return $errors;
    }
}
?>
