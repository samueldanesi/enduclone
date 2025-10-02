<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizione Rapida - <?= htmlspecialchars($team['nome']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            backdrop-filter: blur(20px);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: #666;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
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

        .participants-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .participant-row {
            display: grid;
            grid-template-columns: 2fr 2fr 1fr 100px;
            gap: 15px;
            margin-bottom: 15px;
            align-items: end;
        }

        .btn-remove {
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-add {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 10px;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Iscrizione Rapida</h1>
            <p class="subtitle">Team: <strong><?= htmlspecialchars($team['nome']) ?></strong></p>
        </div>

        <form method="POST" id="quickRegistrationForm">
            <div class="form-group">
                <label for="event_id">Seleziona Evento</label>
                <select name="event_id" id="event_id" class="form-control" required>
                    <option value="">-- Scegli un evento --</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>" data-price="<?= $event['prezzo_base'] ?>">
                            <?= htmlspecialchars($event['titolo']) ?> - 
                            €<?= number_format($event['prezzo_base'], 2) ?> - 
                            <?= date('d/m/Y', strtotime($event['data_evento'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="participants-section">
                <h3><i class="fas fa-users"></i> Partecipanti</h3>
                <div id="participantsList">
                    <div class="participant-row">
                        <div>
                            <label>Nome</label>
                            <input type="text" name="participants[0][nome]" class="form-control" required>
                        </div>
                        <div>
                            <label>Cognome</label>
                            <input type="text" name="participants[0][cognome]" class="form-control" required>
                        </div>
                        <div>
                            <label>Email</label>
                            <input type="email" name="participants[0][email]" class="form-control" required>
                        </div>
                        <div>
                            <button type="button" class="btn-remove" onclick="removeParticipant(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-add" onclick="addParticipant()">
                    <i class="fas fa-plus"></i> Aggiungi Partecipante
                </button>
            </div>

            <div class="form-group">
                <label for="note">Note aggiuntive</label>
                <textarea name="note" id="note" class="form-control" rows="3" placeholder="Note opzionali per l'iscrizione..."></textarea>
            </div>

            <div class="actions">
                <a href="/teams/collective-registrations/<?= $team['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Indietro
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Conferma Iscrizione
                </button>
            </div>
        </form>
    </div>

    <script>
        let participantIndex = 1;

        function addParticipant() {
            const participantsList = document.getElementById('participantsList');
            const newRow = document.createElement('div');
            newRow.className = 'participant-row';
            newRow.innerHTML = `
                <div>
                    <label>Nome</label>
                    <input type="text" name="participants[${participantIndex}][nome]" class="form-control" required>
                </div>
                <div>
                    <label>Cognome</label>
                    <input type="text" name="participants[${participantIndex}][cognome]" class="form-control" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="participants[${participantIndex}][email]" class="form-control" required>
                </div>
                <div>
                    <button type="button" class="btn-remove" onclick="removeParticipant(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            participantsList.appendChild(newRow);
            participantIndex++;
        }

        function removeParticipant(button) {
            const participantsList = document.getElementById('participantsList');
            if (participantsList.children.length > 1) {
                button.closest('.participant-row').remove();
            } else {
                alert('Devi avere almeno un partecipante!');
            }
        }
    </script>
</body>
</html>