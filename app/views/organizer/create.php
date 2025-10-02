<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Nuovo Evento - SportEvents</title>
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
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <!-- Navigation unificata -->
    <?php 
    require_once __DIR__ . '/../components/navbar.php';
    renderNavbar('organizer'); 
    ?>    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-500 mb-8">
            <a href="/organizer" class="hover:text-primary-600">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium">Crea Nuovo Evento</span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Crea Nuovo Evento</h1>
            <p class="text-gray-600 mt-2">Compila i dettagli per creare un nuovo evento sportivo</p>
        </div>

        <!-- Form -->
        <form id="create-event-form" method="POST" action="/organizer/create" enctype="multipart/form-data" class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Informazioni di Base</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="lg:col-span-2">
                        <label for="titolo" class="block text-sm font-medium text-gray-700 mb-2">Titolo Evento *</label>
                        <input type="text" id="titolo" name="titolo" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. Maratona di Milano 2025">
                    </div>

                    <div>
                        <label for="sport" class="block text-sm font-medium text-gray-700 mb-2">Sport *</label>
                        <select id="sport" name="sport" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Seleziona sport</option>
                            <option value="Corsa">Corsa</option>
                            <option value="Ciclismo">Ciclismo</option>
                            <option value="Triathlon">Triathlon</option>
                            <option value="Nuoto">Nuoto</option>
                            <option value="Trail Running">Trail Running</option>
                            <option value="Atletica">Atletica</option>
                            <option value="Calcio">Calcio</option>
                            <option value="Tennis">Tennis</option>
                            <option value="Pallavolo">Pallavolo</option>
                            <option value="Altro">Altro</option>
                        </select>
                    </div>

                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">Categoria *</label>
                        <select id="categoria_id" name="categoria_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Seleziona categoria</option>
                            <?php if (isset($categories) && !empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['categoria_id'] ?>">
                                        <?= htmlspecialchars($category['nome_categoria']) ?> - <?= htmlspecialchars($category['descrizione']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">Running - Eventi di corsa su strada e trail</option>
                                <option value="2">Ciclismo - Eventi ciclistici</option>
                                <option value="3">Triathlon - Nuoto, ciclismo e corsa</option>
                                <option value="4">Trail - Corsa in natura</option>
                                <option value="5">Camminata - Eventi non competitivi</option>
                            <?php endif; ?>
                        </select>
                    </div>



                    <div>
                        <label for="data_evento" class="block text-sm font-medium text-gray-700 mb-2">Data e Ora *</label>
                        <input type="datetime-local" id="data_evento" name="data_evento" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label for="luogo_partenza" class="block text-sm font-medium text-gray-700 mb-2">Luogo di Partenza *</label>
                        <input type="text" id="luogo_partenza" name="luogo_partenza" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. Piazza Duomo">
                    </div>

                    <div>
                        <label for="citta" class="block text-sm font-medium text-gray-700 mb-2">Città *</label>
                        <input type="text" id="citta" name="citta" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. Milano" value="Milano">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="descrizione" class="block text-sm font-medium text-gray-700 mb-2">Descrizione *</label>
                        <textarea id="descrizione" name="descrizione" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-y"
                                  placeholder="Descrivi il tuo evento, il percorso, cosa include l'iscrizione..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Dettagli Evento</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="max_partecipanti" class="block text-sm font-medium text-gray-700 mb-2">Massimo Partecipanti *</label>
                        <input type="number" id="max_partecipanti" name="max_partecipanti" required min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. 500">
                    </div>

                    <div>
                        <label for="prezzo_base" class="block text-sm font-medium text-gray-700 mb-2">Prezzo Base (€)</label>
                        <input type="number" id="prezzo_base" name="prezzo_base" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="0.00">
                    </div>

                    <div>
                        <label for="distanza_km" class="block text-sm font-medium text-gray-700 mb-2">Distanza (km)</label>
                        <input type="number" id="distanza_km" name="distanza_km" step="0.1" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                               placeholder="es. 42.195">
                    </div>


                </div>
            </div>

            <!-- Media Upload -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Media</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="immagine" class="block text-sm font-medium text-gray-700 mb-2">Immagine Evento</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-4">
                                <label for="immagine" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        Carica un'immagine
                                    </span>
                                    <input id="immagine" name="immagine" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF fino a 5MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="file_gpx" class="block text-sm font-medium text-gray-700 mb-2">File GPX (Percorso)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div class="mt-4">
                                <label for="file_gpx" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        Carica file GPX
                                    </span>
                                    <input id="file_gpx" name="file_gpx" type="file" accept=".gpx" class="sr-only">
                                </label>
                                <p class="mt-1 text-xs text-gray-500">File GPX del percorso</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="/organizer" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors text-center">
                    Annulla
                </a>
                <button type="submit" name="status" value="bozza"
                        class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                    Salva come Bozza
                </button>
                <button type="submit" name="status" value="pubblicato"
                        class="px-6 py-3 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                    Pubblica Evento
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2025 SportEvents. Tutti i diritti riservati.</p>
        </div>
    </footer>

    <script>
        // Form handling
        document.getElementById('create-event-form').addEventListener('submit', function(e) {
            const submitButton = e.submitter;
            
            // Set status based on which button was clicked
            const hiddenStatusInput = document.createElement('input');
            hiddenStatusInput.type = 'hidden';
            hiddenStatusInput.name = 'status';
            hiddenStatusInput.value = submitButton.value;
            this.appendChild(hiddenStatusInput);
            
            // Show loading state
            submitButton.innerHTML = 'Salvataggio...';
            submitButton.disabled = true;
            
            // Allow normal form submission
            return true;
        });

        // File upload preview
        document.getElementById('immagine').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Show preview (implementation would go here)
                    console.log('Image uploaded:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });

        // Set minimum date to today
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('data_evento').min = tomorrow.toISOString().slice(0, 16);
    </script>
</body>
</html>
