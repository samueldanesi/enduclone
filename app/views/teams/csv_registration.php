<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizione CSV - <?= htmlspecialchars($team['nome']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            backdrop-filter: blur(20px);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: #666;
            font-size: 1.2rem;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }

        .step {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 15px 25px;
            border-radius: 10px;
            margin: 0 10px;
            font-weight: 600;
            color: #6c757d;
        }

        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .instructions {
            background: #e3f2fd;
            border-left: 5px solid #2196f3;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .instructions h3 {
            color: #1976d2;
            margin-bottom: 15px;
        }

        .instructions ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 8px;
            color: #424242;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .file-upload {
            border: 3px dashed #667eea;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload:hover {
            background: #f0f2ff;
            border-color: #5a67d8;
        }

        .file-upload.dragover {
            background: #e6f3ff;
            border-color: #3182ce;
        }

        .file-upload i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
        }

        .template-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .template-info h4 {
            color: #856404;
            margin-bottom: 10px;
        }

        .file-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-csv"></i> Iscrizione CSV</h1>
            <p class="subtitle">Team: <strong><?= htmlspecialchars($team['nome']) ?></strong></p>
        </div>

        <div class="step-indicator">
            <div class="step active">
                <i class="fas fa-download"></i>
                <span>1. Scarica Template</span>
            </div>
            <div class="step active">
                <i class="fas fa-edit"></i>
                <span>2. Compila Dati</span>
            </div>
            <div class="step active">
                <i class="fas fa-upload"></i>
                <span>3. Carica File</span>
            </div>
        </div>

        <div class="instructions">
            <h3><i class="fas fa-info-circle"></i> Istruzioni per l'uso</h3>
            <ol>
                <li>Scarica il template CSV cliccando sul pulsante qui sotto</li>
                <li>Apri il file con Excel, Google Sheets o un editor di testo</li>
                <li>Compila i dati dei partecipanti seguendo il formato indicato</li>
                <li>Salva il file in formato CSV</li>
                <li>Carica il file compilato usando il form sottostante</li>
            </ol>
        </div>

        <div class="template-info">
            <h4><i class="fas fa-file-download"></i> Template CSV</h4>
            <p>Scarica il template con le colonne corrette per inserire i dati dei partecipanti.</p>
            <a href="/teams/download-csv-template" class="btn btn-success" target="_blank">
                <i class="fas fa-download"></i> Scarica Template CSV
            </a>
        </div>

        <form method="POST" enctype="multipart/form-data" id="csvForm">
            <div class="form-group">
                <label for="event_id">Seleziona Evento</label>
                <select name="event_id" id="event_id" class="form-control" required>
                    <option value="">-- Scegli un evento --</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>" data-price="<?= $event['prezzo_base'] ?>">
                            <?= htmlspecialchars($event['titolo']) ?> - 
                            €<?= number_format($event['prezzo_base'], 2) ?> - 
                            <?= date('d/m/Y', strtotime($event['data_evento'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>File CSV con partecipanti</label>
                <div class="file-upload" onclick="document.getElementById('csvFile').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h4>Trascina qui il file CSV o clicca per selezionare</h4>
                    <p>Formato supportato: .csv (massimo 5MB)</p>
                    <input type="file" name="csv_file" id="csvFile" accept=".csv" required style="display: none;">
                </div>
                <div class="file-info" id="fileInfo"></div>
            </div>

            <div class="form-group">
                <label for="note">Note aggiuntive</label>
                <textarea name="note" id="note" class="form-control" rows="3" placeholder="Note opzionali per l'iscrizione..."></textarea>
            </div>

            <div class="actions">
                <a href="/teams/collective-registrations/<?= $team['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Indietro
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-upload"></i> Carica e Processa CSV
                </button>
            </div>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('csvFile');
        const fileInfo = document.getElementById('fileInfo');
        const submitBtn = document.getElementById('submitBtn');
        const fileUpload = document.querySelector('.file-upload');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                showFileInfo(file);
            }
        });

        function showFileInfo(file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            fileInfo.innerHTML = `
                <strong><i class="fas fa-file"></i> File selezionato:</strong> ${file.name}<br>
                <strong>Dimensione:</strong> ${fileSize} MB<br>
                <strong>Tipo:</strong> ${file.type}
            `;
            fileInfo.style.display = 'block';
            submitBtn.disabled = false;
        }

        // Drag & Drop functionality
        fileUpload.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        fileUpload.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        fileUpload.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showFileInfo(files[0]);
            }
        });
    </script>
</body>
</html>