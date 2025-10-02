<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SportEvents' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .message-composer {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .composer-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }

        .composer-header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }

        .event-info {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .composer-form {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control.textarea {
            min-height: 150px;
            resize: vertical;
            font-family: inherit;
        }

        .recipients-info {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .recipients-info .icon {
            color: #0ea5e9;
            margin-right: 8px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-outline {
            background: transparent;
            color: #2563eb;
            border: 2px solid #2563eb;
        }

        .btn-outline:hover {
            background: #2563eb;
            color: white;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .character-count {
            font-size: 12px;
            color: #6b7280;
            text-align: right;
            margin-top: 5px;
        }

        .character-count.warning {
            color: #f59e0b;
        }

        .character-count.danger {
            color: #ef4444;
        }

        .preview-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .preview-button {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .preview-button:hover {
            background: #e5e7eb;
        }

        .previous-messages {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .message-history {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }

        .message-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 4px solid #2563eb;
        }

        .message-item:last-child {
            margin-bottom: 0;
        }

        .message-meta {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .message-subject {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .message-stats {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #059669;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            max-width: 400px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .success-message {
            background: #ecfdf5;
            border: 1px solid #10b981;
            color: #047857;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #ef4444;
            color: #dc2626;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        @media (max-width: 768px) {
            .composer-form {
                padding: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message-composer">
            <div class="composer-header">
                <h1>💬 Invia Messaggio</h1>
                <p>Comunica con tutti gli iscritti del tuo evento</p>
                
                <div class="event-info">
                    <h3>📅 <?= htmlspecialchars($event['titolo']) ?></h3>
                    <p><?= date('d/m/Y H:i', strtotime($event['data_evento'])) ?> - <?= htmlspecialchars($event['luogo_partenza']) ?></p>
                </div>
            </div>

            <div class="composer-form">
                <div class="success-message" id="successMessage">
                    <strong>✅ Messaggio inviato con successo!</strong>
                    <div id="sendStats"></div>
                </div>

                <div class="error-message" id="errorMessage">
                    <strong>❌ Errore nell'invio del messaggio</strong>
                    <div id="errorDetails"></div>
                </div>

                <div class="recipients-info">
                    <span class="icon">👥</span>
                    <strong><?= $participants_count ?> destinatari</strong> - Il messaggio sarà inviato a tutti gli iscritti confermati per questo evento
                </div>

                <form id="messageForm">
                    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                    
                    <div class="form-group">
                        <label for="subject">
                            📝 Oggetto del messaggio *
                        </label>
                        <input 
                            type="text" 
                            id="subject" 
                            name="subject" 
                            class="form-control" 
                            placeholder="Es: Informazioni importanti per la gara"
                            maxlength="200"
                            required
                        >
                        <div class="character-count" id="subjectCount">0/200 caratteri</div>
                    </div>

                    <div class="form-group">
                        <label for="message">
                            📄 Messaggio *
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            class="form-control textarea" 
                            placeholder="Scrivi qui il tuo messaggio...&#10;&#10;Esempi:&#10;- Aggiornamenti su orari o percorso&#10;- Informazioni logistiche&#10;- Comunicazioni meteo&#10;- Modifiche al programma"
                            maxlength="5000"
                            required
                        ></textarea>
                        <div class="character-count" id="messageCount">0/5000 caratteri</div>
                    </div>

                    <div class="button-group">
                        <div>
                            <button type="button" class="btn btn-outline preview-button" id="previewBtn">
                                👁️ Anteprima Email
                            </button>
                        </div>
                        
                        <div style="display: flex; gap: 15px;">
                            <a href="/events/<?= $event['event_id'] ?>/statistics" class="btn btn-secondary">
                                ← Torna alle Statistiche
                            </a>
                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                🚀 Invia Messaggio
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($previous_messages)): ?>
                <div class="previous-messages">
                    <h3>📬 Messaggi Precedenti</h3>
                    <div class="message-history">
                        <?php foreach ($previous_messages as $msg): ?>
                        <div class="message-item">
                            <div class="message-meta">
                                <?= date('d/m/Y H:i', strtotime($msg['sent_at'])) ?>
                            </div>
                            <div class="message-subject">
                                <?= htmlspecialchars($msg['subject']) ?>
                            </div>
                            <div class="message-stats">
                                <span>✅ Inviati: <?= $msg['sent_count'] ?></span>
                                <?php if ($msg['failed_count'] > 0): ?>
                                <span style="color: #dc2626;">❌ Falliti: <?= $msg['failed_count'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h3>Invio in corso...</h3>
            <p>Stiamo inviando il messaggio a tutti i partecipanti. Questo potrebbe richiedere alcuni minuti.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('messageForm');
            const subjectInput = document.getElementById('subject');
            const messageTextarea = document.getElementById('message');
            const subjectCount = document.getElementById('subjectCount');
            const messageCount = document.getElementById('messageCount');
            const sendBtn = document.getElementById('sendBtn');
            const previewBtn = document.getElementById('previewBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');

            // Character counters
            function updateCharacterCount(input, counter, max) {
                const count = input.value.length;
                counter.textContent = `${count}/${max} caratteri`;
                
                if (count > max * 0.9) {
                    counter.className = 'character-count danger';
                } else if (count > max * 0.8) {
                    counter.className = 'character-count warning';
                } else {
                    counter.className = 'character-count';
                }
            }

            subjectInput.addEventListener('input', () => {
                updateCharacterCount(subjectInput, subjectCount, 200);
            });

            messageTextarea.addEventListener('input', () => {
                updateCharacterCount(messageTextarea, messageCount, 5000);
            });

            // Form validation
            function validateForm() {
                const subject = subjectInput.value.trim();
                const message = messageTextarea.value.trim();
                
                if (!subject || !message) {
                    return false;
                }
                
                if (subject.length > 200 || message.length > 5000) {
                    return false;
                }
                
                return true;
            }

            // Enable/disable send button
            function updateSendButton() {
                sendBtn.disabled = !validateForm();
            }

            subjectInput.addEventListener('input', updateSendButton);
            messageTextarea.addEventListener('input', updateSendButton);

            // Preview email
            previewBtn.addEventListener('click', function() {
                const subject = encodeURIComponent(subjectInput.value || 'Oggetto del messaggio');
                const message = encodeURIComponent(messageTextarea.value || 'Contenuto del messaggio...');
                const url = `/messages/preview/<?= $event['event_id'] ?>?subject=${subject}&message=${message}`;
                
                window.open(url, '_blank', 'width=800,height=600,scrollbars=yes');
            });

            // Form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!validateForm()) {
                    alert('Compila tutti i campi obbligatori');
                    return;
                }

                // Show loading
                loadingOverlay.style.display = 'flex';
                sendBtn.disabled = true;
                
                // Hide previous messages
                successMessage.style.display = 'none';
                errorMessage.style.display = 'none';

                try {
                    const formData = new FormData(form);
                    const response = await fetch('/messages/send', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Show success message
                        document.getElementById('sendStats').innerHTML = `
                            <div style="margin-top: 10px;">
                                <div>📧 <strong>${result.total_recipients}</strong> destinatari totali</div>
                                <div>✅ <strong>${result.sent_count}</strong> messaggi inviati</div>
                                ${result.failed_count > 0 ? `<div>❌ <strong>${result.failed_count}</strong> invii falliti</div>` : ''}
                            </div>
                        `;
                        successMessage.style.display = 'block';
                        
                        // Reset form
                        form.reset();
                        updateCharacterCount(subjectInput, subjectCount, 200);
                        updateCharacterCount(messageTextarea, messageCount, 5000);
                        
                        // Scroll to success message
                        successMessage.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        // Show error message
                        document.getElementById('errorDetails').textContent = result.error;
                        errorMessage.style.display = 'block';
                        errorMessage.scrollIntoView({ behavior: 'smooth' });
                    }
                } catch (error) {
                    // Show error message
                    document.getElementById('errorDetails').textContent = 'Errore di connessione. Riprova.';
                    errorMessage.style.display = 'block';
                    errorMessage.scrollIntoView({ behavior: 'smooth' });
                } finally {
                    // Hide loading
                    loadingOverlay.style.display = 'none';
                    sendBtn.disabled = false;
                    updateSendButton();
                }
            });

            // Initial setup
            updateSendButton();
        });
    </script>
</body>
</html>
