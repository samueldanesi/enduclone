<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventi Sportivi - SportEvents</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }



        .search-section {
            background: rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .search-box {
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
            padding: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .search-input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 16px;
            outline: none;
            padding: 10px 15px;
        }

        .search-input::placeholder {
            color: #666;
        }

        .filter-chips {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .filter-chip {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 25px;
            padding: 8px 16px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-chip:hover, .filter-chip.active {
            background: rgba(255,255,255,0.9);
            color: #333;
            transform: translateY(-1px);
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            padding: 0 20px;
        }

        .event-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .event-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            position: relative;
        }

        .event-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255,255,255,0.9);
            color: #333;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .event-content {
            padding: 25px;
        }

        .event-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.3;
        }

        .event-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
            color: #666;
        }

        .event-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .event-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }

        .event-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
        }

        .participants-bar {
            background: #f0f0f0;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
            margin: 10px 0;
        }

        .participants-fill {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .section-title p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .stats-info {
            background: rgba(255,255,255,0.1);
            padding: 15px 25px;
            border-radius: 15px;
            color: white;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 768px) {
            .search-box {
                flex-direction: column;
                gap: 15px;
            }
            
            .event-grid {
                grid-template-columns: 1fr;
                padding: 0 15px;
            }
            
            .section-title h1 {
                font-size: 2rem;
            }
            

        }
    </style>
</head>
<body>
    <!-- Navigation unificata -->
    <?php 
    require_once __DIR__ . '/../components/navbar.php';
    renderNavbar('events'); 
    ?>

    <!-- Hero Section -->
    <section style="padding: 40px 0;">
        <div class="container">
            <div class="section-title">
                <h1 class="gradient-text">Trova il Tuo Evento Perfetto</h1>
                <p>Scopri migliaia di eventi sportivi in tutta Italia</p>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section style="padding: 20px 0;">
        <div class="container">
            <div class="search-section">
                <form method="GET" action="/events" id="searchForm">
                    <div class="search-box">
                        <i class="fas fa-search" style="color: #666; font-size: 20px;"></i>
                        <input type="text" 
                               name="search" 
                               class="search-input" 
                               placeholder="Cerca eventi per nome, città, organizzatore..."
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary" style="flex: none;">
                            <i class="fas fa-search"></i> Cerca
                        </button>
                    </div>

                    <!-- Filter Chips -->
                    <div class="filter-chips">
                        <div class="filter-chip <?= ($_GET['sport'] ?? '') === '' ? 'active' : '' ?>" 
                             onclick="setFilter('sport', '')">
                            <i class="fas fa-globe"></i> Tutti gli Sport
                        </div>
                        <div class="filter-chip <?= ($_GET['sport'] ?? '') === 'running' ? 'active' : '' ?>" 
                             onclick="setFilter('sport', 'running')">
                            🏃‍♂️ Running
                        </div>
                        <div class="filter-chip <?= ($_GET['sport'] ?? '') === 'cycling' ? 'active' : '' ?>" 
                             onclick="setFilter('sport', 'cycling')">
                            🚴‍♂️ Ciclismo
                        </div>
                        <div class="filter-chip <?= ($_GET['sport'] ?? '') === 'triathlon' ? 'active' : '' ?>" 
                             onclick="setFilter('sport', 'triathlon')">
                            🏊‍♂️ Triathlon
                        </div>
                        <div class="filter-chip <?= ($_GET['sport'] ?? '') === 'swimming' ? 'active' : '' ?>" 
                             onclick="setFilter('sport', 'swimming')">
                            🏊‍♀️ Nuoto
                        </div>
                        <div class="filter-chip <?= ($_GET['sport'] ?? '') === 'trail' ? 'active' : '' ?>" 
                             onclick="setFilter('sport', 'trail')">
                            🥾 Trail
                        </div>
                    </div>

                    <!-- Hidden inputs for filters -->
                    <input type="hidden" name="sport" id="sportFilter" value="<?= htmlspecialchars($_GET['sport'] ?? '') ?>">
                    <input type="hidden" name="città" id="cittàFilter" value="<?= htmlspecialchars($_GET['città'] ?? '') ?>">
                </form>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section style="padding: 40px 0;">
        <div class="container">
            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stats-info">
                    <strong><?= count($events ?? []) ?></strong> eventi trovati
                </div>
                <div style="display: flex; gap: 15px;">
                    <select onchange="sortEvents(this.value)" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); border-radius: 10px; padding: 10px 15px; color: white; backdrop-filter: blur(10px);">
                        <option value="date">Ordina per Data</option>
                        <option value="price">Ordina per Prezzo</option>
                        <option value="name">Ordina per Nome</option>
                        <option value="popularity">Ordina per Popolarità</option>
                    </select>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="event-grid">
                <?php
                // Gli eventi vengono caricati dal controller dal database
                if (empty($events)) {
                    $events = [];
                }
                
                // Controllo se ci sono eventi da mostrare
                if (count($events) === 0): ?>
                    <div class="no-events" style="text-align: center; padding: 60px 20px; color: rgba(255,255,255,0.8);">
                        <i class="fas fa-calendar-times" style="font-size: 4rem; margin-bottom: 20px; color: rgba(255,255,255,0.5);"></i>
                        <h3 style="font-size: 1.5rem; margin-bottom: 15px;">Nessun evento disponibile</h3>
                        <p style="font-size: 1.1rem;">Al momento non ci sono eventi pubblicati. Torna presto per nuove opportunità sportive!</p>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'organizer'): ?>
                            <a href="/organizer/create" style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 25px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                                <i class="fas fa-plus"></i> Crea il primo evento
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                
                <?php 
                // Array di supporto per visualizzazione
                $sportEmojis = [
                    'running' => '🏃‍♂️',
                    'cycling' => '🚴‍♂️',
                    'triathlon' => '🏊‍♂️',
                    'swimming' => '🏊‍♀️',
                    'trail' => '🥾',
                    'mtb' => '🚵‍♂️'
                ];

                $sportLabels = [
                    'running' => 'Running',
                    'cycling' => 'Ciclismo',
                    'triathlon' => 'Triathlon',
                    'swimming' => 'Nuoto',
                    'trail' => 'Trail Running',
                    'mtb' => 'Mountain Bike'
                ];
                ?>
                <?php endif; ?>
                
                <?php foreach ($events as $event):
                    $participationRate = $event['max_partecipanti'] > 0 ? 
                        ($event['registrations_count'] / $event['max_partecipanti']) * 100 : 0;
                ?>
                    <div class="event-card">
                        <div class="event-image">
                            <?= $sportEmojis[$event['sport']] ?? '🏃‍♂️' ?>
                            <div class="event-badge">
                                <?= $sportLabels[$event['sport']] ?? 'Sport' ?>
                            </div>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?= htmlspecialchars($event['titolo']) ?></h3>
                            
                            <div class="event-info">
                                <div class="event-info-item">
                                    <i class="fas fa-calendar-alt" style="color: #667eea;"></i>
                                    <span><?= date('d M Y', strtotime($event['data_evento'])) ?></span>
                                </div>
                                <div class="event-info-item">
                                    <i class="fas fa-map-marker-alt" style="color: #667eea;"></i>
                                    <span><?= htmlspecialchars($event['luogo_partenza']) ?></span>
                                </div>
                                <?php if ($event['distanza_km']): ?>
                                <div class="event-info-item">
                                    <i class="fas fa-route" style="color: #667eea;"></i>
                                    <span><?= $event['distanza_km'] ?> km</span>
                                </div>
                                <?php endif; ?>
                                <div class="event-info-item">
                                    <i class="fas fa-user" style="color: #667eea;"></i>
                                    <span><?= htmlspecialchars($event['organizer_name']) ?></span>
                                </div>
                            </div>

                            <div class="event-price">€<?= number_format($event['prezzo_base'], 0) ?></div>

                            <div style="margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; font-size: 12px; color: #666; margin-bottom: 5px;">
                                    <span><?= $event['registrations_count'] ?> iscritti</span>
                                    <span><?= $event['max_partecipanti'] ?> posti</span>
                                </div>
                                <div class="participants-bar">
                                    <div class="participants-fill" style="width: <?= min($participationRate, 100) ?>%"></div>
                                </div>
                            </div>

                            <div class="event-actions">
                                <a href="/events/<?= $event['event_id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-info-circle"></i> Dettagli
                                </a>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="<?= BASE_URL ?>/events/<?= $event['event_id'] ?>/register" class="btn btn-secondary">
                                        <i class="fas fa-plus"></i> Iscriviti
                                    </a>
                                <?php else: ?>
                                    <a href="/login" class="btn btn-secondary">
                                        <i class="fas fa-sign-in-alt"></i> Accedi
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <script>
        function setFilter(type, value) {
            document.getElementById(type + 'Filter').value = value;
            document.getElementById('searchForm').submit();
        }

        function sortEvents(sortBy) {
            // Implementa logica di ordinamento
            const url = new URL(window.location);
            url.searchParams.set('sort', sortBy);
            window.location.href = url.toString();
        }

        // Gestione menu profilo a tendina
        function toggleProfileMenu() {
            const menu = document.getElementById('profileMenu');
            menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
        }

        // Chiudi menu se si clicca fuori
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('profileMenu');
            const button = event.target.closest('.btn');
            
            if (!button || !button.onclick) {
                if (menu) menu.style.display = 'none';
            }
        });

        // Carica conteggio notifiche
        function loadNotificationCount() {
            <?php if (isset($_SESSION['user_id'])): ?>
            fetch('/api/notifications/count')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.getElementById('notification-count');
                    if (data.count > 0) {
                        countElement.textContent = data.count > 99 ? '99+' : data.count;
                        countElement.style.display = 'block';
                    } else {
                        countElement.style.display = 'none';
                    }
                })
                .catch(error => console.log('Errore caricamento notifiche:', error));
            <?php endif; ?>
        }

        // Animazioni di caricamento
        window.addEventListener('load', function() {
            // Carica conteggio notifiche
            loadNotificationCount();
            
            const cards = document.querySelectorAll('.event-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });
        });
    </script>
</body>
</html>