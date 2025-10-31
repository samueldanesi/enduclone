<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community <?= htmlspecialchars($event['titolo']) ?> - SportEvents</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .event-community {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .event-header {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .event-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #666;
            margin-bottom: 20px;
        }

        .event-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }

        .event-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 15px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .post-form {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .post-form h3 {
            margin: 0 0 20px 0;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .posts-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .post {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .post:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .post-header {
            padding: 20px 25px 15px 25px;
            border-bottom: 1px solid #eee;
        }

        .post-author {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .author-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .author-info h4 {
            margin: 0;
            font-size: 1rem;
            color: #333;
        }

        .author-info span {
            font-size: 0.8rem;
            color: #666;
        }

        .post-content {
            padding: 0 25px;
            color: #333;
            line-height: 1.6;
        }

        .post-actions {
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
        }

        .post-stats {
            display: flex;
            gap: 20px;
        }

        .post-stat {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 0.9rem;
        }

        .like-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .like-btn:hover,
        .like-btn.liked {
            background: #e3f2fd;
            color: #1976d2;
        }

        .comments-section {
            border-top: 1px solid #eee;
            padding: 20px 25px;
            background: #fafafa;
        }

        .comment {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .comment:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .comment-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .comment-content {
            flex: 1;
        }

        .comment-author {
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }

        .comment-text {
            color: #555;
            line-height: 1.4;
            font-size: 0.9rem;
        }

        .comment-time {
            font-size: 0.8rem;
            color: #999;
            margin-top: 5px;
        }

        .comment-form {
            display: flex;
            gap: 12px;
            margin-top: 15px;
        }

        .comment-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .comment-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
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

        .navigation {
            margin-bottom: 20px;
        }

        .breadcrumb {
            background: rgba(255,255,255,0.8);
            padding: 15px 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .event-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .event-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .post {
                margin: 0 -10px;
            }
        }
    </style>
</head>
<body>
    <div class="event-community">
        <!-- Navigation -->
        <div class="navigation">
            <div class="breadcrumb">
                <a href="/community"><i class="fas fa-home"></i> Community</a> /
                <a href="/community/events"><i class="fas fa-calendar"></i> Eventi</a> /
                <strong><?= htmlspecialchars($event['titolo']) ?></strong>
            </div>
        </div>

        <!-- Event Header -->
        <div class="event-header">
            <h1 class="event-title">
                <i class="fas fa-users"></i> 
                Community: <?= htmlspecialchars($event['titolo']) ?>
            </h1>
            
            <div class="event-meta">
                <span><i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($event['data_evento'])) ?></span>
                <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['luogo_partenza']) ?></span>
                <span><i class="fas fa-running"></i> <?= htmlspecialchars($event['sport'] ?? 'Sport non specificato') ?></span>
                <?php if ($event['distanza_km']): ?>
                    <span><i class="fas fa-route"></i> <?= $event['distanza_km'] ?> km</span>
                <?php endif; ?>
            </div>

            <div class="event-stats">
                <div class="stat">
                    <div class="stat-number"><?= $totalParticipants ?></div>
                    <div class="stat-label">Partecipanti</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?= count($eventPosts) ?></div>
                    <div class="stat-label">Post</div>
                </div>
                <div class="stat">
                    <div class="stat-number">
                        <?= (strtotime($event['data_evento']) > time()) ? 
                            ceil((strtotime($event['data_evento']) - time()) / (60*60*24)) : 
                            ceil((time() - strtotime($event['data_evento'])) / (60*60*24))
                        ?>
                    </div>
                    <div class="stat-label">
                        <?= (strtotime($event['data_evento']) > time()) ? 'Giorni Mancanti' : 'Giorni Fa' ?>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="/events/<?= $event['event_id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-info-circle"></i> Dettagli Evento
                </a>
            </div>
        </div>

        <!-- Post Form -->
        <?php if ($isRegistered): ?>
        <form class="post-form" method="POST" action="/community/create">
            <h3><i class="fas fa-pen"></i> Condividi con i partecipanti</h3>
            <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
            <div class="form-group">
                <textarea name="contenuto" 
                          class="form-control" 
                          placeholder="Condividi la tua esperienza, foto, consigli o domande con gli altri partecipanti..."
                          required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-share"></i> Pubblica Post
            </button>
        </form>
        <?php else: ?>
        <div class="post-form">
            <h3><i class="fas fa-lock"></i> Accesso Limitato</h3>
            <p>Solo i partecipanti registrati a questo evento possono pubblicare nella sua community.</p>
            <a href="<?= BASE_URL ?>/events/<?= $event['event_id'] ?>/register" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Iscriviti all'Evento
            </a>
        </div>
        <?php endif; ?>

        <!-- Posts -->
        <div class="posts-container">
            <?php if (empty($eventPosts)): ?>
            <div class="empty-state">
                <i class="fas fa-comments"></i>
                <h3>Nessun post ancora</h3>
                <p>Sii il primo a condividere qualcosa con gli altri partecipanti!</p>
                <?php if (!$isRegistered): ?>
                <p><small>Nota: devi essere iscritto all'evento per pubblicare.</small></p>
                <?php endif; ?>
            </div>
            <?php else: ?>
                <?php foreach ($eventPosts as $post): ?>
                <div class="post">
                    <div class="post-header">
                        <div class="post-author">
                            <div class="author-avatar">
                                <?= strtoupper(substr($post['author_name'], 0, 1)) ?>
                            </div>
                            <div class="author-info">
                                <h4><?= htmlspecialchars($post['author_name']) ?></h4>
                                <span><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <p><?= nl2br(htmlspecialchars($post['contenuto'])) ?></p>
                    </div>
                    
                    <div class="post-actions">
                        <div class="post-stats">
                            <div class="post-stat">
                                <i class="fas fa-heart"></i>
                                <span><?= $post['likes_count'] ?> Mi piace</span>
                            </div>
                            <div class="post-stat">
                                <i class="fas fa-comment"></i>
                                <span><?= $post['comments_count'] ?> Commenti</span>
                            </div>
                        </div>
                        
                        <?php if ($isRegistered): ?>
                        <button class="like-btn <?= $post['user_liked'] ? 'liked' : '' ?>" 
                                data-post-id="<?= $post['id'] ?>">
                            <i class="fas fa-heart"></i>
                            <span><?= $post['user_liked'] ? 'Ti piace' : 'Mi piace' ?></span>
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Comments -->
                    <?php if (!empty($post['comments'])): ?>
                    <div class="comments-section">
                        <?php foreach ($post['comments'] as $comment): ?>
                        <div class="comment">
                            <div class="comment-avatar">
                                <?= strtoupper(substr($comment['author_name'], 0, 1)) ?>
                            </div>
                            <div class="comment-content">
                                <div class="comment-author"><?= htmlspecialchars($comment['author_name']) ?></div>
                                <div class="comment-text"><?= nl2br(htmlspecialchars($comment['contenuto'])) ?></div>
                                <div class="comment-time"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if ($isRegistered): ?>
                        <form class="comment-form" method="POST" action="/community/comment">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <input type="text" 
                                   name="contenuto" 
                                   class="comment-input" 
                                   placeholder="Scrivi un commento..."
                                   required>
                            <button type="submit" class="comment-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Info Box -->
        <div style="background: rgba(255,255,255,0.9); border-radius: 15px; padding: 20px; margin-top: 30px; border-left: 4px solid #667eea;">
            <h4 style="margin: 0 0 10px 0; color: #667eea;">
                <i class="fas fa-info-circle"></i> Community dell'Evento
            </h4>
            <ul style="margin: 0; padding-left: 20px; color: #666; line-height: 1.6;">
                <li>Questa community è <strong>esclusiva</strong> per i partecipanti registrati</li>
                <li>Condividi <strong>esperienze, foto e consigli</strong> specifici di questo evento</li>
                <li>Interagisci con <strong>altri partecipanti</strong> prima, durante e dopo l'evento</li>
                <li>Rispetta sempre le <strong>regole della community</strong> e sii rispettoso</li>
            </ul>
        </div>
    </div>

    <script>
        // Gestione like posts
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const postId = this.dataset.postId;
                const isLiked = this.classList.contains('liked');
                
                try {
                    const response = await fetch('/community/like', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `post_id=${postId}&action=${isLiked ? 'unlike' : 'like'}`
                    });
                    
                    if (response.ok) {
                        location.reload(); // Ricarica per aggiornare i conteggi
                    }
                } catch (error) {
                    console.error('Errore nel like:', error);
                }
            });
        });

        // Auto-expand textarea
        document.querySelector('textarea[name="contenuto"]')?.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(100, this.scrollHeight) + 'px';
        });
    </script>
</body>
</html>