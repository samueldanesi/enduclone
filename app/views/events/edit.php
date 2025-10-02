<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Evento - SportEvents</title>
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
                    <span class="ml-4 px-3 py-1 bg-primary-100 text-primary-600 rounded-full text-sm font-medium">
                        Organizzatore
                    </span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-600 hover:text-primary-600 transition-colors">Home</a>
                    <a href="/events" class="text-gray-600 hover:text-primary-600 transition-colors">Eventi</a>
                    <a href="/organizer" class="text-primary-600 font-medium">Dashboard</a>
                    <a href="/profile" class="text-gray-600 hover:text-primary-600 transition-colors">Profilo</a>
                    <a href="/logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">Esci</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="/organizer" class="text-primary-600 hover:text-primary-700">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Modifica Evento</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifica Evento</h1>
            <p class="text-gray-600 mt-2">Aggiorna i dettagli del tuo evento sportivo</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-600"><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

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

        <!-- Form -->
        <form method="POST" action="/organizer/events/<?= $event_data['event_id'] ?? $event_data['id'] ?? '' ?>" enctype="multipart/form-data" class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Informazioni di Base</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="lg:col-span-2">
                        <label for="titolo" class="block text-sm font-medium text-gray-700 mb-2">Titolo Evento *</label>
                        <input type="text" id="titolo" name="titolo" required
                               value="<?= htmlspecialchars($event_data['titolo']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. Maratona di Milano 2025">
                    </div>

                    <div>
                        <label for="sport" class="block text-sm font-medium text-gray-700 mb-2">Sport *</label>
                        <select id="sport" name="sport" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Seleziona sport</option>
                            <option value="running" <?= $event_data['sport'] === 'running' ? 'selected' : '' ?>>🏃‍♂️ Running</option>
                            <option value="cycling" <?= $event_data['sport'] === 'cycling' ? 'selected' : '' ?>>🚴‍♂️ Ciclismo</option>
                            <option value="triathlon" <?= $event_data['sport'] === 'triathlon' ? 'selected' : '' ?>>🏊‍♂️ Triathlon</option>
                            <option value="swimming" <?= $event_data['sport'] === 'swimming' ? 'selected' : '' ?>>🏊‍♀️ Nuoto</option>
                            <option value="trail" <?= $event_data['sport'] === 'trail' ? 'selected' : '' ?>>🥾 Trail Running</option>
                            <option value="mtb" <?= $event_data['sport'] === 'mtb' ? 'selected' : '' ?>>🚵‍♂️ Mountain Bike</option>
                        </select>
                    </div>

                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                        <select id="categoria_id" name="categoria_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Seleziona categoria</option>
                            <option value="1" <?= ($event_data['categoria_id'] ?? '') == '1' ? 'selected' : '' ?>>Running</option>
                            <option value="2" <?= ($event_data['categoria_id'] ?? '') == '2' ? 'selected' : '' ?>>Ciclismo</option>
                            <option value="3" <?= ($event_data['categoria_id'] ?? '') == '3' ? 'selected' : '' ?>>Triathlon</option>
                            <option value="4" <?= ($event_data['categoria_id'] ?? '') == '4' ? 'selected' : '' ?>>Trail</option>
                            <option value="5" <?= ($event_data['categoria_id'] ?? '') == '5' ? 'selected' : '' ?>>Camminata</option>
                        </select>
                    </div>

                    <div>
                        <label for="disciplina" class="block text-sm font-medium text-gray-700 mb-2">Disciplina</label>
                        <input type="text" id="disciplina" name="disciplina"
                               value="<?= htmlspecialchars($event_data['disciplina'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. Maratona, Half Marathon, 10K">
                    </div>

                    <div>
                        <label for="data_evento" class="block text-sm font-medium text-gray-700 mb-2">Data e Ora Evento *</label>
                        <input type="datetime-local" id="data_evento" name="data_evento" required
                               value="<?= date('Y-m-d\TH:i', strtotime($event_data['data_evento'])) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="descrizione" class="block text-sm font-medium text-gray-700 mb-2">Descrizione *</label>
                        <textarea id="descrizione" name="descrizione" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                                  placeholder="Descrivi il tuo evento in dettaglio..."><?= htmlspecialchars($event_data['descrizione']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Location and Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Luogo e Dettagli</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="luogo_partenza" class="block text-sm font-medium text-gray-700 mb-2">Luogo di Partenza *</label>
                        <input type="text" id="luogo_partenza" name="luogo_partenza" required
                               value="<?= htmlspecialchars($event_data['luogo_partenza']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. Piazza Duomo">
                    </div>

                    <div>
                        <label for="citta" class="block text-sm font-medium text-gray-700 mb-2">Città *</label>
                        <input type="text" id="citta" name="citta" required
                               value="<?= htmlspecialchars($event_data['citta'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. Milano">
                    </div>

                    <div>
                        <label for="distanza_km" class="block text-sm font-medium text-gray-700 mb-2">Distanza (km)</label>
                        <input type="number" id="distanza_km" name="distanza_km" step="0.1" min="0"
                               value="<?= $event_data['distanza_km'] ?? $event_data['lunghezza_km'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. 21.1">
                    </div>


                </div>
            </div>

            <!-- Pricing and Capacity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Prezzo e Capienza</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="prezzo_base" class="block text-sm font-medium text-gray-700 mb-2">Prezzo Base (€) *</label>
                        <input type="number" id="prezzo_base" name="prezzo_base" step="0.01" min="0" required
                               value="<?= $event_data['prezzo_base'] ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. 25.00">
                    </div>

                    <div>
                        <label for="max_partecipanti" class="block text-sm font-medium text-gray-700 mb-2">Max Partecipanti *</label>
                        <input type="number" id="max_partecipanti" name="max_partecipanti" min="1" required
                               value="<?= $event_data['max_partecipanti'] ?? $event_data['capienza_massima'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. 1000">
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Media</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="immagine" class="block text-sm font-medium text-gray-700 mb-2">Immagine Evento</label>
                        <input type="file" id="immagine" name="immagine" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <p class="text-sm text-gray-500 mt-1">Formati supportati: JPG, PNG, WebP (max 5MB)</p>
                        <?php if ($event_data['immagine']): ?>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">Immagine attuale:</p>
                                <img src="/uploads/<?= htmlspecialchars($event_data['immagine']) ?>" 
                                     alt="Immagine attuale" class="w-32 h-20 object-cover rounded-lg mt-1">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="file_gpx" class="block text-sm font-medium text-gray-700 mb-2">File GPX Percorso</label>
                        <input type="file" id="file_gpx" name="file_gpx" accept=".gpx"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <p class="text-sm text-gray-500 mt-1">File GPX del percorso (opzionale)</p>
                        <?php if (isset($event_data['file_gpx']) && $event_data['file_gpx']): ?>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">
                                    File GPX attuale: 
                                    <a href="/uploads/<?= htmlspecialchars($event_data['file_gpx']) ?>" 
                                       target="_blank" class="text-primary-600 hover:text-primary-700 underline">
                                        Scarica
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Stato Evento</h2>
                
                <div>
                    <label for="stato" class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                    <select id="stato" name="stato" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="bozza" <?= ($event_data['stato'] ?? '') === 'bozza' ? 'selected' : '' ?>>📝 Bozza</option>
                        <option value="pubblicato" <?= ($event_data['stato'] ?? '') === 'pubblicato' ? 'selected' : '' ?>>🌟 Pubblicato</option>
                        <option value="chiuso" <?= ($event_data['stato'] ?? '') === 'chiuso' ? 'selected' : '' ?>>🔒 Chiuso</option>
                        <option value="annullato" <?= ($event_data['stato'] ?? '') === 'annullato' ? 'selected' : '' ?>>❌ Annullato</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Gli eventi pubblicati sono visibili ai partecipanti</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" name="action" value="update"
                            class="flex-1 bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salva Modifiche
                    </button>
                    <a href="/organizer" 
                       class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Annulla
                    </a>
                </div>
            </div>
        </form>

        <!-- Sezione File GPX -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mt-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">File GPX</h3>
                    <p class="text-gray-600 mt-1">Carica i percorsi GPX per i partecipanti iscritti</p>
                </div>
                <div class="text-sm text-gray-500">
                    Solo file .gpx | Max 5MB
                </div>
            </div>

            <!-- Upload Form -->
            <form action="/organizer/gpx/upload" method="POST" enctype="multipart/form-data" class="mb-6">
                <input type="hidden" name="event_id" value="<?php echo $event->event_id; ?>">
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="file" name="gpx_file" accept=".gpx" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Carica GPX
                    </button>
                </div>
            </form>

            <!-- Lista File GPX Esistenti -->
            <div id="gpx-files-list">
                <?php
                require_once __DIR__ . '/../../models/GpxFile.php';
                $gpxFile = new GpxFile($db);
                $gpxFiles = $gpxFile->getByEventId($event->event_id);
                
                if (empty($gpxFiles)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium mb-2">Nessun file GPX caricato</p>
                        <p>I partecipanti potranno scaricare i percorsi GPX dopo l'iscrizione</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($gpxFiles as $file): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($file['original_name']); ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?php echo number_format($file['file_size'] / 1024, 1); ?> KB • 
                                            <?php echo $file['download_count']; ?> download •
                                            Caricato il <?php echo date('d/m/Y H:i', strtotime($file['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="/download/gpx/<?php echo $file['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        Scarica
                                    </a>
                                    <form action="/organizer/gpx/delete" method="POST" class="inline" 
                                          onsubmit="return confirm('Sei sicuro di voler eliminare questo file GPX?')">
                                        <input type="hidden" name="gpx_id" value="<?php echo $file['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Elimina
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Set minimum date to today
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const dateInput = document.getElementById('data_evento');
        dateInput.min = tomorrow.toISOString().slice(0, 16);
    </script>
</body>
</html>
