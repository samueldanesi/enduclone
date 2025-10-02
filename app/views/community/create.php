<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Post - Community SportEvents</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .create-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 1.2rem;
        }

        .create-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .post-type-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .type-option {
            padding: 20px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: white;
        }

        .type-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .type-option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .type-option i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        .event-selector {
            display: none;
        }

        .event-selector.show {
            display: block;
        }

        .media-upload {
            border: 2px dashed #ccc;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .media-upload:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }

        .media-upload.dragover {
            border-color: #667eea;
            background: #e3f2fd;
        }

        .media-preview {
            margin-top: 15px;
            display: none;
        }

        .media-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: space-between;
            margin-top: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #ffe6e6;
            border: 1px solid #ffcdd2;
            color: #c62828;
        }

        .alert-success {
            background: #e8f5e8;
            border: 1px solid #c8e6c9;
            color: #2e7d32;
        }

        @media (max-width: 768px) {
            .create-container {
                padding: 15px;
            }
            
            .create-card {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="create-container">
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Crea Nuovo Post</h1>
            <p>Condividi la tua esperienza sportiva con la community</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="create-card">
            <form method="POST" action="/community/create" enctype="multipart/form-data" id="createPostForm">
                <!-- Tipo di Post -->
                <div class="form-group">
                    <label>Tipo di Post</label>
                    <div class="post-type-selector">
                        <div class="type-option selected" data-type="general">
                            <i class="fas fa-comment-dots"></i>
                            <div>Post Generale</div>
                            <small>Condividi nella community universale</small>
                        </div>
                    </div>
                    <input type="hidden" name="type" id="postType" value="general">
                    
                    <div style="margin-top: 15px; padding: 15px; background: #e3f2fd; border-radius: 10px; border-left: 4px solid #2196f3;">
                        <strong><i class="fas fa-info-circle"></i> Community Universale</strong><br>
                        <small>I post creati qui saranno visibili a tutti gli utenti della piattaforma. 
                        Per contenuti specifici di un evento, vai alla pagina dell'evento e usa la sua community dedicata.</small>
                    </div>
                </div>

                <!-- Selezione Evento (NASCOSTO per community universale) -->
                <input type="hidden" name="event_id" value="">

                <!-- Titolo -->
                <div class="form-group">
                    <label for="title">Titolo (opzionale)</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           class="form-control" 
                           placeholder="Aggiungi un titolo al tuo post..."
                           value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                </div>

                <!-- Contenuto -->
                <div class="form-group">
                    <label for="content">Contenuto *</label>
                    <textarea name="content" 
                              id="content" 
                              class="form-control" 
                              placeholder="Cosa vuoi condividere con la community?"
                              required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
                </div>

                <!-- Upload Media -->
                <div class="form-group">
                    <label>Aggiungi Foto/Video (opzionale)</label>
                    <div class="media-upload" id="mediaUpload">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                        <p>Trascina un file qui o clicca per selezionare</p>
                        <small>Formati supportati: JPG, PNG, GIF, MP4 (max 10MB)</small>
                        <input type="file" 
                               name="media" 
                               id="mediaFile" 
                               accept="image/*,video/*" 
                               style="display: none;">
                    </div>
                    <div class="media-preview" id="mediaPreview">
                        <img id="previewImage" style="display: none;">
                        <video id="previewVideo" controls style="display: none; max-width: 100%; max-height: 300px;"></video>
                    </div>
                </div>

                <!-- Didascalia Media -->
                <div class="form-group" id="captionGroup" style="display: none;">
                    <label for="media_caption">Didascalia Media</label>
                    <input type="text" 
                           name="media_caption" 
                           id="media_caption" 
                           class="form-control" 
                           placeholder="Aggiungi una didascalia..."
                           value="<?= htmlspecialchars($old['media_caption'] ?? '') ?>">
                </div>

                <!-- Pulsanti Azione -->
                <div class="action-buttons">
                    <a href="/community" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Pubblica Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Gestione tipo post (SOLO GENERALE per community universale)
        // Nessuna selezione necessaria - sempre post generale

        // Gestione upload media
        const mediaUpload = document.getElementById('mediaUpload');
        const mediaFile = document.getElementById('mediaFile');
        const mediaPreview = document.getElementById('mediaPreview');
        const previewImage = document.getElementById('previewImage');
        const previewVideo = document.getElementById('previewVideo');
        const captionGroup = document.getElementById('captionGroup');

        mediaUpload.addEventListener('click', () => mediaFile.click());

        mediaUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            mediaUpload.classList.add('dragover');
        });

        mediaUpload.addEventListener('dragleave', () => {
            mediaUpload.classList.remove('dragover');
        });

        mediaUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            mediaUpload.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                mediaFile.files = files;
                handleFileSelect(files[0]);
            }
        });

        mediaFile.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            // Verifica dimensione (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                alert('Il file è troppo grande. Dimensione massima: 10MB');
                mediaFile.value = '';
                return;
            }

            // Verifica tipo
            if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                alert('Formato file non supportato. Usa JPG, PNG, GIF o MP4.');
                mediaFile.value = '';
                return;
            }

            // Mostra anteprima
            const reader = new FileReader();
            reader.onload = function(e) {
                mediaPreview.style.display = 'block';
                captionGroup.style.display = 'block';
                
                if (file.type.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewVideo.style.display = 'none';
                } else {
                    previewVideo.src = e.target.result;
                    previewVideo.style.display = 'block';
                    previewImage.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }

        // Validazione form
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            const content = document.getElementById('content').value.trim();
            const postType = document.getElementById('postType').value;
            const eventId = document.getElementById('event_id').value;
            
            if (!content) {
                e.preventDefault();
                alert('Il contenuto del post è obbligatorio');
                return;
            }
            
            if ((postType === 'event_experience' || postType === 'event_photo') && !eventId) {
                e.preventDefault();
                alert('Seleziona un evento per questo tipo di post');
                return;
            }
            
            // Disabilita pulsante per evitare doppi invii
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</body>
</html>