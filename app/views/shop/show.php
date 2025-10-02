<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['nome']) ?> - Shop SportEvents</title>
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

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            margin: 30px 0;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(-5px);
        }

        .product-detail-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
            align-items: start;
        }

        .product-image-section {
            position: sticky;
            top: 20px;
        }

        .product-main-image {
            width: 100%;
            height: 500px;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-main-image .placeholder {
            font-size: 6rem;
            color: white;
        }

        .product-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.9);
            color: #333;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .availability-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
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

        .product-info-section {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .product-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .product-event {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            color: #333;
        }

        .product-event i {
            color: #667eea;
        }

        .product-price {
            font-size: 3rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
        }

        .product-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 30px;
        }

        .product-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .meta-item {
            background: rgba(102, 126, 234, 0.05);
            padding: 15px;
            border-radius: 15px;
            text-align: center;
        }

        .meta-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .meta-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .purchase-section {
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 25px;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 2px solid #667eea;
            border-radius: 12px;
            overflow: hidden;
        }

        .quantity-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover:not(:disabled) {
            background: #5a6fd8;
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-input {
            border: none;
            padding: 12px 20px;
            text-align: center;
            font-weight: 600;
            background: white;
            width: 80px;
            outline: none;
        }

        .purchase-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 15px 25px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            flex: 1;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-secondary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .organizer-section {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .organizer-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .organizer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .organizer-info h4 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 5px;
        }

        .organizer-info p {
            color: #666;
            font-size: 0.95rem;
        }

        .related-products {
            margin-top: 60px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-align: center;
            margin-bottom: 40px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        @media (max-width: 768px) {
            .product-detail-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .product-title {
                font-size: 2rem;
            }
            
            .product-price {
                font-size: 2.5rem;
            }
            
            .product-meta {
                grid-template-columns: 1fr;
            }
            
            .purchase-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        <!-- Back Button -->
        <a href="/shop" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Torna al Shop
        </a>

        <!-- Product Detail Layout -->
        <div class="product-detail-layout">
            <!-- Product Image Section -->
            <div class="product-image-section">
                <div class="product-main-image">
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
                        ?>
                        <div class="placeholder">
                            <?= $categoryEmojis[$product['categoria']] ?? '🏆' ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="product-badge">
                        <?= ucfirst(str_replace('_', ' ', $product['categoria'])) ?>
                    </div>
                    
                    <?php 
                    $disponibili = $product['quantita_disponibile'] - $product['quantita_venduta'];
                    $availability = $disponibili > 10 ? 'available' : ($disponibili > 0 ? 'limited' : 'soldout');
                    $availabilityText = $disponibili > 10 ? 'Disponibile' : ($disponibili > 0 ? "Solo {$disponibili}" : 'Esaurito');
                    ?>
                    <div class="availability-badge <?= $availability ?>">
                        <?= $availabilityText ?>
                    </div>
                </div>
            </div>

            <!-- Product Info Section -->
            <div class="product-info-section">
                <h1 class="product-title"><?= htmlspecialchars($product['nome']) ?></h1>
                
                <div class="product-event">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <strong><?= htmlspecialchars($product['evento_nome']) ?></strong>
                        <br>
                        <small><?= date('d/m/Y', strtotime($product['evento_data'])) ?></small>
                    </div>
                </div>
                
                <div class="product-price">
                    €<?= number_format($product['prezzo'], 2) ?>
                </div>
                
                <div class="product-description">
                    <?= nl2br(htmlspecialchars($product['descrizione'])) ?>
                </div>
                
                <!-- Product Meta -->
                <div class="product-meta">
                    <div class="meta-item">
                        <div class="meta-label">Categoria</div>
                        <div class="meta-value"><?= ucfirst(str_replace('_', ' ', $product['categoria'])) ?></div>
                    </div>
                    
                    <div class="meta-item">
                        <div class="meta-label">Disponibilità</div>
                        <div class="meta-value"><?= $disponibili ?> pezzi</div>
                    </div>
                </div>

                <!-- Purchase Section -->
                <?php if ($disponibili > 0): ?>
                    <div class="purchase-section">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="/shop/<?= $product['id'] ?>/purchase" id="purchaseForm">
                                <div class="quantity-selector">
                                    <label style="font-weight: 600; color: #333;">Quantità:</label>
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" 
                                               name="quantita" 
                                               class="quantity-input" 
                                               value="1" 
                                               min="1" 
                                               max="<?= $disponibili ?>" 
                                               id="quantityInput">
                                        <button type="button" class="quantity-btn" onclick="changeQuantity(1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <span style="color: #666; font-size: 0.9rem;">
                                        Massimo <?= $disponibili ?> disponibili
                                    </span>
                                </div>
                                
                                <div class="purchase-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart"></i>
                                        Acquista Ora
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="addToWishlist()">
                                        <i class="fas fa-heart"></i>
                                        Aggiungi ai Preferiti
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="purchase-actions">
                                <a href="/login?redirect=/shop/<?= $product['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Accedi per Acquistare
                                </a>
                                <a href="/register" class="btn btn-secondary">
                                    <i class="fas fa-user-plus"></i>
                                    Registrati
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="purchase-section">
                        <div class="purchase-actions">
                            <button class="btn btn-primary" disabled>
                                <i class="fas fa-times"></i>
                                Prodotto Esaurito
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Organizer Section -->
        <div class="organizer-section">
            <div class="organizer-header">
                <div class="organizer-avatar">
                    <?= strtoupper(substr($product['organizer_nome'], 0, 1)) ?>
                </div>
                <div class="organizer-info">
                    <h4>Organizzato da <?= htmlspecialchars($product['organizer_nome']) ?></h4>
                    <p>Organizzatore verificato • <?= $product['organizer_eventi_count'] ?> eventi creati</p>
                </div>
            </div>
            <div style="display: flex; gap: 15px;">
                <a href="/events/organizer/<?= $product['organizer_id'] ?>" class="btn btn-primary" style="flex: none;">
                    <i class="fas fa-calendar"></i> Vedi Altri Eventi
                </a>
                <a href="/messages/compose?to=<?= $product['organizer_id'] ?>" class="btn btn-secondary" style="flex: none;">
                    <i class="fas fa-envelope"></i> Contatta
                </a>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="related-products">
                <h2 class="section-title">Prodotti Correlati</h2>
                <div class="products-grid">
                    <?php foreach ($relatedProducts as $related): 
                        $relatedDisponibili = $related['quantita_disponibile'] - $related['quantita_venduta'];
                    ?>
                        <div class="product-card">
                            <div class="product-image" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                <?php if ($related['immagine']): ?>
                                    <img src="/uploads/products/<?= htmlspecialchars($related['immagine']) ?>" 
                                         alt="<?= htmlspecialchars($related['nome']) ?>"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <?= $categoryEmojis[$related['categoria']] ?? '🏆' ?>
                                <?php endif; ?>
                            </div>
                            
                            <div style="padding: 20px;">
                                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: #333;">
                                    <?= htmlspecialchars($related['nome']) ?>
                                </h3>
                                <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;">
                                    €<?= number_format($related['prezzo'], 2) ?>
                                </p>
                                <a href="/shop/<?= $related['id'] ?>" class="btn btn-primary" style="width: 100%;">
                                    Vedi Dettagli
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function changeQuantity(delta) {
            const input = document.getElementById('quantityInput');
            const currentValue = parseInt(input.value);
            const newValue = currentValue + delta;
            const max = parseInt(input.getAttribute('max'));
            
            if (newValue >= 1 && newValue <= max) {
                input.value = newValue;
            }
        }

        function addToWishlist() {
            // Implementa logica wishlist
            alert('Prodotto aggiunto ai preferiti! (Funzione da implementare)');
        }

        // Gestione form di acquisto
        document.getElementById('purchaseForm')?.addEventListener('submit', function(e) {
            const quantity = parseInt(document.getElementById('quantityInput').value);
            if (!quantity || quantity < 1) {
                e.preventDefault();
                alert('Seleziona una quantità valida');
                return;
            }
            
            if (!confirm(`Confermi l'acquisto di ${quantity} ${quantity === 1 ? 'pezzo' : 'pezzi'} di "${<?= json_encode($product['nome']) ?>}" per €${(<?= $product['prezzo'] ?> * quantity).toFixed(2)}?`)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>