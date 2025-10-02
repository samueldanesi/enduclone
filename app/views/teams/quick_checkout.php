<?php
if (!isset($_SESSION['quick_collective_data'])) {
    header('Location: /teams');
    exit();
}

$data = $_SESSION['quick_collective_data'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Iscrizione Collettiva</title>
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
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .content {
            padding: 30px;
        }

        .summary-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .participants-list {
            max-height: 300px;
            overflow-y: auto;
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .participant-item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .participant-item:last-child {
            border-bottom: none;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .price-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .total-price {
            background: #e8f5e8 !important;
            border-left-color: #27ae60 !important;
            font-size: 1.1em;
            font-weight: bold;
        }

        .payment-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ffeaa7;
            text-align: center;
            margin: 25px 0;
        }

        .confirm-btn {
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

        .confirm-btn:hover {
            transform: translateY(-2px);
        }

        .back-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .highlight {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
            padding: 3px 8px;
            border-radius: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Checkout Iscrizione Collettiva</h1>
            <p>Riepilogo della tua iscrizione</p>
        </div>

        <div class="content">
            <a href="/teams/quick-collective/<?= $data['team_id'] ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Torna Indietro
            </a>

            <div class="summary-section">
                <h3><i class="fas fa-info-circle"></i> Dettagli Iscrizione</h3>
                <div style="margin: 15px 0;">
                    <strong>Team:</strong> <?= htmlspecialchars($data['team_name']) ?><br>
                    <strong>Evento:</strong> <?= htmlspecialchars($data['event_name']) ?><br>
                    <strong>Partecipanti:</strong> <span class="highlight"><?= $data['participant_count'] ?></span>
                </div>

                <h4><i class="fas fa-users"></i> Lista Partecipanti</h4>
                <div class="participants-list">
                    <?php $index = 1; ?>
                    <?php foreach ($data['participants'] as $participant): ?>
                        <?php if (empty($participant['nome']) || empty($participant['cognome'])) continue; ?>
                        <div class="participant-item">
                            <i class="fas fa-user" style="margin-right: 10px; color: #3498db;"></i>
                            <?= $index ?>. <?= htmlspecialchars($participant['nome']) ?> <?= htmlspecialchars($participant['cognome']) ?>
                        </div>
                        <?php $index++; ?>
                    <?php endforeach; ?>
                </div>

                <div class="pricing-grid">
                    <div class="price-item">
                        <strong>Prezzo Base:</strong><br>
                        €<?= number_format($data['base_price'], 2) ?> per persona
                    </div>
                    <div class="price-item">
                        <strong>Sconto Gruppo:</strong><br>
                        <?= $data['discount_percent'] ?>% (<?= $data['participant_count'] ?> partecipanti)
                    </div>
                    <div class="price-item">
                        <strong>Prezzo Scontato:</strong><br>
                        €<?= number_format($data['discounted_price'], 2) ?> per persona
                    </div>
                    <div class="price-item total-price">
                        <strong>Totale da Pagare:</strong><br>
                        €<?= number_format($data['total_amount'], 2) ?>
                    </div>
                </div>

                <?php if (!empty($data['notes'])): ?>
                    <div style="margin-top: 15px;">
                        <strong>Note:</strong><br>
                        <em><?= htmlspecialchars($data['notes']) ?></em>
                    </div>
                <?php endif; ?>
            </div>

            <div class="payment-section">
                <h3><i class="fas fa-credit-card"></i> Modalità di Pagamento</h3>
                <p>Per questa demo, il pagamento viene simulato.<br>
                In un'implementazione reale, qui ci sarebbe l'integrazione con un gateway di pagamento (PayPal, Stripe, etc.)</p>
            </div>

            <form action="/teams/confirm-quick-payment" method="POST">
                <button type="submit" class="confirm-btn">
                    <i class="fas fa-check-circle"></i> Conferma e Completa Iscrizione
                </button>
            </form>

            <div style="text-align: center; margin-top: 15px; color: #6c757d; font-size: 0.9em;">
                <i class="fas fa-shield-alt"></i> I tuoi dati sono sicuri e protetti
            </div>
        </div>
    </div>
</body>
</html>