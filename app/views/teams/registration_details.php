<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Iscrizione Collettiva - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="registration-details-container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-list-alt"></i> Dettagli Iscrizione Collettiva</h1>
                    <p>Team: <strong><?= htmlspecialchars($registration['team_nome']) ?></strong></p>
                </div>
                <div class="header-right">
                    <a href="/teams/view/<?= $registration['team_id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Torna al Team
                    </a>
                </div>
            </div>
        </div>

        <!-- Informazioni Iscrizione -->
        <div class="registration-info-card">
            <h2><i class="fas fa-info-circle"></i> Informazioni Iscrizione</h2>
            <div class="info-grid">
                <div class="info-section">
                    <h3>Evento</h3>
                    <div class="event-details">
                        <div class="event-name"><?= htmlspecialchars($registration['event_nome']) ?></div>
                        <div class="event-meta">
                            <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($registration['event_data'])) ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($registration['event_location']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3>Responsabile</h3>
                    <div class="contact-details">
                        <div class="contact-name"><?= htmlspecialchars($registration['responsabile_nome']) ?></div>
                        <div class="contact-info">
                            <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($registration['responsabile_email']) ?></span>
                            <span><i class="fas fa-phone"></i> <?= htmlspecialchars($registration['responsabile_telefono']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3>Costi</h3>
                    <div class="cost-breakdown">
                        <div class="cost-item">
                            <span>Partecipanti:</span>
                            <strong><?= $registration['numero_partecipanti'] ?></strong>
                        </div>
                        <div class="cost-item">
                            <span>Quota individuale:</span>
                            <strong>€<?= number_format($registration['quota_individuale'], 2) ?></strong>
                        </div>
                        <div class="cost-item">
                            <span>Subtotale:</span>
                            <strong>€<?= number_format($registration['quota_individuale'] * $registration['numero_partecipanti'], 2) ?></strong>
                        </div>
                        <?php if ($registration['sconto_percentuale'] > 0): ?>
                        <div class="cost-item discount">
                            <span>Sconto collettivo (<?= $registration['sconto_percentuale'] ?>%):</span>
                            <strong>-€<?= number_format(($registration['quota_individuale'] * $registration['numero_partecipanti']) * ($registration['sconto_percentuale'] / 100), 2) ?></strong>
                        </div>
                        <?php endif; ?>
                        <div class="cost-item total">
                            <span>Totale finale:</span>
                            <strong>€<?= number_format($registration['quota_totale'], 2) ?></strong>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3>Status</h3>
                    <div class="status-info">
                        <span class="status-badge status-<?= $registration['status'] ?>">
                            <?= ucfirst($registration['status']) ?>
                        </span>
                        <div class="status-date">
                            Creata il <?= date('d/m/Y H:i', strtotime($registration['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista Partecipanti -->
        <div class="participants-card">
            <div class="participants-header">
                <h2><i class="fas fa-users"></i> Partecipanti (<?= count($members) ?>)</h2>
                <div class="participants-actions">
                    <button onclick="exportParticipants()" class="btn btn-outline">
                        <i class="fas fa-download"></i> Esporta CSV
                    </button>
                    <button onclick="printParticipants()" class="btn btn-outline">
                        <i class="fas fa-print"></i> Stampa
                    </button>
                </div>
            </div>

            <div class="participants-table-container">
                <table class="participants-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Email</th>
                            <th>Data Nascita</th>
                            <th>Sesso</th>
                            <th>Status</th>
                            <th>Iscritto il</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($member['nome']) ?></td>
                            <td><?= htmlspecialchars($member['cognome']) ?></td>
                            <td><?= htmlspecialchars($member['email']) ?></td>
                            <td><?= $member['data_nascita'] ? date('d/m/Y', strtotime($member['data_nascita'])) : '-' ?></td>
                            <td>
                                <?php if ($member['sesso']): ?>
                                <span class="gender-badge gender-<?= strtolower($member['sesso']) ?>">
                                    <?= $member['sesso'] === 'M' ? 'Maschio' : 'Femmina' ?>
                                </span>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="registration-status status-<?= $member['registration_status'] ?>">
                                    <?= ucfirst($member['registration_status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($member['iscritto_il'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistiche Veloci -->
        <div class="quick-stats">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?= count($members) ?></div>
                        <div class="stat-label">Partecipanti</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-mars"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?= count(array_filter($members, fn($m) => $m['sesso'] === 'M')) ?></div>
                        <div class="stat-label">Maschi</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-venus"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?= count(array_filter($members, fn($m) => $m['sesso'] === 'F')) ?></div>
                        <div class="stat-label">Femmine</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <div class="stat-info">
                        <?php
                        $ages = array_filter(array_map(function($m) {
                            return $m['data_nascita'] ? date('Y') - date('Y', strtotime($m['data_nascita'])) : null;
                        }, $members));
                        $avg_age = !empty($ages) ? round(array_sum($ages) / count($ages)) : 0;
                        ?>
                        <div class="stat-number"><?= $avg_age ?></div>
                        <div class="stat-label">Età Media</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .registration-details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
    }

    .header-left p {
        margin: 0;
        opacity: 0.9;
    }

    .registration-info-card,
    .participants-card,
    .quick-stats {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .registration-info-card h2,
    .participants-card h2 {
        background: #f8f9fa;
        margin: 0;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        color: #333;
    }

    .info-grid {
        padding: 30px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .info-section h3 {
        color: #333;
        margin: 0 0 15px 0;
        font-size: 16px;
        font-weight: 600;
        padding-bottom: 8px;
        border-bottom: 2px solid #17a2b8;
    }

    .event-name,
    .contact-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .event-meta,
    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .event-meta span,
    .contact-info span {
        color: #666;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cost-breakdown {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .cost-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
    }

    .cost-item.discount {
        color: #28a745;
        font-weight: 600;
    }

    .cost-item.total {
        border-top: 2px solid #17a2b8;
        padding-top: 12px;
        margin-top: 8px;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .status-completed {
        background: #cce7ff;
        color: #004085;
    }

    .status-date {
        margin-top: 8px;
        color: #666;
        font-size: 14px;
    }

    .participants-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
    }

    .participants-header h2 {
        background: none;
        padding: 0;
        margin: 0;
        border: none;
    }

    .participants-actions {
        display: flex;
        gap: 10px;
    }

    .participants-table-container {
        overflow-x: auto;
    }

    .participants-table {
        width: 100%;
        border-collapse: collapse;
    }

    .participants-table th,
    .participants-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e9ecef;
    }

    .participants-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
        position: sticky;
        top: 0;
    }

    .participants-table tbody tr:hover {
        background: #f8f9fa;
    }

    .gender-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .gender-m {
        background: #cce7ff;
        color: #004085;
    }

    .gender-f {
        background: #f8d7da;
        color: #721c24;
    }

    .quick-stats {
        padding: 25px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #17a2b8, #138496);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .stat-number {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        line-height: 1;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        margin-top: 4px;
    }

    .btn-outline {
        background: transparent;
        color: #17a2b8;
        border: 2px solid #17a2b8;
    }

    .btn-outline:hover {
        background: #17a2b8;
        color: white;
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .participants-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .participants-actions {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media print {
        .page-header,
        .participants-actions,
        .btn {
            display: none !important;
        }

        .registration-details-container {
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .registration-info-card,
        .participants-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
    </style>

    <script>
    function exportParticipants() {
        // Crea CSV dei partecipanti
        const participants = <?= json_encode($members) ?>;
        const headers = ['#', 'Nome', 'Cognome', 'Email', 'Data Nascita', 'Sesso', 'Status', 'Iscritto il'];
        
        let csvContent = headers.join(',') + '\n';
        
        participants.forEach((participant, index) => {
            const row = [
                index + 1,
                `"${participant.nome}"`,
                `"${participant.cognome}"`,
                `"${participant.email}"`,
                participant.data_nascita || '',
                participant.sesso || '',
                participant.registration_status,
                new Date(participant.iscritto_il).toLocaleDateString('it-IT')
            ];
            csvContent += row.join(',') + '\n';
        });

        // Download del file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'partecipanti_<?= $registration['team_nome'] ?>_<?= date('Y-m-d') ?>.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printParticipants() {
        window.print();
    }
    </script>
</body>
</html>
