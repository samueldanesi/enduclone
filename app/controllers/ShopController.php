<?php
/**
 * Controller per i prodotti e servizi aggiuntivi degli eventi
 */
class ShopController {
    private $conn;
    private $eventProduct;
    private $productOrder;

    public function __construct($db) {
        $this->conn = $db;
        require_once __DIR__ . '/../models/EventProduct.php';
        require_once __DIR__ . '/../models/ProductOrder.php';
        
        $this->eventProduct = new EventProduct($db);
        $this->productOrder = new ProductOrder($db);
    }

    /**
     * Homepage shop - Lista tutti i prodotti
     */
    public function index() {
        $filters = [];
        
        // Filtri dalla query string
        if (isset($_GET['categoria'])) $filters['categoria'] = $_GET['categoria'];
        if (isset($_GET['event_id'])) $filters['event_id'] = $_GET['event_id'];
        if (isset($_GET['search'])) $filters['search'] = $_GET['search'];
        if (isset($_GET['disponibili'])) $filters['disponibili'] = true;

        $stmt = $this->eventProduct->readAll($filters);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Statistiche shop
        $totalProducts = count($products);
        $categories = ['abbigliamento', 'accessori', 'pacco_gara', 'foto', 'donazione', 'altro'];

        include '../app/views/shop/index.php';
    }

    /**
     * Dettaglio prodotto
     */
    public function show($id) {
        $this->eventProduct->id = $id;
        $product = $this->eventProduct->readOne();
        
        if (!$product) {
            http_response_code(404);
            include '../app/views/404.php';
            return;
        }

        // Prodotti correlati dello stesso evento
        $relatedStmt = $this->eventProduct->readAll(['event_id' => $product['event_id']]);
        $relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Rimuovi il prodotto corrente dai correlati
        $relatedProducts = array_filter($relatedProducts, function($p) use ($id) {
            return $p['id'] != $id;
        });

        include '../app/views/shop/product.php';
    }

    /**
     * Aggiungi al carrello / Acquista direttamente
     */
    public function purchase($product_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = '/shop/' . $product_id . '/purchase';
            header('Location: /login');
            exit;
        }

        $this->eventProduct->id = $product_id;
        $product = $this->eventProduct->readOne();
        
