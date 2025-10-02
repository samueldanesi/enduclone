<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Iscrizione Collettiva - <?= htmlspecialchars($collective_registration['team_name']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/teams.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px 0;
            min-height: 100vh;
        }

        .details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .page-header h1 {
            font-size: 2.2em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 15px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9em;
            margin: 10px 5px;
        }

        .status-submitted { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-pending { background: #e0f2fe; color: #0c4a6e; }
        .status-paid { background: #d1fae5; color: #065f46; }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .detail-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .detail-card h3 {
            font-size: 1.3em;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-card h3 i {
            color: #667eea;
            font-size: 1.2em;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.9em;
        }

        .detail-value {
            font-weight: 500;
            color: #1e293b;
        }

        .participants-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .participants-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .participants-table th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 0.9em;
            border-bottom: 2px solid #e5e7eb;
        }

        .participants-table th:first-child {
            border-radius: 12px 0 0 0;
        }

        .participants-table th:last-child {
            border-radius: 0 12px 0 0;
        }

        .participants-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9em;
        }

        .participants-table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(100, 116, 139, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(100, 116, 139, 0.4);
        }

        .actions-section {
            text-align: center;
            margin-top: 30px;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .pricing-highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
            border: 1px solid #f59e0b;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }

        .pricing-highlight h4 {
            margin: 0 0 10px 0;
            color: #92400e;
            font-size: 1.2em;
        }

        .original-price {
            text-decoration: line-through;
            color: #6b7280;
            font-size: 0.9em;
        }

        .discounted-price {
            color: #065f46;
            font-size: 1.3em;
            font-weight: 700;
        }

        .file-link {
            color: #667eea;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .file-link:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .details-container {
                padding: 15px;
            }

            .page-header {
                padding: 30px 20px;
            }

            .page-header h1 {
                font-size: 1.8em;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .participants-table {
                font-size: 0.8em;
            }

            .participants-table th,
            .participants-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="details-container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-users-cog"></i> Dettagli Iscrizione Collettiva</h1>
            <div>
                <span class="status-badge status-<?= $collective_registration['status'] ?>">
                    <?= ucfirst($collective_registration['status']) ?>
                </span>
                <span class="status-badge status-<?= $collective_registration['payment_status'] ?>">
                    Pagamento: <?= ucfirst($collective_registration['payment_status']) ?>
                </span>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>

        <!-- Dettagli principali -->
        <div class="details-grid">
            <!-- Informazioni evento -->
            <div class="detail-card">
                <h3><i class="fas fa-calendar-alt"></i> Evento</h3>
                <div class="detail-row">
                    <span class="detail-label">Nome Evento</span>
                    <span class="detail-value"><?= htmlspecialchars($collective_registration['event_title']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Data</span>
                    <span class="detail-value"><?= date('d/m/Y', strtotime($collective_registration['data_evento'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Luogo</span>
                    <span class="detail-value"><?= htmlspecialchars($collective_registration['luogo_partenza']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Prezzo Base</span>
                    <span class="detail-value">€<?= number_format($collective_registration['event_base_price'], 2) ?></span>
                </div>
            </div>

            <!-- Informazioni team e responsabile -->
            <div class="detail-card">
                <h3><i class="fas fa-users"></i> Team & Responsabile</h3>
                <div class="detail-row">
                    <span class="detail-label">Team</span>
                    <span class="detail-value"><?= htmlspecialchars($collective_registration['team_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Responsabile</span>
                    <span class="detail-value"><?= htmlspecialchars($collective_registration['responsible_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?= htmlspecialchars($collective_registration['responsible_email']) ?></span>
                </div>
                <?php if (!empty($collective_registration['responsible_phone'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Telefono</span>
                    <span class="detail-value"><?= htmlspecialchars($collective_registration['responsible_phone']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Informazioni economiche -->
            <div class="detail-card">
                <h3><i class="fas fa-euro-sign"></i> Dettagli Economici</h3>
                <div class="detail-row">
                    <span class="detail-label">Partecipanti</span>
                    <span class="detail-value"><?= $collective_registration['total_participants'] ?> persone</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sconto Applicato</span>
                    <span class="detail-value"><?= $collective_registration['discount_percentage'] ?>%</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Prezzo per Persona</span>
                    <span class="detail-value">
                        <?php if ($collective_registration['discount_percentage'] > 0): ?>
                            <span class="original-price">€<?= number_format($collective_registration['base_price_per_person'], 2) ?></span>
                            <span class="discounted-price">€<?= number_format($collective_registration['discounted_price_per_person'], 2) ?></span>
                        <?php else: ?>
                            €<?= number_format($collective_registration['discounted_price_per_person'], 2) ?>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="pricing-highlight">
                    <h4>Totale da Pagare</h4>
                    <div class="discounted-price">€<?= number_format($collective_registration['total_amount'], 2) ?></div>
                    <?php if ($collective_registration['discount_percentage'] > 0): ?>
                        <div style="font-size: 0.9em; color: #059669; margin-top: 5px;">
                            Risparmi €<?= number_format(($collective_registration['base_price_per_person'] * $collective_registration['total_participants']) - $collective_registration['total_amount'], 2) ?>!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- File e metadati -->
            <div class="detail-card">
                <h3><i class="fas fa-file-excel"></i> File e Informazioni</h3>
                <div class="detail-row">
                    <span class="detail-label">File Originale</span>
                    <span class="detail-value">
                        <a href="/<?= htmlspecialchars($collective_registration['excel_file_path']) ?>" 
                           class="file-link" target="_blank">
                            <i class="fas fa-download"></i>
                            <?= htmlspecialchars($collective_registration['excel_filename']) ?>
                        </a>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Data Iscrizione</span>
                    <span class="detail-value"><?= date('d/m/Y H:i', strtotime($collective_registration['created_at'])) ?></span>
                </div>
                <?php if ($collective_registration['confirmed_at']): ?>
                <div class="detail-row">
                    <span class="detail-label">Data Conferma</span>
                    <span class="detail-value"><?= date('d/m/Y H:i', strtotime($collective_registration['confirmed_at'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($collective_registration['notes'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Note</span>
                    <span class="detail-value"><?= htmlspecialchars($collective_registration['notes']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lista partecipanti -->
        <div class="participants-section">
            <h3><i class="fas fa-list"></i> Lista Partecipanti (<?= count($participants) ?>)</h3>
            
            <?php if (!empty($participants)): ?>
            <table class="participants-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Data Nascita</th>
                        <th>Sesso</th>
                        <th>Codice Fiscale</th>
                        <th>Telefono</th>
                        <th>Città</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                    <tr>
                        <td><?= htmlspecialchars($participant['nome']) ?></td>
                        <td><?= htmlspecialchars($participant['cognome']) ?></td>
                        <td><?= $participant['email'] ? htmlspecialchars($participant['email']) : '-' ?></td>
                        <td><?= $participant['data_nascita'] ? date('d/m/Y', strtotime($participant['data_nascita'])) : '-' ?></td>
                        <td><?= htmlspecialchars($participant['sesso'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($participant['codice_fiscale'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($participant['cellulare'] ?? $participant['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($participant['citta'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #64748b; font-style: italic;">
                Nessun partecipante trovato.
            </p>
            <?php endif; ?>
        </div>

        <!-- Azioni -->
        <div class="actions-section">
            <?php if ($collective_registration['payment_status'] === 'pending'): ?>
                <a href="/teams/confirm-collective-payment/<?= $collective_registration['id'] ?>" 
                   class="btn btn-success">
                    <i class="fas fa-credit-card"></i>
                    Conferma Pagamento
                </a>
            <?php endif; ?>
            
            <a href="/teams/view/<?= $collective_registration['team_id'] ?>" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Torna al Team
            </a>
        </div>
    </div>
</body>
</html>