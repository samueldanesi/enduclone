<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SportEvents' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .message-details {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .message-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
        }

        .message-header h1 {
            margin: 0 0 10px 0;
            font-size: 1.8rem;
        }

        .message-meta {
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

        .message-content {
            padding: 40px;
        }

        .content-section {
            margin-bottom: 30px;
        }

        .content-section h3 {
            margin: 0 0 15px 0;
            color: #374151;
            font-size: 1.2rem;
        }

        .message-text {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            line-height: 1.6;
            white-space: pre-line;
        }

        .delivery-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .stat-card.success {
            border-color: #10b981;
            background: #ecfdf5;
        }

        .stat-card.warning {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .stat-card.danger {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }

        .recipients-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table-header {
            background: #f9fafb;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: #374151;
        }

        .recipient-row {
            padding: 15px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: grid;
            grid-template-columns: 2fr 2fr 1.5fr 1fr;
            gap: 15px;
            align-items: center;
        }

        .recipient-row:last-child {
            border-bottom: none;
        }

        .recipient-row:hover {
            background: #f9fafb;
        }

        .recipient-name {
            font-weight: 600;
            color: #374151;
        }

        .recipient-email {
            color: #6b7280;
            font-size: 14px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.sent {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.failed {
            background: #fecaca;
            color: #991b1b;
        }

        .status-badge.opened {
            background: #dbeafe;
            color: #1e40af;
        }

        .sent-time {
            font-size: 12px;
            color: #6b7280;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
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

        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .filter-btn:hover {
            border-color: #2563eb;
        }

        .search-box {
            max-width: 300px;
            margin-bottom: 20px;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .message-content {
                padding: 20px;
            }
            
            .recipient-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .delivery-stats {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message-details">
            <div class="message-header">
                <h1>📧 <?= htmlspecialchars($message['subject']) ?></h1>
                
                <div class="message-meta">
                    <div class="meta-item">
                        <div class="meta-label">Evento</div>
                        <div class="meta-value"><?= htmlspecialchars($message['event_title']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Inviato il</div>
                        <div class="meta-value">
                            <?= $message['sent_at'] ? date('d/m/Y H:i', strtotime($message['sent_at'])) : 'In attesa' ?>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Destinatari</div>
                        <div class="meta-value"><?= $message['recipients_count'] ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Stato</div>
                        <div class="meta-value">
                            <?php
                            $status_text = [
                                'sent' => '✅ Inviato',
                                'pending' => '⏳ In attesa',
                                'failed' => '❌ Fallito'
                            ];
                            echo $status_text[$message['delivery_status']] ?? '❓ Sconosciuto';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="message-content">
                <div class="content-section">
                    <h3>📊 Statistiche di Consegna</h3>
                    <div class="delivery-stats">
                        <?php
                        $sent_count = 0;
                        $failed_count = 0;
                        $opened_count = 0;
                        $pending_count = 0;

                        foreach ($recipients as $recipient) {
                            switch ($recipient['delivery_status']) {
                                case 'sent':
                                    $sent_count++;
                                    if ($recipient['opened_at']) {
                                        $opened_count++;
                                    }
                                    break;
                                case 'failed':
                                    $failed_count++;
                                    break;
                                case 'pending':
                                    $pending_count++;
                                    break;
                            }
                        }
                        ?>
                        
                        <div class="stat-card success">
                            <div class="stat-number" style="color: #059669;"><?= $sent_count ?></div>
                            <div class="stat-label">Inviati</div>
                        </div>
                        
                        <div class="stat-card <?= $opened_count > 0 ? 'success' : '' ?>">
                            <div class="stat-number" style="color: #2563eb;"><?= $opened_count ?></div>
                            <div class="stat-label">Aperti</div>
                        </div>
                        
                        <?php if ($pending_count > 0): ?>
                        <div class="stat-card warning">
                            <div class="stat-number" style="color: #d97706;"><?= $pending_count ?></div>
                            <div class="stat-label">In Attesa</div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($failed_count > 0): ?>
                        <div class="stat-card danger">
                            <div class="stat-number" style="color: #dc2626;"><?= $failed_count ?></div>
                            <div class="stat-label">Falliti</div>
                        </div>
                        <?php endif; ?>

                        <div class="stat-card">
                            <div class="stat-number" style="color: #6b7280;"><?= number_format(($sent_count / count($recipients)) * 100, 1) ?>%</div>
                            <div class="stat-label">Tasso Successo</div>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h3>📝 Contenuto del Messaggio</h3>
                    <div class="message-text"><?= htmlspecialchars($message['message']) ?></div>
                </div>

                <div class="content-section">
                    <h3>👥 Destinatari (<?= count($recipients) ?>)</h3>
                    
                    <div class="search-box">
                        <input 
                            type="text" 
                            id="searchRecipients" 
                            class="search-input" 
                            placeholder="🔍 Cerca per nome o email..."
                        >
                    </div>

                    <div class="filters">
                        <button class="filter-btn active" data-filter="all">Tutti</button>
                        <button class="filter-btn" data-filter="sent">✅ Inviati</button>
                        <button class="filter-btn" data-filter="opened">👁️ Aperti</button>
                        <button class="filter-btn" data-filter="pending">⏳ In Attesa</button>
                        <?php if ($failed_count > 0): ?>
                        <button class="filter-btn" data-filter="failed">❌ Falliti</button>
                        <?php endif; ?>
                    </div>

                    <div class="recipients-table">
                        <div class="table-header">
                            <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr 1fr; gap: 15px;">
                                <div>Nome</div>
                                <div>Email</div>
                                <div>Stato</div>
                                <div>Inviato</div>
                            </div>
                        </div>
                        
                        <div id="recipientsList">
                            <?php foreach ($recipients as $recipient): ?>
                            <div class="recipient-row" 
                                 data-status="<?= $recipient['delivery_status'] ?>"
                                 data-search="<?= strtolower($recipient['nome'] . ' ' . $recipient['cognome'] . ' ' . $recipient['email']) ?>">
                                
                                <div>
                                    <div class="recipient-name">
                                        <?= htmlspecialchars($recipient['nome'] . ' ' . $recipient['cognome']) ?>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="recipient-email">
                                        <?= htmlspecialchars($recipient['email']) ?>
                                    </div>
                                </div>
                                
                                <div>
                                    <?php if ($recipient['delivery_status'] === 'sent'): ?>
                                        <?php if ($recipient['opened_at']): ?>
                                            <span class="status-badge opened">👁️ Aperto</span>
                                        <?php else: ?>
                                            <span class="status-badge sent">✅ Inviato</span>
                                        <?php endif; ?>
                                    <?php elseif ($recipient['delivery_status'] === 'failed'): ?>
                                        <span class="status-badge failed">❌ Fallito</span>
                                    <?php else: ?>
                                        <span class="status-badge pending">⏳ In Attesa</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <?php if ($recipient['sent_at']): ?>
                                        <div class="sent-time">
                                            <?= date('d/m H:i', strtotime($recipient['sent_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($recipient['opened_at']): ?>
                                        <div class="sent-time" style="color: #2563eb;">
                                            Aperto: <?= date('d/m H:i', strtotime($recipient['opened_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="emptyState" class="empty-state" style="display: none;">
                            <p>Nessun destinatario trovato con i filtri selezionati.</p>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <div>
                        <a href="/events/<?= $message['event_id'] ?>/statistics" class="btn btn-secondary">
                            ← Torna alle Statistiche
                        </a>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <a href="/messages/compose/<?= $message['event_id'] ?>" class="btn btn-outline">
                            📝 Nuovo Messaggio
                        </a>
                        <button class="btn btn-primary" onclick="window.print()">
                            🖨️ Stampa Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchRecipients');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const recipientRows = document.querySelectorAll('.recipient-row');
            const emptyState = document.getElementById('emptyState');

            let currentFilter = 'all';
            let currentSearch = '';

            // Search functionality
            searchInput.addEventListener('input', function() {
                currentSearch = this.value.toLowerCase();
                filterRecipients();
            });

            // Filter functionality
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Update active filter button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    currentFilter = this.dataset.filter;
                    filterRecipients();
                });
            });

            function filterRecipients() {
                let visibleCount = 0;

                recipientRows.forEach(row => {
                    const status = row.dataset.status;
                    const searchData = row.dataset.search;
                    
                    // Check filter
                    let matchesFilter = true;
                    if (currentFilter !== 'all') {
                        if (currentFilter === 'opened') {
                            matchesFilter = status === 'sent' && row.querySelector('.status-badge.opened');
                        } else {
                            matchesFilter = status === currentFilter;
                        }
                    }

                    // Check search
                    const matchesSearch = currentSearch === '' || searchData.includes(currentSearch);

                    if (matchesFilter && matchesSearch) {
                        row.style.display = 'grid';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show/hide empty state
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            // Auto-refresh stats every 30 seconds for pending messages
            if (<?= $pending_count ?> > 0) {
                setInterval(() => {
                    location.reload();
                }, 30000);
            }
        });
    </script>
</body>
</html>
