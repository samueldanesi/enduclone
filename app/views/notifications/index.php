<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SportEvents' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .notifications-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .notifications-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .notifications-header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            opacity: 0.9;
        }

        .notifications-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
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

        .notification-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #e5e7eb;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .notification-item.unread {
            border-left-color: #2563eb;
            background: linear-gradient(to right, #eff6ff, white);
        }

        .notification-item.unread::before {
            content: "●";
            color: #2563eb;
            font-size: 12px;
            position: absolute;
            margin-left: -10px;
            margin-top: -5px;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .notification-subject {
            font-size: 1.1rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 5px;
        }

        .notification-meta {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #6b7280;
            flex-wrap: wrap;
        }

        .notification-event {
            background: #f0f9ff;
            color: #0369a1;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        .notification-date {
            color: #6b7280;
        }

        .notification-organizer {
            color: #059669;
        }

        .notification-message {
            color: #4b5563;
            line-height: 1.6;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f3f4f6;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn.read {
            background: #f3f4f6;
            color: #4b5563;
        }

        .action-btn.read:hover {
            background: #e5e7eb;
        }

        .action-btn.delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .action-btn.delete:hover {
            background: #fee2e2;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            opacity: 0.5;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination a {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            text-decoration: none;
            color: #4b5563;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #f9fafb;
            border-color: #2563eb;
            color: #2563eb;
        }

        .pagination a.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .notifications-container {
                padding: 10px;
            }
            
            .notifications-header {
                padding: 20px;
            }
            
            .notifications-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .notification-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .notification-meta {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="notifications-container">
        <div class="notifications-header">
            <h1>🔔 Le Mie Notifiche</h1>
            <p>Tutti i messaggi dai tuoi eventi</p>
            
            <?php if (!empty($stats)): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_notifications'] ?? 0 ?></div>
                    <div class="stat-label">Totali</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['unread_notifications'] ?? 0 ?></div>
                    <div class="stat-label">Non Lette</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['events_with_messages'] ?? 0 ?></div>
                    <div class="stat-label">Eventi</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="notifications-actions">
            <div>
                <a href="/" class="btn btn-outline">
                    ← Torna alla Home
                </a>
            </div>
            
            <?php if (($stats['unread_notifications'] ?? 0) > 0): ?>
            <div>
                <button id="markAllRead" class="btn btn-primary">
                    ✅ Segna Tutte Come Lette
                </button>
            </div>
            <?php endif; ?>
        </div>

        <div class="notifications-list">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?= !$notification['is_read'] ? 'unread' : '' ?>" 
                     data-id="<?= $notification['id'] ?>"
                     onclick="viewNotification(<?= $notification['id'] ?>)">
                    
                    <div class="notification-header">
                        <div>
                            <div class="notification-subject">
                                <?= htmlspecialchars($notification['subject']) ?>
                            </div>
                            <div class="notification-meta">
                                <span class="notification-event">
                                    📅 <?= htmlspecialchars($notification['event_title']) ?>
                                </span>
                                <span class="notification-organizer">
                                    👤 <?= htmlspecialchars($notification['organizer_name'] . ' ' . $notification['organizer_surname']) ?>
                                </span>
                                <span class="notification-date">
                                    🕐 <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?>
                                </span>
                                <?php if ($notification['read_at']): ?>
                                <span style="color: #059669;">
                                    ✅ Letto il <?= date('d/m H:i', strtotime($notification['read_at'])) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="notification-message">
                        <?= nl2br(htmlspecialchars(substr($notification['message'], 0, 200))) ?>
                        <?php if (strlen($notification['message']) > 200): ?>
                            <span style="color: #6b7280;">... <em>clicca per leggere tutto</em></span>
                        <?php endif; ?>
                    </div>

                    <div class="notification-actions" onclick="event.stopPropagation()">
                        <?php if (!$notification['is_read']): ?>
                        <button class="action-btn read" onclick="markAsRead(<?= $notification['id'] ?>)">
                            ✅ Segna Come Letta
                        </button>
                        <?php endif; ?>
                        <button class="action-btn delete" onclick="deleteNotification(<?= $notification['id'] ?>)">
                            🗑️ Elimina
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">← Precedente</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?= $i ?>" <?= $i === $page ? 'class="active"' : '' ?>>
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>">Successiva →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 7h16M4 7l4-4M4 7l4 4"></path>
                    </svg>
                    <h3>Nessuna notifica</h3>
                    <p>Non hai ancora ricevuto messaggi dai tuoi eventi.</p>
                    <div style="margin-top: 20px;">
                        <a href="/events" class="btn btn-primary">
                            Scopri Gli Eventi
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Visualizza notifica completa
        function viewNotification(id) {
            window.location.href = `/notifications/${id}`;
        }

        // Marca come letta
        async function markAsRead(id) {
            try {
                const response = await fetch(`/notifications/read/${id}`, {
                    method: 'POST'
                });
                const result = await response.json();
                
                if (result.success) {
                    // Rimuovi classe unread e aggiorna UI
                    const item = document.querySelector(`[data-id="${id}"]`);
                    item.classList.remove('unread');
                    
                    // Nascondi pulsante "segna come letta"
                    const readBtn = item.querySelector('.action-btn.read');
                    if (readBtn) readBtn.style.display = 'none';
                    
                    // Aggiorna contatore se presente
                    updateNotificationCounter();
                }
            } catch (error) {
                alert('Errore nel segnare come letta');
            }
        }

        // Elimina notifica
        async function deleteNotification(id) {
            if (!confirm('Sei sicuro di voler eliminare questa notifica?')) {
                return;
            }

            try {
                const response = await fetch(`/notifications/delete/${id}`, {
                    method: 'DELETE'
                });
                const result = await response.json();
                
                if (result.success) {
                    // Rimuovi elemento dalla pagina
                    const item = document.querySelector(`[data-id="${id}"]`);
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-100%)';
                    setTimeout(() => item.remove(), 300);
                    
                    updateNotificationCounter();
                }
            } catch (error) {
                alert('Errore nell\'eliminazione');
            }
        }

        // Segna tutte come lette
        document.getElementById('markAllRead')?.addEventListener('click', async function() {
            if (!confirm('Segnare tutte le notifiche come lette?')) {
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner"></span> Elaborazione...';

            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST'
                });
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Errore nell\'operazione');
                }
            } catch (error) {
                alert('Errore di connessione');
            } finally {
                this.disabled = false;
                this.innerHTML = '✅ Segna Tutte Come Lette';
            }
        });

        // Aggiorna contatore notifiche (se presente nel header)
        function updateNotificationCounter() {
            // Implementa logica per aggiornare contatore nel header
            // se presente in futuro
        }

        // Auto-refresh ogni 30 secondi per nuove notifiche
        setInterval(() => {
            // Controlla se ci sono nuove notifiche
            fetch('/notifications/api/unread-count')
                .then(response => response.json())
                .then(data => {
                    // Aggiorna UI se necessario
                    if (data.count > <?= $stats['unread_notifications'] ?? 0 ?>) {
                        // Mostra indicatore di nuove notifiche
                        const indicator = document.createElement('div');
                        indicator.innerHTML = '🔔 Nuove notifiche disponibili - <a href="javascript:location.reload()">Aggiorna</a>';
                        indicator.style.cssText = 'position:fixed;top:0;left:0;right:0;background:#2563eb;color:white;padding:10px;text-align:center;z-index:9999;';
                        document.body.appendChild(indicator);
                    }
                });
        }, 30000);
    </script>
</body>
</html>
