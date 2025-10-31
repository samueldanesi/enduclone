<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Include controllers necessari
require_once __DIR__ . '/../app/controllers/CommunityController.php';

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

// Controlla token "ricordami" se presente
$authController = new AuthController();
$authController->checkRememberToken();

// Router semplice
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/sportevents', '', $path); // Rimuovi il prefisso base
$path = rtrim($path, '/'); // Rimuovi slash finale

// Serve static files
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $path)) {
    return false; // Let PHP built-in server handle static files
}

// Serve uploads files
if (strpos($path, '/uploads/') === 0) {
    $file_path = __DIR__ . '/..' . $path;
    if (file_exists($file_path) && is_file($file_path)) {
        $mime_type = mime_content_type($file_path);
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        http_response_code(404);
        echo "File not found";
        exit;
    }
}

// Estrai parametri dalla URL
$segments = explode('/', trim($path, '/'));
$action = $segments[0] ?? '';
$param1 = $segments[1] ?? null;
$param2 = $segments[2] ?? null;

switch ($action) {
    case '':
    case 'home':
        require_once __DIR__ . '/../app/views/home.php';
        break;
    
    case 'events':
        $controller = new EventController();
        if ($param1 && is_numeric($param1)) {
            if ($param2 === 'register') {
                // Gestione iscrizione
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
    
    case 'register':
        $authController->register();
        break;
    
    case 'login':
        $authController->login();
        break;
    
    case 'logout':
        $authController->logout();
        break;
    
    case 'forgot-password':
        $authController->forgotPassword();
        break;
    
    case 'about':
        require_once __DIR__ . '/../app/views/about.php';
        break;
    
    case 'contact':
        require_once __DIR__ . '/../app/views/contact.php';
        break;
    
    case 'profile':
        AuthController::requireAuth();
        $profileController = new ProfileController();
        $profileController->show();
        break;
    
    // Sezione calendario rimossa - redirect a eventi
    case 'calendar':
        header('Location: /events');
        exit;
        break;
    
    case 'download':
        AuthController::requireAuth();
        $gpxController = new GpxController();
        if ($param1 === 'gpx' && $param2 && is_numeric($param2)) {
            $gpxController->download($param2);
        } else {
            http_response_code(404);
            require_once __DIR__ . '/../app/views/404.php';
        }
        break;
    
    case 'organizer':
        AuthController::requireRole('organizer');
        $eventController = new EventController();
        
        switch ($param1) {
            case 'create':
                $eventController->create();
                break;
                
            case 'events':
                if ($param2 === 'create') {
                    $eventController->create();
                } elseif ($param2 && is_numeric($param2)) {
                    $eventController->edit($param2);
                } else {
                    $eventController->organizerDashboard();
                }
                break;
            
            case 'statistics':
                if ($param2 && is_numeric($param2)) {
                    $eventController->statistics($param2);
                }
                break;
            
            case 'download':
                if ($param2 && is_numeric($param2)) {
                    $eventController->downloadRegistrations($param2);
                }
                break;
            
            case 'gpx':
                $gpxController = new GpxController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if ($param2 === 'upload') {
                        $gpxController->upload();
                    } elseif ($param2 === 'delete' && isset($_POST['gpx_id'])) {
                        $gpxController->delete($_POST['gpx_id']);
                    }
                }
                break;
            
            default:
                $eventController->organizerDashboard();
                break;
        }
        break;
    
    case 'messages':
        require_once __DIR__ . '/../app/controllers/MessageController.php';
        $messageController = new MessageController();
        
        switch ($param1) {
            case '':
            case null:
                // Pagina principale messaggi
                $messageController->index();
                break;
                
            case 'compose':
                if ($param2 && is_numeric($param2)) {
                    $messageController->compose($param2);
                } else {
                    header('Location: /404');
                }
                break;
                
            case 'send':
                $messageController->send();
                break;
                
            case 'view':
                if ($param2 && is_numeric($param2)) {
                    $messageController->view($param2);
                } else {
                    header('Location: /404');
                }
                break;
                
            case 'preview':
                if ($param2 && is_numeric($param2)) {
                    $messageController->previewEmail($param2);
                } else {
                    header('Location: /404');
                }
                break;
                
            case 'track':
                if ($param2 && is_numeric($param2)) {
                    $messageController->markOpened($param2);
                } else {
                    header('Location: /404');
                }
                break;
                
            case 'api':
                if ($param2 === 'events' && isset($_GET['event_id'])) {
                    $messageController->getEventMessages($_GET['event_id']);
                } elseif ($param2 === 'stats') {
                    $messageController->getStats();
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint non trovato']);
                }
                break;
                
            default:
                header('Location: /404');
                break;
        }
        break;
    
    case 'notifications':
        require_once __DIR__ . '/../app/controllers/NotificationController.php';
        $notificationController = new NotificationController();
        
        switch ($param1) {
            case '':
            case null:
                $notificationController->index();
                break;
                
            case 'read':
                if ($param2 && is_numeric($param2)) {
                    $notificationController->markRead($param2);
                } else {
                    http_response_code(404);
                }
                break;
                
            case 'delete':
                if ($param2 && is_numeric($param2)) {
                    $notificationController->delete($param2);
                } else {
                    http_response_code(404);
                }
                break;
                
            case 'mark-all-read':
                $notificationController->markAllRead();
                break;
                
            case 'api':
                if ($param2 === 'unread-count') {
                    $notificationController->getUnreadCount();
                } elseif ($param2 === 'recent') {
                    $notificationController->getRecent();
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint non trovato']);
                }
                break;
                
            default:
                if (is_numeric($param1)) {
                    $notificationController->show($param1);
                } else {
                    header('Location: /404');
                }
                break;
        }
        break;
    
    case 'teams':
        $controller = new TeamController();
        if ($param1 === 'create') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->store();
            } else {
                $controller->create();
            }
        } elseif ($param1 === 'search') {
            $controller->search();
        } elseif ($param1 && is_numeric($param1)) {
            if ($param2 === 'join') {
                $controller->requestJoin($param1);
            } elseif ($param2 === 'leave') {
                $controller->leave($param1);
            } elseif ($param2 === 'chat') {
                $controller->chat($param1);
            } elseif ($param2 === 'manage-requests') {
                $controller->manageRequests($param1);
            } elseif ($param2 === 'collective-registration') {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->processCollectiveRegistration($param1);
                } else {
                    $controller->collectiveRegistration($param1);
                }
            } elseif ($param2 === 'registration-details') {
                $controller->registrationDetails($param1);
            } else {
                $controller->view($param1);
            }
        } else {
            $controller->index();
        }
        break;

    case 'community':
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
        require_once __DIR__ . '/../app/controllers/ShopController.php';
        $shopController = new ShopController($db);
        
        switch ($param1) {
            case '':
            case null:
                $shopController->index();
                break;
                
            case 'create':
                AuthController::requireRole('organizer');
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $shopController->store();
                } else {
                    $shopController->create();
                }
                break;
                
            case 'orders':
                AuthController::requireAuth();
                if ($param2 && is_numeric($param2)) {
                    if (isset($segments[3])) {
                        switch ($segments[3]) {
                            case 'cancel':
                                $shopController->cancelOrder($param2);
                                break;
                            case 'receipt':
                                $shopController->downloadReceipt($param2);
                                break;
                            default:
                                $shopController->showOrder($param2);
                        }
                    } else {
                        $shopController->showOrder($param2);
                    }
                } else {
                    $shopController->orders();
                }
                break;
                
            case 'organizer':
                AuthController::requireRole('organizer');
                switch ($param2) {
                    case 'products':
                        $shopController->organizerProducts();
                        break;
                    case 'orders':
                        $shopController->organizerOrders();
                        break;
                    case 'statistics':
                        $shopController->organizerStatistics();
                        break;
                    default:
                        $shopController->organizerDashboard();
                }
                break;
                
            default:
                if (is_numeric($param1)) {
                    if ($param2 === 'purchase') {
                        AuthController::requireAuth();
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $shopController->processPurchase($param1);
                        } else {
                            $shopController->purchase($param1);
                        }
                    } elseif ($param2 === 'edit') {
                        AuthController::requireRole('organizer');
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $shopController->update($param1);
                        } else {
                            $shopController->edit($param1);
                        }
                    } elseif ($param2 === 'delete') {
                        AuthController::requireRole('organizer');
                        $shopController->delete($param1);
                    } elseif ($param2 === 'review') {
                        AuthController::requireAuth();
                        $shopController->review($param1);
                    } else {
                        $shopController->show($param1);
                    }
                } else {
                    http_response_code(404);
                    require_once __DIR__ . '/../app/views/404.php';
                }
                break;
        }
        break;
    
    case 'api':
        header('Content-Type: application/json');
        switch ($param1) {
            case 'events':
                $eventController = new EventController();
                $eventController->apiSearch();
                break;
            
            case 'teams':
                require_once __DIR__ . '/../app/controllers/TeamController.php';
                $teamController = new TeamController();
                $teamController->searchApi();
                break;
            
            case 'notifications':
                require_once __DIR__ . '/../app/controllers/NotificationApiController.php';
                $notificationController = new NotificationApiController($db);
                
                switch ($param2) {
                    case 'count':
                        $notificationController->count();
                        break;
                    case 'list':
                        $notificationController->list();
                        break;
                    case 'mark-read':
                        $notificationController->markAsRead();
                        break;
                    case 'mark-all-read':
                        $notificationController->markAllAsRead();
                        break;
                    default:
                        http_response_code(404);
                        echo json_encode(['error' => 'Notification API endpoint not found']);
                }
                break;
            
            default:
                http_response_code(404);
                echo json_encode(['error' => 'API endpoint not found']);
                break;
        }
        break;
    
    default:
        http_response_code(404);
        require_once __DIR__ . '/../app/views/404.php';
        break;
}
?>
