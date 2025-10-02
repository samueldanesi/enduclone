<?php
/**
 * Controller per la gestione delle iscrizioni agli eventi
 */
class RegistrationController {
    private $db;
    private $registration;
    private $event;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->registration = new Registration($this->db);
        $this->event = new Event($this->db);
        $this->user = new User($this->db);
    }

    // Mostra form di iscrizione
    public function show($event_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Devi effettuare il login per iscriverti agli eventi';
            header('Location: /login');
            exit;
        }

        if ($_SESSION['user_type'] !== 'participant') {
            $_SESSION['error'] = 'Solo i partecipanti possono iscriversi agli eventi';
            header('Location: /events/' . $event_id);
            exit;
        }

        // Verifica se l'evento esiste
        $this->event->id = $event_id;
        $event_data = $this->event->readOne();
        
        if (!$event_data) {
            http_response_code(404);
            include __DIR__ . '/../views/404.php';
            return;
        }

        // Verifica se l'utente è già iscritto
        if ($this->registration->isUserRegistered($_SESSION['user_id'], $event_id)) {
            $_SESSION['error'] = 'Sei già iscritto a questo evento';
            header('Location: /events/' . $event_id);
            exit;
        }

        // Verifica se l'utente è già registrato
        $already_registered = $this->registration->isUserRegistered($_SESSION['user_id'], $event_id);

        // Verifica disponibilità posti
        $availability = $this->registration->checkEventAvailability($event_id);
        if ($availability['available_spots'] <= 0) {
            $_SESSION['error'] = 'Evento al completo, non ci sono più posti disponibili';
            header('Location: /events/' . $event_id);
            exit;
        }

        // Carica dati utente
        $this->user->id = $_SESSION['user_id'];
        if ($this->user->readOne()) {
            $user_data = [
                'nome' => $this->user->nome,
                'cognome' => $this->user->cognome,
                'email' => $this->user->email,
                'telefono' => $this->user->cellulare
            ];
        } else {
            $user_data = [];
        }

        include __DIR__ . '/../views/events/register.php';
    }

    // Processa l'iscrizione
    public function store($event_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Devi effettuare il login per iscriverti agli eventi';
            header('Location: /login');
            exit;
        }

        if ($_SESSION['user_type'] !== 'participant') {
            $_SESSION['error'] = 'Solo i partecipanti possono iscriversi agli eventi';
            header('Location: /events/' . $event_id);
            exit;
        }

        // Verifica se l'evento esiste
        $this->event->id = $event_id;
        $event_data = $this->event->readOne();
        
        if (!$event_data) {
            http_response_code(404);
            return;
        }

        // Verifica se l'utente è già iscritto
        if ($this->registration->isUserRegistered($_SESSION['user_id'], $event_id)) {
            $_SESSION['error'] = 'Sei già iscritto a questo evento';
            header('Location: /events/' . $event_id);
            exit;
        }

        // Verifica se l'utente è già registrato
        if ($this->registration->isUserRegistered($_SESSION['user_id'], $event_id)) {
            $_SESSION['error'] = 'Sei già registrato a questo evento';
            header('Location: /events/' . $event_id);
            exit;
        }

        // Verifica disponibilità posti
        $availability = $this->registration->checkEventAvailability($event_id);
        if ($availability['available_spots'] <= 0) {
            $_SESSION['error'] = 'Evento al completo, non ci sono più posti disponibili';
            header('Location: /events/' . $event_id);
            exit;
        }

        // Validazione dati
        $errors = $this->validateRegistrationData($_POST);
        
        if (empty($errors)) {
            $this->registration->user_id = $_SESSION['user_id'];
            $this->registration->event_id = $event_id;
            $this->registration->prezzo_pagato = $_POST['prezzo_pagato'] ?? $event_data['prezzo_base'];
            $this->registration->totale_pagato = $_POST['prezzo_pagato'] ?? $event_data['prezzo_base'];
            $this->registration->metodo_pagamento = $_POST['metodo_pagamento'] ?? 'carta';
            $this->registration->stato = 'confermata'; // In un'app reale sarebbe 'pending' fino al pagamento
            $this->registration->note = $_POST['note'] ?? '';

            if ($this->registration->create()) {
                // Crea ricevuta automaticamente
                $receipt = new Receipt($this->db);
                $receipt->user_id = $_SESSION['user_id'];
                $receipt->registration_id = $this->registration->registration_id;
                $receipt->amount = $this->registration->prezzo_pagato;
                $receipt->create();
                
                // Aggiungi evento al calendario personale (temporaneamente disabilitato)
                // $calendar = new Calendar($this->db);
                // $calendar->addSportEventToCalendar($_SESSION['user_id'], $event_id);
                
                $_SESSION['success'] = 'Iscrizione completata con successo!';
                header('Location: /profile?tab=registrations');
                exit;
            } else {
                $errors[] = 'Errore durante l\'iscrizione. Riprova.';
            }
        }

        // Ricarica form con errori
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        $this->show($event_id);
    }

    // Lista iscrizioni dell'utente
    public function userRegistrations() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $stmt = $this->registration->getUserRegistrations($_SESSION['user_id']);
        $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../views/profile/registrations.php';
    }

    // Cancella iscrizione
    public function cancel($event_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Verifica se l'iscrizione esiste
        if (!$this->registration->isUserRegistered($_SESSION['user_id'], $event_id)) {
            $_SESSION['error'] = 'Non sei iscritto a questo evento';
            header('Location: /events');
            exit;
        }

        // Verifica se è possibile cancellare (es. non troppo vicino alla data evento)
        $this->event->id = $event_id;
        $event_data = $this->event->readOne();
        
        $event_date = new DateTime($event_data['data_evento']);
        $current_date = new DateTime();
        $days_until_event = $current_date->diff($event_date)->days;

        if ($days_until_event < 7) {
            $_SESSION['error'] = 'Non è possibile cancellare l\'iscrizione meno di 7 giorni prima dell\'evento';
            header('Location: /profile?tab=registrations');
            exit;
        }

        if ($this->registration->cancel($_SESSION['user_id'], $event_id)) {
            $_SESSION['success'] = 'Iscrizione cancellata con successo';
        } else {
            $_SESSION['error'] = 'Errore durante la cancellazione dell\'iscrizione';
        }

        header('Location: /profile?tab=registrations');
        exit;
    }

    // Validazione dati iscrizione
    private function validateRegistrationData($data) {
        $errors = [];

        if (empty($data['metodo_pagamento'])) {
            $errors[] = 'Il metodo di pagamento è obbligatorio';
        }

        if (!empty($data['prezzo_pagato']) && (!is_numeric($data['prezzo_pagato']) || $data['prezzo_pagato'] < 0)) {
            $errors[] = 'Il prezzo deve essere un numero valido';
        }

        return $errors;
    }

    // API per controllo disponibilità
    public function apiCheckAvailability($event_id) {
        header('Content-Type: application/json');
        
        $availability = $this->registration->checkEventAvailability($event_id);
        
        if ($availability) {
            echo json_encode([
                'success' => true,
                'data' => $availability
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Evento non trovato'
            ]);
        }
    }
}
?>
