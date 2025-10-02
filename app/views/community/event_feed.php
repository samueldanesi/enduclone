<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed Evento: <?= htmlspecialchars($event['nome']) ?> - Community SportEvents</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .event-feed-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .event-header {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .event-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .event-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .event-details h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            color: #333;
        }

        .event-meta {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 1rem;
        }

        .event-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .tabs-container {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            margin-bottom: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .tabs-nav {
            display: flex;
            background: #f8f9fa;
        }

        .tab-btn {
            flex: 1;
            padding: 15px 20px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .tab-content {
            padding: 25px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .post-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            margin-bottom: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .post-header {
            padding: 20px 25px 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .post-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .post-info h4 {
            margin: 0 0 5px 0;
            color: #333;
            font-weight: 600;
        }

        .post-time {
            color: #666;
            font-size: 0.9rem;
        }

        .post-content {
            padding: 0 25px 20px;
        }

        .post-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .post-text {
            color: #555;
            line-height: 1.6;
        }

        .post-media img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 15px;
        }

        .result-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .result-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .result-details {
            color: #666;
            font-size: 0.9rem;
        }

        .result-time {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .position-badge {
            background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .review-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reviewer-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e74c3c 0%, #f39c12 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .stars {
            color: #ffd700;
            font-size: 1.2rem;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="event-feed-container">
        <!-- Header Evento -->
        <div class="event-header">
            <div class="event-info">
                <div class="event-icon">
                    <i class="fas fa-running"></i>
                </div>
                <div class="event-details">
                    <h1><?= htmlspecialchars($event['nome']) ?></h1>
                    <div class="event-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($event['data_evento'])) ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['luogo']) ?></span>
                        <span><i class="fas fa-users"></i> <?= $event['max_partecipanti'] ?> partecipanti</span>
                    </div>
                </div>
            </div>

            <div class="event-stats">
                <div class="stat-card">
                    <div class="stat-number"><?= count($posts) ?></div>
                    <div class="stat-label">Post</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($results) ?></div>
                    <div class="stat-label">Risultati</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($reviews) ?></div>
                    <div class="stat-label">Recensioni</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($photos) ?></div>
                    <div class="stat-label">Foto</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="posts">
                    <i class="fas fa-comments"></i> Post (<?= count($posts) ?>)
                </button>
                <button class="tab-btn" data-tab="results">
                    <i class="fas fa-trophy"></i> Classifica (<?= count($results) ?>)
                </button>
                <button class="tab-btn" data-tab="reviews">
                    <i class="fas fa-star"></i> Recensioni (<?= count($reviews) ?>)
                </button>
                <button class="tab-btn" data-tab="gallery">
                    <i class="fas fa-images"></i> Galleria (<?= count($photos) ?>)
                </button>
            </div>

            <div class="tab-content">
                <!-- Tab Post -->
                <div class="tab-pane active" id="posts">
                    <?php if (empty($posts)): ?>
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h3>Nessun post ancora</h3>
                        <p>Sii il primo a condividere la tua esperienza per questo evento!</p>
                        <a href="/community/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crea Post
                        </a>
                    </div>
                    <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div class="post-avatar">
                                <?= strtoupper(substr($post['user_nome'], 0, 1)) ?>
                            </div>
                            <div class="post-info">
                                <h4><?= htmlspecialchars($post['user_nome'] . ' ' . $post['user_cognome']) ?></h4>
                                <div class="post-time"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></div>
                            </div>
                        </div>
                        <div class="post-content">
                            <?php if ($post['title']): ?>
                                <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                            <?php endif; ?>
                            <div class="post-text"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
                            <?php if ($post['media_url']): ?>
                                <img src="<?= htmlspecialchars($post['media_url']) ?>" 
                                     alt="<?= htmlspecialchars($post['media_caption'] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Tab Classifica -->
                <div class="tab-pane" id="results">
                    <?php if (empty($results)): ?>
                    <div class="empty-state">
                        <i class="fas fa-trophy"></i>
                        <h3>Nessun risultato ancora</h3>
                        <p>I risultati verranno pubblicati al termine dell'evento.</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($results as $result): ?>
                    <div class="result-card">
                        <div class="result-info">
                            <h4><?= htmlspecialchars($result['user_nome'] . ' ' . $result['user_cognome']) ?></h4>
                            <div class="result-details">
                                Categoria: <?= htmlspecialchars($result['category']) ?><br>
                                Passo: <?= htmlspecialchars($result['pace']) ?>/km
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div class="position-badge"><?= $result['position'] ?>°</div>
                            <div class="result-time"><?= htmlspecialchars($result['finish_time']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Tab Recensioni -->
                <div class="tab-pane" id="reviews">
                    <?php if (empty($reviews)): ?>
                    <div class="empty-state">
                        <i class="fas fa-star"></i>
                        <h3>Nessuna recensione ancora</h3>
                        <p>Condividi la tua opinione sull'evento!</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">
                                    <?= strtoupper(substr($review['user_nome'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h5><?= htmlspecialchars($review['user_nome'] . ' ' . $review['user_cognome']) ?></h5>
                                    <small><?= date('d/m/Y', strtotime($review['created_at'])) ?></small>
                                </div>
                            </div>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p><?= nl2br(htmlspecialchars($review['review'])) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Tab Galleria -->
                <div class="tab-pane" id="gallery">
                    <?php if (empty($photos)): ?>
                    <div class="empty-state">
                        <i class="fas fa-images"></i>
                        <h3>Nessuna foto ancora</h3>
                        <p>Condividi le tue foto dell'evento!</p>
                        <a href="/community/create" class="btn btn-primary">
                            <i class="fas fa-camera"></i> Carica Foto
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="gallery-grid">
                        <?php foreach ($photos as $photo): ?>
                        <div class="gallery-item">
                            <img src="<?= htmlspecialchars($photo['media_url']) ?>" 
                                 alt="<?= htmlspecialchars($photo['media_caption'] ?? '') ?>"
                                 onclick="openModal('<?= htmlspecialchars($photo['media_url']) ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pulsante Torna alla Community -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="/community" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Torna alla Community
            </a>
        </div>
    </div>

    <!-- Modal per galleria -->
    <div id="imageModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8);" onclick="closeModal()">
        <img id="modalImage" style="margin: 5% auto; display: block; max-width: 90%; max-height: 90%;">
    </div>

    <script>
        // Gestione tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Rimuovi active da tutti
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                
                // Attiva corrente
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        // Modal galleria
        function openModal(src) {
            document.getElementById('imageModal').style.display = 'block';
            document.getElementById('modalImage').src = src;
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Chiudi modal con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>