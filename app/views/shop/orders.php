<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Miei Ordini - SportEvents</title>
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
            max-width: 1200px;
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

        .orders-filters {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .filters-row {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            background: rgba(255,255,255,0.9);
            font-size: 14px;
            outline: none;
            min-width: 150px;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            background: rgba(255,255,255,0.9);
            font-size: 14px;
            outline: none;
        }

        .orders-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            color: white;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        }

        .order-header {
            background: rgba(102, 126, 234, 0.05);
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .order-number {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
        }

        .order-date {
            color: #666;
            font-size: 0.95rem;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(251, 146, 60, 0.2);
            color: #f59e0b;
        }

        .status-confirmed {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-shipped {
            background: rgba(168, 85, 247, 0.2);
            color: #a855f7;
        }

        .status-delivered {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .order-content {
            padding: 25px;
        }

        .order-items {
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            gap: 20px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-event {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .item-quantity {
            color: #666;
            font-size: 0.9rem;
        }

        .item-price {
            text-align: right;
            font-weight: 600;
            color: #333;
        }

        .order-summary {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .summary-row.total {
            font-size: 1.2rem;
            font-weight: 700;
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 12px;
            margin-top: 12px;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .btn-danger:hover {
            background: #ef4444;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .empty-state i {
            font-size: 4rem;
            color: rgba(255,255,255,0.5);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 30px;
        }

        .shipping-info {
            background: rgba(59, 130, 246, 0.05);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .shipping-info h4 {
            color: #3b82f6;
            font-size: 0.95rem;
            margin-bottom: 8px;
        }

        .shipping-address {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .filters-row {
                flex-direction: column;
            }
            
            .filter-select,
            .search-input {
                min-width: 100%;
            }
            
            .orders-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-actions {
                width: 100%;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
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
                <i class="fas fa-shopping-bag" style="margin-right: 15px; color: #667eea;"></i>
                I Miei Ordini
            </h1>
            <p class="page-subtitle">
                Tieni traccia di tutti i tuoi acquisti e ordini
            </p>
        </div>

        <!-- Filters -->
        <div class="orders-filters">
            <form method="GET" action="/shop/orders" class="filters-row">
                <select name="status" class="filter-select">
                    <option value="">Tutti gli Stati</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>In Attesa</option>
                    <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confermato</option>
                    <option value="shipped" <?= ($_GET['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Spedito</option>
                    <option value="delivered" <?= ($_GET['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Consegnato</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Annullato</option>
                </select>
                
                <select name="periodo" class="filter-select">
                    <option value="">Tutti i Periodi</option>
                    <option value="7" <?= ($_GET['periodo'] ?? '') === '7' ? 'selected' : '' ?>>Ultimi 7 giorni</option>
                    <option value="30" <?= ($_GET['periodo'] ?? '') === '30' ? 'selected' : '' ?>>Ultimo mese</option>
                    <option value="90" <?= ($_GET['periodo'] ?? '') === '90' ? 'selected' : '' ?>>Ultimi 3 mesi</option>
                    <option value="365" <?= ($_GET['periodo'] ?? '') === '365' ? 'selected' : '' ?>>Ultimo anno</option>
                </select>
                
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Cerca per prodotto, evento, numero ordine..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cerca
                </button>
            </form>
        </div>

        <!-- Statistics -->
        <div class="orders-stats">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?? 0 ?></div>
                <div class="stat-label">Ordini Totali</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['pending'] ?? 0 ?></div>
                <div class="stat-label">In Elaborazione</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['delivered'] ?? 0 ?></div>
                <div class="stat-label">Consegnati</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">€<?= number_format($stats['total_spent'] ?? 0, 2) ?></div>
                <div class="stat-label">Totale Speso</div>
            </div>
        </div>

        <!-- Orders List -->
        <?php if (!empty($orders)): ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): 
                    $statusClasses = [
                        'pending' => 'status-pending',
                        'confirmed' => 'status-confirmed', 
                        'shipped' => 'status-shipped',
                        'delivered' => 'status-delivered',
                        'cancelled' => 'status-cancelled'
                    ];
                    
                    $statusLabels = [
                        'pending' => 'In Attesa',
                        'confirmed' => 'Confermato',
                        'shipped' => 'Spedito', 
                        'delivered' => 'Consegnato',
                        'cancelled' => 'Annullato'
                    ];
                ?>
                    <div class="order-card">
                        <!-- Order Header -->
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-number">
                                    Ordine #<?= htmlspecialchars($order['numero_ordine']) ?>
                                </div>
                                <div class="order-date">
                                    <?= date('d/m/Y H:i', strtotime($order['data_ordine'])) ?>
                                </div>
                            </div>
                            
                            <div class="order-status <?= $statusClasses[$order['status']] ?>">
                                <?= $statusLabels[$order['status']] ?>
                            </div>
                        </div>

                        <!-- Order Content -->
                        <div class="order-content">
                            <!-- Order Items -->
                            <div class="order-items">
                                <?php 
                                $categoryEmojis = [
                                    'abbigliamento' => '👕',
                                    'accessori' => '🎒',
                                    'pacco_gara' => '📦',
                                    'foto' => '📸',
                                    'donazione' => '❤️',
                                    'altro' => '🎁'
                                ];
                                ?>
                                <div class="order-item">
                                    <div class="item-image">
                                        <?php if ($order['prodotto_immagine']): ?>
                                            <img src="/uploads/products/<?= htmlspecialchars($order['prodotto_immagine']) ?>" 
                                                 alt="<?= htmlspecialchars($order['prodotto_nome']) ?>">
                                        <?php else: ?>
                                            <?= $categoryEmojis[$order['prodotto_categoria']] ?? '🏆' ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="item-details">
                                        <div class="item-name"><?= htmlspecialchars($order['prodotto_nome']) ?></div>
                                        <div class="item-event">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= htmlspecialchars($order['evento_nome']) ?>
                                        </div>
                                        <div class="item-quantity">
                                            Quantità: <?= $order['quantita'] ?>
                                        </div>
                                    </div>
                                    
                                    <div class="item-price">
                                        €<?= number_format($order['prezzo_unitario'], 2) ?>
                                        <?php if ($order['quantita'] > 1): ?>
                                            <br><small>x <?= $order['quantita'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Info -->
                            <?php if ($order['indirizzo_spedizione']): ?>
                                <div class="shipping-info">
                                    <h4><i class="fas fa-truck"></i> Informazioni di Spedizione</h4>
                                    <div class="shipping-address">
                                        <?= nl2br(htmlspecialchars($order['indirizzo_spedizione'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Order Summary -->
                            <div class="order-summary">
                                <div class="summary-row">
                                    <span>Subtotale:</span>
                                    <span>€<?= number_format($order['prezzo_unitario'] * $order['quantita'], 2) ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Spedizione:</span>
                                    <span>€<?= number_format($order['costo_spedizione'] ?? 5.00, 2) ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>IVA:</span>
                                    <span>€<?= number_format($order['iva'] ?? 0, 2) ?></span>
                                </div>
                                <div class="summary-row total">
                                    <span>Totale:</span>
                                    <span>€<?= number_format($order['totale'], 2) ?></span>
                                </div>
                            </div>

                            <!-- Order Actions -->
                            <div class="order-actions">
                                <a href="/shop/orders/<?= $order['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Dettagli
                                </a>
                                
                                <?php if ($order['status'] === 'delivered'): ?>
                                    <a href="/shop/<?= $order['prodotto_id'] ?>/review" class="btn btn-secondary">
                                        <i class="fas fa-star"></i> Recensione
                                    </a>
                                    <a href="/shop/orders/<?= $order['id'] ?>/receipt" class="btn btn-secondary">
                                        <i class="fas fa-receipt"></i> Ricevuta
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (in_array($order['status'], ['pending', 'confirmed'])): ?>
                                    <button onclick="cancelOrder(<?= $order['id'] ?>)" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Annulla
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($order['status'] === 'delivered'): ?>
                                    <a href="/shop/<?= $order['prodotto_id'] ?>" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Riordina
                                    </a>
                                <?php endif; ?>
                                
                                <a href="/messages/compose?to=<?= $order['organizer_id'] ?>&subject=Ordine <?= $order['numero_ordine'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-envelope"></i> Contatta
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>Nessun ordine trovato</h3>
                <p>Non hai ancora effettuato ordini o non ci sono ordini che corrispondono ai filtri selezionati.</p>
                
                <div style="margin-top: 20px;">
                    <a href="/shop" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Vai al Shop
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-submit dei filtri
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.filter-select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });

        // Funzione per annullare ordine
        function cancelOrder(orderId) {
            if (confirm('Sei sicuro di voler annullare questo ordine?')) {
                fetch(`/shop/orders/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Ordine annullato con successo');
                        location.reload();
                    } else {
                        alert('Errore nell\'annullamento dell\'ordine: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Errore di connessione');
                    console.error(error);
                });
            }
        }

        // Animazioni al caricamento
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.order-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });
        });
    </script>
</body>
</html>