<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risultati & Classifiche - SportEvents</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-a    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    
    <div class="community-container">me/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .results-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .results-header {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .results-header h1 {
            margin: 0 0 15px 0;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .results-section {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            margin-bottom: 25px;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 20px 25px;
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
        }

        .results-table th,
        .results-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .results-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .results-table tr:hover {
            background: #f8f9fa;
        }

        .position-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            font-weight: bold;
            color: white;
            font-size: 0.9rem;
        }

        .position-1 { background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333; }
        .position-2 { background: linear-gradient(135deg, #c0c0c0 0%, #e2e8f0 100%); color: #333; }
        .position-3 { background: linear-gradient(135deg, #cd7f32 0%, #d4a574 100%); }
        .position-other { background: #6c757d; }

        .time-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-family: monospace;
            font-size: 0.9rem;
        }

        .event-link {
            color: #fd7e14;
            text-decoration: none;
            font-weight: 500;
        }

        .event-link:hover {
            text-decoration: underline;
        }

        .achievement-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 10px 0;
        }

        .achievement-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .personal-bests {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .pb-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #fd7e14;
        }

        .pb-discipline {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .pb-time {
            font-size: 1.4rem;
            font-weight: bold;
            color: #fd7e14;
            font-family: monospace;
        }

        .pb-event {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
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
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .results-table {
                font-size: 0.9rem;
            }
            
            .results-table th,
            .results-table td {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="results-container">
        <!-- Header -->
        <div class="results-header">
            <h1><i class="fas fa-trophy"></i> Risultati & Classifiche</h1>
            <p>I tuoi risultati e prestazioni negli eventi completati</p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="/events" class="btn btn-primary">
                    <i class="fas fa-calendar-plus"></i> Partecipa ad Altri Eventi
                </a>
                <a href="/community" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna alle Sezioni
                </a>
            </div>
        </div>

        <!-- Statistiche Generali -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: #28a745;">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="stat-number" style="color: #28a745;">
                    <?= count($userResults) ?>
                </div>
                <div class="stat-label">Eventi Completati</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #ffd700;">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-number" style="color: #ffd700;">
                    <?php
                    $podiums = 0;
                    foreach ($userResults as $result) {
                        if ($result['posizione'] <= 3) $podiums++;
                    }
                    echo $podiums;
                    ?>
                </div>
                <div class="stat-label">Podi Conquistati</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #17a2b8;">
                    <i class="fas fa-stopwatch"></i>
                </div>
                <div class="stat-number" style="color: #17a2b8;">
                    <?php
                    // Calcola tempo medio (se disponibile)
                    $totalMinutes = 0;
                    $validTimes = 0;
                    foreach ($userResults as $result) {
                        if ($result['tempo_finale']) {
                            $time = explode(':', $result['tempo_finale']);
                            $minutes = ($time[0] * 60) + $time[1];
                            $totalMinutes += $minutes;
                            $validTimes++;
                        }
                    }
                    if ($validTimes > 0) {
                        $avgMinutes = $totalMinutes / $validTimes;
                        $hours = floor($avgMinutes / 60);
                        $mins = floor($avgMinutes % 60);
                        echo sprintf('%d:%02d', $hours, $mins);
                    } else {
                        echo '--:--';
                    }
                    ?>
                </div>
                <div class="stat-label">Tempo Medio</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #e83e8c;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number" style="color: #e83e8c;">
                    <?php
                    // Calcola posizione media
                    $totalPositions = 0;
                    foreach ($userResults as $result) {
                        $totalPositions += $result['posizione'];
                    }
                    echo count($userResults) > 0 ? round($totalPositions / count($userResults)) . '°' : '--';
                    ?>
                </div>
                <div class="stat-label">Posizione Media</div>
            </div>
        </div>

        <?php if (empty($userResults)): ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <h3>Nessun risultato disponibile</h3>
            <p>Completa alcuni eventi per vedere i tuoi risultati e statistiche!</p>
            <a href="/events" class="btn btn-primary">
                <i class="fas fa-running"></i> Trova Eventi da Completare
            </a>
        </div>
        <?php else: ?>
        
        <!-- Risultati Recenti -->
        <div class="results-section">
            <div class="section-header">
                <i class="fas fa-list-ol"></i> I Tuoi Risultati
            </div>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Pos.</th>
                        <th>Evento</th>
                        <th>Data</th>
                        <th>Tempo</th>
                        <th>Disciplina</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userResults as $result): ?>
                    <tr>
                        <td>
                            <span class="position-badge <?= 
                                $result['posizione'] == 1 ? 'position-1' : 
                                ($result['posizione'] == 2 ? 'position-2' : 
                                ($result['posizione'] == 3 ? 'position-3' : 'position-other'))
                            ?>">
                                <?= $result['posizione'] ?>°
                            </span>
                        </td>
                        <td>
                            <a href="/events/<?= $result['evento_id'] ?>" class="event-link">
                                <?= htmlspecialchars($result['event_title']) ?>
                            </a>
                        </td>
                        <td><?= date('d/m/Y', strtotime($result['event_date'])) ?></td>
                        <td>
                            <?php if ($result['tempo_finale']): ?>
                                <span class="time-badge"><?= $result['tempo_finale'] ?></span>
                            <?php else: ?>
                                <span style="color: #999;">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($result['event_discipline']) ?></td>
                        <td>
                            <a href="/community/event/<?= $result['evento_id'] ?>" 
                               style="color: #28a745; text-decoration: none; font-size: 0.9rem;">
                                <i class="fas fa-comments"></i> Community
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Personal Bests -->
        <?php
        // Raggruppa i risultati per disciplina per trovare i personal bests
        $personalBests = [];
        foreach ($userResults as $result) {
            $discipline = $result['event_discipline'];
            if (!isset($personalBests[$discipline]) || 
                ($result['tempo_finale'] && 
                 (!$personalBests[$discipline]['tempo_finale'] || 
                  $result['tempo_finale'] < $personalBests[$discipline]['tempo_finale']))) {
                $personalBests[$discipline] = $result;
            }
        }
        ?>

        <?php if (!empty($personalBests)): ?>
        <div class="results-section">
            <div class="section-header">
                <i class="fas fa-star"></i> Personal Best per Disciplina
            </div>
            <div class="personal-bests">
                <?php foreach ($personalBests as $discipline => $result): ?>
                <div class="pb-card">
                    <div class="pb-discipline"><?= htmlspecialchars($discipline) ?></div>
                    <div class="pb-time">
                        <?= $result['tempo_finale'] ?: 'N/A' ?>
                    </div>
                    <div class="pb-event">
                        <?= htmlspecialchars($result['event_title']) ?> 
                        (<?= date('d/m/Y', strtotime($result['event_date'])) ?>)
                        - Pos. <?= $result['posizione'] ?>°
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Achievements -->
        <?php if ($podiums > 0): ?>
        <div class="results-section">
            <div class="section-header">
                <i class="fas fa-award"></i> Traguardi Raggiunti
            </div>
            <div style="padding: 20px;">
                <?php if ($podiums >= 1): ?>
                <div class="achievement-card">
                    <div class="achievement-title">🥇 Primo Podio!</div>
                    <div>Hai conquistato il tuo primo posto sul podio</div>
                </div>
                <?php endif; ?>
                
                <?php if ($podiums >= 5): ?>
                <div class="achievement-card">
                    <div class="achievement-title">🏆 Veterano del Podio</div>
                    <div>Hai raggiunto 5 podi - sei un atleta esperto!</div>
                </div>
                <?php endif; ?>
                
                <?php if (count($userResults) >= 10): ?>
                <div class="achievement-card">
                    <div class="achievement-title">🎯 Atleta Costante</div>
                    <div>Hai completato 10 eventi - la costanza premia!</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>

        <!-- Info Box -->
        <div style="background: rgba(255,255,255,0.9); border-radius: 15px; padding: 20px; margin-top: 30px; border-left: 4px solid #fd7e14;">
            <h4 style="margin: 0 0 10px 0; color: #fd7e14;">
                <i class="fas fa-info-circle"></i> Come vengono calcolati i risultati
            </h4>
            <ul style="margin: 0; padding-left: 20px; color: #666; line-height: 1.6;">
                <li>I risultati vengono inseriti dagli <strong>organizzatori</strong> dopo l'evento</li>
                <li>Le <strong>posizioni</strong> sono calcolate automaticamente in base ai tempi</li>
                <li>I <strong>Personal Best</strong> sono i tuoi migliori tempi per disciplina</li>
                <li>Gli <strong>achievement</strong> si sbloccano automaticamente raggiungendo certi traguardi</li>
            </ul>
        </div>
    </div>
</body>
</html>