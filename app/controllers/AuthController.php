<?php
/**
 * Controller per l'autenticazione degli utenti
 */
class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // Gestisce il processo di registrazione
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegistration();
        } else {
            include __DIR__ . '/../views/register.php';
        }
    }

    // Processa i dati di registrazione
    private function processRegistration() {
        $errors = $this->validateRegistrationData($_POST);
        
        if (empty($errors)) {
            // Verifica se l'email esiste già
            $this->user->email = $_POST['email'];
            if ($this->user->emailExists()) {
                $errors[] = 'Questa email è già registrata';
            }
        }

        if (empty($errors)) {
            // Imposta i dati dell'utente
            $this->user->nome = trim($_POST['nome']);
            $this->user->cognome = trim($_POST['cognome']);
            $this->user->email = trim($_POST['email']);
            $this->user->password = $_POST['password'];
            $this->user->data_nascita = $_POST['data_nascita'];
            $this->user->sesso = $_POST['sesso'];
            $this->user->cellulare = trim($_POST['cellulare']);
            $this->user->user_type = $_POST['user_type'] ?? 'participant';

            // Registra l'utente
            if ($this->user->register()) {
                // Upload documenti se presenti
                $this->handleDocumentUploads();

                // Imposta sessione
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['nome'] = $this->user->nome;
                $_SESSION['cognome'] = $this->user->cognome;
                $_SESSION['email'] = $this->user->email;
                $_SESSION['user_type'] = $this->user->user_type;

                $_SESSION['success'] = 'Registrazione completata con successo! Benvenuto/a su SportEvents.';
                
                // Redirect basato sul tipo di utente
                if ($this->user->user_type === 'organizer') {
                    header('Location: /organizer');
                } else {
                    header('Location: /profile');
                }
                exit;
            } else {
                $errors[] = 'Errore durante la registrazione. Riprova.';
            }
        }

        // Ricarica form con errori
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        include __DIR__ . '/../views/register.php';
    }

    // Gestisce il processo di login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            include __DIR__ . '/../views/login.php';
        }
    }

    // Processa i dati di login
    private function processLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Inserisci email e password';
            $_SESSION['form_data'] = $_POST;
            include __DIR__ . '/../views/login.php';
            return;
        }

        if ($this->user->login($email, $password)) {
            // Login riuscito
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['nome'] = $this->user->nome;
            $_SESSION['cognome'] = $this->user->cognome;
            $_SESSION['email'] = $this->user->email;
            $_SESSION['user_type'] = $this->user->user_type;

            // Gestisci "Ricordami"
            if (isset($_POST['remember'])) {
                $this->setRememberToken();
            }

            // Redirect basato sul tipo di utente e sulla pagina precedente
            $redirect_url = $_SESSION['redirect_after_login'] ?? $this->getDefaultRedirect();
            unset($_SESSION['redirect_after_login']);
            
            header('Location: ' . $redirect_url);
            exit;
        } else {
            $_SESSION['error'] = 'Email o password non corretti';
            $_SESSION['form_data'] = $_POST;
            include __DIR__ . '/../views/login.php';
        }
    }

    // Logout utente
    public function logout() {
        // Rimuovi token "ricordami" se presente
        if (isset($_COOKIE['remember_token'])) {
            $this->removeRememberToken();
        }

        // Distruggi sessione
        session_destroy();
        
        // Redirect alla homepage
        header('Location: /?logged_out=1');
        exit;
    }

    // Validazione dati registrazione
    private function validateRegistrationData($data) {
        $errors = [];

        // Nome e cognome
        if (empty(trim($data['nome'] ?? ''))) {
            $errors[] = 'Il nome è obbligatorio';
        }
        if (empty(trim($data['cognome'] ?? ''))) {
            $errors[] = 'Il cognome è obbligatorio';
        }

        // Email
        if (empty($data['email'])) {
            $errors[] = 'L\'email è obbligatoria';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Formato email non valido';
        }

        // Password
        if (empty($data['password'])) {
            $errors[] = 'La password è obbligatoria';
        } elseif (strlen($data['password']) < 8) {
            $errors[] = 'La password deve contenere almeno 8 caratteri';
        }

        // Conferma password
        if (empty($data['password_confirm'])) {
            $errors[] = 'Conferma la password';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Le password non corrispondono';
        }

        // Data di nascita
        if (empty($data['data_nascita'])) {
            $errors[] = 'La data di nascita è obbligatoria';
        } else {
            $birth_date = new DateTime($data['data_nascita']);
            $today = new DateTime();
            $age = $today->diff($birth_date)->y;
            
            if ($age < 13) {
                $errors[] = 'Devi avere almeno 13 anni per registrarti';
            } elseif ($age > 120) {
                $errors[] = 'Data di nascita non valida';
            }
        }

        // Sesso
        if (empty($data['sesso'])) {
            $errors[] = 'Seleziona il sesso';
        } elseif (!in_array($data['sesso'], ['M', 'F', 'altro'])) {
            $errors[] = 'Valore sesso non valido';
        }

        // Cellulare
        if (empty(trim($data['cellulare'] ?? ''))) {
            $errors[] = 'Il numero di cellulare è obbligatorio';
        } elseif (!preg_match('/^[+]?[\d\s\-\(\)]{8,}$/', $data['cellulare'])) {
            $errors[] = 'Formato numero di telefono non valido';
        }

        // Tipo utente
        if (!empty($data['user_type']) && !in_array($data['user_type'], ['participant', 'organizer'])) {
            $errors[] = 'Tipo di utente non valido';
        }

        // Privacy
        if (empty($data['privacy'])) {
            $errors[] = 'Devi accettare i termini di servizio e la privacy policy';
        }

        return $errors;
    }

    // Gestisce l'upload dei documenti durante la registrazione
    private function handleDocumentUploads() {
        // Certificato medico
        if (isset($_FILES['certificato_medico']) && $_FILES['certificato_medico']['error'] === 0) {
            $this->user->uploadCertificato($_FILES['certificato_medico'], 'agonistico', null);
        }

        // Tessera affiliazione
        if (isset($_FILES['tessera_affiliazione']) && $_FILES['tessera_affiliazione']['error'] === 0) {
            $this->user->uploadTessera($_FILES['tessera_affiliazione']);
        }
    }

    // Imposta token "ricordami"
    private function setRememberToken() {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 giorni

        // Salva nel database
        $query = "INSERT INTO user_sessions (id, user_id, ip_address, user_agent, expires_at) 
                 VALUES (:token, :user_id, :ip, :user_agent, FROM_UNIXTIME(:expires))";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':user_id', $this->user->id);
        $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->bindParam(':expires', $expires);
        $stmt->execute();

        // Imposta cookie
        setcookie('remember_token', $token, $expires, '/', '', false, true);
    }

    // Rimuovi token "ricordami"
    private function removeRememberToken() {
        $token = $_COOKIE['remember_token'];
        
        // Rimuovi dal database
        $query = "DELETE FROM user_sessions WHERE id = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        // Rimuovi cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }

    // Controlla token "ricordami" ad ogni richiesta
    public function checkRememberToken() {
        if (isset($_COOKIE['remember_token']) && !isset($_SESSION['user_id'])) {
            $token = $_COOKIE['remember_token'];
            
            $query = "SELECT u.*, s.expires_at 
                     FROM user_sessions s 
                     JOIN users u ON s.user_id = u.id 
                     WHERE s.id = :token AND s.expires_at > NOW() AND u.status = 'active'";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Ripristina sessione
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['nome'] = $user_data['nome'];
                $_SESSION['cognome'] = $user_data['cognome'];
                $_SESSION['email'] = $user_data['email'];
                $_SESSION['user_type'] = $user_data['user_type'];

                // Aggiorna last_activity
                $update_query = "UPDATE user_sessions SET last_activity = NOW() WHERE id = :token";
                $update_stmt = $this->db->prepare($update_query);
                $update_stmt->bindParam(':token', $token);
                $update_stmt->execute();
            } else {
                // Token non valido, rimuovi cookie
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }
    }

    // Determina URL di redirect dopo login
    private function getDefaultRedirect() {
        switch ($_SESSION['user_type']) {
            case 'organizer':
                return '/organizer';
            case 'admin':
                return '/admin';
            default:
                return '/profile';
        }
    }

    // Password dimenticata (placeholder)
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                $_SESSION['error'] = 'Inserisci la tua email';
            } else {
                // Qui implementeresti l'invio dell'email di reset
                $_SESSION['success'] = 'Se l\'email esiste nel nostro sistema, riceverai le istruzioni per il reset della password.';
            }
        }
        
        include __DIR__ . '/../views/forgot-password.php';
    }

    // Verifica che l'utente sia autenticato
    public static function requireAuth($redirect = '/login') {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirect);
            exit;
        }
    }

    // Verifica che l'utente abbia un ruolo specifico
    public static function requireRole($role, $redirect = '/') {
        self::requireAuth();
        
        if ($_SESSION['user_type'] !== $role) {
            $_SESSION['error'] = 'Non hai i permessi per accedere a questa pagina';
            header('Location: ' . $redirect);
            exit;
        }
    }
}
?>
