<?php
// Verifica che l'utente sia loggato
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '/../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Miei Messaggi - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="glass-theme">
    <div class="page-wrapper">
        <?php renderNavbar('messages'); ?>
        
        <main class="main-content">
            <div class="container">
                <div class="glass-card">
                    <div class="card-header">
                        <h1 class="section-title">
                            <span class="icon">✉️</span>
                            <?php if ($_SESSION['user_type'] === 'organizer'): ?>
                                Messaggi dei Tuoi Eventi
                            <?php else: ?>
                                I Tuoi Messaggi
                            <?php endif; ?>
                        </h1>
                        <p class="section-subtitle">
                            <?php if ($_SESSION['user_type'] === 'organizer'): ?>
                                Gestisci le comunicazioni con i partecipanti ai tuoi eventi
                            <?php else: ?>
                                Messaggi dagli organizzatori degli eventi a cui sei iscritto
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if (!empty($messages)): ?>
                    <div class="messages-list">
                        <?php foreach ($messages as $message): ?>
                        <div class="message-card">
                            <div class="message-header">
                                <div class="message-info">
                                    <h3 class="message-title"><?= htmlspecialchars($message['titolo']) ?></h3>
                                    <div class="message-meta">
                                        <span class="event-name">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= htmlspecialchars($message['evento_nome'] ?? 'Evento non trovato') ?>
                                        </span>
                                        <span class="message-date">
                                            <i class="fas fa-clock"></i>
                                            <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
                                        </span>
                                        <?php if ($_SESSION['user_type'] === 'organizer'): ?>
                                        <span class="message-recipients">
                                            <i class="fas fa-users"></i>
                                            <?= $message['destinatari_count'] ?? '0' ?> destinatari
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="message-actions">
                                    <?php 
                                    $priority_class = '';
                                    $priority_icon = '';
                                    switch($message['priorita']) {
                                        case 'alta':
                                            $priority_class = 'priority-high';
                                            $priority_icon = 'fa-exclamation-triangle';
                                            break;
                                        case 'media':
                                            $priority_class = 'priority-medium';
                                            $priority_icon = 'fa-exclamation-circle';
                                            break;
                                        default:
                                            $priority_class = 'priority-low';
                                            $priority_icon = 'fa-info-circle';
                                    }
                                    ?>
                                    <span class="priority-badge <?= $priority_class ?>">
                                        <i class="fas <?= $priority_icon ?>"></i>
                                        <?= ucfirst($message['priorita']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars(substr($message['contenuto'], 0, 200))) ?>
                                <?php if (strlen($message['contenuto']) > 200): ?>
                                    <span class="read-more">... <a href="/messages/view/<?= $message['id'] ?>">Leggi tutto</a></span>
                                <?php endif; ?>
                            </div>

                            <div class="message-footer">
                                <div class="message-stats">
                                    <?php if ($_SESSION['user_type'] === 'organizer'): ?>
                                    <span class="stat">
                                        <i class="fas fa-eye"></i>
                                        <?= $message['visualizzazioni'] ?? 0 ?> visualizzazioni
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="message-actions">
                                    <a href="/messages/view/<?= $message['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        Leggi
                                    </a>
                                    <?php if ($_SESSION['user_type'] === 'organizer' && $message['organizer_id'] == $_SESSION['user_id']): ?>
                                    <a href="/messages/compose/<?= $message['evento_id'] ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-plus"></i>
                                        Nuovo messaggio
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📬</div>
                        <h3>Nessun messaggio</h3>
                        <?php if ($_SESSION['user_type'] === 'organizer'): ?>
                        <p>Non hai ancora inviato messaggi ai partecipanti dei tuoi eventi.</p>
                        <a href="/organizer/dashboard" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Vai alla Dashboard
                        </a>
                        <?php else: ?>
                        <p>Non hai ancora ricevuto messaggi dagli organizzatori.</p>
                        <a href="/events" class="btn btn-primary">
                            <i class="fas fa-calendar-alt"></i>
                            Esplora Eventi
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <style>
        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 2rem;
        }

        .message-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.12);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .message-title {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .message-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
        }

        .message-meta span {
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .event-name {
            color: #60a5fa !important;
        }

        .priority-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .priority-high {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid #f87171;
        }

        .priority-medium {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid #fbbf24;
        }

        .priority-low {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid #4ade80;
        }

        .message-content {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .read-more a {
            color: #60a5fa;
            text-decoration: none;
        }

        .read-more a:hover {
            text-decoration: underline;
        }

        .message-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .message-stats {
            display: flex;
            gap: 1rem;
        }

        .stat {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .message-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 0.8rem;
        }

        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .message-header {
                flex-direction: column;
                gap: 1rem;
            }

            .message-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .message-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .message-actions {
                justify-content: center;
            }
        }
    </style>
</body>
</html>