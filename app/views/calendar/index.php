<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il Mio Calendario - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
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
    renderNavbar('calendar'); 
    ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Il Mio Calendario</h1>
                    <p class="mt-2 text-gray-600">Gestisci eventi sportivi, allenamenti e promemoria personali</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="/calendar/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-primary-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nuovo Evento
                    </a>
                    <a href="/calendar/day?date=<?php echo date('Y-m-d'); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        Vista Giorno
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-green-800"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-800"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Calendar Main -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiche Mese</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Eventi Totali</span>
                            <span class="font-semibold text-gray-900"><?php echo $stats['total_events'] ?? 0; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Eventi Sportivi</span>
                            <span class="font-semibold text-green-600"><?php echo $stats['sport_events'] ?? 0; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Allenamenti</span>
                            <span class="font-semibold text-blue-600"><?php echo $stats['training_events'] ?? 0; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Eventi Personali</span>
                            <span class="font-semibold text-purple-600"><?php echo $stats['personal_events'] ?? 0; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Prossimi Eventi</h3>
                    <?php if (empty($upcomingEvents)): ?>
                        <div class="text-center py-4 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">Nessun evento in programma</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($upcomingEvents as $event): ?>
                                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 text-sm"><?php echo htmlspecialchars($event['title']); ?></h4>
                                            <p class="text-xs text-gray-600 mt-1">
                                                <?php echo date('d/m/Y H:i', strtotime($event['start_datetime'])); ?>
                                            </p>
                                            <?php if ($event['location']): ?>
                                                <p class="text-xs text-gray-500 mt-1">📍 <?php echo htmlspecialchars($event['location']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="w-3 h-3 rounded-full ml-3 mt-1" style="background-color: <?php echo $event['color']; ?>"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Legend -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Legenda</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-green-500 mr-3"></div>
                            <span class="text-sm text-gray-700">Eventi Sportivi</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-blue-500 mr-3"></div>
                            <span class="text-sm text-gray-700">Allenamenti</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-yellow-500 mr-3"></div>
                            <span class="text-sm text-gray-700">Promemoria</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-purple-500 mr-3"></div>
                            <span class="text-sm text-gray-700">Eventi Personali</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'it',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Oggi',
                    month: 'Mese',
                    week: 'Settimana',
                    day: 'Giorno'
                },
                height: 'auto',
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('/calendar/events?year=' + fetchInfo.start.getFullYear() + '&month=' + (fetchInfo.start.getMonth() + 1))
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(err => failureCallback(err));
                },
                eventClick: function(info) {
                    window.location.href = '/calendar/' + info.event.id;
                },
                dateClick: function(info) {
                    window.location.href = '/calendar/create?date=' + info.dateStr;
                },
                eventDidMount: function(info) {
                    info.el.style.cursor = 'pointer';
                }
            });
            
            calendar.render();
        });
    </script>
</body>
</html>
