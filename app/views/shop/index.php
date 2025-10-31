<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Sportivo - SportEvents</title>
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

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero-section {
            padding: 40px 0;
            text-align: center;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 30px;
        }

        .search-filters {
            background: rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
        }

        .filter-row {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 15px 20px;
            border: none;
            border-radius: 15px;
            background: rgba(255,255,255,0.9);
            font-size: 16px;
            outline: none;
        }

        .filter-select {
            padding: 15px 20px;
            border: none;
            border-radius: 15px;
            background: rgba(255,255,255,0.9);
            font-size: 16px;
            outline: none;
            min-width: 180px;
        }

        .category-chips {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .category-chip {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .category-chip:hover, .category-chip.active {
            background: rgba(255,255,255,0.9);
            color: #333;
            transform: translateY(-2px);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .product-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image .placeholder-icon {
            font-size: 3rem;
            color: white;
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255,255,255,0.9);
            color: #333;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .availability-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .available {
            background: rgba(34, 197, 94, 0.9);
            color: white;
        }

        .limited {
            background: rgba(251, 146, 60, 0.9);
            color: white;
        }

        .soldout {
            background: rgba(239, 68, 68, 0.9);
            color: white;
        }

        .product-content {
            padding: 25px;
        }

        .product-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
        }

        .product-event {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .product-description {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            flex: 1;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-secondary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .stats-info {
            background: rgba(255,255,255,0.1);
            padding: 15px 25px;
            border-radius: 15px;
            color: white;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .search-input,
            .filter-select {
                min-width: 100%;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .category-chips {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php 
    require_once __DIR__ . '/../components/navbar.php';
    renderNavbar('shop'); 
    ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title gradient-text">
                <i class="fas fa-shopping-bag" style="margin-right: 15px;"></i>
                Shop Sportivo
            </h1>
            <p class="hero-subtitle">
                Scopri prodotti esclusivi degli eventi: maglie, accessori, pacchi gara e molto altro
            </p>
        </div>
    </section>

    <!-- Search & Filters -->
    <section style="padding: 20px 0;">
        <div class="container">
            <div class="search-filters">
                <form method="GET" action="/shop" id="filterForm">
                    <div class="filter-row">
                        <input type="text" 
                               name="search" 
                               class="search-input" 
                               placeholder="Cerca prodotti, eventi, organizzatori..."
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        
                        <select name="event_id" class="filter-select">
                            <option value="">Tutti gli Eventi</option>
                            <!-- Qui verranno popolati gli eventi dinamicamente -->
                        </select>
                        
                        <label style="display: flex; align-items: center; gap: 10px; color: #333;">
                            <input type="checkbox" name="disponibili" value="1" 
                                   <?= isset($_GET['disponibili']) ? 'checked' : '' ?>>
                            Solo disponibili
                        </label>
                        
                        <button type="submit" class="btn btn-primary" style="flex: none;">
                            <i class="fas fa-search"></i> Cerca
                        </button>
                    </div>

                    <!-- Category Chips -->
                    <div class="category-chips">
                        <a href="/shop" class="category-chip <?= !isset($_GET['categoria']) ? 'active' : '' ?>">
                            <i class="fas fa-th-large"></i> Tutti
                        </a>
                        <a href="/shop?categoria=abbigliamento" class="category-chip <?= ($_GET['categoria'] ?? '') === 'abbigliamento' ? 'active' : '' ?>">
                            👕 Abbigliamento
                        </a>
                        <a href="/shop?categoria=accessori" class="category-chip <?= ($_GET['categoria'] ?? '') === 'accessori' ? 'active' : '' ?>">
                            🎒 Accessori
                        </a>
                        <a href="/shop?categoria=pacco_gara" class="category-chip <?= ($_GET['categoria'] ?? '') === 'pacco_gara' ? 'active' : '' ?>">
                            📦 Pacchi Gara
                        </a>
                        <a href="/shop?categoria=foto" class="category-chip <?= ($_GET['categoria'] ?? '') === 'foto' ? 'active' : '' ?>">
                            📸 Foto
                        </a>
                        <a href="/shop?categoria=donazione" class="category-chip <?= ($_GET['categoria'] ?? '') === 'donazione' ? 'active' : '' ?>">
                            ❤️ Donazioni
                        </a>
                        <a href="/shop?categoria=altro" class="category-chip <?= ($_GET['categoria'] ?? '') === 'altro' ? 'active' : '' ?>">
                            🎁 Altro
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section style="padding: 40px 0;">
        <div class="container">
            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stats-info">
                    <strong><?= count($products ?? []) ?></strong> prodotti trovati
                </div>
                
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'organizer'): ?>
                <div style="display: flex; gap: 15px;">
                    <a href="/shop/create" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Nuovo Prodotto
                    </a>
                    <a href="/shop/organizer" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Gestisci Prodotti
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Products Grid -->
            <?php if (!empty($products)): ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): 
                        $disponibili = $product['quantita_disponibile'] - $product['quantita_venduta'];
                        $availability = $disponibili > 10 ? 'available' : ($disponibili > 0 ? 'limited' : 'soldout');
                        $availabilityText = $disponibili > 10 ? 'Disponibile' : ($disponibili > 0 ? "Solo {$disponibili}" : 'Esaurito');
                        
                        // Categorie emoji
                        $categoryEmojis = [
                            'abbigliamento' => '👕',
                            'accessori' => '🎒',
                            'pacco_gara' => '📦',
                            'foto' => '📸',
                            'donazione' => '❤️',
                            'altro' => '🎁'
                        ];
                    ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if ($product['immagine']): ?>
                                    <img src="/uploads/products/<?= htmlspecialchars($product['immagine']) ?>" 
                                         alt="<?= htmlspecialchars($product['nome']) ?>">
                                <?php else: ?>
                                    <div class="placeholder-icon">
                                        <?= $categoryEmojis[$product['categoria']] ?? '🏆' ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-badge">
                                    <?= ucfirst(str_replace('_', ' ', $product['categoria'])) ?>
                                </div>
                                
                                <div class="availability-badge <?= $availability ?>">
                                    <?= $availabilityText ?>
                                </div>
                            </div>
                            
                            <div class="product-content">
                                <h3 class="product-title"><?= htmlspecialchars($product['nome']) ?></h3>
                                
                                <div class="product-event">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= htmlspecialchars($product['evento_nome']) ?>
                                </div>
                                
                                <p class="product-description">
                                    <?= htmlspecialchars($product['descrizione']) ?>
                                </p>
                                
                                <div class="product-price">
                                    €<?= number_format($product['prezzo'], 2) ?>
                                </div>
                                
                                <div class="product-actions">
                                    <a href="/shop/<?= $product['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> Dettagli
                                    </a>
                                    
                                    <?php if ($disponibili > 0): ?>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <a href="/shop/<?= $product['id'] ?>/purchase" class="btn btn-secondary">
                                                <i class="fas fa-shopping-cart"></i> Acquista
                                            </a>
                                        <?php else: ?>
                                            <a href="/login" class="btn btn-secondary">
                                                <i class="fas fa-sign-in-alt"></i> Accedi
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-times"></i> Esaurito
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>Nessun prodotto trovato</h3>
                    <p>Prova a modificare i filtri di ricerca o esplora altre categorie.</p>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'organizer'): ?>
                        <div style="margin-top: 20px;">
                            <a href="/shop/create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crea il Primo Prodotto
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Auto-submit form quando cambia il filtro eventi
        document.addEventListener('DOMContentLoaded', function() {
            const eventSelect = document.querySelector('select[name="event_id"]');
            const availableCheckbox = document.querySelector('input[name="disponibili"]');
            
            if (eventSelect) {
                eventSelect.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
            
            if (availableCheckbox) {
                availableCheckbox.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });
    </script>
</body>
</html>