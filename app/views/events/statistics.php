<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiche - <?= htmlspecialchars($event_data['titolo']) ?> - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li class="text-gray-600">Statistiche Evento</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($event_data['titolo']) ?></h1>
            <p class="text-gray-600 mt-2">Statistiche e analytics dettagliate</p>
        </div>

        <!-- Event Info Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600"><?= date('d/m/Y', strtotime($event_data['data_evento'])) ?></div>
                    <div class="text-sm text-gray-600">Data Evento</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600"><?= ucfirst($event_data['stato'] ?? 'bozza') ?></div>
                    <div class="text-sm text-gray-600">Stato</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600"><?= htmlspecialchars($event_data['sport']) ?></div>
                    <div class="text-sm text-gray-600">Sport</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">€<?= number_format($event_data['prezzo_base'], 2) ?></div>
                    <div class="text-sm text-gray-600">Prezzo Base</div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Iscritti Totali</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $statistics['total_registrations'] ?? 0 ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121M12 20v-2a3 3 0 013-3h0a3 3 0 013 3v2M6 16a3 3 0 100-6 3 3 0 000 6zm4 4v-2a3 3 0 00-3-3H4a3 3 0 00-3 3v2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-gray-600"><?= ($event_data['max_partecipanti'] ?? 0) - ($statistics['total_registrations'] ?? 0) ?> posti rimasti</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= ($event_data['max_partecipanti'] ?? 0) > 0 ? min(100, (($statistics['total_registrations'] ?? 0) / $event_data['max_partecipanti']) * 100) : 0 ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Incassi Totali</p>
                        <p class="text-3xl font-bold text-gray-900">€<?= number_format($statistics['revenue'] ?? 0, 2) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">
                        Media: €<?= ($statistics['total_registrations'] ?? 0) > 0 ? number_format(($statistics['revenue'] ?? 0) / ($statistics['total_registrations'] ?? 1), 2) : '0.00' ?> per iscritto
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tasso di Riempimento</p>
                        <p class="text-3xl font-bold text-gray-900"><?= ($event_data['max_partecipanti'] ?? 0) > 0 ? number_format((($statistics['total_registrations'] ?? 0) / $event_data['max_partecipanti']) * 100, 1) : 0 ?>%</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Giorni Rimanenti</p>
                        <?php 
                        $event_date = new DateTime($event_data['data_evento']);
                        $current_date = new DateTime();
                        $days_left = $current_date->diff($event_date)->days;
                        if ($event_date < $current_date) $days_left = 0;
                        ?>
                        <p class="text-3xl font-bold text-gray-900"><?= $days_left ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Detailed Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Gender Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuzione per Sesso</h3>
                <div class="relative h-64">
                    <canvas id="genderChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                    <?php if (!empty($statistics['by_gender'])): ?>
                        <?php foreach ($statistics['by_gender'] as $gender): ?>
                            <div>
                                <div class="text-2xl font-bold text-gray-900"><?= $gender['count'] ?></div>
                                <div class="text-sm text-gray-600"><?= $gender['sesso'] === 'M' ? 'Uomini' : 'Donne' ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-2 text-gray-500">Nessun dato disponibile</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Age Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuzione per Età</h3>
                <div class="relative h-64">
                    <canvas id="ageChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    <?php if (!empty($statistics['by_age'])): ?>
                        <?php foreach ($statistics['by_age'] as $age_group): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600"><?= $age_group['age_group'] ?></span>
                                <span class="text-sm font-medium text-gray-900"><?= $age_group['count'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-gray-500">Nessun dato disponibile</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Teams Statistics -->
        <?php if (!empty($statistics['by_team'])): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Squadre Partecipanti</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Squadra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atleti</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uomini</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Donne</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Età Media</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($statistics['by_team'] as $team): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($team['team_name']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                <?= $team['total_members'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                <?= $team['male_count'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                <?= $team['female_count'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                <?= round($team['avg_age']) ?> anni
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Registrations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Gestione Iscrizioni</h3>
                <div class="flex gap-3">
                    <button onclick="showImportModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Importa Excel
                    </button>
                    <a href="/api/organizer/event/<?= $event_data['event_id'] ?>/export-excel" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Esporta Excel
                    </a>
                </div>
            </div>
            
            <?php if (!empty($recent_registrations)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partecipante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Iscrizione</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prezzo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach (array_slice($recent_registrations, 0, 10) as $registration): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($registration['nome'] . ' ' . $registration['cognome']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($registration['email']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('d/m/Y H:i', strtotime($registration['data_registrazione'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        €<?= number_format($registration['prezzo_pagato'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="text-gray-400 text-6xl mb-4">📊</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna iscrizione ancora</h3>
                    <p class="text-gray-600">Le iscrizioni appariranno qui una volta che i partecipanti inizieranno a registrarsi.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 flex-wrap">
            <a href="/organizer" 
               class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                Torna alla Dashboard
            </a>
            <a href="/events/<?= $event_data['event_id'] ?>" 
               class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                Visualizza Evento
            </a>
            <a href="/messages/compose/<?= $event_data['event_id'] ?>" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center gap-2">
                💬 Invia Messaggio
            </a>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div id="import-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-8 max-w-lg w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Importa Iscrizioni da Excel</h3>
            
            <form id="import-form" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Formati supportati: .xlsx, .xls, .csv</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Costo Pettorale (€)</label>
                        <input type="number" name="pettorale_fee" value="5.00" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sconto Gruppo (%)</label>
                        <input type="number" name="group_discount" value="0" min="0" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-3">📋 Campi Obbligatori Excel:</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm text-blue-800">
                        <div>• Nome</div>
                        <div>• Cognome</div>
                        <div>• Email</div>
                        <div>• Data Nascita</div>
                        <div>• Sesso (M/F)</div>
                        <div>• Squadra (opzionale)</div>
                    </div>
                    <p class="text-xs text-blue-700 mt-2">
                        Formato data: YYYY-MM-DD (es: 1990-03-15)
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Importa Iscrizioni
                    </button>
                    <button type="button" onclick="hideImportModal()" class="flex-1 bg-gray-200 text-gray-800 px-4 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                        Annulla
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Import Modal Functions
        function showImportModal() {
            document.getElementById('import-modal').classList.remove('hidden');
        }

        function hideImportModal() {
            document.getElementById('import-modal').classList.add('hidden');
        }

        // Handle Import Form
        document.getElementById('import-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('event_id', <?= $event_data['event_id'] ?>);
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Importando...';
            submitBtn.disabled = true;
            
            fetch('/api/organizer/import-excel', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`✅ Importate ${data.imported} iscrizioni con successo!\n` +
                          `💰 Totale incassi: €${data.total_revenue}\n` +
                          `❌ ${data.errors || 0} errori`);
                    hideImportModal();
                    location.reload();
                } else {
                    alert('❌ Errore durante l\'importazione:\n' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Errore di connessione durante l\'importazione');
                console.error(error);
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Real-time Updates (aggiorna ogni 30 secondi)
        setInterval(function() {
            fetch(`/api/organizer/event/${<?= $event_data['event_id'] ?>}/realtime-stats`)
                .then(response => response.json())
                .then(data => {
                    // Aggiorna contatori in tempo reale
                    const totalElement = document.querySelector('[data-stat="total"]');
                    if (totalElement) totalElement.textContent = data.total_registrations;
                })
                .catch(error => console.log('Aggiornamento stats fallito'));
        }, 30000);

        // Gender Chart
        const genderData = <?= json_encode($statistics['by_gender'] ?? []) ?>;
        if (genderData.length > 0) {
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: genderData.map(item => item.sesso === 'M' ? 'Uomini' : 'Donne'),
                    datasets: [{
                        data: genderData.map(item => item.count),
                        backgroundColor: ['#3b82f6', '#ec4899'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Age Chart
        const ageData = <?= json_encode($statistics['by_age'] ?? []) ?>;
        if (ageData.length > 0) {
            const ageCtx = document.getElementById('ageChart').getContext('2d');
            new Chart(ageCtx, {
                type: 'bar',
                data: {
                    labels: ageData.map(item => item.age_group),
                    datasets: [{
                        label: 'Iscritti',
                        data: ageData.map(item => item.count),
                        backgroundColor: '#3b82f6',
                        borderColor: '#2563eb',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
