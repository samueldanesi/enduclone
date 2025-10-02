<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Evento - SportEvents</title>
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
                    <span class="ml-4 px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">
                        Nuovo Evento
                    </span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-600 hover:text-primary-600 transition-colors">Home</a>
                    <a href="/events" class="text-gray-600 hover:text-primary-600 transition-colors">Eventi</a>
                    <a href="/calendar" class="text-primary-600 font-medium">Calendario</a>
                    <a href="/profile" class="text-gray-600 hover:text-primary-600 transition-colors">Profilo</a>
                    <a href="/logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">Esci</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="/calendar" class="hover:text-primary-600">Calendario</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900 font-medium">Nuovo Evento</li>
            </ol>
        </nav>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-red-800 font-medium mb-2">Si sono verificati alcuni errori:</h3>
                        <ul class="space-y-1">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li class="text-red-700 text-sm">• <?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Nuovo Evento</h1>
                <p class="text-gray-600">Aggiungi un nuovo evento al tuo calendario personale</p>
            </div>

            <form method="POST" action="/calendar/store" class="space-y-6">
                <!-- Titolo -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titolo *
                    </label>
                    <input type="text" name="title" id="title" required
                           value="<?php echo htmlspecialchars($_SESSION['form_data']['title'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Es. Allenamento corsa, Visita medica...">
                </div>

                <!-- Descrizione -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrizione
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Dettagli aggiuntivi..."><?php echo htmlspecialchars($_SESSION['form_data']['description'] ?? ''); ?></textarea>
                </div>

                <!-- Tipo Evento e Colore -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo Evento
                        </label>
                        <select name="event_type" id="event_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="personal" <?php echo ($_SESSION['form_data']['event_type'] ?? '') === 'personal' ? 'selected' : ''; ?>>
                                Personale
                            </option>
                            <option value="training" <?php echo ($_SESSION['form_data']['event_type'] ?? '') === 'training' ? 'selected' : ''; ?>>
                                Allenamento
                            </option>
                            <option value="reminder" <?php echo ($_SESSION['form_data']['event_type'] ?? '') === 'reminder' ? 'selected' : ''; ?>>
                                Promemoria
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            Colore
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="color" id="color"
                                   value="<?php echo $_SESSION['form_data']['color'] ?? '#3b82f6'; ?>"
                                   class="h-12 w-20 border border-gray-300 rounded-lg cursor-pointer">
                            <span class="text-sm text-gray-600">Scegli il colore per identificare l'evento</span>
                        </div>
                    </div>
                </div>

                <!-- Luogo -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Luogo
                    </label>
                    <input type="text" name="location" id="location"
                           value="<?php echo htmlspecialchars($_SESSION['form_data']['location'] ?? ''); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Es. Palestra, Parco, Casa...">
                </div>

                <!-- Tutto il giorno -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_all_day" id="is_all_day" 
                           <?php echo isset($_SESSION['form_data']['is_all_day']) ? 'checked' : ''; ?>
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                           onchange="toggleTimeFields()">
                    <label for="is_all_day" class="ml-2 text-sm font-medium text-gray-700">
                        Evento di tutto il giorno
                    </label>
                </div>

                <!-- Date e Orari -->
                <div class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Data Inizio *
                            </label>
                            <input type="date" name="start_date" id="start_date" required
                                   value="<?php echo $_SESSION['form_data']['start_date'] ?? $date; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Data Fine
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                   value="<?php echo $_SESSION['form_data']['end_date'] ?? ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6" id="time_fields">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Orario Inizio
                            </label>
                            <input type="time" name="start_time" id="start_time"
                                   value="<?php echo $_SESSION['form_data']['start_time'] ?? $time; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Orario Fine
                            </label>
                            <input type="time" name="end_time" id="end_time"
                                   value="<?php echo $_SESSION['form_data']['end_time'] ?? ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Notifica -->
                <div>
                    <label for="notification_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notifica Prima Dell'Evento
                    </label>
                    <select name="notification_minutes" id="notification_minutes"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="0">Nessuna notifica</option>
                        <option value="15" <?php echo ($_SESSION['form_data']['notification_minutes'] ?? 30) == 15 ? 'selected' : ''; ?>>15 minuti prima</option>
                        <option value="30" <?php echo ($_SESSION['form_data']['notification_minutes'] ?? 30) == 30 ? 'selected' : ''; ?>>30 minuti prima</option>
                        <option value="60" <?php echo ($_SESSION['form_data']['notification_minutes'] ?? 30) == 60 ? 'selected' : ''; ?>>1 ora prima</option>
                        <option value="120" <?php echo ($_SESSION['form_data']['notification_minutes'] ?? 30) == 120 ? 'selected' : ''; ?>>2 ore prima</option>
                        <option value="1440" <?php echo ($_SESSION['form_data']['notification_minutes'] ?? 30) == 1440 ? 'selected' : ''; ?>>1 giorno prima</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit"
                            class="flex-1 bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Crea Evento
                    </button>
                    <a href="/calendar" 
                       class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Annulla
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleTimeFields() {
            const isAllDay = document.getElementById('is_all_day').checked;
            const timeFields = document.getElementById('time_fields');
            const startTime = document.getElementById('start_time');
            const endTime = document.getElementById('end_time');
            
            if (isAllDay) {
                timeFields.style.display = 'none';
                startTime.removeAttribute('required');
                endTime.removeAttribute('required');
            } else {
                timeFields.style.display = 'grid';
            }
        }

        // Inizializza la visualizzazione dei campi orario
        document.addEventListener('DOMContentLoaded', function() {
            toggleTimeFields();
        });
    </script>

    <?php unset($_SESSION['form_data']); ?>
</body>
</html>
