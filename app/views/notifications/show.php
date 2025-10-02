<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SportEvents' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .notification-detail {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .notification-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
        }

        .notification-header h1 {
            margin: 0 0 15px 0;
            font-size: 1.8rem;
        }

        .notification-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .meta-item {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
        }

        .meta-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .meta-value {
            font-size: 16px;
            font-weight: 600;
        }

        .notification-content {
            padding: 40px;
        }

        .event-card {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .event-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 10px;
        }

        .event-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            color: #0369a1;
        }

        .event-detail {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message-content {
            background: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            line-height: 1.8;
            color: #374151;
            white-space: pre-line;
            border-left: 4px solid #2563eb;
        }

        .organizer-info {
            background: #ecfdf5;
            border: 1px solid #10b981;
            border-radius: 8px;
            padding: 15px;
            margin-top: 30px;
        }

        .organizer-info h4 {
            margin: 0 0 10px 0;
            color: #047857;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
            flex-wrap: wrap;
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

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .read-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .read-status.read {
            background: #dcfce7;
            color: #166534;
        }

        .read-status.unread {
            background: #dbeafe;
            color: #1e40af;
        }

        .timestamp {
            font-size: 12px;
            color: #6b7280;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }

        @media (max-width: 768px) {
            .notification-content {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .notification-meta {
                grid-template-columns: 1fr;
            }
            
            .event-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="notification-detail">
            <div class="notification-header">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h1>📧 <?= htmlspecialchars($notification['subject']) ?></h1>
                        <div class="read-status <?= $notification['is_read'] ? 'read' : 'unread' ?>">
                            <?= $notification['is_read'] ? '✅ Letto' : '📩 Non Letto' ?>
                        </div>
                    </div>
                </div>
                
                <div class="notification-meta">
                    <div class="meta-item">
                        <div class="meta-label">Ricevuto il</div>
                        <div class="meta-value">
                            <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?>
                        </div>
                    </div>
                    <?php if ($notification['read_at']): ?>
                    <div class="meta-item">
                        <div class="meta-label">Letto il</div>
                        <div class="meta-value">
                            <?= date('d/m/Y H:i', strtotime($notification['read_at'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="meta-item">
                        <div class="meta-label">Da</div>
                        <div class="meta-value">
                            <?= htmlspecialchars($notification['organizer_name'] . ' ' . $notification['organizer_surname']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="notification-content">
                <!-- Event Information -->
                <div class="event-card">
                    <div class="event-title">
                        📅 <?= htmlspecialchars($notification['event_title']) ?>
                    </div>
                    <div class="event-details">
                        <div class="event-detail">
                            <span>🕐</span>
                            <span><?= date('d/m/Y H:i', strtotime($notification['data_evento'])) ?></span>
                        </div>
                        <div class="event-detail">
                            <span>📍</span>
                            <span><?= htmlspecialchars($notification['luogo_partenza']) ?></span>
                        </div>
                        <div class="event-detail">
                            <span>🔗</span>
                            <a href="/events/<?= $notification['event_id'] ?>" class="text-blue-600 hover:underline">
                                Vai all'Evento
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Message Content -->
                <div>
                    <h3 style="margin-bottom: 15px; color: #374151;">📝 Messaggio</h3>
                    <div class="message-content">
                        <?= nl2br(htmlspecialchars($notification['message'])) ?>
                    </div>
                </div>

                <!-- Organizer Info -->
                <div class="organizer-info">
                    <h4>👤 Organizzatore</h4>
                    <p><?= htmlspecialchars($notification['organizer_name'] . ' ' . $notification['organizer_surname']) ?></p>
                    <small style="color: #047857;">
                        Questo messaggio è stato inviato dall'organizzatore dell'evento per tenerti aggiornato.
                    </small>
                </div>

                <div class="action-buttons">
                    <div>
                        <a href="/notifications" class="btn btn-secondary">
                            ← Tutte le Notifiche
                        </a>
                    </div>
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="/events/<?= $notification['event_id'] ?>" class="btn btn-primary">
                            📅 Visualizza Evento
                        </a>
                        
                        <?php if (!$notification['is_read']): ?>
                        <button id="markReadBtn" class="btn btn-outline" onclick="markAsRead()">
                            ✅ Segna Come Letta
                        </button>
                        <?php endif; ?>
                        
                        <button id="deleteBtn" class="btn btn-danger" onclick="deleteNotification()">
                            🗑️ Elimina
                        </button>
                    </div>
                </div>

                <div class="timestamp">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                        <span>
                            📤 Inviato: <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?>
                        </span>
                        <?php if ($notification['read_at']): ?>
                        <span>
                            👁️ Aperto: <?= date('d/m/Y H:i', strtotime($notification['read_at'])) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Marca come letta
        async function markAsRead() {
            const btn = document.getElementById('markReadBtn');
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-block;width:12px;height:12px;border:2px solid #fff;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;"></span> Elaborazione...';

            try {
                const response = await fetch(`/notifications/read/<?= $notification['id'] ?>`, {
                    method: 'POST'
                });
                const result = await response.json();
                
                if (result.success) {
                    // Aggiorna UI
                    location.reload();
                } else {
                    alert('Errore nel segnare come letta');
                }
            } catch (error) {
                alert('Errore di connessione');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '✅ Segna Come Letta';
            }
        }

        // Elimina notifica
        async function deleteNotification() {
            if (!confirm('Sei sicuro di voler eliminare questa notifica?\nNon potrai più recuperarla.')) {
                return;
            }

            const btn = document.getElementById('deleteBtn');
            btn.disabled = true;
            btn.innerHTML = '<span style="display:inline-block;width:12px;height:12px;border:2px solid #fff;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;"></span> Eliminazione...';

            try {
                const response = await fetch(`/notifications/delete/<?= $notification['id'] ?>`, {
                    method: 'DELETE'
                });
                const result = await response.json();
                
                if (result.success) {
                    // Reindirizza alle notifiche
                    window.location.href = '/notifications';
                } else {
                    alert('Errore nell\'eliminazione');
                }
            } catch (error) {
                alert('Errore di connessione');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '🗑️ Elimina';
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.location.href = '/notifications';
            }
            if (e.key === 'r' && !<?= $notification['is_read'] ? 'true' : 'false' ?>) {
                markAsRead();
            }
            if (e.key === 'd' && e.ctrlKey) {
                e.preventDefault();
                deleteNotification();
            }
        });

        // Animazione CSS per spinner
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
