<!DOCTYPE html>
<html lang="it" class="scroll-smooth">
<head>
    <meta charset=    <!-- Hero Section -->
                        <select name="citta" class="w-full px-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm">section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="glass-card p-12">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 gradient-text">
                    Tutti gli Eventi Sportivi
                </h1>
                <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                    Scopri migliaia di eventi sportivi in tutta Italia. Trova la tua prossima sfida!
                </p>
            </div>
        </div>
    </section>  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventi Sportivi - SportEvents</title>
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
</head>
<body class="font-inter">
    <!-- Header -->
    <header class="glass-card mx-4 mt-4 sticky top-4 z-50">
        <div class="max-w-7xl mx-auto px-6">
            <nav class="flex justify-between items-center py-4">
                <a href="/" class="text-2xl font-bold gradient-text">SportEvents</a>
                
                <ul class="hidden md:flex space-x-8">
                    <li><a href="/" class="text-gray-700 hover:text-primary font-medium transition">Home</a></li>
                    <li><a href="/events" class="text-primary font-medium">Eventi</a></li>
                    <li><a href="/about" class="text-gray-700 hover:text-primary font-medium transition">Chi Siamo</a></li>
                    <li><a href="/contact" class="text-gray-700 hover:text-primary font-medium transition">Contatti</a></li>
                </ul>

                <div class="flex space-x-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/profile" class="px-4 py-2 text-primary border border-primary rounded-lg hover:bg-primary hover:text-white transition"><?= htmlspecialchars($_SESSION['nome'] ?? 'Utente') ?></a>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'organizer'): ?>
                            <a href="/organizer" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">Dashboard</a>
                        <?php endif; ?>
                        <a href="/logout" class="px-4 py-2 text-primary border border-primary rounded-lg hover:bg-primary hover:text-white transition">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="px-4 py-2 text-primary border border-primary rounded-lg hover:bg-primary hover:text-white transition">Accedi</a>
                        <a href="/register" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">Registrati</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section Eventi -->
    <section class="bg-gradient-to-r from-primary to-primary-dark text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Tutti gli Eventi Sportivi
            </h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">
                Scopri migliaia di eventi sportivi in tutta Italia. Trova la tua prossima sfida!
            </p>
        </div>
    </section>

    <!-- Filtri Avanzati -->
    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-card p-8">
                <h2 class="text-2xl font-bold mb-6 text-center gradient-text">Filtra Gli Eventi</h2>
                <form method="GET" action="/events" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <select name="sport" class="w-full px-4 py-3 bg-white/70 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm">
                        <option value="">🏃 Tutti gli Sport</option>
                        <option value="running" <?= ($_GET['sport'] ?? '') === 'running' ? 'selected' : '' ?>>🏃‍♂️ Running</option>
                        <option value="cycling" <?= ($_GET['sport'] ?? '') === 'cycling' ? 'selected' : '' ?>>🚴‍♂️ Ciclismo</option>
                        <option value="triathlon" <?= ($_GET['sport'] ?? '') === 'triathlon' ? 'selected' : '' ?>>🏊‍♂️ Triathlon</option>
                        <option value="swimming" <?= ($_GET['sport'] ?? '') === 'swimming' ? 'selected' : '' ?>>🏊‍♀️ Nuoto</option>
                        <option value="trail" <?= ($_GET['sport'] ?? '') === 'trail' ? 'selected' : '' ?>>🥾 Trail Running</option>
                        <option value="mtb" <?= ($_GET['sport'] ?? '') === 'mtb' ? 'selected' : '' ?>>🚵‍♂️ Mountain Bike</option>
                    </select>
                </div>
                
                <div>
                    <input type="text" name="luogo" placeholder="📍 Città o Regione" 
                           value="<?= htmlspecialchars($_GET['luogo'] ?? '') ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <input type="date" name="data_da" value="<?= $_GET['data_da'] ?? '' ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <input type="date" name="data_a" value="<?= $_GET['data_a'] ?? '' ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                                        <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition font-medium shadow-lg">
                        🔍 Cerca Eventi
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Lista Eventi -->
    <section class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-card p-8 mb-8">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold gradient-text">
                        Eventi Trovati 
                        <span class="gradient-text">(<?= count($events ?? []) ?>)</span>
                    </h2>
                
                <div class="flex space-x-2">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option>📅 Data più vicina</option>
                        <option>💰 Prezzo crescente</option>
                        <option>💰 Prezzo decrescente</option>
                        <option>📍 Distanza</option>
                    </select>
                </div>
            </div>

            <?php
            // Eventi di esempio per la demo
            $events = $events ?? [
                [
                    'id' => 1,
                    'titolo' => 'Maratona di Milano',
                    'data_evento' => '2024-04-14',
                    'luogo_partenza' => 'Milano, Lombardia',
                    'sport' => 'running',
                    'distanza_km' => 42.195,
                    'descrizione' => 'La classica maratona urbana di Milano attraverso i luoghi più belli della città.',
                    'prezzo_base' => 45.00,
                    'max_partecipanti' => 15000,
                    'registrations_count' => 1250,
                    'immagine' => null,
                    'organizer_name' => 'Milano Marathon',
                    'categoria' => 'Maratona'
                ],
                [
                    'id' => 2,
                    'titolo' => 'Gran Fondo delle Dolomiti',
                    'data_evento' => '2024-07-15',
                    'luogo_partenza' => 'Cortina d\'Ampezzo, Veneto',
                    'sport' => 'cycling',
                    'distanza_km' => 135.5,
                    'descrizione' => 'Granfondo ciclistica tra le meravigliose Dolomiti con panorami mozzafiato.',
                    'prezzo_base' => 80.00,
                    'max_partecipanti' => 3000,
                    'registrations_count' => 850,
                    'immagine' => null,
                    'organizer_name' => 'Dolomiti Cycling',
                    'categoria' => 'Gran Fondo'
                ],
                [
                    'id' => 3,
                    'titolo' => 'Triathlon Lago di Garda',
                    'data_evento' => '2024-09-10',
                    'luogo_partenza' => 'Riva del Garda, Trentino',
                    'sport' => 'triathlon',
                    'distanza_km' => 51.5,
                    'descrizione' => 'Triathlon olimpico sulle sponde del Lago di Garda.',
                    'prezzo_base' => 120.00,
                    'max_partecipanti' => 1500,
                    'registrations_count' => 450,
                    'immagine' => null,
                    'organizer_name' => 'Garda Triathlon',
                    'categoria' => 'Olimpico'
                ]
            ];
            ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php foreach ($events as $event): ?>
                    <div class="glass-card overflow-hidden">
                        <div class="md:flex">
                            <div class="md:w-1/3">
                                <?php if (isset($event['immagine']) && $event['immagine']): ?>
                                    <img src="/uploads/<?= htmlspecialchars($event['immagine']) ?>" 
                                         alt="<?= htmlspecialchars($event['titolo']) ?>"
                                         class="w-full h-48 md:h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-48 md:h-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-3xl font-bold">
                                        🏃‍♂️
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="md:w-2/3 p-6">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                                        Evento Sportivo
                                    </span>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-primary">€<?= number_format($event['prezzo_base'], 0) ?></div>
                                        <div class="text-sm text-gray-500"><?= $event['registrations_count'] ?? 0 ?>/<?= $event['max_partecipanti'] ?? 'N.A.' ?> iscritti</div>
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-bold mb-2 text-gray-900">
                                    <a href="/events/<?= $event['event_id'] ?>" class="hover:text-primary transition">
                                        <?= htmlspecialchars($event['titolo']) ?>
                                    </a>
                                </h3>
                                
                                <div class="space-y-1 mb-3 text-gray-600 text-sm">
                                    <div class="flex items-center">
                                        <span class="mr-2">📅</span>
                                        <?= date('d M Y', strtotime($event['data_evento'])) ?>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="mr-2">📍</span>
                                        <?= $event['luogo_partenza'] ? htmlspecialchars($event['luogo_partenza']) : 'Luogo da definire' ?>
                                    </div>
                                    <?php if (isset($event['distanza_km']) && $event['distanza_km']): ?>
                                    <div class="flex items-center">
                                        <span class="mr-2">🏁</span>
                                        <?= $event['distanza_km'] ?>km
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex items-center">
                                        <span class="mr-2">👤</span>
                                        <?= htmlspecialchars($event['organizer_name'] ?? 'Organizzatore') ?>
                                    </div>
                                </div>
                                
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    <?= $event['descrizione'] ? htmlspecialchars($event['descrizione']) : 'Descrizione da definire' ?>
                                </p>
                                
                                <div class="flex space-x-3">
                                    <a href="/events/<?= $event['event_id'] ?>" 
                                       class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition text-center shadow-lg">
                                        📖 Dettagli
                                    </a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="/events/<?= $event['event_id'] ?>/register" 
                                           class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl font-medium hover:from-purple-600 hover:to-purple-700 transition text-center shadow-lg">
                                            ⚡ Iscriviti
                                        </a>
                                    <?php else: ?>
                                        <a href="/login" 
                                           class="flex-1 px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl font-medium hover:from-gray-600 hover:to-gray-700 transition text-center shadow-lg">
                                            🔑 Login
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginazione -->
            <div class="mt-12 flex justify-center">
                <nav class="flex space-x-2">
                    <a href="#" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Precedente</a>
                    <a href="#" class="px-3 py-2 bg-primary text-white rounded-lg">1</a>
                    <a href="#" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">2</a>
                    <a href="#" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">3</a>
                    <a href="#" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Successivo</a>
                </nav>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; 2024 SportEvents. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