        if (!$product) {
            http_response_code(404);
            include '../app/views/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processPurchase($product);
        } else {
            include '../app/views/shop/purchase.php';
        }
    }

    /**
     * Processa l'acquisto
     */
    private function processPurchase($product) {
        $errors = [];
        
        $quantita = (int)($_POST['quantita'] ?? 1);
        $taglia = $_POST['taglia'] ?? null;
        $colore = $_POST['colore'] ?? null;
        $note_ordine = $_POST['note_ordine'] ?? '';
        $indirizzo_spedizione = $_POST['indirizzo_spedizione'] ?? '';

        // Validazioni
        if ($quantita <= 0) {
            $errors[] = 'La quantità deve essere maggiore di 0';
        }

        $disponibili = $product['quantita_disponibile'] - $product['quantita_venduta'];
        if ($quantita > $disponibili) {
            $errors[] = 'Quantità non disponibile. Disponibili: ' . $disponibili;
        }

        if (empty($indirizzo_spedizione) && $product['categoria'] !== 'donazione') {
            $errors[] = 'Indirizzo di spedizione obbligatorio';
        }

        if (empty($errors)) {
            // Crea ordine
            $this->productOrder->user_id = $_SESSION['user_id'];
            $this->productOrder->product_id = $product['id'];
            $this->productOrder->event_id = $product['event_id'];
            $this->productOrder->quantita = $quantita;
            $this->productOrder->taglia = $taglia;
            $this->productOrder->colore = $colore;
            $this->productOrder->prezzo_unitario = $product['prezzo'];
            $this->productOrder->totale = $quantita * $product['prezzo'];
            $this->productOrder->status = 'pending';
            $this->productOrder->note_ordine = $note_ordine;
            $this->productOrder->indirizzo_spedizione = $indirizzo_spedizione;

            if ($this->productOrder->create()) {
                // Aggiorna quantità venduta
                $this->eventProduct->updateQuantitaVenduta($quantita);

                $_SESSION['success'] = 'Ordine creato con successo! Ti contatteremo presto per i dettagli.';
                header('Location: /shop/orders');
                exit;
            } else {
                $errors[] = 'Errore durante la creazione dell\'ordine';
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        include '../app/views/shop/purchase.php';
    }

    /**
     * I miei ordini
     */
    public function orders() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $orders = $this->productOrder->getUserOrders($_SESSION['user_id']);
        $stats = $this->productOrder->getOrderStats($_SESSION['user_id']);

        include '../app/views/shop/orders.php';
    }

    /**
     * Dettaglio ordine
     */
    public function orderDetail($order_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->productOrder->id = $order_id;
        $order = $this->productOrder->readOne();
        
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            http_response_code(404);
            include '../app/views/404.php';
            return;
        }

        include '../app/views/shop/order_detail.php';
    }

    // ==========================================
    // SEZIONE ORGANIZZATORI
    // ==========================================

    /**
     * Dashboard prodotti organizzatore
     */
    public function organizerDashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        $stmt = $this->eventProduct->getByOrganizer($_SESSION['user_id']);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $orders = $this->productOrder->getOrganizerOrders($_SESSION['user_id']);
        $orderStats = $this->productOrder->getOrderStats(null, $_SESSION['user_id']);

        include '../app/views/shop/organizer_dashboard.php';
    }

    /**
     * Alias per createProduct - usato dal router
     */
    public function create() {
        $this->createProduct();
    }

    /**
     * Crea nuovo prodotto (solo organizzatori)
     */
    public function createProduct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->storeProduct();
        } else {
            // Ottieni eventi dell'organizzatore
            require_once '../app/models/Event.php';
            $event = new Event($this->conn);
            $events_stmt = $event->getByOrganizer($_SESSION['user_id']);
            $events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            include '../app/views/shop/create_product.php';
        }
    }

    /**
     * Salva nuovo prodotto
     */
    private function storeProduct() {
        $errors = $this->validateProductData($_POST);
        
        if (empty($errors)) {
            $this->eventProduct->event_id = $_POST['event_id'];
            $this->eventProduct->organizer_id = $_SESSION['user_id'];
            $this->eventProduct->nome = $_POST['nome'];
            $this->eventProduct->descrizione = $_POST['descrizione'];
            $this->eventProduct->categoria = $_POST['categoria'];
            $this->eventProduct->prezzo = $_POST['prezzo'];
            $this->eventProduct->quantita_disponibile = $_POST['quantita_disponibile'];
            $this->eventProduct->quantita_venduta = 0;
            $this->eventProduct->taglia_disponibili = json_encode($_POST['taglie'] ?? []);
            $this->eventProduct->colori_disponibili = json_encode($_POST['colori'] ?? []);
            $this->eventProduct->is_attivo = $_POST['is_attivo'] ?? 1;

            if ($this->eventProduct->create()) {
                // Upload immagine se presente
                if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === 0) {
                    $this->eventProduct->uploadImage($_FILES['immagine']);
                }

                $_SESSION['success'] = 'Prodotto creato con successo!';
                header('Location: /shop/organizer');
                exit;
            } else {
                $errors[] = 'Errore durante la creazione del prodotto';
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        $this->createProduct();
    }

    /**
     * Modifica prodotto
     */
    public function editProduct($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        $this->eventProduct->id = $id;
        $product = $this->eventProduct->readOne();
        
        if (!$product || $product['organizer_id'] != $_SESSION['user_id']) {
            http_response_code(404);
            include '../app/views/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProduct($product);
        } else {
            include '../app/views/shop/edit_product.php';
        }
    }

    /**
     * Aggiorna prodotto
     */
    private function updateProduct($product) {
        $errors = $this->validateProductData($_POST);
        
        if (empty($errors)) {
            $this->eventProduct->nome = $_POST['nome'];
            $this->eventProduct->descrizione = $_POST['descrizione'];
            $this->eventProduct->categoria = $_POST['categoria'];
            $this->eventProduct->prezzo = $_POST['prezzo'];
            $this->eventProduct->quantita_disponibile = $_POST['quantita_disponibile'];
            $this->eventProduct->taglia_disponibili = json_encode($_POST['taglie'] ?? []);
            $this->eventProduct->colori_disponibili = json_encode($_POST['colori'] ?? []);
            $this->eventProduct->is_attivo = $_POST['is_attivo'] ?? 1;

            if ($this->eventProduct->update()) {
                // Upload nuova immagine se presente
                if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === 0) {
                    $this->eventProduct->uploadImage($_FILES['immagine']);
                }

                $_SESSION['success'] = 'Prodotto aggiornato con successo!';
                header('Location: /shop/organizer');
                exit;
            } else {
                $errors[] = 'Errore durante l\'aggiornamento del prodotto';
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        include '../app/views/shop/edit_product.php';
    }

    /**
     * Gestione ordini organizzatore
     */
    public function manageOrders() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        $orders = $this->productOrder->getOrganizerOrders($_SESSION['user_id']);
        include '../app/views/shop/manage_orders.php';
    }

    /**
     * Aggiorna status ordine
     */
    public function updateOrderStatus($order_id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'organizer') {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? '';
            
            $this->productOrder->id = $order_id;
            if ($this->productOrder->updateStatus($status)) {
                $_SESSION['success'] = 'Status ordine aggiornato!';
            } else {
                $_SESSION['error'] = 'Errore aggiornamento status';
            }
        }

        header('Location: /shop/organizer/orders');
        exit;
    }

    // ==========================================
    // METODI DI SUPPORTO
    // ==========================================

    /**
     * Validazione dati prodotto
     */
    private function validateProductData($data) {
        $errors = [];

        if (empty($data['nome'])) {
            $errors[] = 'Il nome del prodotto è obbligatorio';
        }

        if (empty($data['descrizione'])) {
            $errors[] = 'La descrizione è obbligatoria';
        }

        if (empty($data['categoria'])) {
            $errors[] = 'La categoria è obbligatoria';
        }

        if (!isset($data['prezzo']) || !is_numeric($data['prezzo']) || $data['prezzo'] < 0) {
            $errors[] = 'Il prezzo deve essere un numero valido';
        }

        if (!isset($data['quantita_disponibile']) || !is_numeric($data['quantita_disponibile']) || $data['quantita_disponibile'] < 0) {
            $errors[] = 'La quantità deve essere un numero valido';
        }

        if (empty($data['event_id']) || !is_numeric($data['event_id'])) {
            $errors[] = 'Seleziona un evento valido';
        }

        return $errors;
    }
}
?>