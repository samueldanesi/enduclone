<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Eventi - SportEvents</tit    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    
    <div class="community-container">    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .events-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .events-header {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .events-header h1 {
            margin: 0 0 15px 0;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .event-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            margin-bottom: 25px;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .event-header {
            padding: 25px;
            border-bottom: 1px solid #eee;
        }

        .event-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .event-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .event-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            padding: 20px 25px;
            background: #f8f9fa;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .event-actions {
            padding: 20px 25px;
            display: flex;
            gap: 15px;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #28a745;
            color: #28a745;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
            backdrop-filter: blur(20px);
        }

        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-upcoming {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-completed {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .status-cancelled {
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="events-container">
        <!-- Header -->
        <div class="events-header">
            <h1><i class="fas fa-calendar-alt"></i> Community Eventi</h1>
            <p>Accedi alle community dedicate degli eventi a cui partecipi</p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="/events" class="btn btn-primary">
                    <i class="fas fa-search"></i> Trova Altri Eventi
                </a>
                <a href="/community" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna alle Sezioni
                </a>
            </div>
        </div>

        <!-- Eventi dell'utente -->
        <?php if (empty($userEvents)): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Non sei ancora iscritto a nessun evento</h3>
            <p>Iscriviti ad alcuni eventi per accedere alle loro community dedicate!</p>
            <a href="/events" class="btn btn-primary">
                <i class="fas fa-plus"></i> Esplora Eventi Disponibili
            </a>
        </div>
        <?php else: ?>
        <?php foreach ($userEvents as $event): ?>
        <div class="event-card">
            <!-- Event Header -->
            <div class="event-header">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <h3 class="event-title"><?= htmlspecialchars($event['titolo']) ?></h3>
                    <span class="status-badge <?= 
                        (strtotime($event['data_evento']) > time()) ? 'status-upcoming' : 
                        (($event['status'] ?? 'published') === 'completed' ? 'status-completed' : 'status-upcoming')
                    ?>">
                        <?= (strtotime($event['data_evento']) > time()) ? 'Prossimo' : 'Completato' ?>
                    </span>
                </div>
                
                <div class="event-meta">
                    <span><i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($event['data_evento'])) ?></span>
                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['luogo_partenza']) ?></span>
                    <span><i class="fas fa-running"></i> <?= htmlspecialchars($event['sport'] . ' - ' . $event['disciplina']) ?></span>
                    <?php if ($event['distanza_km']): ?>
                        <span><i class="fas fa-route"></i> <?= $event['distanza_km'] ?> km</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Event Stats -->
            <div class="event-stats">
                <div class="stat">
                    <div class="stat-number"><?= $event['participants_count'] ?></div>
                    <div class="stat-label">Partecipanti</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?= $event['posts_count'] ?></div>
                    <div class="stat-label">Post Community</div>
                </div>
                <div class="stat">
                    <div class="stat-number">
                        <?php
                        // Calcola giorni dalla registrazione
                        $regDate = new DateTime($event['registration_date']);
                        $now = new DateTime();
                        $diff = $now->diff($regDate);
                        echo $diff->days;
                        ?>
                    </div>
                    <div class="stat-label">Giorni Iscritto</div>
                </div>
                <div class="stat">
                    <div class="stat-number">
                        <?php
                        // Status partecipazione
                        echo (strtotime($event['data_evento']) > time()) ? '⏳' : '✅';
                        ?>
                    </div>
                    <div class="stat-label">Status</div>
                </div>
            </div>

            <!-- Event Actions -->
            <div class="event-actions">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="/community/event/<?= $event['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-comments"></i> Entra in Community
                    </a>
                    <a href="/events/<?= $event['id'] ?>" class="btn btn-outline">
                        <i class="fas fa-info-circle"></i> Dettagli Evento
                    </a>
                </div>
                
                <div style="color: #666; font-size: 0.8rem;">
                    Iscritto il <?= date('d/m/Y', strtotime($event['registration_date'])) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <!-- Info Box -->
        <div style="background: rgba(255,255,255,0.9); border-radius: 15px; padding: 20px; margin-top: 30px; border-left: 4px solid #28a745;">
            <h4 style="margin: 0 0 10px 0; color: #28a745;">
                <i class="fas fa-info-circle"></i> Come funziona
            </h4>
            <ul style="margin: 0; padding-left: 20px; color: #666; line-height: 1.6;">
                <li>Ogni evento ha la sua <strong>community dedicata</strong></li>
                <li>Solo i <strong>partecipanti iscritti</strong> possono accedere</li>
                <li>Condividi <strong>esperienze, foto e risultati</strong> specifici dell'evento</li>
                <li>Interagisci con <strong>altri partecipanti</strong> dello stesso evento</li>
            </ul>
        </div>
    </div>
</body>
</html>