<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community SportEvents</title>
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

        .event-badge {
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
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

        .post-media img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
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

        .comments-section {
            padding: 0 25px 25px;
            border-top: 1px solid #eee;
        }

        .comment {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
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
        }

        .comment-content h5 {
            margin: 0 0 5px 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
        }

        .comment-text {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .comment-time {
            color: #999;
            font-size: 0.8rem;
            margin-top: 5px;
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
    <?php 
    require_once __DIR__ . '/../components/navbar.php';
    renderNavbar('community'); 
    ?>
    
    <div class="community-container">
        <!-- Header -->
        <div class="community-header">
            <h1><i class="fas fa-users"></i> Community SportEvents</h1>
            <p>Scegli la sezione che ti interessa</p>
        </div>

        <!-- Sezioni Community -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-bottom: 30px;">
            
            <!-- 1. Community Universale -->
            <div class="post-card" style="cursor: pointer;" onclick="window.location.href='/community/universal'">
                <div class="post-header">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white;">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="post-info">
                        <h4>Community Universale</h4>
                        <div class="post-time">Chat generale per tutti</div>
                    </div>
                </div>
                <div class="post-content">
                    <div class="post-text">
                        Discuti di tutto con tutti gli utenti della piattaforma. 
                        Condividi domande, esperienze, consigli sportivi generali.
                    </div>
                </div>
                <div class="post-actions">
                    <div class="action-btn">
                        <i class="fas fa-arrow-right"></i> Entra nella Community
                    </div>
                </div>
            </div>

            <!-- 2. Community Eventi -->
            <div class="post-card" style="cursor: pointer;" onclick="window.location.href='/community/events'">
                <div class="post-header">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="post-info">
                        <h4>Community Eventi</h4>
                        <div class="post-time">Chat per eventi specifici</div>
                    </div>
                </div>
                <div class="post-content">
                    <div class="post-text">
                        Accedi alle community dedicate degli eventi a cui partecipi. 
                        Solo con altri partecipanti dello stesso evento.
                    </div>
                </div>
                <div class="post-actions">
                    <div class="action-btn">
                        <i class="fas fa-arrow-right"></i> Vedi Miei Eventi
                    </div>
                </div>
            </div>

            <!-- 3. Risultati -->
            <div class="post-card" style="cursor: pointer;" onclick="window.location.href='/community/results'">
                <div class="post-header">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="post-info">
                        <h4>Risultati & Classifiche</h4>
                        <div class="post-time">Statistiche e tempi</div>
                    </div>
                </div>
                <div class="post-content">
                    <div class="post-text">
                        Consulta i risultati degli eventi, classifiche, tempi e statistiche personali. 
                        Confronta le tue performance.
                    </div>
                </div>
                <div class="post-actions">
                    <div class="action-btn">
                        <i class="fas fa-arrow-right"></i> Vedi Risultati
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Post Card -->
        <div class="create-post-card">
            <div class="create-post-prompt" onclick="window.location.href='/community/create'">
                <div class="post-avatar">
                    <?= strtoupper(substr($_SESSION['user_nome'] ?? 'U', 0, 1)) ?>
                </div>
                <span>Cosa vuoi condividere oggi?</span>
            </div>
        </div>

        <!-- Statistiche rapide -->
        <div class="post-card">
            <div class="post-header">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #e74c3c 0%, #f39c12 100%); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: white;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="post-info">
                    <h4>Le tue statistiche</h4>
                    <div class="post-time">Panoramica rapida</div>
                </div>
            </div>
            <div class="post-content">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
                    <div>
                        <div style="font-size: 2rem; font-weight: bold; color: #667eea;"><?= $userStats['posts_count'] ?? 0 ?></div>
                        <div style="color: #666; font-size: 0.9rem;">Post Creati</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: bold; color: #28a745;"><?= $userStats['events_count'] ?? 0 ?></div>
                        <div style="color: #666; font-size: 0.9rem;">Eventi Partecipati</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: bold; color: #ff6b6b;"><?= $userStats['results_count'] ?? 0 ?></div>
                        <div style="color: #666; font-size: 0.9rem;">Risultati Ottenuti</div>
                    </div>
                </div>
            </div>
        </div>
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

        // Auto-refresh ogni 30 secondi
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>