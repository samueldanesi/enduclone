<?php
// Avvia sessione PRIMA di tutto
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log errori su file temporaneo (permessi sicuri)
ini_set('log_errors', '1');
ini_set('error_log', '/tmp/biglietteria-php-error.log');
error_log('[BOOT] index_minimal avviato - ' . ($_SERVER['REQUEST_URI'] ?? '/'));

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Test della connessione database
try {
    $database = new Database();
    $db = $database->getConnection();
    if (!$db) {
        throw new Exception("Connessione database fallita");
    }
} catch (Exception $e) {
    die("Errore database: " . $e->getMessage());
}

// Include controllers solo quando necessari
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = rtrim($path, '/');

// Estrai parametri dalla URL
try {
    $segments = explode('/', trim($path, '/'));
    $action = $segments[0] ?? '';
    $param1 = $segments[1] ?? null;
    $param2 = $segments[2] ?? null;

    switch ($action) {
    case '':
    case 'home':
        require_once __DIR__ . '/app/views/home.php';
        break;
    
    case 'login':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $authController = new AuthController();
        $authController->login();
        break;
    
    case 'register':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $authController = new AuthController();
        $authController->register();
        break;
    
    case 'logout':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $authController = new AuthController();
        $authController->logout();
        break;
    
    case 'events':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        require_once __DIR__ . '/app/controllers/EventController.php';
        require_once __DIR__ . '/app/controllers/RegistrationController.php';
        
        $controller = new EventController();
        if ($param1 && is_numeric($param1)) {
            if ($param2 === 'register') {
                $registrationController = new RegistrationController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $registrationController->store($param1);
                } else {
                    $registrationController->show($param1);
                }
            } else {
                $controller->show($param1);
            }
        } else {
            $controller->index();
        }
        break;
    
    case 'profile':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        require_once __DIR__ . '/app/controllers/ProfileController.php';
        AuthController::requireAuth();
        $profileController = new ProfileController();
        $profileController->show();
        break;
    
    case 'community':
        require_once __DIR__ . '/app/controllers/CommunityController.php';
        $controller = new CommunityController($db);
        if ($param1 === 'universal') {
            $controller->universal();
        } elseif ($param1 === 'events') {
            $controller->events();
        } elseif ($param1 === 'results') {
            $controller->results();
        } elseif ($param1 === 'create') {
            $controller->createPost();
        } elseif ($param1 === 'event' && $param2 && is_numeric($param2)) {
            $controller->eventCommunity($param2);
        } elseif ($param1 === 'toggle-like') {
            $controller->toggleLike();
        } elseif ($param1 === 'add-comment') {
            $controller->addComment();
        } else {
            $controller->index();
        }
        break;
    
    case 'shop':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        require_once __DIR__ . '/app/controllers/ShopController.php';
        $shopController = new ShopController($db);
        
        switch ($param1) {
            case '':
            case null:
                $shopController->index();
                break;
            default:
                if (is_numeric($param1)) {
                    $shopController->show($param1);
                } else {
                    http_response_code(404);
                    require_once __DIR__ . '/app/views/404.php';
                }
                break;
        }
        break;
    
    case 'teams':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        require_once __DIR__ . '/app/controllers/TeamController.php';
        $controller = new TeamController();
        if ($param1 && is_numeric($param1)) {
            $controller->view($param1);
        } else {
            $controller->index();
        }
        break;
    
    default:
        http_response_code(404);
        require_once __DIR__ . '/app/views/404.php';
        break;
    }
} catch (Throwable $e) {
    error_log('[Router ERROR] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
        http_response_code(500);
        echo 'Errore: ' . htmlspecialchars($e->getMessage());
    } else {
        http_response_code(500);
        echo '<h1>Errore interno</h1>';
    }
}
?>