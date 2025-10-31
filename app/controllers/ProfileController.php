<?php
/**
 * Controller per la gestione del profilo utente
 */
class ProfileController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // Mostra profilo utente
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission();
        } else {
            include __DIR__ . '/../views/profile.php';
        }
    }

    // Gestisce submission dei form
    private function handleFormSubmission() {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'update_profile':
                $this->updateProfile();
                break;
            case 'change_password':
                $this->changePassword();
                break;
            case 'upload_certificate':
                $this->uploadCertificate();
                break;
            case 'upload_card':
                $this->uploadCard();
                break;
            default:
                $_SESSION['error'] = 'Azione non riconosciuta';
                break;
        }

        header('Location: /profile?tab=' . ($_GET['tab'] ?? 'profile'));
        exit;
    }

    // Aggiorna dati profilo
    private function updateProfile() {
        $errors = $this->validateProfileData($_POST);
        
        if (empty($errors)) {
            $this->user->id = $_SESSION['user_id'];
            $this->user->nome = $_POST['nome'];
            $this->user->cognome = $_POST['cognome'];
            $this->user->email = $_POST['email'];
            $this->user->cellulare = $_POST['cellulare'];
            $this->user->data_nascita = $_POST['data_nascita'];
            $this->user->sesso = $_POST['sesso'];

            if ($this->user->update()) {
                // Aggiorna dati in sessione
                $_SESSION['nome'] = $_POST['nome'];
                $_SESSION['cognome'] = $_POST['cognome'];
                $_SESSION['email'] = $_POST['email'];
                
                $_SESSION['success'] = 'Profilo aggiornato con successo!';
            } else {
                $_SESSION['error'] = 'Errore durante l\'aggiornamento del profilo';
            }
        } else {
            $_SESSION['errors'] = $errors;
        }
    }

    // Cambia password
    private function changePassword() {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (empty($current_password)) {
            $errors[] = 'La password attuale è obbligatoria';
        }

        if (empty($new_password)) {
            $errors[] = 'La nuova password è obbligatoria';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'La nuova password deve essere di almeno 6 caratteri';
        }

        if ($new_password !== $confirm_password) {
            $errors[] = 'Le password non coincidono';
        }

        if (empty($errors)) {
            $this->user->id = $_SESSION['user_id'];
            
            if ($this->user->changePassword($current_password, $new_password)) {
                $_SESSION['success'] = 'Password cambiata con successo!';
            } else {
                $_SESSION['error'] = 'Password attuale non corretta';
            }
        } else {
            $_SESSION['errors'] = $errors;
        }
    }

    // Upload certificato medico
    private function uploadCertificate() {
        if (!isset($_FILES['certificato_medico']) || $_FILES['certificato_medico']['error'] !== 0) {
            $_SESSION['error'] = 'Errore durante l\'upload del file';
            return;
        }

        $file = $_FILES['certificato_medico'];
        $tipo_certificato = $_POST['tipo_certificato'] ?? '';
        $scadenza_certificato = $_POST['scadenza_certificato'] ?? null;

        if (empty($tipo_certificato)) {
            $_SESSION['error'] = 'Seleziona il tipo di certificato';
            return;
        }

        // Validazione file
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = 'Il file è troppo grande (max 5MB)';
            return;
        }

        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = 'Formato file non supportato (solo PDF, JPG, PNG)';
            return;
        }

        $this->user->id = $_SESSION['user_id'];
        
        if ($this->user->uploadCertificato($file, $tipo_certificato, $scadenza_certificato)) {
            $_SESSION['success'] = 'Certificato medico caricato con successo!';
        } else {
            $_SESSION['error'] = 'Errore durante il caricamento del certificato';
        }
    }

    // Upload tessera affiliazione
    private function uploadCard() {
        if (!isset($_FILES['tessera_affiliazione']) || $_FILES['tessera_affiliazione']['error'] !== 0) {
            $_SESSION['error'] = 'Errore durante l\'upload del file';
            return;
        }

        $file = $_FILES['tessera_affiliazione'];

        // Validazione file
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = 'Il file è troppo grande (max 5MB)';
            return;
        }

        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = 'Formato file non supportato (solo PDF, JPG, PNG)';
            return;
        }

        $this->user->id = $_SESSION['user_id'];
        
        if ($this->user->uploadTessera($file)) {
            $_SESSION['success'] = 'Tessera di affiliazione caricata con successo!';
        } else {
            $_SESSION['error'] = 'Errore durante il caricamento della tessera';
        }
    }

    // Validazione dati profilo
    private function validateProfileData($data) {
        $errors = [];

        if (empty($data['nome'])) {
            $errors[] = 'Il nome è obbligatorio';
        }

        if (empty($data['cognome'])) {
            $errors[] = 'Il cognome è obbligatorio';
        }

        if (empty($data['email'])) {
            $errors[] = 'L\'email è obbligatoria';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Formato email non valido';
        } else {
            // Controlla se email già esiste per altro utente
            $query = "SELECT id FROM users WHERE email = :email AND id != :current_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':current_id', $_SESSION['user_id']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'Questa email è già utilizzata da un altro utente';
            }
        }

        if (empty($data['cellulare'])) {
            $errors[] = 'Il numero di telefono è obbligatorio';
        }

        if (empty($data['data_nascita'])) {
            $errors[] = 'La data di nascita è obbligatoria';
        } else {
            $birth_date = new DateTime($data['data_nascita']);
            $today = new DateTime();
            $age = $today->diff($birth_date)->y;
            
            if ($age < 13) {
                $errors[] = 'Età minima: 13 anni';
            } elseif ($age > 100) {
                $errors[] = 'Data di nascita non valida';
            }
        }

        if (empty($data['sesso'])) {
            $errors[] = 'Il sesso è obbligatorio';
        } elseif (!in_array($data['sesso'], ['M','F'])) {
            $errors[] = 'Valore sesso non valido';
        }

        return $errors;
    }
}
?>
