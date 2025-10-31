<!DOCTYPE html>
<html lang="it" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['titolo'] ?? 'Evento') ?> - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
<body class="font-inter bg-gray-50">
    <!-- Navigation unificata -->
    <?php 
    require_once __DIR__ . '/../components/navbar.php';
    renderNavbar('events'); 
    ?>    <?php if (isset($event) && $event): ?>
    <!-- Breadcrumb -->
    <div class="bg-gray-100 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex text-sm text-gray-500">
                <a href="/" class="hover:text-primary">Home</a>
                <span class="mx-2">/</span>
                <a href="/events" class="hover:text-primary">Eventi</a>
                <span class="mx-2">/</span>
                <span class="text-primary font-medium"><?= htmlspecialchars($event['titolo']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Event Hero -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Event Image -->
                <div class="lg:col-span-2">
                    <?php if (!empty($event['immagine'])): ?>
                        <img src="/uploads/events/<?= htmlspecialchars($event['immagine']) ?>" 
                             alt="<?= htmlspecialchars($event['titolo']) ?>" 
                             class="w-full h-96 object-cover rounded-xl shadow-lg">
                    <?php else: ?>
                        <div class="w-full h-96 bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-lg flex items-center justify-center">
                            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Event Info Card -->
                <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-lg h-fit">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-green-600 font-medium text-sm uppercase tracking-wide">
                            <?php if (strtotime($event['data_evento']) > time()): ?>
                                Iscrizioni Aperte
                            <?php else: ?>
                                Evento Concluso
                            <?php endif; ?>
                        </span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-6"><?= htmlspecialchars($event['titolo']) ?></h1>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-600"><?= date('d/m/Y', strtotime($event['data_evento'])) ?></span>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600"><?= date('H:i', strtotime($event['data_evento'])) ?></span>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-600"><?= htmlspecialchars($event['luogo_partenza'] ?? '') ?></span>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-gray-600"><?= $event['registrations_count'] ?? 0 ?> / <?= $event['capienza_massima'] ?? $event['max_partecipanti'] ?? 100 ?> partecipanti</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="text-gray-600">
                                <?php if ($event['prezzo_base'] > 0): ?>
                                    €<?= number_format($event['prezzo_base'], 2) ?>
                                <?php else: ?>
                                    Gratuito
                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium">
                                <?= htmlspecialchars($event['categoria'] ?? 'Non specificata') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Registration Status -->
                    <div class="mb-6">
                        <?php
                        $max_partecipanti = $event['max_partecipanti'] ?? $event['capienza_massima'] ?? 100;
                        $registrations_count = $event['registrations_count'] ?? 0;
                        $spots_left = $max_partecipanti - $registrations_count;
                        $percentage = $max_partecipanti > 0 ? ($registrations_count / $max_partecipanti) * 100 : 0;
                        ?>
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Posti disponibili</span>
                            <span><?= $spots_left ?> rimasti</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </div>

                    <!-- Registration Button -->
                    <?php if (strtotime($event['data_evento']) > time() && $spots_left > 0): ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php
                            // Verifica se l'utente è già iscritto (usa l'istanza dal controller)
                            $isAlreadyRegistered = $registration->isUserRegistered($_SESSION['user_id'], $event['event_id']);
                            ?>
                            <?php if ($isAlreadyRegistered): ?>
                                <button disabled class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold cursor-not-allowed text-lg flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Già Iscritto
                                </button>
                                
                                <!-- Community Evento - Solo per iscritti -->
                                <a href="/community/event/<?php echo $event['event_id']; ?>" class="block w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors text-lg text-center flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-2-2V10a2 2 0 012-2h2"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 6v2H9V6a3 3 0 016 0z"></path>
                                    </svg>
                                    Community Evento
                                </a>
                            <?php else: ?>
                                <a id="register-now-btn" data-event-id="<?php echo $event['event_id']; ?>" href="<?= BASE_URL ?>/events/<?php echo $event['event_id']; ?>/register" class="block w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-dark transition-colors text-lg text-center">
                                    Iscriviti Ora
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/login" class="block w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-dark transition-colors text-lg text-center">
                                Accedi per Iscriverti
                            </a>
                        <?php endif; ?>
                    <?php elseif ($spots_left <= 0): ?>
                        <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold cursor-not-allowed text-lg">
                            Evento al Completo
                        </button>
                    <?php else: ?>
                        <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold cursor-not-allowed text-lg">
                            Evento Concluso
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Description -->
                    <div class="bg-white rounded-xl p-8 shadow-sm">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Descrizione</h2>
                        <div class="prose prose-lg max-w-none text-gray-700">
                            <?= nl2br(htmlspecialchars($event['descrizione'])) ?>
                        </div>
                    </div>

                    <!-- Requirements -->
                    <?php if (!empty($event['requisiti'])): ?>
                    <div class="bg-white rounded-xl p-8 shadow-sm">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Requisiti</h2>
                        <div class="prose prose-lg max-w-none text-gray-700">
                            <?= nl2br(htmlspecialchars($event['requisiti'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Safety Info -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8">
                        <div class="flex items-start gap-4">
                            <svg class="w-6 h-6 text-yellow-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Informazioni Importanti</h3>
                                <ul class="text-yellow-700 space-y-1 text-sm">
                                    <li>• È richiesto certificato medico per attività sportiva non agonistica</li>
                                    <li>• La partecipazione è a proprio rischio e pericolo</li>
                                    <li>• Seguire sempre le indicazioni degli organizzatori</li>
                                    <li>• Rispettare le norme di sicurezza e fair play</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Organizer Info -->
                    <div class="bg-white rounded-xl p-8 shadow-sm">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Organizzatore</h3>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-lg">
                                    <?= strtoupper(substr($event['organizer_name'] ?? 'O', 0, 1)) ?>
                                </span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900"><?= htmlspecialchars($event['organizer_name'] ?? 'Organizzatore') ?></div>
                                <div class="text-sm text-gray-600">Organizzatore verificato</div>
                            </div>
                        </div>
                        <a href="mailto:<?= htmlspecialchars($event['organizer_email'] ?? '') ?>" 
                           class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-200 transition-colors text-center block">
                            Contatta
                        </a>
                    </div>

                    <!-- Share Event -->
                    <div class="bg-white rounded-xl p-8 shadow-sm">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Condividi Evento</h3>
                        <div class="flex gap-3">
                            <button class="flex-1 bg-blue-600 text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                Facebook
                            </button>
                            <button class="flex-1 bg-sky-500 text-white py-2 px-3 rounded-lg hover:bg-sky-600 transition-colors text-sm">
                                Twitter
                            </button>
                            <button class="flex-1 bg-green-600 text-white py-2 px-3 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                WhatsApp
                            </button>
                        </div>
                    </div>

                    <!-- File GPX per Partecipanti Iscritti -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                        // Verifica se l'utente è iscritto (usa l'istanza dal controller)
                        $isRegistered = $registration->isUserRegistered($_SESSION['user_id'], $event['event_id']);
                        
                        if ($isRegistered):
                            require_once __DIR__ . '/../../models/GpxFile.php';
                            $gpxFile = new GpxFile($db);
                            $gpxFiles = $gpxFile->getByEventId($event['event_id']);
                            
                            if (!empty($gpxFiles)):
                                // Verifica se l'utente può scaricare
                                $gpxFile->event_id = $event['event_id'];
                                $downloadPermission = $gpxFile->canUserDownload($_SESSION['user_id']);
                        ?>
                        <div class="bg-white rounded-xl p-8 shadow-sm">
                            <div class="flex items-center mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900">Percorsi GPX</h3>
                                <span class="ml-3 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    Solo Iscritti Paganti
                                </span>
                            </div>
                            <p class="text-gray-600 mb-4 text-sm">File GPS per navigatori e smartphone - Disponibili dopo il pagamento</p>
                            
                            <div class="space-y-3">
                                <?php foreach ($gpxFiles as $file): ?>
                                    <div class="flex items-center justify-between p-3 <?php echo $downloadPermission['can_download'] ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200'; ?> rounded-lg border">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 <?php echo $downloadPermission['can_download'] ? 'text-blue-600' : 'text-gray-400'; ?> mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium <?php echo $downloadPermission['can_download'] ? 'text-gray-900' : 'text-gray-500'; ?>">
                                                    <?php echo htmlspecialchars($file['original_name']); ?>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    <?php echo number_format($file['file_size'] / 1024, 1); ?> KB
                                                    <?php if ($downloadPermission['can_download']): ?>
                                                        • <?php echo $file['download_count']; ?> download
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php if ($downloadPermission['can_download']): ?>
                                            <a href="/download/gpx/<?php echo $file['id']; ?>" 
                                               class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Scarica
                                            </a>
                                        <?php else: ?>
                                            <div class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                Bloccato
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($downloadPermission['can_download']): ?>
                                <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-green-800">Come usare i file GPX</p>
                                            <p class="text-xs text-green-700 mt-1">Importa il file nella tua app GPS preferita (Garmin Connect, Strava, Komoot) per seguire il percorso durante l'evento.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mt-4 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-amber-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-amber-800">File GPX Protetti</p>
                                            <p class="text-xs text-amber-700 mt-1">
                                                <?php echo $downloadPermission['reason']; ?>
                                                <br>I percorsi GPS sono disponibili solo per partecipanti che hanno completato il pagamento.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Similar Events -->
                    <div class="bg-white rounded-xl p-8 shadow-sm">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Eventi Simili</h3>
                        <div class="space-y-4">
                            <!-- Placeholder for similar events -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="text-sm font-medium text-gray-900 mb-1">Maratona di Roma</div>
                                <div class="text-xs text-gray-600 mb-2">15 Novembre 2025</div>
                                <div class="text-xs text-primary">Roma, RM</div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="text-sm font-medium text-gray-900 mb-1">Corsa al Parco</div>
                                <div class="text-xs text-gray-600 mb-2">22 Novembre 2025</div>
                                <div class="text-xs text-primary">Milano, MI</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- Event Not Found -->
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="text-center">
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.469.896-6.038 2.362l-.707.707C3.9 19.425 2.9 21.188 4.1 22.1c1.2.912 2.963-.087 4.319-1.443L12 17.071l3.581 3.586c1.356 1.356 3.119 2.355 4.319 1.443 1.2-.912.2-2.675-1.155-4.031l-.707-.707A7.962 7.962 0 0112 15z"></path>
            </svg>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Evento Non Trovato</h1>
            <p class="text-gray-600 mb-8">L'evento che stai cercando non esiste o è stato rimosso.</p>
            <a href="/events" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-dark transition-colors">
                Torna agli Eventi
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold mb-4">SportEvents</div>
                    <p class="text-gray-400">La piattaforma italiana per eventi sportivi di ogni livello.</p>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Eventi</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/events" class="hover:text-white transition-colors">Tutti gli Eventi</a></li>
                        <li><a href="/events?categoria=corsa" class="hover:text-white transition-colors">Corsa</a></li>
                        <li><a href="/events?categoria=ciclismo" class="hover:text-white transition-colors">Ciclismo</a></li>
                        <li><a href="/events?categoria=triathlon" class="hover:text-white transition-colors">Triathlon</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Azienda</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/about" class="hover:text-white transition-colors">Chi Siamo</a></li>
                        <li><a href="/contact" class="hover:text-white transition-colors">Contatti</a></li>
                        <li><a href="/privacy" class="hover:text-white transition-colors">Privacy</a></li>
                        <li><a href="/terms" class="hover:text-white transition-colors">Termini</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Supporto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/help" class="hover:text-white transition-colors">Centro Assistenza</a></li>
                        <li><a href="/faq" class="hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="/contact" class="hover:text-white transition-colors">Contattaci</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 SportEvents. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            // Add mobile menu functionality
            console.log('Mobile menu toggled');
        });

        // Forza la navigazione al form di registrazione anche se altri script interferiscono
        const APP_BASE_URL = '<?= BASE_URL ?>';
        (function() {
            const btn = document.getElementById('register-now-btn');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    // Se l'href manca o il click viene intercettato, esegui un redirect manuale
                    const id = this.getAttribute('data-event-id');
                    const expectedHref = `${APP_BASE_URL}/events/${id}/register`;
                    const currentHref = this.getAttribute('href') || '';

                    // Evita doppi invii
                    if (this.dataset.navigating === '1') return;
                    this.dataset.navigating = '1';

                    // Previeni eventuali blocchi e reindirizza in modo affidabile
                    e.preventDefault();
                    try { window.location.assign(expectedHref); } catch (err) { window.location.href = expectedHref; }
                }, { capture: true });
            }
        })();

        // Share buttons
        document.querySelectorAll('[class*="bg-blue-600"], [class*="bg-sky-500"], [class*="bg-green-600"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const platform = this.textContent.trim();
                const url = window.location.href;
                const title = document.title;
                
                let shareUrl = '';
                switch(platform) {
                    case 'Facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                        break;
                    case 'Twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
                        break;
                    case 'WhatsApp':
                        shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`;
                        break;
                }
                
                if (shareUrl) {
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                }
            });
        });
    </script>
</body>
</html>
