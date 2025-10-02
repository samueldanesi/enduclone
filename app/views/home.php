<!DOCTYPE html>
<html lang="it" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportEvents - Piattaforma Eventi Sportivi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#2563eb',
                        'primary-dark': '#1d4ed8',
                        'secondary': '#f59e0b',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-inter bg-gray-50">
    <!-- Includi navbar unificata -->
    <?php 
    require_once __DIR__ . '/components/navbar.php';
    renderNavbar('home'); 
    ?>

    <!-- Hero Section -->
        <!-- Hero Section -->
    <section class="py-20 overflow-hidden relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="glass-card p-12 mb-12">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 gradient-text">
                    Sport<span class="gradient-text">Events</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-700">
                    La piattaforma leader per iscrizioni e gestione di eventi sportivi in Italia
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/events" class="px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full font-semibold hover:from-blue-600 hover:to-purple-700 transition transform hover:scale-105 shadow-lg">
                        🏃 Esplora Eventi
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="/register" class="px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full font-semibold hover:from-purple-600 hover:to-pink-600 transition transform hover:scale-105 shadow-lg">
                            ✨ Registrati Gratis
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtri Eventi -->
    <section class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-card p-8">
                <h2 class="text-2xl font-bold mb-6 text-center gradient-text">Trova il Tuo Evento Perfetto</h2>
                <form method="GET" action="/events" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <select name="sport" class="w-full px-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm">
                        <option value="">🏃 Tutti gli Sport</option>
                        <option value="running">🏃‍♂️ Running</option>
                        <option value="cycling">🚴‍♂️ Ciclismo</option>
                        <option value="triathlon">🏊‍♂️ Triathlon</option>
                        <option value="swimming">🏊‍♀️ Nuoto</option>
                        <option value="trail">🥾 Trail Running</option>
                        <option value="mtb">🚵‍♂️ Mountain Bike</option>
                    </select>
                </div>
                
                <div>
                    <input type="text" name="luogo" placeholder="📍 Città o Regione" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <input type="date" name="data_da" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <button type="submit" class="w-full px-4 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition">
                        🔍 Cerca Eventi
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Eventi in Evidenza -->
    <section class="py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="glass-card p-8 mb-8">
                    <h2 class="text-4xl font-bold mb-4 gradient-text">Eventi in Evidenza</h2>
                    <p class="text-xl text-gray-700">
                        Scopri gli eventi sportivi più interessanti e iscriviti subito
                    </p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                // Carica eventi recenti con gestione errori
                $featuredEvents = [];
                try {
                    $database = new Database();
                    $conn = $database->getConnection();
                    if ($conn) {
                        $event = new Event($conn);
                        $stmt = $event->readAll();
                        $featuredEvents = array_slice($stmt->fetchAll(PDO::FETCH_ASSOC), 0, 6);
                    }
                } catch (Exception $e) {
                    // In caso di errore del database, nessun evento
                    $featuredEvents = [];

                }
                
                foreach ($featuredEvents as $event): ?>
                    <div class="glass-card overflow-hidden hover:transform hover:-translate-y-1">
                        <?php if ($event['immagine']): ?>
                            <img src="/uploads/<?= htmlspecialchars($event['immagine']) ?>" 
                                 alt="<?= htmlspecialchars($event['titolo']) ?>"
                                 class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold">
                                <?php
                                $sportEmojis = [
                                    'running' => '🏃‍♂️',
                                    'cycling' => '🚴‍♂️',
                                    'triathlon' => '🏊‍♂️',
                                    'swimming' => '🏊‍♀️',
                                    'trail' => '🥾',
                                    'mtb' => '🚵‍♂️'
                                ];
                                echo $sportEmojis[$event['sport']] ?? '🏃‍♂️';
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-900">
                                <a href="/events/<?= $event['event_id'] ?>" class="hover:text-primary transition">
                                    <?= htmlspecialchars($event['titolo']) ?>
                                </a>
                            </h3>
                            
                            <div class="space-y-2 mb-4 text-gray-600">
                                <div class="flex items-center">
                                    <span class="mr-2">📅</span>
                                    <?= date('d M Y', strtotime($event['data_evento'])) ?>
                                </div>
                                <div class="flex items-center">
                                    <span class="mr-2">📍</span>
                                    <?= $event['luogo_partenza'] ? htmlspecialchars($event['luogo_partenza']) : 'Luogo da definire' ?>
                                </div>
                                <div class="flex items-center">
                                    <span class="mr-2">🏃</span>
                                    <?= $event['sport'] ? ucfirst(htmlspecialchars($event['sport'])) : 'Evento Sportivo' ?>
                                    <?php if ($event['distanza_km']): ?>
                                        - <?= $event['distanza_km'] ?>km
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                <?= htmlspecialchars($event['descrizione']) ?>
                            </p>
                            
                            <div class="flex justify-between items-center pt-4 border-t border-gray-100 mb-4">
                                <div class="text-2xl font-bold text-primary">
                                    €<?= number_format($event['prezzo_base'], 0) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= $event['registrations_count'] ?>/<?= $event['max_partecipanti'] ?> iscritti
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="/events/<?= $event['event_id'] ?>" 
                                   class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-center font-medium hover:bg-gray-200 transition">
                                    Dettagli
                                </a>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'participant'): ?>
                                    <a href="/events/<?= $event['event_id'] ?>?action=register" 
                                       class="flex-1 px-4 py-2 bg-primary text-white rounded-lg text-center font-medium hover:bg-primary-dark transition">
                                        Iscriviti
                                    </a>
                                <?php elseif (!isset($_SESSION['user_id'])): ?>
                                    <a href="/login" 
                                       class="flex-1 px-4 py-2 bg-primary text-white rounded-lg text-center font-medium hover:bg-primary-dark transition">
                                        Iscriviti
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="/events" class="px-8 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition transform hover:scale-105">
                    Vedi Tutti gli Eventi
                </a>
            </div>
        </div>
    </section>

    <!-- Caratteristiche -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center mb-4 text-gray-900">Perché Scegliere SportEvents</h2>
            <p class="text-xl text-gray-600 text-center mb-12 max-w-3xl mx-auto">
                La piattaforma più avanzata per atleti e organizzatori di eventi sportivi
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🎯</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">Facile da Usare</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Iscrizione semplice e veloce con pochi clic. 
                        Gestisci le tue partecipazioni da un'unica dashboard intuitiva.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🔒</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">Pagamenti Sicuri</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Transazioni protette con PayPal e altri sistemi di pagamento sicuri. 
                        I tuoi dati sono sempre al sicuro.
                    </p>
                </div>
                
                <div class="text-center p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">📱</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">Sempre Connesso</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Ricevi notifiche e aggiornamenti sui tuoi eventi. 
                        Accesso ottimizzato da qualsiasi dispositivo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action per Organizzatori -->
    <section class="py-16 bg-gradient-to-r from-primary to-primary-dark text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-4">Sei un Organizzatore?</h2>
            <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto">
                Gestisci i tuoi eventi sportivi con la nostra piattaforma professionale. 
                Strumenti avanzati per iscrizioni, pagamenti e gestione partecipanti.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/register?type=organizer" class="px-8 py-4 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105">
                    🚀 Diventa Organizzatore
                </a>
                <a href="/contact" class="px-8 py-4 bg-secondary text-white rounded-lg font-semibold hover:bg-yellow-500 transition transform hover:scale-105">
                    💬 Contattaci
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4 text-primary">SportEvents</h3>
                    <p class="text-gray-300 leading-relaxed">
                        La piattaforma leader per eventi sportivi in Italia. 
                        Connettendo atleti e organizzatori dal 2024.
                    </p>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-400 hover:text-white transition">📘</a>
                        <a href="#" class="text-gray-400 hover:text-white transition">📷</a>
                        <a href="#" class="text-gray-400 hover:text-white transition">🐦</a>
                        <a href="#" class="text-gray-400 hover:text-white transition">💼</a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Link Utili</h4>
                    <ul class="space-y-2">
                        <li><a href="/events" class="text-gray-300 hover:text-white transition">Eventi</a></li>
                        <li><a href="/register" class="text-gray-300 hover:text-white transition">Registrati</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Supporto</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Termini di Servizio</a></li>
                    </ul>
                </div>
                
                <div id="contact">
                    <h4 class="text-lg font-semibold mb-4">Contatti</h4>
                    <div class="space-y-3 text-gray-300">
                        <div class="flex items-center">
                            <span class="mr-2">📧</span>
                            <a href="mailto:info@sportevents.com" class="hover:text-white transition">info@sportevents.com</a>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-2">📞</span>
                            <a href="tel:+390212345678" class="hover:text-white transition">+39 02 1234 5678</a>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-2">📍</span>
                            <span>Milano, Italia</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 SportEvents. Tutti i diritti riservati. Fatto con ❤️ in Italia.</p>
            </div>
        </div>
    </footer>

    <!-- Popup Benvenuto Sconto -->
    <div id="welcome-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Benvenuto su SportEvents!</h3>
            <p class="text-gray-600 mb-4">Ottieni il <strong>5% di sconto</strong> sulla tua prima iscrizione</p>
            <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 mb-4">
                <div class="text-2xl font-bold text-primary-600 mb-1">SPORT5</div>
                <div class="text-sm text-gray-600">Copia questo codice e usalo al checkout</div>
            </div>
            <div class="flex gap-3">
                <button id="copy-code" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                    Copia Codice
                </button>
                <button id="close-popup" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Chiudi
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const welcomePopup = document.getElementById('welcome-popup');
            const copyCodeBtn = document.getElementById('copy-code');
            const closePopupBtn = document.getElementById('close-popup');

            // Debug: controlla lo stato del localStorage
            console.log('Welcome shown status:', localStorage.getItem('sportevents_welcome_shown'));
            
            // Mostra popup benvenuto se è la prima visita
            if (!localStorage.getItem('sportevents_welcome_shown')) {
                console.log('Showing welcome popup immediately...');
                // Mostra immediatamente senza ritardo
                welcomePopup.classList.remove('hidden');
                console.log('Popup displayed');
            } else {
                console.log('Welcome popup already shown, skipping');
            }

            // Copia codice sconto
            copyCodeBtn.addEventListener('click', function() {
                navigator.clipboard.writeText('SPORT5').then(function() {
                    copyCodeBtn.textContent = 'Copiato!';
                    copyCodeBtn.classList.add('bg-green-600');
                    copyCodeBtn.classList.remove('bg-primary');
                    
                    // Salva il codice nel localStorage per il checkout
                    localStorage.setItem('sportevents_discount_code', 'SPORT5');
                    
                    setTimeout(() => {
                        copyCodeBtn.textContent = 'Copia Codice';
                        copyCodeBtn.classList.remove('bg-green-600');
                        copyCodeBtn.classList.add('bg-primary');
                    }, 2000);
                }).catch(function() {
                    // Fallback se clipboard API non è supportata
                    localStorage.setItem('sportevents_discount_code', 'SPORT5');
                    copyCodeBtn.textContent = 'Codice Salvato!';
                    copyCodeBtn.classList.add('bg-green-600');
                    copyCodeBtn.classList.remove('bg-primary');
                    
                    setTimeout(() => {
                        copyCodeBtn.textContent = 'Copia Codice';
                        copyCodeBtn.classList.remove('bg-green-600');
                        copyCodeBtn.classList.add('bg-primary');
                    }, 2000);
                });
            });

            // Chiudi popup
            closePopupBtn.addEventListener('click', function() {
                welcomePopup.classList.add('hidden');
                localStorage.setItem('sportevents_welcome_shown', 'true');
            });

            // Chiudi popup cliccando fuori
            welcomePopup.addEventListener('click', function(e) {
                if (e.target === welcomePopup) {
                    welcomePopup.classList.add('hidden');
                    localStorage.setItem('sportevents_welcome_shown', 'true');
                }
            });

            // Chiudi popup con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !welcomePopup.classList.contains('hidden')) {
                    welcomePopup.classList.add('hidden');
                    localStorage.setItem('sportevents_welcome_shown', 'true');
                }
            });

            // TEMPORANEO: Pulsante per testare il popup (rimuovi dopo il test)
            // Premi Ctrl+Shift+P per mostrare il popup
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                    console.log('Forcing popup display for testing');
                    welcomePopup.classList.remove('hidden');
                }
            });
        });

        // Gestione notifiche
        <?php if (isset($_SESSION['user_id'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationCount = document.getElementById('notificationCount');
            const notificationList = document.getElementById('notificationList');
            
            let isDropdownOpen = false;

            // Carica contatore notifiche
            async function loadNotificationCount() {
                try {
                    const response = await fetch('/notifications/api/unread-count');
                    const data = await response.json();
                    
                    if (data.count > 0) {
                        notificationCount.textContent = data.count > 99 ? '99+' : data.count;
                        notificationCount.classList.remove('hidden');
                    } else {
                        notificationCount.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Errore nel caricamento contatore notifiche:', error);
                }
            }

            // Carica notifiche recenti
            async function loadRecentNotifications() {
                try {
                    const response = await fetch('/notifications/api/recent');
                    const data = await response.json();
                    
                    if (data.notifications && data.notifications.length > 0) {
                        notificationList.innerHTML = data.notifications.map(notification => `
                            <div class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer ${!notification.is_read ? 'bg-blue-50' : ''}"
                                 onclick="window.location.href='/notifications/${notification.id}'">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="font-medium text-sm text-gray-900 truncate pr-2">
                                        ${notification.subject}
                                    </div>
                                    <div class="text-xs text-gray-500 whitespace-nowrap">
                                        ${formatNotificationTime(notification.created_at)}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-600 truncate mb-1">
                                    📅 ${notification.event_title}
                                </div>
                                <div class="text-xs text-gray-500 truncate">
                                    ${notification.message.substring(0, 60)}${notification.message.length > 60 ? '...' : ''}
                                </div>
                                ${!notification.is_read ? '<div class="w-2 h-2 bg-blue-500 rounded-full mt-1"></div>' : ''}
                            </div>
                        `).join('');
                        
                        // Aggiungi link "Vedi tutte"
                        notificationList.innerHTML += `
                            <div class="p-3 text-center">
                                <a href="/notifications" class="text-primary text-sm hover:underline">
                                    Vedi tutte le notifiche →
                                </a>
                            </div>
                        `;
                    } else {
                        notificationList.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <div class="mb-2">📭</div>
                                <div class="text-sm">Nessuna notifica</div>
                            </div>
                        `;
                    }
                } catch (error) {
                    console.error('Errore nel caricamento notifiche:', error);
                    notificationList.innerHTML = `
                        <div class="p-4 text-center text-red-500">
                            <div class="text-sm">Errore nel caricamento</div>
                        </div>
                    `;
                }
            }

            // Formatta tempo notifica
            function formatNotificationTime(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMins / 60);
                const diffDays = Math.floor(diffHours / 24);

                if (diffMins < 1) return 'ora';
                if (diffMins < 60) return `${diffMins}m`;
                if (diffHours < 24) return `${diffHours}h`;
                if (diffDays < 7) return `${diffDays}g`;
                return date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' });
            }

            // Toggle dropdown
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                
                if (isDropdownOpen) {
                    notificationDropdown.classList.add('hidden');
                    isDropdownOpen = false;
                } else {
                    notificationDropdown.classList.remove('hidden');
                    isDropdownOpen = true;
                    loadRecentNotifications();
                }
            });

            // Chiudi dropdown cliccando fuori
            document.addEventListener('click', function(e) {
                if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                    isDropdownOpen = false;
                }
            });

            // Carica contatore iniziale
            loadNotificationCount();

            // Aggiorna contatore ogni 30 secondi
            setInterval(loadNotificationCount, 30000);
        });
        <?php endif; ?>
    </script>

    <script src="/assets/js/app.js"></script>
</body>
</html>
