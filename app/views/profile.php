<?php
// Carica dati utente con documenti e storico
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user->id = $_SESSION['user_id'];
$userData = $user->readOne();

// Carica storico iscrizioni
$registrationHistory = $user->getRegistrationHistory();

// Carica ricevute
$receipt = new Receipt($db);
$receipts = $receipt->getUserReceipts($_SESSION['user_id']);

// Determina tab attiva
$activeTab = $_GET['tab'] ?? 'profile';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Personale - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
    require_once __DIR__ . '/components/navbar.php';
    renderNavbar('profile'); 
    ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Area Personale</h1>
            <p class="text-gray-600 mt-2">Gestisci il tuo profilo, documenti e iscrizioni</p>
        </div>

        <!-- Notifications -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-600"><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600"><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1">
                <nav class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <ul class="space-y-2">
                        <li>
                            <a href="/profile?tab=profile" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?= $activeTab === 'profile' ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' ?>">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Dati Personali
                            </a>
                        </li>
                        <li>
                            <a href="/profile?tab=documents" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?= $activeTab === 'documents' ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' ?>">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Documenti
                                <?php if ($user->isCertificatoScaduto()): ?>
                                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full">Scaduto</span>
                                <?php elseif ($user->giorniScadenzaCertificato() !== null && $user->giorniScadenzaCertificato() <= 30): ?>
                                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-600 text-xs rounded-full"><?= $user->giorniScadenzaCertificato() ?>gg</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="/profile?tab=registrations" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?= $activeTab === 'registrations' ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' ?>">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Le Mie Iscrizioni
                                <span class="ml-2 px-2 py-1 bg-primary-100 text-primary-600 text-xs rounded-full"><?= count($registrationHistory) ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="/profile?tab=receipts" 
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?= $activeTab === 'receipts' ? 'bg-primary-50 text-primary-600' : 'text-gray-600 hover:bg-gray-50' ?>">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                                </svg>
                                Ricevute
                                <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full"><?= count($receipts) ?></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <?php if ($activeTab === 'profile'): ?>
                        <!-- TAB: Dati Personali -->
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Dati Personali</h2>
                        
                        <form method="POST" action="/profile" class="space-y-6">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                                    <input type="text" name="nome" value="<?= htmlspecialchars($userData['nome'] ?? '') ?>" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                                    <input type="text" name="cognome" value="<?= htmlspecialchars($userData['cognome'] ?? '') ?>" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefono *</label>
                                    <input type="tel" name="cellulare" value="<?= htmlspecialchars($userData['cellulare'] ?? '') ?>" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Data di Nascita *</label>
                                    <input type="date" name="data_nascita" value="<?= $userData['data_nascita'] ?? '' ?>" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sesso *</label>
                                    <select name="sesso" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="M" <?= ($userData['sesso'] ?? '') === 'M' ? 'selected' : '' ?>>Maschio</option>
                                        <option value="F" <?= ($userData['sesso'] ?? '') === 'F' ? 'selected' : '' ?>>Femmina</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                                    Aggiorna Profilo
                                </button>
                            </div>
                        </form>

                        <!-- Change Password Section -->
                        <div class="mt-12 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Cambia Password</h3>
                            <form method="POST" action="/profile" class="max-w-md space-y-4">
                                <input type="hidden" name="action" value="change_password">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Attuale</label>
                                    <input type="password" name="current_password" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nuova Password</label>
                                    <input type="password" name="new_password" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Conferma Nuova Password</label>
                                    <input type="password" name="confirm_password" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                    Cambia Password
                                </button>
                            </form>
                        </div>

                    <?php elseif ($activeTab === 'documents'): ?>
                        <!-- TAB: Documenti -->
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">I Miei Documenti</h2>
                        
                        <!-- Certificato Medico -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificato Medico</h3>
                            
                            <?php if (!empty($userData['certificato_medico'])): ?>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-green-900">Certificato Caricato</h4>
                                            <p class="text-sm text-green-700">
                                                Tipo: <?= ucfirst($userData['tipo_certificato'] ?? 'Non specificato') ?>
                                                <?php if (!empty($userData['scadenza_certificato'])): ?>
                                                    • Scadenza: <?= date('d/m/Y', strtotime($userData['scadenza_certificato'])) ?>
                                                    <?php if ($user->isCertificatoScaduto()): ?>
                                                        <span class="text-red-600 font-medium">• SCADUTO</span>
                                                    <?php elseif ($user->giorniScadenzaCertificato() <= 30): ?>
                                                        <span class="text-yellow-600 font-medium">• Scade tra <?= $user->giorniScadenzaCertificato() ?> giorni</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="/uploads/<?= $userData['certificato_medico'] ?>" target="_blank"
                                               class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                                Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="/profile" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="action" value="upload_certificate">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Certificato</label>
                                        <select name="tipo_certificato" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            <option value="">Seleziona tipo</option>
                                            <option value="agonistico">Agonistico</option>
                                            <option value="non_agonistico">Non Agonistico</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Data Scadenza (opzionale)</label>
                                        <input type="date" name="scadenza_certificato"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">File Certificato</label>
                                    <input type="file" name="certificato_medico" accept=".pdf,.jpg,.jpeg,.png" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">Formati supportati: PDF, JPG, PNG (max 5MB)</p>
                                </div>
                                
                                <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                                    <?= !empty($userData['certificato_medico']) ? 'Aggiorna' : 'Carica' ?> Certificato
                                </button>
                            </form>
                        </div>

                        <!-- Tessera Affiliazione -->
                        <div class="pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tessera di Affiliazione</h3>
                            
                            <?php if (!empty($userData['tessera_affiliazione'])): ?>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-blue-900">Tessera Caricata</h4>
                                            <p class="text-sm text-blue-700">Documento valido caricato</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="/uploads/<?= $userData['tessera_affiliazione'] ?>" target="_blank"
                                               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                                Visualizza
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="/profile" enctype="multipart/form-data" class="space-y-4">
                                <input type="hidden" name="action" value="upload_card">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">File Tessera</label>
                                    <input type="file" name="tessera_affiliazione" accept=".pdf,.jpg,.jpeg,.png" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">Formati supportati: PDF, JPG, PNG (max 5MB)</p>
                                </div>
                                
                                <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                                    <?= !empty($userData['tessera_affiliazione']) ? 'Aggiorna' : 'Carica' ?> Tessera
                                </button>
                            </form>
                        </div>

                    <?php elseif ($activeTab === 'registrations'): ?>
                        <!-- TAB: Le Mie Iscrizioni -->
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Le Mie Iscrizioni</h2>
                        
                        <?php if (!empty($registrationHistory)): ?>
                            <div class="space-y-6">
                                <?php foreach ($registrationHistory as $registration): ?>
                                    <div class="border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($registration['event_title']) ?></h3>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                                <?php 
                                                switch($registration['status'] ?? 'pending') {
                                                    case 'confermata': echo 'bg-green-100 text-green-600'; break;
                                                    case 'pagata': echo 'bg-blue-100 text-blue-600'; break;
                                                    case 'pending': echo 'bg-yellow-100 text-yellow-600'; break;
                                                    case 'annullata': echo 'bg-red-100 text-red-600'; break;
                                                    default: echo 'bg-gray-100 text-gray-600';
                                                }
                                                ?>">
                                                <?php
                                                $statusLabels = [
                                                    'confermata' => 'Confermata',
                                                    'pagata' => 'Pagata',  
                                                    'pending' => 'In Attesa',
                                                    'annullata' => 'Annullata'
                                                ];
                                                echo $statusLabels[$registration['status'] ?? 'pending'] ?? 'Sconosciuto';
                                                ?>
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-4">
                                            <div>
                                                <strong>Data Evento:</strong><br>
                                                <?= date('d/m/Y H:i', strtotime($registration['data_evento'])) ?>
                                            </div>
                                            <div>
                                                <strong>Luogo:</strong><br>
                                                <?= htmlspecialchars($registration['luogo_partenza']) ?>
                                            </div>
                                            <div>
                                                <strong>Sport:</strong><br>
                                                <?= ucfirst(htmlspecialchars($registration['sport'])) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-lg font-bold text-primary-600">€<?= number_format($registration['prezzo_pagato'], 2) ?></span>
                                                <span class="text-sm text-gray-500 ml-2">Iscritto il <?= $registration['created_at'] ? date('d/m/Y', strtotime($registration['created_at'])) : 'Data non disponibile' ?></span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="/events/<?= $registration['event_id'] ?>" 
                                                   class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                                    Dettagli Evento
                                                </a>
                                                <?php if (!empty($registration['receipt_number'])): ?>
                                                    <a href="/uploads/<?= $registration['receipt_pdf'] ?>" target="_blank"
                                                       class="bg-primary-100 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-200 transition-colors">
                                                        Ricevuta
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="text-gray-400 text-6xl mb-4">🏃‍♂️</div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna iscrizione ancora</h3>
                                <p class="text-gray-600 mb-6">Inizia a partecipare agli eventi sportivi più entusiasmanti!</p>
                                <a href="/events" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                                    Esplora Eventi
                                </a>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($activeTab === 'receipts'): ?>
                        <!-- TAB: Ricevute -->
                        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Le Mie Ricevute</h2>
                        
                        <?php if (!empty($receipts)): ?>
                            <div class="space-y-4">
                                <?php foreach ($receipts as $receipt): ?>
                                    <div class="border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="font-semibold text-gray-900">Ricevuta <?= htmlspecialchars($receipt['receipt_number']) ?></h3>
                                                <p class="text-sm text-gray-600"><?= htmlspecialchars($receipt['event_title']) ?></p>
                                                <p class="text-sm text-gray-500">Data: <?= date('d/m/Y H:i', strtotime($receipt['created_at'])) ?></p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-gray-900">€<?= number_format($receipt['amount'], 2) ?></div>
                                                <div class="text-sm text-gray-500"><?= ucfirst($receipt['payment_method'] ?? 'Metodo non specificato') ?></div>
                                                <?php if (!empty($receipt['pdf_file'])): ?>
                                                    <a href="/uploads/<?= $receipt['pdf_file'] ?>" target="_blank"
                                                       class="inline-flex items-center mt-2 text-primary-600 hover:text-primary-700 text-sm font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        Scarica PDF
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="text-gray-400 text-6xl mb-4">🧾</div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna ricevuta ancora</h3>
                                <p class="text-gray-600">Le ricevute dei tuoi pagamenti appariranno qui.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
