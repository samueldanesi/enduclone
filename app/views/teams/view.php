<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($team['nome']) ?> - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="team-view-container">
        <!-- Header Team -->
        <div class="team-header">
            <div class="header-content">
                <div class="team-info">
                    <h1><?= htmlspecialchars($team['nome']) ?></h1>
                    <div class="team-meta">
                        <span class="team-status">
                            <i class="fas fa-tag"></i>
                            <?= ucfirst($team['status']) ?>
                        </span>
                        <?php if (!empty($team['indirizzo'])): ?>
                        <span class="team-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($team['indirizzo']) ?>
                        </span>
                        <?php endif; ?>
                        <span class="team-members">
                            <i class="fas fa-users"></i>
                            <?= count($members) ?> membri
                        </span>
                    </div>
                </div>
                <div class="team-actions">
                    <?php if ($can_manage): ?>
                    <a href="/teams/collective-registrations/<?= $team['id'] ?>" class="btn btn-primary" style="margin-right: 10px;">
                        <i class="fas fa-clipboard-list"></i> Dashboard Iscrizioni
                    </a>
                    <a href="/teams/manage-members/<?= $team['id'] ?>" class="btn btn-success">
                        <i class="fas fa-users-cog"></i> Gestisci Membri
                    </a>
                    <a href="/teams/stats/<?= $team['id'] ?>" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Statistiche
                    </a>
                    <?php endif; ?>
                    <a href="/teams" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Torna ai Team
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiche -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $stats['membri_attivi'] ?></div>
                        <div class="stat-label">Membri Attivi</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $stats['iscrizioni_eventi'] ?></div>
                        <div class="stat-label">Eventi Iscritti</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $stats['eventi_completati'] ?></div>
                        <div class="stat-label">Eventi Completati</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">€<?= number_format($stats['totale_speso'] ?? 0, 0) ?></div>
                        <div class="stat-label">Totale Speso</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Informazioni Team -->
            <div class="team-details-card">
                <h2><i class="fas fa-info-circle"></i> Informazioni Team</h2>
                <div class="details-grid">
                    <?php if (!empty($team['email'])): ?>
                    <div class="detail-item">
                        <label>Email:</label>
                        <value><?= htmlspecialchars($team['email']) ?></value>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($team['telefono'])): ?>
                    <div class="detail-item">
                        <label>Telefono:</label>
                        <value><?= htmlspecialchars($team['telefono']) ?></value>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($team['indirizzo'])): ?>
                    <div class="detail-item">
                        <label>Indirizzo:</label>
                        <value><?= htmlspecialchars($team['indirizzo']) ?></value>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($team['referente_nome']) || !empty($team['referente_cognome'])): ?>
                    <div class="detail-item">
                        <label>Referente:</label>
                        <value>
                            <?= htmlspecialchars(trim(($team['referente_nome'] ?? '') . ' ' . ($team['referente_cognome'] ?? ''))) ?>
                        </value>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($team['codice_affiliazione'])): ?>
                    <div class="detail-item">
                        <label>Codice Affiliazione:</label>
                        <value><?= htmlspecialchars($team['codice_affiliazione']) ?></value>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Membri -->
            <div class="members-card">
                <h2><i class="fas fa-users"></i> Membri del Team</h2>
                <div class="members-list">
                    <?php foreach ($members as $member): ?>
                    <div class="member-item">
                        <div class="member-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="member-info">
                            <div class="member-name">
                                <?= htmlspecialchars($member['nome']) ?> <?= htmlspecialchars($member['cognome']) ?>
                            </div>
                            <div class="member-details">
                                <span class="member-role role-<?= $member['ruolo'] ?>">
                                    <?= ucfirst($member['ruolo']) ?>
                                </span>
                                <span class="member-email">
                                    <i class="fas fa-envelope"></i>
                                    <?= htmlspecialchars($member['email']) ?>
                                </span>
                                <span class="member-joined">
                                    <i class="fas fa-calendar"></i>
                                    Dal <?= date('d/m/Y', strtotime($member['joined_at'])) ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($can_manage && $member['ruolo'] !== 'manager'): ?>
                        <div class="member-actions">
                            <button class="btn btn-sm btn-outline" onclick="changeRole(<?= $member['user_id'] ?>)">
                                <i class="fas fa-user-cog"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="removeMember(<?= $member['user_id'] ?>)">
                                <i class="fas fa-user-times"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Iscrizioni Collettive -->
        <?php if (!empty($collective_registrations)): ?>
        <div class="registrations-section">
            <h2><i class="fas fa-list-alt"></i> Iscrizioni Collettive</h2>
            <div class="registrations-grid">
                <?php foreach ($collective_registrations as $registration): ?>
                <div class="registration-card">
                    <div class="registration-header">
                        <h3><?= htmlspecialchars($registration['event_title']) ?></h3>
                        <span class="registration-status status-<?= $registration['status'] ?>">
                            <?= ucfirst($registration['status']) ?>
                        </span>
                        <span class="payment-status status-<?= $registration['payment_status'] ?>">
                            Pag: <?= ucfirst($registration['payment_status']) ?>
                        </span>
                    </div>
                    <div class="registration-info">
                        <p><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($registration['data_evento'])) ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($registration['luogo_partenza']) ?></p>
                        <p><i class="fas fa-users"></i> <?= $registration['total_participants'] ?> partecipanti</p>
                        <p><i class="fas fa-euro-sign"></i> €<?= number_format($registration['total_amount'], 2) ?></p>
                        <?php if ($registration['discount_percentage'] > 0): ?>
                        <p class="discount-info">
                            <i class="fas fa-percentage"></i> 
                            Sconto: <?= $registration['discount_percentage'] ?>%
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="registration-actions">
                        <a href="/teams/collective-details/<?= $registration['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Dettagli
                        </a>
                        <?php if ($registration['payment_status'] === 'pending'): ?>
                        <a href="/teams/confirm-collective-payment/<?= $registration['id'] ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-credit-card"></i> Paga
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <style>
    .team-view-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .team-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 40px;
        border-radius: 15px;
        margin-bottom: 30px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .team-info h1 {
        margin: 0 0 15px 0;
        font-size: 32px;
        font-weight: bold;
    }

    .team-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .team-meta span {
        background: rgba(255,255,255,0.1);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .team-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .stats-section {
        margin-bottom: 30px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff, #0056b3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        line-height: 1;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        margin-top: 5px;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .team-details-card,
    .members-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .team-details-card h2,
    .members-card h2 {
        background: #f8f9fa;
        margin: 0;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        color: #333;
    }

    .details-grid {
        padding: 25px;
        display: grid;
        gap: 20px;
    }

    .detail-item {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 15px;
        align-items: start;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
        display: block;
    }

    .detail-item label {
        font-weight: 600;
        color: #666;
        font-size: 14px;
    }

    .detail-item value {
        color: #333;
    }

    .detail-item a {
        color: #007bff;
        text-decoration: none;
    }

    .detail-item a:hover {
        text-decoration: underline;
    }

    .members-list {
        padding: 0;
    }

    .member-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.3s ease;
    }

    .member-item:hover {
        background: #f8f9fa;
    }

    .member-item:last-child {
        border-bottom: none;
    }

    .member-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff, #0056b3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .member-info {
        flex: 1;
    }

    .member-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 16px;
    }

    .member-details {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        font-size: 13px;
    }

    .member-role {
        padding: 4px 10px;
        border-radius: 15px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
    }

    .role-manager {
        background: #dc3545;
        color: white;
    }

    .role-captain {
        background: #fd7e14;
        color: white;
    }

    .role-member {
        background: #28a745;
        color: white;
    }

    .member-email,
    .member-joined {
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .member-actions {
        display: flex;
        gap: 8px;
    }

    .registrations-section {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .registrations-section h2 {
        background: #f8f9fa;
        margin: 0;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        color: #333;
    }

    .registrations-grid {
        padding: 25px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
    }

    .registration-card {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        transition: border-color 0.3s ease, transform 0.3s ease;
    }

    .registration-card:hover {
        border-color: #007bff;
        transform: translateY(-3px);
    }

    .registration-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }

    .registration-header h3 {
        margin: 0;
        color: #333;
        font-size: 18px;
    }

    .registration-status {
        padding: 5px 12px;
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

    .registration-info p {
        margin: 8px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
        font-size: 14px;
    }

    .registration-info i {
        width: 16px;
        color: #007bff;
    }

    .discount-info {
        color: #28a745 !important;
        font-weight: 600;
    }

    .registration-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-outline {
        background: transparent;
        color: #007bff;
        border: 2px solid #007bff;
    }

    .btn-outline:hover {
        background: #007bff;
        color: white;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .team-meta {
            justify-content: center;
        }

        .content-grid {
            grid-template-columns: 1fr;
        }

        .detail-item {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .member-details {
            flex-direction: column;
            gap: 8px;
        }

        .registrations-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>

    <script>
    function showInviteModal() {
        // TODO: Implementare modal per invitare membri
        alert('Funzionalità in arrivo: Modal per invitare nuovi membri');
    }

    function changeRole(userId) {
        // TODO: Implementare cambio ruolo
        alert('Funzionalità in arrivo: Cambio ruolo membro');
    }

    function removeMember(userId) {
        if (confirm('Sei sicuro di voler rimuovere questo membro dal team?')) {
            // TODO: Implementare rimozione membro
            alert('Funzionalità in arrivo: Rimozione membro');
        }
    }
    </script>
</body>
</html>
