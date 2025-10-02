<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trova Team - SportEvents</title>
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .search-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .search-filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .filter-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .team-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .team-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            position: relative;
        }
        
        .team-name {
            font-size: 1.4em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .team-category {
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            display: inline-block;
        }
        
        .team-card-body {
            padding: 20px;
        }
        
        .team-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
            height: 60px;
            overflow: hidden;
        }
        
        .team-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.9em;
            color: #777;
        }
        
        .team-members {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .team-visibility {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .visibility-pubblico {
            background: #d4edda;
            color: #155724;
        }
        
        .visibility-privato {
            background: #f8d7da;
            color: #721c24;
        }
        
        .team-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            flex: 1;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .no-teams {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
        }
        
        .modal-header {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <!-- Header -->
        <div class="search-header">
            <h1>🏃‍♂️ Trova il Tuo Team</h1>
            <p>Scopri team pubblici e unisciti alla community sportiva</p>
        </div>
        
        <!-- Messaggi di Sistema -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Filtri di Ricerca -->
        <div class="search-filters">
            <form method="GET" action="/teams/search">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">🔍 Cerca per Nome</label>
                        <input type="text" name="search" class="filter-input" 
                               placeholder="Nome team o parole chiave..." 
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">🏃 Categoria Sport</label>
                        <select name="categoria" class="filter-input">
                            <option value="">Tutte le categorie</option>
                            <option value="running" <?= ($_GET['categoria'] ?? '') === 'running' ? 'selected' : '' ?>>Running</option>
                            <option value="ciclismo" <?= ($_GET['categoria'] ?? '') === 'ciclismo' ? 'selected' : '' ?>>Ciclismo</option>
                            <option value="triathlon" <?= ($_GET['categoria'] ?? '') === 'triathlon' ? 'selected' : '' ?>>Triathlon</option>
                            <option value="trail" <?= ($_GET['categoria'] ?? '') === 'trail' ? 'selected' : '' ?>>Trail</option>
                            <option value="nuoto" <?= ($_GET['categoria'] ?? '') === 'nuoto' ? 'selected' : '' ?>>Nuoto</option>
                            <option value="mtb" <?= ($_GET['categoria'] ?? '') === 'mtb' ? 'selected' : '' ?>>Mountain Bike</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">Cerca Team</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Griglia Team -->
        <div class="team-grid">
            <?php if (empty($teams)): ?>
                <div class="no-teams">
                    <h3>😔 Nessun team trovato</h3>
                    <p>Prova a modificare i filtri di ricerca o crea un nuovo team!</p>
                    <a href="/teams/create" class="btn btn-success">Crea Nuovo Team</a>
                </div>
            <?php else: ?>
                <?php foreach ($teams as $team): ?>
                    <div class="team-card">
                        <div class="team-card-header">
                            <div class="team-name"><?= htmlspecialchars($team['nome']) ?></div>
                            <div class="team-category"><?= htmlspecialchars($team['categoria_eventi']) ?></div>
                        </div>
                        <div class="team-card-body">
                            <div class="team-description">
                                <?= htmlspecialchars($team['descrizione'] ?: 'Nessuna descrizione disponibile') ?>
                            </div>
                            <div class="team-info">
                                <div class="team-members">
                                    👥 <?= $team['membri_count'] ?? 0 ?> membri
                                </div>
                                <div class="team-visibility visibility-<?= $team['visibilita'] ?>">
                                    <?= $team['visibilita'] === 'pubblico' ? '🌍 Pubblico' : '🔒 Privato' ?>
                                </div>
                            </div>
                            <div class="team-actions">
                                <?php if ($team['visibilita'] === 'pubblico'): ?>
                                    <button class="btn btn-success" onclick="joinTeam(<?= $team['id'] ?>, '<?= htmlspecialchars($team['nome']) ?>')">
                                        ✅ Unisciti
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-warning" onclick="requestJoin(<?= $team['id'] ?>, '<?= htmlspecialchars($team['nome']) ?>')">
                                        📩 Richiedi
                                    </button>
                                <?php endif; ?>
                                <a href="/teams/view?id=<?= $team['id'] ?>" class="btn btn-secondary">
                                    👁️ Visualizza
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Link di navigazione -->
        <div style="margin-top: 40px; text-align: center;">
            <a href="/teams" class="btn btn-secondary">← I Miei Team</a>
            <a href="/teams/create" class="btn btn-success">➕ Crea Nuovo Team</a>
        </div>
    </div>
    
    <!-- Modal per Richiesta Adesione -->
    <div id="joinRequestModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">📩 Richiesta di Adesione</div>
            <form id="joinRequestForm">
                <input type="hidden" id="teamId" name="team_id">
                <div class="form-group">
                    <label class="form-label">Team:</label>
                    <input type="text" id="teamName" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Messaggio (opzionale):</label>
                    <textarea name="messaggio" class="form-control" rows="4" 
                              placeholder="Presenta brevemente la tua esperienza sportiva..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Annulla</button>
                    <button type="submit" class="btn btn-success">Invia Richiesta</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Unisciti direttamente a team pubblico
        function joinTeam(teamId, teamName) {
            if (confirm(`Vuoi unirti al team "${teamName}"?`)) {
                const formData = new FormData();
                formData.append('team_id', teamId);
                
                fetch('/teams/request-join', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('Errore di connessione');
                });
            }
        }
        
        // Richiedi adesione a team privato
        function requestJoin(teamId, teamName) {
            document.getElementById('teamId').value = teamId;
            document.getElementById('teamName').value = teamName;
            document.getElementById('joinRequestModal').style.display = 'block';
        }
        
        // Chiudi modal
        function closeModal() {
            document.getElementById('joinRequestModal').style.display = 'none';
        }
        
        // Invia richiesta di adesione
        document.getElementById('joinRequestForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/teams/request-join', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Errore di connessione');
            });
        });
        
        // Chiudi modal cliccando fuori
        window.onclick = function(event) {
            const modal = document.getElementById('joinRequestModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
