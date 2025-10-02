<?php
$this->requireAuth();
$this->requireTeamLeader($team_id);

$team = $this->team->findById($team_id);
$registrations = $this->collectiveRegistration->getByTeam($team_id) ?? [];
$available_events = $this->event->getAvailable() ?? [];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Iscrizioni - <?= htmlspecialchars($team['nome']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-btn {
            background: white;
            border: none;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #2c3e50;
            transition: transform 0.3s;
            text-align: center;
        }

        .action-btn:hover {
            transform: translateY(-5px);
            color: #2c3e50;
        }

        .action-btn i {
            font-size: 2em;
            margin-bottom: 10px;
            display: block;
        }

        .quick-btn i { color: #e74c3c; }
        .csv-btn i { color: #27ae60; }
        .template-btn i { color: #3498db; }

        .registrations-grid {
            display: grid;
            gap: 20px;
        }

        .registration-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .card-body {
            padding: 20px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .status-submitted { background: #fff3cd; color: #856404; }
        .status-paid { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4em;
            margin-bottom: 20px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-clipboard-list"></i> Dashboard Iscrizioni Collettive</h1>
            <p>Team: <strong><?= htmlspecialchars($team['nome']) ?></strong></p>
            <p>Gestisci tutte le iscrizioni del tuo team da qui</p>
        </div>

        <!-- Pulsanti Azione -->
        <div class="action-buttons">
            <a href="/teams/quick-registration/<?= $team['id'] ?>" class="action-btn quick-btn">
                <i class="fas fa-bolt"></i>
                <strong>Iscrizione Rapida</strong>
                <small>Inserimento manuale veloce</small>
            </a>
            
            <a href="/teams/csv-registration/<?= $team['id'] ?>" class="action-btn csv-btn">
                <i class="fas fa-file-csv"></i>
                <strong>Upload CSV</strong>
                <small>Carica lista partecipanti</small>
            </a>
            
            <a href="/teams/download-csv-template" class="action-btn template-btn">
                <i class="fas fa-download"></i>
                <strong>Scarica Template</strong>
                <small>Formato CSV di esempio</small>
            </a>
        </div>

        <!-- Lista Iscrizioni -->
        <div class="registrations-section">
            <h2><i class="fas fa-list"></i> Iscrizioni Attive</h2>
            
            <?php if (empty($registrations)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Nessuna iscrizione ancora</h3>
                    <p>Inizia creando la tua prima iscrizione collettiva!</p>
                    <a href="/teams/quick-registration/<?= $team['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crea Prima Iscrizione
                    </a>
                </div>
            <?php else: ?>
                <div class="registrations-grid">
                    <?php foreach ($registrations as $reg): ?>
                        <div class="registration-card">
                            <div class="card-header">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h4><?= htmlspecialchars($reg['event_name'] ?? 'Evento') ?></h4>
                                    <span class="status-badge status-<?= $reg['status'] ?? 'submitted' ?>">
                                        <?= ucfirst($reg['status'] ?? 'Inviato') ?>
                                    </span>
                                </div>
                                <small>Creata il <?= date('d/m/Y', strtotime($reg['created_at'] ?? 'now')) ?></small>
                            </div>
                            
                            <div class="card-body">
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                                    <div>
                                        <strong>Partecipanti</strong><br>
                                        <span style="font-size: 1.2em; color: #3498db;"><?= $reg['total_participants'] ?? 0 ?></span>
                                    </div>
                                    <div>
                                        <strong>Prezzo Totale</strong><br>
                                        <span style="font-size: 1.2em; color: #27ae60;">€<?= number_format($reg['total_amount'] ?? 0, 2) ?></span>
                                    </div>
                                    <div>
                                        <strong>Sconto</strong><br>
                                        <span style="font-size: 1.2em; color: #e74c3c;"><?= $reg['discount_percentage'] ?? 0 ?>%</span>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 20px; text-align: right;">
                                    <?php if (($reg['status'] ?? '') === 'submitted'): ?>
                                        <a href="/teams/checkout-collective/<?= $reg['id'] ?>" class="btn btn-primary">
                                            <i class="fas fa-credit-card"></i> Procedi al Pagamento
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-success" disabled>
                                            <i class="fas fa-check"></i> Pagamento Completato
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="btn btn-outline" onclick="viewDetails(<?= $reg['id'] ?>)">
                                        <i class="fas fa-eye"></i> Dettagli
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="/teams/view/<?= $team['id'] ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Torna al Team
            </a>
        </div>
    </div>

    <script>
        function viewDetails(registrationId) {
            // TODO: Implementare modal o pagina dettagli
            alert('Dettagli iscrizione #' + registrationId);
        }
    </script>
</body>
</html>