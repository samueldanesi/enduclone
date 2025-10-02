<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestisci Richieste - <?= htmlspecialchars($team->nome) ?></title>
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .manage-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 1.8em;
            margin-bottom: 10px;
        }
        
        .stats-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .stat-item {
            flex: 1;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        
        .requests-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .requests-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .request-card {
            padding: 20px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s;
        }
        
        .request-card:hover {
            background: #f8f9fa;
        }
        
        .request-card:last-child {
            border-bottom: none;
        }
        
        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
        }
        
        .user-details h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .user-details p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
        }
        
        .request-time {
            text-align: right;
            color: #999;
            font-size: 0.85em;
        }
        
        .request-message {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .request-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
            font-size: 14px;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .no-requests {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-requests-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        
        .navigation-bar {
            margin-top: 30px;
            text-align: center;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .filter-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
        }
        
        .filter-tab {
            padding: 10px 20px;
            border: 1px solid #dee2e6;
            background: white;
            cursor: pointer;
            border-radius: 25px;
            transition: all 0.3s;
        }
        
        .filter-tab.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .filter-tab:hover {
            background: #f8f9fa;
        }
        
        .filter-tab.active:hover {
            background: #5a6fd8;
        }
        
        .confirm-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .confirm-content {
            background: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }
        
        .confirm-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="manage-container">
        <!-- Header della Pagina -->
        <div class="page-header">
            <div class="page-title">👥 Gestisci Richieste di Adesione</div>
            <p>Team: <strong><?= htmlspecialchars($team->nome) ?></strong></p>
        </div>
        
        <!-- Messaggi di Sistema -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                ✅ <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                ❌ <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Statistiche -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number"><?= count($requests) ?></div>
                <div class="stat-label">Richieste Pendenti</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $team->countMembers() ?></div>
                <div class="stat-label">Membri Totali</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $team->visibilita === 'pubblico' ? '🌍' : '🔒' ?></div>
                <div class="stat-label"><?= ucfirst($team->visibilita) ?></div>
            </div>
        </div>
        
        <!-- Container Richieste -->
        <div class="requests-container">
            <div class="requests-header">
                <h3>📋 Richieste di Adesione</h3>
                <p>Gestisci le richieste degli utenti che vogliono unirsi al tuo team</p>
            </div>
            
            <?php if (empty($requests)): ?>
                <div class="no-requests">
                    <div class="no-requests-icon">😊</div>
                    <h4>Nessuna richiesta pendente</h4>
                    <p>Al momento non ci sono richieste di adesione da gestire.</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $request): ?>
                    <div class="request-card">
                        <div class="request-header">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($request['nome'], 0, 1) . substr($request['cognome'], 0, 1)) ?>
                                </div>
                                <div class="user-details">
                                    <h4><?= htmlspecialchars($request['nome'] . ' ' . $request['cognome']) ?></h4>
                                    <p>📧 <?= htmlspecialchars($request['email']) ?></p>
                                </div>
                            </div>
                            <div class="request-time">
                                <div>📅 <?= date('d/m/Y', strtotime($request['data_richiesta'])) ?></div>
                                <div>🕐 <?= date('H:i', strtotime($request['data_richiesta'])) ?></div>
                            </div>
                        </div>
                        
                        <?php if (!empty($request['messaggio'])): ?>
                            <div class="request-message">
                                <strong>💬 Messaggio dell'utente:</strong><br>
                                <?= nl2br(htmlspecialchars($request['messaggio'])) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="request-actions">
                            <button class="btn btn-success" 
                                    onclick="handleRequest(<?= $request['id'] ?>, 'approved', '<?= htmlspecialchars($request['nome'] . ' ' . $request['cognome']) ?>')">
                                ✅ Approva
                            </button>
                            <button class="btn btn-danger" 
                                    onclick="handleRequest(<?= $request['id'] ?>, 'rejected', '<?= htmlspecialchars($request['nome'] . ' ' . $request['cognome']) ?>')">
                                ❌ Rifiuta
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Navigazione -->
        <div class="navigation-bar">
            <a href="/teams/chat?team_id=<?= $team->id ?>" class="btn btn-primary">💬 Chat Team</a>
            <a href="/teams/view?id=<?= $team->id ?>" class="btn btn-secondary">👁️ Visualizza Team</a>
            <a href="/teams" class="btn btn-secondary">← I Miei Team</a>
        </div>
    </div>
    
    <!-- Modal di Conferma -->
    <div id="confirmModal" class="confirm-modal">
        <div class="confirm-content">
            <h3 id="confirmTitle">Conferma Azione</h3>
            <p id="confirmMessage">Sei sicuro di voler procedere?</p>
            <div class="confirm-actions">
                <button class="btn btn-secondary" onclick="closeConfirmModal()">Annulla</button>
                <button class="btn" id="confirmButton" onclick="executeAction()">Conferma</button>
            </div>
        </div>
    </div>
    
    <script>
        let currentRequestId = null;
        let currentAction = null;
        
        // Gestisci richiesta con conferma
        function handleRequest(requestId, action, userName) {
            currentRequestId = requestId;
            currentAction = action;
            
            const modal = document.getElementById('confirmModal');
            const title = document.getElementById('confirmTitle');
            const message = document.getElementById('confirmMessage');
            const button = document.getElementById('confirmButton');
            
            if (action === 'approved') {
                title.textContent = '✅ Approva Richiesta';
                message.innerHTML = `Vuoi approvare la richiesta di <strong>${userName}</strong>?<br>L'utente verrà aggiunto al team.`;
                button.textContent = 'Approva';
                button.className = 'btn btn-success';
            } else {
                title.textContent = '❌ Rifiuta Richiesta';
                message.innerHTML = `Vuoi rifiutare la richiesta di <strong>${userName}</strong>?<br>L'utente riceverà una notifica.`;
                button.textContent = 'Rifiuta';
                button.className = 'btn btn-danger';
            }
            
            modal.style.display = 'block';
        }
        
        // Esegui azione confermata
        function executeAction() {
            if (!currentRequestId || !currentAction) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const requestIdInput = document.createElement('input');
            requestIdInput.name = 'request_id';
            requestIdInput.value = currentRequestId;
            
            const actionInput = document.createElement('input');
            actionInput.name = 'action';
            actionInput.value = currentAction;
            
            form.appendChild(requestIdInput);
            form.appendChild(actionInput);
            document.body.appendChild(form);
            form.submit();
        }
        
        // Chiudi modal di conferma
        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
            currentRequestId = null;
            currentAction = null;
        }
        
        // Chiudi modal cliccando fuori
        window.onclick = function(event) {
            const modal = document.getElementById('confirmModal');
            if (event.target === modal) {
                closeConfirmModal();
            }
        }
        
        // Auto-refresh ogni 30 secondi per nuove richieste
        setInterval(() => {
            // Ricarica solo se non ci sono modal aperti
            if (!document.getElementById('confirmModal').style.display || 
                document.getElementById('confirmModal').style.display === 'none') {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>
