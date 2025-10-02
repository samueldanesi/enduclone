<?php
require_once '../config/database.php';
require_once '../app/models/User.php';
require_once '../app/models/Team.php';
require_once '../app/models/Event.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$database = new Database();
$pdo = $database->getConnection();

$team = new Team($pdo);
$event = new Event($pdo);
$user = new User($pdo);

$team_id = $_GET['team_id'] ?? null;
if (!$team_id) {
    header('Location: /teams');
    exit();
}

$team_data = $team->findById($team_id);
if (!$team_data || $team_data['leader_id'] != $_SESSION['user_id']) {
    header('Location: /teams');
    exit();
}

$events = $event->findAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizione Collettiva Rapida - <?= htmlspecialchars($team_data['nome']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .content {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        select, input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .participants-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .participant-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .add-participant {
            background: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            transition: background 0.3s;
        }

        .add-participant:hover {
            background: #219a52;
        }

        .remove-participant {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            align-self: center;
        }

        .pricing-info {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }

        .submit-btn {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .counter {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Iscrizione Collettiva Rapida</h1>
            <p>Team: <?= htmlspecialchars($team_data['nome']) ?></p>
            <p>Inserisci i partecipanti e vai direttamente al pagamento</p>
        </div>

        <div class="content">
            <form action="/teams/quick-collective-checkout" method="POST" id="quick-form">
                <input type="hidden" name="team_id" value="<?= $team_id ?>">
                
                <div class="form-group">
                    <label for="event_id">Seleziona Evento *</label>
                    <select name="event_id" id="event_id" required onchange="updatePricing()">
                        <option value="">-- Scegli un evento --</option>
                        <?php foreach ($events as $evt): ?>
                            <option value="<?= $evt['id'] ?>" data-price="<?= $evt['prezzo_base'] ?>">
                                <?= htmlspecialchars($evt['nome']) ?> - €<?= number_format($evt['prezzo_base'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="participants-section">
                    <h3><i class="fas fa-list"></i> Lista Partecipanti</h3>
                    <div class="counter">
                        <span id="participant-count">0</span> partecipanti
                    </div>
                    
                    <button type="button" class="add-participant" onclick="addParticipant()">
                        <i class="fas fa-plus"></i> Aggiungi Partecipante
                    </button>
                    
                    <div id="participants-container"></div>
                </div>

                <div class="pricing-info" id="pricing-info" style="display: none;">
                    <h4><i class="fas fa-calculator"></i> Riepilogo Prezzi</h4>
                    <div id="pricing-details"></div>
                </div>

                <div class="form-group">
                    <label for="notes">Note (opzionale)</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Eventuali richieste speciali o note per l'organizzazione..."></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-credit-card"></i> Vai al Pagamento
                </button>
            </form>
        </div>
    </div>

    <script>
        let participantCount = 0;

        function addParticipant() {
            participantCount++;
            const container = document.getElementById('participants-container');
            const div = document.createElement('div');
            div.className = 'participant-row';
            div.innerHTML = `
                <input type="text" name="participants[${participantCount}][nome]" placeholder="Nome" required>
                <input type="text" name="participants[${participantCount}][cognome]" placeholder="Cognome" required>
                <button type="button" class="remove-participant" onclick="removeParticipant(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(div);
            updateCounter();
            updatePricing();
        }

        function removeParticipant(button) {
            button.closest('.participant-row').remove();
            updateCounter();
            updatePricing();
        }

        function updateCounter() {
            const count = document.querySelectorAll('.participant-row').length;
            document.getElementById('participant-count').textContent = count;
        }

        function updatePricing() {
            const eventSelect = document.getElementById('event_id');
            const selectedOption = eventSelect.options[eventSelect.selectedIndex];
            const participantRows = document.querySelectorAll('.participant-row');
            const count = participantRows.length;
            
            if (!selectedOption.value || count === 0) {
                document.getElementById('pricing-info').style.display = 'none';
                return;
            }

            const basePrice = parseFloat(selectedOption.dataset.price);
            let discountPercent = 0;
            
            // Calcola sconto in base al numero di partecipanti
            if (count >= 50) discountPercent = 20;
            else if (count >= 30) discountPercent = 15;
            else if (count >= 20) discountPercent = 12;
            else if (count >= 15) discountPercent = 10;
            else if (count >= 10) discountPercent = 8;
            else if (count >= 5) discountPercent = 5;

            const discountedPrice = basePrice * (1 - discountPercent / 100);
            const totalAmount = discountedPrice * count;

            document.getElementById('pricing-details').innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <strong>Prezzo base:</strong> €${basePrice.toFixed(2)}<br>
                        <strong>Partecipanti:</strong> ${count}<br>
                        <strong>Sconto gruppo:</strong> ${discountPercent}%
                    </div>
                    <div>
                        <strong>Prezzo scontato:</strong> €${discountedPrice.toFixed(2)}<br>
                        <strong style="font-size: 1.2em; color: #27ae60;">Totale: €${totalAmount.toFixed(2)}</strong>
                    </div>
                </div>
            `;
            document.getElementById('pricing-info').style.display = 'block';
        }

        // Aggiungi automaticamente il primo partecipante
        addParticipant();
    </script>
</body>
</html>