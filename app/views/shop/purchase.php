<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acquista <?= htmlspecialchars($product['nome']) ?> - SportEvents</title>
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
        }

        .container {
            max-width: 800px;
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

        .purchase-container {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 40px;
        }

        .purchase-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .purchase-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e5e7eb;
            z-index: 1;
        }

        .step {
            background: white;
            border: 3px solid #e5e7eb;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }

        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }

        .step-label {
            position: absolute;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.9rem;
            color: #666;
            white-space: nowrap;
        }

        .product-summary {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .product-info {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .product-image {
            width: 100px;
            height: 100px;
            border-radius: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        .product-details h3 {
            font-size: 1.4rem;
            color: #333;
            margin-bottom: 8px;
        }

        .product-details p {
            color: #666;
            margin-bottom: 5px;
        }

        .price-summary {
            text-align: right;
            flex-shrink: 0;
        }

        .unit-price {
            color: #666;
            font-size: 0.9rem;
        }

        .total-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-option {
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .payment-option:hover {
            border-color: #667eea;
        }

        .payment-option.selected {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .payment-name {
            font-weight: 600;
            color: #333;
        }

        .order-summary {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row.total {
            font-size: 1.2rem;
            font-weight: 700;
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 15px;
            margin-top: 15px;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 16px;
            display: inline-flex;
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
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .purchase-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .security-info {
            background: rgba(34, 197, 94, 0.05);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .security-info i {
            color: #22c55e;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 25px;
        }

        .terms-checkbox input[type="checkbox"] {
            margin-top: 3px;
        }

        .terms-checkbox label {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.5;
        }

        .terms-checkbox a {
            color: #667eea;
            text-decoration: none;
        }

        .terms-checkbox a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .purchase-container {
                padding: 25px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .product-info {
                flex-direction: column;
                text-align: center;
            }
            
            .purchase-steps {
                flex-wrap: wrap;
                gap: 20px;
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
        <a href="/shop/<?= $product['id'] ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Torna al Prodotto
        </a>

        <!-- Purchase Container -->
        <div class="purchase-container">
            <h1 class="page-title">
                <i class="fas fa-shopping-cart"></i>
                Completa l'Acquisto
            </h1>

            <!-- Progress Steps -->
            <div class="purchase-steps">
                <div class="step active">
                    1
                    <div class="step-label">Dati</div>
                </div>
                <div class="step">
                    2
                    <div class="step-label">Pagamento</div>
                </div>
                <div class="step">
                    3
                    <div class="step-label">Conferma</div>
                </div>
            </div>

            <form method="POST" action="/shop/<?= $product['id'] ?>/purchase" id="purchaseForm">
                <!-- Product Summary -->
                <div class="product-summary">
                    <div class="product-info">
                        <div class="product-image">
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
                                <?= $categoryEmojis[$product['categoria']] ?? '🏆' ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-details">
                            <h3><?= htmlspecialchars($product['nome']) ?></h3>
                            <p><strong>Evento:</strong> <?= htmlspecialchars($product['evento_nome']) ?></p>
                            <p><strong>Categoria:</strong> <?= ucfirst(str_replace('_', ' ', $product['categoria'])) ?></p>
                            <p><strong>Quantità:</strong> <?= (int)$_POST['quantita'] ?> <?= (int)$_POST['quantita'] === 1 ? 'pezzo' : 'pezzi' ?></p>
                        </div>
                        
                        <div class="price-summary">
                            <div class="unit-price">€<?= number_format($product['prezzo'], 2) ?> x <?= (int)$_POST['quantita'] ?></div>
                            <div class="total-price">€<?= number_format($product['prezzo'] * (int)$_POST['quantita'], 2) ?></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="quantita" value="<?= (int)$_POST['quantita'] ?>">

                <!-- Shipping Information -->
                <div class="form-section">
                    <h3>
                        <i class="fas fa-shipping-fast"></i>
                        Informazioni di Spedizione
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" class="form-input" 
                                   value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Cognome *</label>
                            <input type="text" name="cognome" class="form-input" 
                                   value="<?= htmlspecialchars($_SESSION['user_surname'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">Indirizzo *</label>
                            <input type="text" name="indirizzo" class="form-input" 
                                   placeholder="Via, numero civico" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Città *</label>
                            <input type="text" name="citta" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">CAP *</label>
                            <input type="text" name="cap" class="form-input" 
                                   pattern="[0-9]{5}" maxlength="5" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Provincia *</label>
                            <input type="text" name="provincia" class="form-input" 
                                   maxlength="2" placeholder="es. MI" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Telefono *</label>
                            <input type="tel" name="telefono" class="form-input" 
                                   value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">Note per la consegna</label>
                            <textarea name="note_consegna" class="form-input form-textarea" 
                                      placeholder="Eventuali istruzioni speciali per la consegna..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h3>
                        <i class="fas fa-credit-card"></i>
                        Metodo di Pagamento
                    </h3>
                    
                    <div class="payment-methods">
                        <label class="payment-option selected">
                            <input type="radio" name="metodo_pagamento" value="carta" checked>
                            <div class="payment-icon">💳</div>
                            <div class="payment-name">Carta di Credito</div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="paypal">
                            <div class="payment-icon">🅿️</div>
                            <div class="payment-name">PayPal</div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="bonifico">
                            <div class="payment-icon">🏦</div>
                            <div class="payment-name">Bonifico</div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="contrassegno">
                            <div class="payment-icon">📮</div>
                            <div class="payment-name">Contrassegno</div>
                        </label>
                    </div>

                    <!-- Security Info -->
                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Pagamenti Sicuri</strong><br>
                            I tuoi dati sono protetti con crittografia SSL 256-bit
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <h3 style="margin-bottom: 20px;">
                        <i class="fas fa-receipt"></i>
                        Riepilogo Ordine
                    </h3>
                    
                    <div class="summary-row">
                        <span>Subtotale (<?= (int)$_POST['quantita'] ?> <?= (int)$_POST['quantita'] === 1 ? 'pezzo' : 'pezzi' ?>):</span>
                        <span>€<?= number_format($product['prezzo'] * (int)$_POST['quantita'], 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Spedizione:</span>
                        <span id="shippingCost">€5.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>IVA (22%):</span>
                        <span id="taxCost">€<?= number_format(($product['prezzo'] * (int)$_POST['quantita'] + 5) * 0.22, 2) ?></span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Totale:</span>
                        <span id="totalCost">€<?= number_format(($product['prezzo'] * (int)$_POST['quantita'] + 5) * 1.22, 2) ?></span>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="accept_terms" required>
                    <label for="terms">
                        Accetto i <a href="/terms" target="_blank">Termini e Condizioni</a> 
                        e la <a href="/privacy" target="_blank">Privacy Policy</a>. 
                        Confermo di aver letto le informazioni sul diritto di recesso.
                    </label>
                </div>

                <!-- Purchase Actions -->
                <div class="purchase-actions">
                    <a href="/shop/<?= $product['id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Indietro
                    </a>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-lock"></i>
                        Conferma e Paga
                        €<?= number_format(($product['prezzo'] * (int)$_POST['quantita'] + 5) * 1.22, 2) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Gestione selezione metodo pagamento
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                // Rimuovi active da tutti
                document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
                // Aggiungi active al selezionato
                this.classList.add('selected');
                // Seleziona il radio button
                this.querySelector('input[type="radio"]').checked = true;
                
                // Aggiorna costo spedizione in base al metodo
                const method = this.querySelector('input[type="radio"]').value;
                updateShippingCost(method);
            });
        });

        function updateShippingCost(method) {
            let shippingCost = 5.00;
            
            switch(method) {
                case 'contrassegno':
                    shippingCost = 8.00;
                    break;
                case 'bonifico':
                    shippingCost = 0.00;
                    break;
            }
            
            const subtotal = <?= $product['prezzo'] * (int)$_POST['quantita'] ?>;
            const tax = (subtotal + shippingCost) * 0.22;
            const total = subtotal + shippingCost + tax;
            
            document.getElementById('shippingCost').textContent = '€' + shippingCost.toFixed(2);
            document.getElementById('taxCost').textContent = '€' + tax.toFixed(2);
            document.getElementById('totalCost').textContent = '€' + total.toFixed(2);
            
            // Aggiorna anche il pulsante
            document.querySelector('.btn-primary').innerHTML = `
                <i class="fas fa-lock"></i>
                Conferma e Paga €${total.toFixed(2)}
            `;
        }

        // Validazione form
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            const required = this.querySelectorAll('[required]');
            let valid = true;
            
            required.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    valid = false;
                } else {
                    field.style.borderColor = '#e5e7eb';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Compila tutti i campi obbligatori');
                return;
            }
            
            // Conferma finale
            const total = document.getElementById('totalCost').textContent;
            if (!confirm(`Confermi l'ordine per un totale di ${total}?`)) {
                e.preventDefault();
            }
        });

        // Validazione CAP
        document.querySelector('[name="cap"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 5);
        });

        // Validazione Provincia
        document.querySelector('[name="provincia"]').addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0, 2);
        });
    </script>
</body>
</html>