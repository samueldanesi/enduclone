<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscriviti a <?= htmlspecialchars($event_data['titolo']) ?> - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-primary-600">SportEvents</a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-600 hover:text-primary-600 transition-colors">Home</a>
                    <a href="/events" class="text-gray-600 hover:text-primary-600 transition-colors">Eventi</a>
                    <a href="/profile" class="text-gray-600 hover:text-primary-600 transition-colors">Profilo</a>
                    <a href="/logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">Esci</a>
                </div>
            </nav>
        </nav>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="/" class="text-primary-600 hover:text-primary-700">Home</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="/events" class="text-primary-600 hover:text-primary-700">Eventi</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="/events/<?= $event_data['event_id'] ?>" class="text-primary-600 hover:text-primary-700"><?= htmlspecialchars($event_data['titolo']) ?></a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Iscrizione</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Iscrizione -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Iscriviti all'Evento</h1>
                    <p class="text-gray-600 mb-8">Completa i dati per finalizzare la tua iscrizione</p>

                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <ul class="text-red-600 text-sm space-y-1">
                                <?php foreach ($_SESSION['errors'] as $error): ?>
                                    <li>• <?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <?php if (isset($already_registered) && $already_registered): ?>
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">Sei già registrato a questo evento!</span>
                            </div>
                            <p class="mt-1 text-sm">Puoi visualizzare i dettagli della tua iscrizione nella tua area personale.</p>
                            <div class="mt-3">
                                <a href="/profile?tab=registrations" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Vai alle tue iscrizioni →
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/events/<?= $event_data['event_id'] ?>/register" class="space-y-6" <?= isset($already_registered) && $already_registered ? 'style="display: none;"' : '' ?>>
                        <!-- Dati Partecipante -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dati Partecipante</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                                    <input type="text" value="<?= htmlspecialchars($user_data['nome']) ?>" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cognome</label>
                                    <input type="text" value="<?= htmlspecialchars($user_data['cognome']) ?>" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" value="<?= htmlspecialchars($user_data['email']) ?>" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefono</label>
                                    <input type="tel" value="<?= htmlspecialchars($user_data['telefono'] ?? '') ?>" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Categoria Prezzo -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Categoria e Prezzo</h3>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Iscrizione Standard</h4>
                                        <p class="text-sm text-gray-600">Include partecipazione, pacco gara e medaglia</p>
                                    </div>
                                    <div class="text-2xl font-bold text-primary-600" id="original-price">
                                        €<?= number_format($event_data['prezzo_base'], 2) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Codice Sconto -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Codice Sconto (opzionale)</label>
                                <div class="flex gap-3">
                                    <div class="flex-1">
                                        <input type="text" 
                                               name="discount_code" 
                                               id="discount-code"
                                               placeholder="Inserisci codice sconto"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent uppercase">
                                    </div>
                                    <button type="button" 
                                            id="apply-discount" 
                                            class="px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                                        Applica
                                    </button>
                                </div>
                                <div id="discount-message" class="mt-2 text-sm hidden"></div>
                            </div>

                            <!-- Riepilogo Prezzo -->
                            <div id="price-summary" class="mt-4 p-4 bg-white border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-center text-base">
                                    <span>Prezzo base:</span>
                                    <span>€<?= number_format($event_data['prezzo_base'], 2) ?></span>
                                </div>
                                <div id="discount-row" class="flex justify-between items-center text-base text-green-600 hidden">
                                    <span id="discount-label">Sconto:</span>
                                    <span id="discount-value">-€0.00</span>
                                </div>
                                <div class="border-t border-gray-200 mt-2 pt-2 flex justify-between items-center font-bold text-lg">
                                    <span>Totale:</span>
                                    <span id="final-price" class="text-primary-600">€<?= number_format($event_data['prezzo_base'], 2) ?></span>
                                </div>
                            </div>

                            <input type="hidden" name="prezzo_pagato" id="prezzo-pagato" value="<?= $event_data['prezzo_base'] ?>">
                            <input type="hidden" name="discount_id" id="discount-id" value="">
                            <input type="hidden" name="discount_amount" id="discount-amount" value="0">
                        </div>

                        <!-- Metodo Pagamento -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Metodo di Pagamento</h3>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="metodo_pagamento" value="card" class="mr-3" id="payment-card" checked>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center mr-3">
                                            💳
                                        </div>
                                        <div>
                                            <div class="font-medium">Carta di Credito/Debito</div>
                                            <div class="text-sm text-gray-600">Visa, Mastercard, American Express</div>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="metodo_pagamento" value="paypal" class="mr-3" id="payment-paypal">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center mr-3">
                                            🔵
                                        </div>
                                        <div>
                                            <div class="font-medium">PayPal</div>
                                            <div class="text-sm text-gray-600">Paga con il tuo account PayPal</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Form Carta di Credito -->
                            <div id="card-form" class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-4">Dati Carta di Credito</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Numero Carta</label>
                                        <input type="text" 
                                               name="card_number" 
                                               placeholder="1234 5678 9012 3456"
                                               maxlength="19"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Scadenza</label>
                                        <input type="text" 
                                               name="card_expiry" 
                                               placeholder="MM/AA"
                                               maxlength="5"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                        <input type="text" 
                                               name="card_cvv" 
                                               placeholder="123"
                                               maxlength="4"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome sulla Carta</label>
                                        <input type="text" 
                                               name="card_name" 
                                               placeholder="Mario Rossi"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                </div>
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center text-blue-800 text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        I tuoi dati di pagamento sono protetti con tecnologia SSL
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Note -->
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Note (opzionale)</label>
                            <textarea id="note" name="note" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Eventuali note o richieste speciali..."><?= htmlspecialchars($_SESSION['form_data']['note'] ?? '') ?></textarea>
                        </div>

                        <!-- Termini e Condizioni -->
                        <div class="border-t border-gray-200 pt-6">
                            <label class="flex items-start space-x-3">
                                <input type="checkbox" required class="mt-1 h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                <div class="text-sm text-gray-600">
                                    Accetto i <a href="/terms" class="text-primary-600 hover:text-primary-700 underline">Termini e Condizioni</a> 
                                    e la <a href="/privacy" class="text-primary-600 hover:text-primary-700 underline">Privacy Policy</a>. 
                                    Confermo di aver letto il regolamento dell'evento.
                                </div>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex gap-4">
                            <button type="submit" 
                                    class="flex-1 bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                                Completa Iscrizione - €<?= number_format($event_data['prezzo_base'], 2) ?>
                            </button>
                            <a href="/events/<?= $event_data['event_id'] ?>" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                Annulla
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Riepilogo Evento -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Riepilogo Evento</h3>
                    
                    <?php if ($event_data['immagine']): ?>
                        <img src="/uploads/<?= htmlspecialchars($event_data['immagine']) ?>" 
                             alt="<?= htmlspecialchars($event_data['titolo']) ?>"
                             class="w-full h-32 object-cover rounded-lg mb-4">
                    <?php else: ?>
                        <div class="w-full h-32 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center text-white text-3xl mb-4">
                            🏃‍♂️
                        </div>
                    <?php endif; ?>
                    
                    <h4 class="font-semibold text-gray-900 mb-3"><?= htmlspecialchars($event_data['titolo']) ?></h4>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center text-gray-600">
                            <span class="mr-2">📅</span>
                            <?= date('d/m/Y H:i', strtotime($event_data['data_evento'])) ?>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <span class="mr-2">📍</span>
                            <?= htmlspecialchars($event_data['luogo_partenza']) ?>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <span class="mr-2">🏃</span>
                            <?= ucfirst(htmlspecialchars($event_data['sport'])) ?>
                            <?php if ($event_data['distanza_km']): ?>
                                - <?= $event_data['distanza_km'] ?>km
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <span class="mr-2">👥</span>
                            <?= $availability['current_registrations'] ?>/<?= $availability['total_capacity'] ?> iscritti
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 mt-6 pt-6">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center">
                                <span>Prezzo base:</span>
                                <span>€<?= number_format($event_data['prezzo_base'], 2) ?></span>
                            </div>
                            <div id="sidebar-discount-row" class="flex justify-between items-center text-green-600 hidden">
                                <span id="sidebar-discount-label">Sconto:</span>
                                <span id="sidebar-discount-value">-€0.00</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 mt-2 pt-2 flex justify-between items-center text-lg font-semibold">
                            <span>Totale:</span>
                            <span id="sidebar-final-price" class="text-primary-600">€<?= number_format($event_data['prezzo_base'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    // Pulisci dati del form dalla sessione
    unset($_SESSION['form_data']); 
    ?>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cardRadio = document.getElementById('payment-card');
            const paypalRadio = document.getElementById('payment-paypal');
            const cardForm = document.getElementById('card-form');
            const cardNumberInput = document.querySelector('[name="card_number"]');
            const cardExpiryInput = document.querySelector('[name="card_expiry"]');
            const cardCvvInput = document.querySelector('[name="card_cvv"]');
            
            // Elementi codici sconto
            const discountCodeInput = document.getElementById('discount-code');
            const applyDiscountBtn = document.getElementById('apply-discount');
            const discountMessage = document.getElementById('discount-message');
            const discountRow = document.getElementById('discount-row');
            const discountLabel = document.getElementById('discount-label');
            const discountValue = document.getElementById('discount-value');
            const finalPrice = document.getElementById('final-price');
            const prezzoPagato = document.getElementById('prezzo-pagato');
            const discountId = document.getElementById('discount-id');
            const discountAmount = document.getElementById('discount-amount');
            
            const originalPrice = <?= $event_data['prezzo_base'] ?>;
            const eventId = <?= $event_data['event_id'] ?>;
            let currentDiscount = null;

            // Controlla se c'è un codice salvato dal popup della home
            const savedCode = localStorage.getItem('sportevents_discount_code');
            if (savedCode) {
                discountCodeInput.value = savedCode;
                // Rimuovi il codice salvato dopo averlo usato
                localStorage.removeItem('sportevents_discount_code');
                // Applica automaticamente il codice
                setTimeout(() => {
                    applyDiscountBtn.click();
                }, 500);
            }

            // Applica codice sconto
            applyDiscountBtn.addEventListener('click', function() {
                const code = discountCodeInput.value.trim().toUpperCase();
                if (!code) {
                    showDiscountMessage('Inserisci un codice sconto', 'error');
                    return;
                }

                applyDiscountBtn.textContent = 'Verifica...';
                applyDiscountBtn.disabled = true;

                // Simula chiamata API per validazione codice
                validateDiscountCode(code, eventId, originalPrice)
                    .then(result => {
                        if (result.valid) {
                            currentDiscount = result;
                            updatePriceDisplay(result);
                            showDiscountMessage(result.message, 'success');
                        } else {
                            resetDiscount();
                            showDiscountMessage(result.message, 'error');
                        }
                    })
                    .catch(error => {
                        resetDiscount();
                        showDiscountMessage('Errore nella validazione del codice', 'error');
                    })
                    .finally(() => {
                        applyDiscountBtn.textContent = 'Applica';
                        applyDiscountBtn.disabled = false;
                    });
            });

            // Reset sconto quando si cambia codice
            discountCodeInput.addEventListener('input', function() {
                if (currentDiscount) {
                    resetDiscount();
                }
            });

            // Simula validazione codice sconto
            async function validateDiscountCode(code, eventId, amount) {
                // Simulazione - in produzione sarebbe una chiamata AJAX
                return new Promise((resolve) => {
                    setTimeout(() => {
                        const codes = {
                            'SPORT5': { valid: true, type: 'percentage', value: 5, message: 'Sconto benvenuto 5% applicato!' },
                            'EARLY10': { valid: true, type: 'fixed', value: 10, message: 'Sconto Early Bird di €10 applicato!' },
                            'RUNNER15': { valid: true, type: 'percentage', value: 15, message: 'Sconto Runner 15% applicato!' }
                        };

                        if (codes[code]) {
                            const discount = codes[code];
                            let discountAmount = 0;
                            
                            if (discount.type === 'percentage') {
                                discountAmount = (amount * discount.value) / 100;
                            } else if (discount.type === 'fixed') {
                                discountAmount = Math.min(discount.value, amount);
                            }

                            resolve({
                                valid: true,
                                discount_id: 1,
                                code: code,
                                type: discount.type,
                                discount_amount: discountAmount,
                                final_amount: Math.max(0, amount - discountAmount),
                                message: discount.message
                            });
                        } else {
                            resolve({
                                valid: false,
                                message: 'Codice sconto non valido o scaduto'
                            });
                        }
                    }, 500);
                });
            }

            // Aggiorna visualizzazione prezzi
            function updatePriceDisplay(discount) {
                // Aggiorna sezione principale
                discountLabel.textContent = `Sconto (${discount.code}):`;
                discountValue.textContent = `-€${discount.discount_amount.toFixed(2)}`;
                finalPrice.textContent = `€${discount.final_amount.toFixed(2)}`;
                
                // Aggiorna sidebar
                const sidebarDiscountRow = document.getElementById('sidebar-discount-row');
                const sidebarDiscountLabel = document.getElementById('sidebar-discount-label');
                const sidebarDiscountValue = document.getElementById('sidebar-discount-value');
                const sidebarFinalPrice = document.getElementById('sidebar-final-price');
                
                sidebarDiscountLabel.textContent = `Sconto (${discount.code}):`;
                sidebarDiscountValue.textContent = `-€${discount.discount_amount.toFixed(2)}`;
                sidebarFinalPrice.textContent = `€${discount.final_amount.toFixed(2)}`;
                sidebarDiscountRow.classList.remove('hidden');
                
                // Aggiorna campi nascosti
                prezzoPagato.value = discount.final_amount.toFixed(2);
                discountId.value = discount.discount_id || '';
                discountAmount.value = discount.discount_amount.toFixed(2);
                
                discountRow.classList.remove('hidden');
            }

            // Reset sconto
            function resetDiscount() {
                // Reset sezione principale
                discountRow.classList.add('hidden');
                finalPrice.textContent = `€${originalPrice.toFixed(2)}`;
                
                // Reset sidebar
                const sidebarDiscountRow = document.getElementById('sidebar-discount-row');
                const sidebarFinalPrice = document.getElementById('sidebar-final-price');
                sidebarDiscountRow.classList.add('hidden');
                sidebarFinalPrice.textContent = `€${originalPrice.toFixed(2)}`;
                
                // Reset campi nascosti
                prezzoPagato.value = originalPrice.toFixed(2);
                discountId.value = '';
                discountAmount.value = '0';
                hideDiscountMessage();
                currentDiscount = null;
            }

            // Mostra messaggio sconto
            function showDiscountMessage(message, type) {
                discountMessage.textContent = message;
                discountMessage.className = `mt-2 text-sm ${type === 'success' ? 'text-green-600' : 'text-red-600'}`;
                discountMessage.classList.remove('hidden');
            }

            // Nascondi messaggio sconto
            function hideDiscountMessage() {
                discountMessage.classList.add('hidden');
            }

            // Mostra/nascondi form carta
            function toggleCardForm() {
                if (cardRadio.checked) {
                    cardForm.style.display = 'block';
                } else {
                    cardForm.style.display = 'none';
                }
            }

            // Event listeners per i radio button
            cardRadio.addEventListener('change', toggleCardForm);
            paypalRadio.addEventListener('change', toggleCardForm);

            // Formattazione numero carta (aggiunge spazi ogni 4 cifre)
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
                let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
                if (formattedValue.length > 19) {
                    formattedValue = formattedValue.substring(0, 19);
                }
                e.target.value = formattedValue;
            });

            // Formattazione scadenza (MM/AA)
            cardExpiryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });

            // Solo numeri per CVV
            cardCvvInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });

            // Formattazione codice sconto in maiuscolo
            discountCodeInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });

            // Inizializza lo stato del form
            toggleCardForm();
        });
    </script>
</body>
</html>
