<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Team - <?= htmlspecialchars($team['nome']) ?></title>
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .chat-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .team-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .chat-box {
            height: 400px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow-y: auto;
            padding: 15px;
            background: #f9f9f9;
            margin-bottom: 20px;
        }
        
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .message-header {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        .message-content {
            line-height: 1.4;
        }
        
        .message.event-request {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
        }
        
        .message.my-message {
            background: #f3e5f5;
            border-left: 4px solid #9c27b0;
        }
        
        .message-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .message-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
            min-height: 40px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .event-request-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
        
        .event-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .participants-count {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .event-request-form {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Header del Team -->
        <div class="team-header">
            <h1><?= htmlspecialchars($team['nome']) ?></h1>
            <?php if (!empty($team['email'])): ?>
            <p>📧 <?= htmlspecialchars($team['email']) ?></p>
            <?php endif; ?>
            <p><strong>Status:</strong> <?= ucfirst($team['status']) ?></p>
        </div>
        
        <!-- Messaggi di Sistema -->
        <div id="system-messages"></div>
        
        <!-- Chat Box -->
        <div class="chat-box" id="chat-box">
            <?php if (empty($messages)): ?>
                <div class="message">
                    <div class="message-content">
                        <em>💬 Nessun messaggio ancora. Inizia la conversazione!</em>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?= $message['message_type'] === 'richiesta_evento' ? 'event-request' : '' ?> <?= $message['user_id'] == $_SESSION['user_id'] ? 'my-message' : '' ?>">
                        <div class="message-header">
                            <strong><?= htmlspecialchars($message['nome_utente']) ?></strong>
                            <span style="float: right;"><?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></span>
                        </div>
                        <div class="message-content">
                            <?php if ($message['message_type'] === 'richiesta_evento' && !empty($message['nome_evento'])): ?>
                                <div class="event-info">
                                    <strong>🏃 Richiesta Evento: <?= htmlspecialchars($message['nome_evento']) ?></strong>
                                    <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                                </div>
                            <?php else: ?>
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Form per Messaggi -->
        <form class="message-form" onsubmit="sendMessage(event)">
            <textarea class="message-input" placeholder="Scrivi un messaggio..." name="messaggio" rows="2" required></textarea>
            <button type="submit" class="btn btn-primary">Invia</button>
        </form>
        
        <!-- Form per Richiesta Evento (solo admin) -->
        <?php if ($isTeamAdmin ?? false): ?>
            <div class="event-request-form">
                <h3>📅 Proponi un Evento al Team</h3>
                <form onsubmit="sendEventRequest(event)">
                    <div class="form-group">
                        <label class="form-label">Seleziona Evento:</label>
                        <select name="event_id" class="form-control" required>
                            <option value="">-- Seleziona un evento --</option>
                            <?php
                            // Carica eventi disponibili
                            $eventModel = new Event();
                            $events = $eventModel->readAll();
                            foreach ($events as $event):
                            ?>
                                <option value="<?= $event['id'] ?>"><?= htmlspecialchars($event['nome']) ?> - <?= date('d/m/Y', strtotime($event['data_evento'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Messaggio:</label>
                        <textarea name="messaggio" class="form-control" rows="3" placeholder="Descrivi perché questo evento è interessante per il team..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">🏃 Proponi Evento</button>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Link di navigazione -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="/teams" class="btn btn-secondary">← Torna ai Team</a>
            <?php if ($isTeamAdmin ?? false): ?>
                <a href="/teams/manage-requests?team_id=<?= $team['id'] ?>" class="btn btn-info">Gestisci Richieste</a>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Invia messaggio
        function sendMessage(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            formData.append('team_id', <?= $team['id'] ?>);
            
            fetch('/teams/send-message', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    form.reset();
                    // Ricarica la chat dopo 1 secondo
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Errore di connessione', 'error');
            });
        }
        
        // Invia richiesta evento
        function sendEventRequest(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            formData.append('team_id', <?= $team['id'] ?>);
            
            fetch('/teams/send-event-request', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    form.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Errore di connessione', 'error');
            });
        }
        
        // Rispondi ad evento
        function respondToEvent(requestId, response) {
            const formData = new FormData();
            formData.append('request_id', requestId);
            formData.append('response', response);
            
            fetch('/teams/respond-event', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Errore di connessione', 'error');
            });
        }
        
        // Mostra messaggi di sistema
        function showMessage(message, type) {
            const container = document.getElementById('system-messages');
            const div = document.createElement('div');
            div.className = type === 'success' ? 'success-message' : 'error-message';
            div.textContent = message;
            container.appendChild(div);
            
            // Rimuovi dopo 5 secondi
            setTimeout(() => {
                div.remove();
            }, 5000);
        }
        
        // Auto-scroll della chat
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
        
        // Auto-refresh ogni 30 secondi
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
