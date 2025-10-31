<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestisci Prodotti - SportEvents</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .page-subtitle {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            backdrop-filter: blur(10px);
            color: #333;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .dashboard-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .products-section {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
        }

        .filters-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .filter-select,
        .search-input {
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .filter-select:focus,
        .search-input:focus {
            border-color: #667eea;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table th,
        .products-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .products-table th {
            background: rgba(102, 126, 234, 0.05);
            font-weight: 600;
            color: #333;
        }

        .product-image-cell {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 1.2rem;
        }

        .product-image-cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .status-low-stock {
            background: rgba(251, 146, 60, 0.2);
            color: #f59e0b;
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 1px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #22c55e 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(34, 197, 94, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            margin: 50px auto;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-actions {
                flex-direction: column;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .filters-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .products-table {
                font-size: 12px;
            }
            
            .products-table th,
            .products-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-store" style="margin-right: 15px; color: #667eea;"></i>
                Gestione Prodotti Shop
            </h1>
            <p class="page-subtitle">
                Gestisci i tuoi prodotti, monitora le vendite e gli ordini
            </p>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-number"><?= $stats['total_products'] ?? 0 ?></div>
                <div class="stat-label">Prodotti Totali</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-number"><?= $stats['active_products'] ?? 0 ?></div>
                <div class="stat-label">Prodotti Attivi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?= $stats['total_orders'] ?? 0 ?></div>
                <div class="stat-label">Ordini Ricevuti</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number">€<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                <div class="stat-label">Ricavi Totali</div>
            </div>
        </div>

        <!-- Dashboard Actions -->
        <div class="dashboard-actions">
            <a href="/shop/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuovo Prodotto
            </a>
            <a href="/shop/organizer/orders" class="btn btn-secondary">
                <i class="fas fa-shopping-cart"></i> Gestisci Ordini
            </a>
            <a href="/shop/organizer/statistics" class="btn btn-secondary">
                <i class="fas fa-chart-bar"></i> Statistiche Dettagliate
            </a>
            <button onclick="exportProducts()" class="btn btn-success">
                <i class="fas fa-download"></i> Esporta Prodotti
            </button>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            <div class="section-header">
                <h2 class="section-title">I Miei Prodotti</h2>
                <div style="display: flex; gap: 10px;">
                    <button onclick="bulkActions()" class="btn btn-warning">
                        <i class="fas fa-tasks"></i> Azioni Multiple
                    </button>
                    <button onclick="refreshProducts()" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Aggiorna
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-row">
                <select id="eventFilter" class="filter-select">
                    <option value="">Tutti gli Eventi</option>
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <option value="<?= $event['id'] ?>" <?= ($_GET['event_id'] ?? '') == $event['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($event['titolo']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <select id="categoryFilter" class="filter-select">
                    <option value="">Tutte le Categorie</option>
                    <option value="abbigliamento" <?= ($_GET['categoria'] ?? '') === 'abbigliamento' ? 'selected' : '' ?>>Abbigliamento</option>
                    <option value="accessori" <?= ($_GET['categoria'] ?? '') === 'accessori' ? 'selected' : '' ?>>Accessori</option>
                    <option value="pacco_gara" <?= ($_GET['categoria'] ?? '') === 'pacco_gara' ? 'selected' : '' ?>>Pacchi Gara</option>
                    <option value="foto" <?= ($_GET['categoria'] ?? '') === 'foto' ? 'selected' : '' ?>>Foto</option>
                    <option value="donazione" <?= ($_GET['categoria'] ?? '') === 'donazione' ? 'selected' : '' ?>>Donazioni</option>
                    <option value="altro" <?= ($_GET['categoria'] ?? '') === 'altro' ? 'selected' : '' ?>>Altro</option>
                </select>

                <select id="statusFilter" class="filter-select">
                    <option value="">Tutti gli Stati</option>
                    <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Attivi</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Disattivi</option>
                    <option value="low_stock" <?= ($_GET['status'] ?? '') === 'low_stock' ? 'selected' : '' ?>>Scorte Basse</option>
                </select>

                <input type="text" 
                       id="searchInput" 
                       class="search-input" 
                       placeholder="Cerca prodotti..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                <button onclick="applyFilters()" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtra
                </button>
            </div>

            <!-- Products Table -->
            <?php if (!empty($products)): ?>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                            <th>Immagine</th>
                            <th>Prodotto</th>
                            <th>Evento</th>
                            <th>Categoria</th>
                            <th>Prezzo</th>
                            <th>Stock</th>
                            <th>Venduti</th>
                            <th>Stato</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): 
                            $disponibili = $product['quantita_disponibile'] - $product['quantita_venduta'];
                            $statusClass = $product['attivo'] ? 
                                ($disponibili <= 5 ? 'status-low-stock' : 'status-active') : 
                                'status-inactive';
                            $statusText = $product['attivo'] ? 
                                ($disponibili <= 5 ? 'Scorte Basse' : 'Attivo') : 
                                'Disattivo';
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox" 
                                           class="product-checkbox" 
                                           value="<?= $product['id'] ?>">
                                </td>
                                <td>
                                    <div class="product-image-cell">
                                        <?php if ($product['immagine']): ?>
                                            <img src="/uploads/products/<?= htmlspecialchars($product['immagine']) ?>" 
                                                 alt="<?= htmlspecialchars($product['nome']) ?>">
                                        <?php else: ?>
                                            <?php 
                                            $categoryEmojis = [
                                                'abbigliamento' => '👕',
                                                'accessori' => '🎒',
                                                'pacco_gara' => '📦',
                                                'foto' => '📸',
                                                'donazione' => '❤️',
                                                'altro' => '🎁'
                                            ];
                                            echo $categoryEmojis[$product['categoria']] ?? '🏆';
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['nome']) ?></strong>
                                    <br>
                                    <small style="color: #666;">
                                        <?= htmlspecialchars(substr($product['descrizione'], 0, 50)) ?>...
                                    </small>
                                </td>
                                <td><?= htmlspecialchars($product['evento_titolo']) ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $product['categoria'])) ?></td>
                                <td><strong>€<?= number_format($product['prezzo'], 2) ?></strong></td>
                                <td><?= $disponibili ?></td>
                                <td><?= $product['quantita_venduta'] ?></td>
                                <td>
                                    <span class="product-status <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="product-actions">
                                        <a href="/shop/<?= $product['id'] ?>" 
                                           class="btn btn-secondary" 
                                           title="Visualizza">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/shop/<?= $product['id'] ?>/edit" 
                                           class="btn btn-primary" 
                                           title="Modifica">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="toggleProductStatus(<?= $product['id'] ?>, <?= $product['attivo'] ? 'false' : 'true' ?>)"
                                                class="btn <?= $product['attivo'] ? 'btn-warning' : 'btn-success' ?>"
                                                title="<?= $product['attivo'] ? 'Disattiva' : 'Attiva' ?>">
                                            <i class="fas fa-<?= $product['attivo'] ? 'pause' : 'play' ?>"></i>
                                        </button>
                                        <button onclick="deleteProduct(<?= $product['id'] ?>)"
                                                class="btn btn-danger"
                                                title="Elimina">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-store"></i>
                    <h3>Nessun prodotto trovato</h3>
                    <p>Non hai ancora creato prodotti o non ci sono prodotti che corrispondono ai filtri.</p>
                    <div style="margin-top: 20px;">
                        <a href="/shop/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crea il Primo Prodotto
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal per azioni multiple -->
    <div id="bulkModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Azioni Multiple</h3>
                <button class="close-btn" onclick="closeBulkModal()">&times;</button>
            </div>
            <div>
                <p>Seleziona un'azione da applicare ai prodotti selezionati:</p>
                <div style="margin: 20px 0;">
                    <button onclick="bulkActivate()" class="btn btn-success" style="width: 100%; margin-bottom: 10px;">
                        <i class="fas fa-play"></i> Attiva Selezionati
                    </button>
                    <button onclick="bulkDeactivate()" class="btn btn-warning" style="width: 100%; margin-bottom: 10px;">
                        <i class="fas fa-pause"></i> Disattiva Selezionati
                    </button>
                    <button onclick="bulkDelete()" class="btn btn-danger" style="width: 100%;">
                        <i class="fas fa-trash"></i> Elimina Selezionati
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funzioni di gestione filtri
        function applyFilters() {
            const params = new URLSearchParams();
            
            const event = document.getElementById('eventFilter').value;
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchInput').value;
            
            if (event) params.append('event_id', event);
            if (category) params.append('categoria', category);
            if (status) params.append('status', status);
            if (search) params.append('search', search);
            
            window.location.href = '/shop/organizer?' + params.toString();
        }

        function refreshProducts() {
            location.reload();
        }

        // Funzioni di selezione
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.product-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function getSelectedProducts() {
            return Array.from(document.querySelectorAll('.product-checkbox:checked'))
                        .map(cb => cb.value);
        }

        // Azioni sui prodotti
        function toggleProductStatus(productId, activate) {
            if (confirm(`Sei sicuro di voler ${activate === 'true' ? 'attivare' : 'disattivare'} questo prodotto?`)) {
                fetch(`/shop/${productId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ active: activate === 'true' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Errore di connessione');
                    console.error(error);
                });
            }
        }

        function deleteProduct(productId) {
            if (confirm('Sei sicuro di voler eliminare questo prodotto? Questa azione non può essere annullata.')) {
                fetch(`/shop/${productId}/delete`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Errore di connessione');
                    console.error(error);
                });
            }
        }

        // Azioni multiple
        function bulkActions() {
            const selected = getSelectedProducts();
            if (selected.length === 0) {
                alert('Seleziona almeno un prodotto');
                return;
            }
            document.getElementById('bulkModal').style.display = 'block';
        }

        function closeBulkModal() {
            document.getElementById('bulkModal').style.display = 'none';
        }

        function bulkActivate() {
            const selected = getSelectedProducts();
            if (confirm(`Attivare ${selected.length} prodotti selezionati?`)) {
                bulkAction('activate', selected);
            }
        }

        function bulkDeactivate() {
            const selected = getSelectedProducts();
            if (confirm(`Disattivare ${selected.length} prodotti selezionati?`)) {
                bulkAction('deactivate', selected);
            }
        }

        function bulkDelete() {
            const selected = getSelectedProducts();
            if (confirm(`ATTENZIONE: Eliminare definitivamente ${selected.length} prodotti selezionati?`)) {
                bulkAction('delete', selected);
            }
        }

        function bulkAction(action, productIds) {
            fetch('/shop/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: action,
                    product_ids: productIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeBulkModal();
                    location.reload();
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => {
                alert('Errore di connessione');
                console.error(error);
            });
        }

        function exportProducts() {
            window.open('/shop/organizer/export', '_blank');
        }

        // Chiudi modal cliccando fuori
        window.onclick = function(event) {
            const modal = document.getElementById('bulkModal');
            if (event.target == modal) {
                closeBulkModal();
            }
        }
    </script>
</body>
</html>