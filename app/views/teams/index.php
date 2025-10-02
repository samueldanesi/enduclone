<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team e Società Sportive - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset e base */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Hero */
        .hero-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .hero-header h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .hero-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Sezioni */
        .section {
            margin-bottom: 50px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 2rem;
            font-weight: 600;
            color: white;
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .section-title i {
            font-size: 2.2rem;
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        /* Grid dei team */
        .teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        /* Card dei team */
        .team-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .team-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 60px rgba(0,0,0,0.15);
        }

        .team-card.member::before {
            background: linear-gradient(90deg, #56ab2f 0%, #a8e6cf 100%);
        }

        .team-card.member {
            border-left: 5px solid #28a745;
            background: linear-gradient(145deg, #f8fff9 0%, #e8f5e8 100%);
        }

        .team-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .team-card h3 i {
            color: #667eea;
            font-size: 1.3rem;
        }

        .team-card p {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        /* Statistiche team */
        .team-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(102, 126, 234, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #667eea;
            font-weight: 500;
        }

        .stat-item i {
            font-size: 1rem;
        }

        /* Azioni dei team */
        .team-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Buttons moderni */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(86, 171, 47, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(86, 171, 47, 0.4);
        }

        .btn-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }

        /* Badge */
        .member-badge {
            background: linear-gradient(45deg, #56ab2f, #a8e6cf);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 10px rgba(86, 171, 47, 0.3);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            background: rgba(255,255,255,0.95);
            padding: 60px 40px;
            border-radius: 20px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #4a5568;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #718096;
            margin-bottom: 30px;
        }

        /* Animazioni */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .team-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .team-card:nth-child(2) { animation-delay: 0.1s; }
        .team-card:nth-child(3) { animation-delay: 0.2s; }
        .team-card:nth-child(4) { animation-delay: 0.3s; }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .hero-header {
                padding: 30px 20px;
            }
            
            .hero-header h1 {
                font-size: 2.2rem;
            }
            
            .teams-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .hero-actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php 
    require_once __DIR__ . '/../components/navbar.php';
    renderNavbar('teams'); 
    ?>
    
    <div class="container">
        <!-- Hero Header -->
        <div class="hero-header">
            <h1><i class="fas fa-users"></i> Team e Società Sportive</h1>
            <p class="hero-subtitle">Unisciti ai migliori team sportivi o crea la tua squadra per partecipare agli eventi più entusiasmanti</p>
            <div class="hero-actions">
                <a href="/teams/create" class="btn btn-hero">
                    <i class="fas fa-plus-circle"></i> Crea Nuovo Team
                </a>
                <a href="/teams/search" class="btn btn-hero">
                    <i class="fas fa-search"></i> Cerca Team
                </a>
            </div>
        </div>
        
        <!-- I Miei Team -->
        <?php if (!empty($user_teams)): ?>
        <section class="section my-teams">
            <h2 class="section-title">
                <i class="fas fa-star"></i> I Miei Team
            </h2>
            <div class="teams-grid">
                <?php foreach ($user_teams as $team): ?>
                <div class="team-card member">
                    <h3>
                        <i class="fas fa-shield-alt"></i>
                        <?= htmlspecialchars($team['nome']) ?>
                        <span class="member-badge">Membro</span>
                    </h3>
                    <p><?= htmlspecialchars($team['descrizione'] ?? 'Nessuna descrizione disponibile') ?></p>
                    
                    <div class="team-stats">
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span><?= $team['members_count'] ?? 0 ?> membri</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?= $team['events_count'] ?? 0 ?> eventi</span>
                        </div>
                    </div>
                    
                    <div class="team-actions">
                        <a href="/teams/view/<?= $team['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Visualizza
                        </a>
                        <a href="/teams/collective-registrations/<?= $team['id'] ?>" class="btn btn-success">
                            <i class="fas fa-clipboard-list"></i> Iscrizioni
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Tutti i Team -->
        <section class="section all-teams">
            <h2 class="section-title">
                <i class="fas fa-globe"></i> Scopri Altri Team
            </h2>
            
            <?php if (empty($teams)): ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h3>Nessun team trovato</h3>
                <p>Non ci sono team disponibili al momento. Che ne dici di crearne uno nuovo?</p>
                <a href="/teams/create" class="btn btn-hero">
                    <i class="fas fa-plus-circle"></i> Crea il Primo Team
                </a>
            </div>
            <?php else: ?>
            <div class="teams-grid">
                <?php foreach ($teams as $team): ?>
                <div class="team-card">
                    <h3>
                        <i class="fas fa-flag"></i>
                        <?= htmlspecialchars($team['nome']) ?>
                    </h3>
                    <p><?= htmlspecialchars($team['descrizione'] ?? 'Nessuna descrizione disponibile') ?></p>
                    
                    <div class="team-stats">
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span><?= $team['members_count'] ?? 0 ?> membri</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($team['indirizzo'] ?? 'Ubicazione non specificata') ?></span>
                        </div>
                    </div>
                    
                    <div class="team-actions">
                        <a href="/teams/view/<?= $team['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-info-circle"></i> Dettagli
                        </a>
                        <?php if ($team['can_join'] ?? false): ?>
                            <form method="POST" action="/teams/join" style="display: inline;">
                                <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus"></i> Unisciti
                                </button>
                            </form>
                        <?php elseif ($team['is_member'] ?? false): ?>
                            <span class="btn btn-success" style="opacity: 0.7; cursor: default;">
                                <i class="fas fa-check-circle"></i> Già Membro
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Script per animazioni -->
    <script>
        // Animazione di caricamento progressivo delle card
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.team-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });

        // Effetto hover sui bottoni
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.05)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>
