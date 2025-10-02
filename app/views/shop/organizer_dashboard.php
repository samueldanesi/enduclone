<?php
// Verifica che l'utente sia loggato e sia un organizzatore
if (!isset($_SESSION['user_id']) || (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] !== 'organizzatore')) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '/../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Organizzatore Shop - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="glass-theme">
    <div class="page-wrapper">
        <?php renderNavbar('shop'); ?>
        
        <main class="main-content">
            <div class="container">
                <!-- Header Dashboard -->
                <div class="glass-card">
                    <div class="dashboard-header">
                        <div class="header-content">
                            <h1 class="section-title">
                                <span class="icon">📊</span>
                                Dashboard Shop
                            </h1>
                            <p class="section-subtitle">Gestisci i tuoi prodotti e ordini</p>
                        </div>
                        <a href="/shop/create" class="btn btn-primary">
                            <span class="icon">➕</span>
                            Nuovo Prodotto
                        </a>
                    </div>
                </div>

                <!-- Statistiche Rapide -->
                <?php if (isset($statistics)): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">🛍️</div>
                        <div class="stat-content">
                            <div class="stat-number"><?= $statistics['prodotti_totali'] ?? 0 ?></div>
                            <div class="stat-label">Prodotti Attivi</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div class="stat-content">
                            <div class="stat-number"><?= $statistics['ordini_totali'] ?? 0 ?></div>
                            <div class="stat-label">Ordini Totali</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">⏳</div>
                        <div class="stat-content">
                            <div class="stat-number"><?= $statistics['ordini_pending'] ?? 0 ?></div>
                            <div class="stat-label">In Attesa</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-content">
                            <div class="stat-number">€<?= number_format($statistics['fatturato_totale'] ?? 0, 2) ?></div>
                            <div class="stat-label">Fatturato</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Prodotti dell'Organizzatore -->
                <div class="glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <span class="icon">🏪</span>
                            I Tuoi Prodotti
                        </h2>
                        <div class="header-actions">
                            <input type="search" id="search-products" placeholder="Cerca prodotti..." class="search-input">
                        </div>
                    </div>

                    <?php if (isset($products) && !empty($products)): ?>
                    <div class="products-table-container">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Prodotto</th>
                                    <th>Evento</th>
                                    <th>Prezzo</th>
                                    <th>Disponibili</th>
                                    <th>Venduti</th>
                                    <th>Status</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr class="product-row">
                                    <td>
                                        <div class="product-info">
                                            <?php if (!empty($product['immagine'])): ?>
                                                <img src="/uploads/products/<?= htmlspecialchars($product['immagine']) ?>" 
                                                     alt="<?= htmlspecialchars($product['nome']) ?>" class="product-thumb">
                                            <?php else: ?>
                                                <div class="product-thumb-placeholder">📦</div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="product-name"><?= htmlspecialchars($product['nome']) ?></div>
                                                <div class="product-category"><?= htmlspecialchars($product['categoria']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="event-info">
                                            <div class="event-name"><?= htmlspecialchars($product['evento_nome'] ?? 'N/A') ?></div>
                                            <?php if (isset($product['data_evento'])): ?>
                                                <div class="event-date"><?= date('d/m/Y', strtotime($product['data_evento'])) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="price">€<?= number_format($product['prezzo'], 2) ?></td>
                                    <td class="stock">
                                        <span class="stock-count"><?= $product['disponibili'] ?? 0 ?></span>
                                    </td>
                                    <td class="sold">
                                        <span class="sold-count"><?= $product['quantita_venduta'] ?? 0 ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $product['attivo'] ? 'active' : 'inactive' ?>">
                                            <?= $product['attivo'] ? 'Attivo' : 'Inattivo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/shop/product/<?= $product['id'] ?>" class="btn btn-sm btn-secondary" title="Visualizza">
                                                👁️
                                            </a>
                                            <a href="/shop/edit/<?= $product['id'] ?>" class="btn btn-sm btn-primary" title="Modifica">
                                                ✏️
                                            </a>
                                            <button class="btn btn-sm btn-danger" title="Elimina" 
                                                    onclick="deleteProduct(<?= $product['id'] ?>)">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">🛍️</div>
                        <h3>Nessun prodotto ancora</h3>
                        <p>Inizia a vendere i tuoi prodotti creando il primo articolo.</p>
                        <a href="/shop/create" class="btn btn-primary">
                            <span class="icon">➕</span>
                            Crea il primo prodotto
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Ordini Recenti -->
                <div class="glass-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <span class="icon">📦</span>
                            Ordini Recenti
                        </h2>
                        <a href="/shop/orders" class="btn btn-secondary">
                            Vedi tutti
                        </a>
                    </div>

                    <?php if (isset($recent_orders) && !empty($recent_orders)): ?>
                    <div class="orders-list">
                        <?php foreach (array_slice($recent_orders, 0, 5) as $order): ?>
                        <div class="order-item">
                            <div class="order-info">
                                <div class="order-number">#<?= htmlspecialchars($order['numero_ordine']) ?></div>
                                <div class="order-details">
                                    <span class="product-name"><?= htmlspecialchars($order['prodotto_nome']) ?></span>
                                    <span class="customer-name">• <?= htmlspecialchars($order['cliente_nome'] . ' ' . $order['cliente_cognome']) ?></span>
                                </div>
                                <div class="order-date"><?= date('d/m/Y H:i', strtotime($order['data_ordine'])) ?></div>
                            </div>
                            <div class="order-meta">
                                <div class="order-total">€<?= number_format($order['totale'], 2) ?></div>
                                <span class="status-badge status-<?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📦</div>
                        <h3>Nessun ordine ancora</h3>
                        <p>Gli ordini dei tuoi prodotti appariranno qui.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Ricerca prodotti
        document.getElementById('search-products')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.product-row');
            
            rows.forEach(row => {
                const productName = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
                const eventName = row.querySelector('.event-name')?.textContent.toLowerCase() || '';
                const category = row.querySelector('.product-category')?.textContent.toLowerCase() || '';
                
                if (productName.includes(searchTerm) || eventName.includes(searchTerm) || category.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Elimina prodotto
        function deleteProduct(productId) {
            if (confirm('Sei sicuro di voler eliminare questo prodotto?')) {
                fetch(`/shop/delete/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore nell\'eliminazione del prodotto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Errore nell\'eliminazione del prodotto');
                });
            }
        }
    </script>

    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .header-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .header-content p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(15px);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.12);
        }

        .stat-icon {
            font-size: 2.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.3rem;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            position: relative;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .card-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .products-table-container {
            overflow-x: auto;
            padding: 1rem 2rem 2rem;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            overflow: hidden;
        }

        .products-table th,
        .products-table td {
            padding: 1.2rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .products-table th {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.9);
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-row {
            transition: all 0.3s ease;
        }

        .product-row:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-thumb,
        .product-thumb-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .product-thumb {
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .product-thumb-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
        }

        .product-name {
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }

        .product-category {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
            display: inline-block;
        }

        .event-info {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .event-name {
            color: white;
            font-weight: 500;
        }

        .event-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            display: inline-block;
            width: fit-content;
        }

        .price {
            color: #4ade80;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .stock-count,
        .sold-count {
            color: white;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            text-align: center;
        }

        .stock-count {
            border: 2px solid #4ade80;
            color: #4ade80;
        }

        .sold-count {
            border: 2px solid #60a5fa;
            color: #60a5fa;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
            border: 2px solid;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.15);
            border-color: #4ade80;
            color: #4ade80;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.3);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            border-color: #f87171;
            color: #f87171;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            border-color: #fbbf24;
            color: #fbbf24;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
        }

        .status-confirmed {
            background: rgba(59, 130, 246, 0.15);
            border-color: #60a5fa;
            color: #60a5fa;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }

        .status-delivered {
            background: rgba(34, 197, 94, 0.15);
            border-color: #4ade80;
            color: #4ade80;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid #f87171;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            padding: 1rem 2rem 2rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .order-item:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .order-number {
            color: #60a5fa;
            font-weight: 700;
            font-size: 1rem;
            background: rgba(96, 165, 250, 0.1);
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .order-details {
            color: white;
            margin: 0.4rem 0;
            font-weight: 500;
        }

        .order-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
        }

        .order-total {
            color: #4ade80;
            font-weight: 700;
            text-align: right;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .search-input {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            color: white;
            width: 280px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 0.8rem;
        }

        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .products-table-container {
                font-size: 0.8rem;
            }

            .order-item {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .search-input {
                width: 100%;
            }
        }
    </style>
</body>
</html>