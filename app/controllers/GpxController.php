<?php
/**
 * Controller per la gestione dei file GPX
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/GpxFile.php';
require_once __DIR__ . '/../models/Event.php';

class GpxController {
    private $db;
    private $gpxFile;
    private $event;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->gpxFile = new GpxFile($this->db);
        $this->event = new Event($this->db);
    }

    // Download protetto di file GPX
    public function download($gpx_id) {
        // Verifica autenticazione
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Devi essere autenticato per scaricare i file GPX';
            header('Location: /login');
            exit;
        }

        // Ottieni file GPX
        if (!$this->gpxFile->getById($gpx_id)) {
            $_SESSION['error'] = 'File GPX non trovato';
            header('Location: /events');
            exit;
        }

        // Verifica se l'utente può scaricare (deve essere iscritto E aver pagato)
        $downloadPermission = $this->gpxFile->canUserDownload($_SESSION['user_id']);
        if (!$downloadPermission['can_download']) {
            $_SESSION['error'] = $downloadPermission['reason'];
            header('Location: /events/' . $this->gpxFile->event_id);
            exit;
        }

        // Verifica esistenza file
        if (!file_exists($this->gpxFile->file_path)) {
            $_SESSION['error'] = 'File GPX non disponibile';
            header('Location: /events/' . $this->gpxFile->event_id);
            exit;
        }

        // Incrementa contatore download
        $this->gpxFile->incrementDownloadCount();

        // Log del download
        $this->logDownload($_SESSION['user_id'], $gpx_id);

        // Forza il download
        header('Content-Type: application/gpx+xml');
        header('Content-Disposition: attachment; filename="' . $this->gpxFile->original_name . '"');
        header('Content-Length: ' . filesize($this->gpxFile->file_path));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        // Output del file
        readfile($this->gpxFile->file_path);
        exit;
    }

    // Upload file GPX (solo organizzatori)
    public function upload() {
        // Verifica autenticazione e permessi
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            $_SESSION['error'] = 'Accesso negato';
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metodo non consentito';
            header('Location: /organizer/dashboard');
            exit;
        }

        $event_id = $_POST['event_id'] ?? 0;

        // Verifica che l'evento appartenga all'organizzatore
        if (!$this->event->getById($event_id) || $this->event->organizer_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Evento non trovato o non autorizzato';
            header('Location: /organizer/dashboard');
            exit;
        }

        // Verifica file uploaded
        if (!isset($_FILES['gpx_file']) || $_FILES['gpx_file']['error'] !== 0) {
            $_SESSION['error'] = 'Nessun file selezionato o errore durante l\'upload';
            header('Location: /events/' . $event_id . '/edit');
            exit;
        }

        // Upload del file
        $result = $this->gpxFile->upload($_FILES['gpx_file'], $event_id);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: /events/' . $event_id . '/edit');
        exit;
    }

    // Elimina file GPX
    public function delete($gpx_id) {
        // Verifica autenticazione e permessi
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            $_SESSION['error'] = 'Accesso negato';
            header('Location: /login');
            exit;
        }

        if (!$this->gpxFile->getById($gpx_id)) {
            $_SESSION['error'] = 'File GPX non trovato';
            header('Location: /organizer/dashboard');
            exit;
        }

        // Verifica che l'evento appartenga all'organizzatore
        if (!$this->event->getById($this->gpxFile->event_id) || $this->event->organizer_id != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Non autorizzato a eliminare questo file';
            header('Location: /organizer/dashboard');
            exit;
        }

        if ($this->gpxFile->delete()) {
            $_SESSION['success'] = 'File GPX eliminato con successo';
        } else {
            $_SESSION['error'] = 'Errore durante l\'eliminazione del file GPX';
        }

        header('Location: /events/' . $this->gpxFile->event_id . '/edit');
        exit;
    }

    // Lista file GPX per evento (API JSON)
    public function getEventGpxFiles($event_id) {
        header('Content-Type: application/json');
        
        // Verifica autenticazione
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Non autenticato']);
            exit;
        }

        $files = $this->gpxFile->getByEventId($event_id);
        echo json_encode($files);
        exit;
    }

    // Log dei download per statistiche
    private function logDownload($user_id, $gpx_id) {
        $query = "INSERT INTO download_logs (user_id, gpx_file_id, downloaded_at) 
                 VALUES (:user_id, :gpx_id, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':gpx_id', $gpx_id);
        $stmt->execute();
    }
}
?>
