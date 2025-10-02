<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizione Collettiva - <?= htmlspecialchars($team['nome'] ?? 'SportEvents') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/teams.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset e base */
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            margin: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        /* Container principale */
        .registration-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header con gradiente */
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            animation: shimmer 2s linear infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .page-header h1 {
            font-size: 2.5em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 15px 0;
        }

        .team-name {
            font-size: 1.2em;
            color: #64748b;
            margin: 0 0 20px 0;
        }

        .team-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9em;
        }

        /* Sezioni del form */
        .form-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .section-title {
            font-size: 1.4em;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 25px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #667eea;
            font-size: 1.2em;
        }

        /* Form groups moderni */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.95em;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1em;
            font-family: inherit;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-control:focus + .form-label {
            color: #667eea;
        }

        /* Select styling */
        select.form-control {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 45px;
        }

        /* File input styling */
        .file-upload-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            background: rgba(248, 250, 252, 0.8);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .file-upload:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .file-upload.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }

        .file-upload-icon {
            font-size: 3em;
            color: #cbd5e0;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }

        .file-upload:hover .file-upload-icon {
            color: #667eea;
        }

        .file-upload-text {
            font-size: 1.1em;
            color: #64748b;
            font-weight: 500;
        }

        .file-upload-subtext {
            font-size: 0.9em;
            color: #94a3b8;
            margin-top: 8px;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-selected {
            background: rgba(34, 197, 94, 0.1);
            border-color: #22c55e;
        }

        .file-selected .file-upload-icon {
            color: #22c55e;
        }

        /* Template download */
        .template-download {
            background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
            border: 1px solid #81d4fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: center;
        }

        .template-download h4 {
            margin: 0 0 15px 0;
            color: #0277bd;
            font-size: 1.2em;
        }

        .template-download p {
            margin: 0 0 20px 0;
            color: #0288d1;
        }

        /* Buttons moderni */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(100, 116, 139, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(100, 116, 139, 0.4);
        }

        .btn-download {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.3);
        }

        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(14, 165, 233, 0.4);
        }

        .btn-lg {
            padding: 18px 40px;
            font-size: 1.1em;
        }

        /* Actions container */
        .form-actions {
            text-align: center;
            margin-top: 40px;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        /* Info cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .info-card {
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
        }

        .info-card .icon {
            font-size: 2em;
            margin-bottom: 15px;
            color: #667eea;
        }

        .info-card h5 {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-weight: 600;
        }

        .info-card p {
            margin: 0;
            color: #64748b;
            font-size: 0.9em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .registration-container {
                padding: 15px;
            }

            .page-header {
                padding: 30px 20px;
            }

            .page-header h1 {
                font-size: 2em;
            }

            .form-section {
                padding: 25px 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animazioni */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section {
            animation: fadeInUp 0.6s ease-out;
        }

        .form-section:nth-child(2) { animation-delay: 0.1s; }
        .form-section:nth-child(3) { animation-delay: 0.2s; }
        .form-section:nth-child(4) { animation-delay: 0.3s; }
        .form-section:nth-child(5) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="registration-container">
        <!-- Header elegante -->
        <div class="page-header">
            <h1><i class="fas fa-users-cog"></i> Iscrizione Collettiva</h1>
            <p class="team-name">
                Team: <span class="team-badge"><?= htmlspecialchars($team['nome'] ?? 'SportEvents') ?></span>
            </p>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="/teams/collective-registration/<?= $team['id'] ?? 1 ?>" method="POST" enctype="multipart/form-data">
            
            <!-- Selezione Evento -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Seleziona Evento
                </h3>
                <div class="form-group">
                    <label class="form-label" for="event_id">Evento Sportivo *</label>
                    <select name="event_id" id="event_id" class="form-control" required>
                        <option value="">🏃‍♂️ Scegli l'evento per l'iscrizione collettiva</option>
                        <?php if (isset($available_events)): ?>
                            <?php foreach ($available_events as $event): ?>
                                <option value="<?= $event['id'] ?>" 
                                        data-quota="<?= $event['prezzo_base'] ?? 0 ?>"
                                        data-location="<?= htmlspecialchars($event['luogo_partenza'] ?? '') ?>"
                                        data-date="<?= $event['data_evento'] ?? '' ?>">
                                    🏆 <?= htmlspecialchars($event['titolo'] ?? 'N/A') ?> 
                                    📅 <?= date('d/m/Y', strtotime($event['data_evento'] ?? 'now')) ?>
                                    💰 €<?= number_format($event['prezzo_base'] ?? 0, 2) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div id="event-info" class="info-grid" style="display: none;">
                    <div class="info-card">
                        <div class="icon">💰</div>
                        <h5>Quota Individuale</h5>
                        <p id="individual-price">€0.00</p>
                    </div>
                    <div class="info-card">
                        <div class="icon">📍</div>
                        <h5>Luogo</h5>
                        <p id="event-location">-</p>
                    </div>
                    <div class="info-card">
                        <div class="icon">🎯</div>
                        <h5>Sconto Gruppo</h5>
                        <p>Automatico in base al numero</p>
                    </div>
                </div>
            </div>

            <!-- Dati Responsabile -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user-tie"></i>
                    Dati del Responsabile
                </h3>
                <div class="form-group">
                    <label class="form-label" for="responsabile_nome">Nome e Cognome *</label>
                    <input type="text" 
                           name="responsabile_nome" 
                           id="responsabile_nome" 
                           class="form-control" 
                           placeholder="Inserisci nome e cognome del responsabile"
                           value="<?= htmlspecialchars($team['referente_nome'] ?? '') ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="responsabile_email">Email di Contatto *</label>
                    <input type="email" 
                           name="responsabile_email" 
                           id="responsabile_email" 
                           class="form-control" 
                           placeholder="email@esempio.com"
                           value="<?= htmlspecialchars($team['referente_email'] ?? '') ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="responsabile_telefono">Numero di Telefono *</label>
                    <input type="tel" 
                           name="responsabile_telefono" 
                           id="responsabile_telefono" 
                           class="form-control" 
                           placeholder="+39 123 456 7890"
                           value="<?= htmlspecialchars($team['referente_telefono'] ?? '') ?>" 
                           required>
                </div>
            </div>

            <!-- Upload File -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-cloud-upload-alt"></i>
                    Lista Partecipanti
                </h3>
                
                <div class="template-download">
                    <h4><i class="fas fa-file-excel"></i> Template CSV Semplificato</h4>
                    <p><strong>Formato richiesto:</strong> Solo Nome e Cognome sono obbligatori!</p>
                    <p style="font-size: 0.9em; color: #0288d1; margin: 10px 0;">
                        📋 <strong>Colonne:</strong> Nome | Cognome | Email (opzionale) | Telefono (opzionale)
                    </p>
                    <a href="/teams/download-template" class="btn btn-download">
                        <i class="fas fa-download"></i> Scarica Template
                    </a>
                </div>

                <div class="form-group">
                    <label class="form-label" for="excel_file">Carica File Partecipanti *</label>
                    <div class="file-upload-wrapper">
                        <div class="file-upload" id="file-drop-zone">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-upload-text">
                                Trascina qui il tuo file CSV
                            </div>
                            <div class="file-upload-subtext">
                                📋 <strong>Solo Nome e Cognome obbligatori!</strong> • Formati: .xlsx, .xls, .csv
                            </div>
                            <input type="file" 
                                   name="excel_file" 
                                   id="excel_file" 
                                   class="file-input" 
                                   accept=".xlsx,.xls,.csv" 
                                   required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-sticky-note"></i>
                    Note Aggiuntive
                </h3>
                <div class="form-group">
                    <label class="form-label" for="note">Richieste Speciali (opzionale)</label>
                    <textarea name="note" 
                              id="note" 
                              class="form-control" 
                              rows="4"
                              placeholder="Eventuali richieste particolari, allergie, esigenze speciali o informazioni utili per l'organizzazione dell'evento..."></textarea>
                </div>
            </div>

            <!-- Pulsanti -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> 
                    Procedi con l'Iscrizione Collettiva
                </button>
            </div>
        </form>

        <!-- Informazioni sconti -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-percentage"></i>
                Sconti di Gruppo Automatici
            </h3>
            <div class="info-grid">
                <div class="info-card">
                    <div class="icon">🥉</div>
                    <h5>5-9 Partecipanti</h5>
                    <p>5% di sconto</p>
                </div>
                <div class="info-card">
                    <div class="icon">🥈</div>
                    <h5>10-19 Partecipanti</h5>
                    <p>10% di sconto</p>
                </div>
                <div class="info-card">
                    <div class="icon">🥇</div>
                    <h5>20+ Partecipanti</h5>
                    <p>20% di sconto</p>
                </div>
            </div>
        </div>

        <div class="back-link">
            <a href="/teams/view/<?= $team['id'] ?? 1 ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> 
                Torna alla Pagina del Team
            </a>
        </div>
    </div>

    <script>
    // Gestione selezione evento
    document.getElementById('event_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const eventInfo = document.getElementById('event-info');
        
        if (this.value) {
            const quota = selectedOption.getAttribute('data-quota');
            const location = selectedOption.getAttribute('data-location');
            
            document.getElementById('individual-price').textContent = '€' + parseFloat(quota).toFixed(2);
            document.getElementById('event-location').textContent = location || 'Da definire';
            
            eventInfo.style.display = 'grid';
        } else {
            eventInfo.style.display = 'none';
        }
    });

    // Gestione drag & drop per file upload
    const dropZone = document.getElementById('file-drop-zone');
    const fileInput = document.getElementById('excel_file');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropZone.classList.add('dragover');
    }

    function unhighlight() {
        dropZone.classList.remove('dragover');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            showFileSelected(files[0]);
        }
    }

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            showFileSelected(this.files[0]);
        }
    });

    function showFileSelected(file) {
        dropZone.classList.add('file-selected');
        dropZone.querySelector('.file-upload-text').textContent = `File selezionato: ${file.name}`;
        dropZone.querySelector('.file-upload-subtext').textContent = `Dimensione: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
        dropZone.querySelector('.file-upload-icon i').className = 'fas fa-check-circle';
    }

    // Click sul drop zone per aprire file dialog
    dropZone.addEventListener('click', () => fileInput.click());
    </script>
</body>
</html>
