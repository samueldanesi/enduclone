<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Universale - SportEvents</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .community-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .community-header {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .community-header h1 {
            margin: 0 0 15px 0;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .community-header p {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 25px;
        }

        .create-post-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .create-post-prompt {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .create-post-prompt:hover {
            background: #e9ecef;
            border-color: #667eea;
        }

        .create-post-prompt img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .create-post-prompt span {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .post-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            margin-bottom: 25px;
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
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .post-info h4 {
            margin: 0 0 5px 0;
            color: #333;
            font-weight: 600;
        }

        .post-meta {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .post-content {
            padding: 0 25px 20px;
        }

        .post-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .post-text {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .post-actions {
            padding: 15px 25px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 20px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border: none;
            background: transparent;
            color: #666;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .action-btn:hover {
            background: #f0f2ff;
            color: #667eea;
        }

        .action-btn.liked {
            color: #e74c3c;
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

        .btn-secondary {
            background: #6c757d;
            color: white;
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
    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    
    <div class="community-container">
        <!-- Header -->
        <div class="community-header">
            <h1><i class="fas fa-globe"></i> Community Universale</h1>
            <p>Chat generale aperta a tutti gli utenti della piattaforma</p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="/community/create?type=universal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crea Post
                </a>
                <a href="/community" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna alle Sezioni
                </a>
            </div>
        </div>

        <!-- Create Post Card -->
        <div class="create-post-card">
            <div class="create-post-prompt" onclick="window.location.href='/community/create?type=universal'">
                <div class="post-avatar">
                    <?= strtoupper(substr($_SESSION['user_nome'] ?? 'U', 0, 1)) ?>
                </div>
                <span>Condividi qualcosa con tutta la community...</span>
            </div>
        </div>

        <!-- Posts Feed -->
        <?php if (empty($posts)): ?>
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3>Nessun post nella community universale</h3>
            <p>Sii il primo a condividere qualcosa con tutti!</p>
            <a href="/community/create?type=universal" class="btn btn-primary">
                <i class="fas fa-plus"></i> Crea il primo post
            </a>
        </div>
        <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <div class="post-card">
            <!-- Post Header -->
            <div class="post-header">
                <div class="post-avatar">
                    <?= strtoupper(substr($post['user_nome'], 0, 1)) ?>
                </div>
                <div class="post-info">
                    <h4><?= htmlspecialchars($post['user_nome'] . ' ' . $post['user_cognome']) ?></h4>
                    <div class="post-meta">
                        <span><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Post Content -->
            <div class="post-content">
                <?php if ($post['title']): ?>
                    <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                <?php endif; ?>
                
                <div class="post-text">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>

                <?php if ($post['media_url']): ?>
                    <div class="post-media">
                        <img src="<?= htmlspecialchars($post['media_url']) ?>" 
                             alt="<?= htmlspecialchars($post['media_caption'] ?? '') ?>"
                             style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 10px;">
                        <?php if ($post['media_caption']): ?>
                            <p style="margin-top: 10px; font-style: italic; color: #666;">
                                <?= htmlspecialchars($post['media_caption']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Post Actions -->
            <div class="post-actions">
                <button class="action-btn like-btn <?= $post['user_has_liked'] ? 'liked' : '' ?>" 
                        data-post-id="<?= $post['id'] ?>">
                    <i class="fas fa-heart"></i>
                    <span class="like-count"><?= $post['likes_count'] ?></span> Mi piace
                </button>
                <button class="action-btn comment-btn">
                    <i class="fas fa-comment"></i>
                    <?= $post['comments_count'] ?> Commenti
                </button>
            </div>

            <!-- Comments -->
            <?php if (!empty($post['comments'])): ?>
            <div style="padding: 0 25px 25px; border-top: 1px solid #eee;">
                <?php foreach ($post['comments'] as $comment): ?>
                <div style="display: flex; gap: 12px; margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                    <div style="width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.9rem;">
                        <?= strtoupper(substr($comment['user_nome'], 0, 1)) ?>
                    </div>
                    <div style="flex: 1;">
                        <h5 style="margin: 0 0 5px 0; font-size: 0.95rem; font-weight: 600; color: #333;">
                            <?= htmlspecialchars($comment['user_nome'] . ' ' . $comment['user_cognome']) ?>
                        </h5>
                        <div style="color: #555; font-size: 0.9rem; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($comment['content'])) ?>
                        </div>
                        <div style="color: #999; font-size: 0.8rem; margin-top: 5px;">
                            <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        // Gestione like
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const postId = this.dataset.postId;
                
                try {
                    const response = await fetch('/community/toggle-like', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `target_type=post&target_id=${postId}`
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.classList.toggle('liked');
                        this.querySelector('.like-count').textContent = result.likes_count;
                    }
                } catch (error) {
                    console.error('Errore:', error);
                }
            });
        });
    </script>
</body>
</html>