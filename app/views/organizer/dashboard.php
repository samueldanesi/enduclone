<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Organizzatore - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
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
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Includi navbar unificata -->
    <?php 
    require_once __DIR__ . '/../components/navbar.php';
    renderNavbar('organizer'); 
    ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Benvenuto, <?= htmlspecialchars($_SESSION['nome'] ?? 'Organizzatore') ?>!
            </h1>
            <p class="text-gray-600 mt-2">Gestisci i tuoi eventi sportivi dalla dashboard</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Events -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Eventi Totali</p>
                        <p class="text-3xl font-bold text-gray-900"><?= count($events ?? []) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Events -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Eventi Attivi</p>
                        <p class="text-3xl font-bold text-green-600">
                            <?php 
                            $activeEvents = 0;
                            if (isset($events)) {
                                foreach ($events as $event) {
                                    if (($event['stato'] ?? '') === 'pubblicato' && strtotime($event['data_evento']) > time()) {
                                        $activeEvents++;
                                    }
                                }
                            }
                            echo $activeEvents;
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Participants -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Partecipanti Totali</p>
                        <p class="text-3xl font-bold text-blue-600">
                            <?php 
                            $totalParticipants = 0;
                            if (isset($events)) {
                                foreach ($events as $event) {
                                    $totalParticipants += $event['registrations_count'] ?? 0;
                                }
                            }
                            echo $totalParticipants;
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ricavi Stimati</p>
                        <p class="text-3xl font-bold text-yellow-600">
                            €<?php 
                            $totalRevenue = 0;
                            if (isset($events)) {
                                foreach ($events as $event) {
                                    $participants = $event['registrations_count'] ?? 0;
                                    $price = $event['prezzo_base'] ?? 0;
                                    $totalRevenue += $participants * $price;
                                }
                            }
                            echo number_format($totalRevenue, 0);
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/organizer/create" 
                   class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Crea Nuovo Evento
                </a>
                <a href="/events" 
                   class="bg-white text-primary-600 border border-primary-600 px-6 py-3 rounded-lg font-semibold hover:bg-primary-50 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Gestisci Eventi
                </a>
            </div>
        </div>

        <!-- Events Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">I Tuoi Eventi</h2>
            </div>
            
            <?php if (isset($events) && count($events) > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partecipanti</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($events as $event): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($event['titolo']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($event['luogo_partenza']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($event['data_evento'])) ?></div>
                                <div class="text-sm text-gray-500"><?= date('H:i', strtotime($event['data_evento'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= $event['registrations_count'] ?? 0 ?> / <?= $event['max_partecipanti'] ?? 0 ?>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <?php 
                                    $percentage = ($event['max_partecipanti'] ?? 0) > 0 ? 
                                        (($event['registrations_count'] ?? 0) / $event['max_partecipanti']) * 100 : 0;
                                    ?>
                                    <div class="bg-primary-600 h-2 rounded-full" style="width: <?= min($percentage, 100) ?>%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($event['stato'] ?? 'bozza') {
                                    case 'pubblicato':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'Pubblicato';
                                        break;
                                    case 'bozza':
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Bozza';
                                        break;
                                    case 'chiuso':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'Chiuso';
                                        break;
                                    case 'annullato':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'Annullato';
                                        break;
                                    default:
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = ucfirst($event['stato'] ?? 'bozza');
                                }
                                ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap gap-2">
                                    <a href="/events/<?= $event['event_id'] ?? $event['id'] ?? '' ?>" 
                                       class="text-primary-600 hover:text-primary-900 transition-colors">
                                        Visualizza
                                    </a>
                                    <a href="/organizer/events/<?= $event['event_id'] ?? $event['id'] ?? '' ?>" 
                                       class="text-gray-600 hover:text-gray-900 transition-colors">
                                        Modifica
                                    </a>
                                    <a href="/organizer/statistics/<?= $event['event_id'] ?? $event['id'] ?? '' ?>" 
                                       class="text-green-600 hover:text-green-900 transition-colors">
                                        Statistiche
                                    </a>
                                    <a href="/messages/compose/<?= $event['event_id'] ?? $event['id'] ?? '' ?>" 
                                       class="text-blue-600 hover:text-blue-900 transition-colors font-medium">
                                        💬 Messaggi
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Nessun evento ancora</h3>
                <p class="text-gray-600 mb-6">Crea il tuo primo evento sportivo per iniziare</p>
                <a href="/organizer/events/create" 
                   class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                    Crea Primo Evento
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attività Recente</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Nuovo partecipante registrato</span>
                        <span class="text-xs text-gray-400">2 ore fa</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Evento pubblicato</span>
                        <span class="text-xs text-gray-400">1 giorno fa</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Aggiornamento evento</span>
                        <span class="text-xs text-gray-400">3 giorni fa</span>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-xl border border-primary-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Suggerimenti</h3>
                <div class="space-y-3">
                    <p class="text-sm text-gray-700">
                        • Aggiungi immagini accattivanti ai tuoi eventi per aumentare le iscrizioni
                    </p>
                    <p class="text-sm text-gray-700">
                        • Invia promemoria ai partecipanti 48 ore prima dell'evento
                    </p>
                    <p class="text-sm text-gray-700">
                        • Usa le statistiche per migliorare i tuoi prossimi eventi
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2025 SportEvents. Tutti i diritti riservati.</p>
        </div>
    </footer>
</body>
</html>
