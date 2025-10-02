<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Pagamento Iscrizione Collettiva</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px 0;
            min-height: 100vh;
        }

        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.2em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 15px 0;
        }

        .payment-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .summary-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #cbd5e0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2em;
            color: #1e293b;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-method {
            position: relative;
        }

        .payment-method input {
            display: none;
        }

        .payment-method label {
            display: block;
            padding: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .payment-method input:checked + label {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(100, 116, 139, 0.3);
        }

        .actions-section {
            text-align: center;
            margin-top: 30px;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            font-weight: 500;
        }

        .alert-info {
            background: #e0f2fe;
            color: #0c4a6e;
            border: 1px solid #7dd3fc;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-credit-card"></i> Conferma Pagamento</h1>
            <p>Iscrizione Collettiva per <?= htmlspecialchars($collective_registration['event_title']) ?></p>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Informazione:</strong> Questo è un sistema di conferma pagamento simulato. 
            In un ambiente di produzione, qui ci sarebbe l'integrazione con un gateway di pagamento reale.
        </div>

        <form method="POST" class="payment-form">
            <!-- Riepilogo Iscrizione -->
            <div class="summary-section">
                <h3><i class="fas fa-receipt"></i> Riepilogo Iscrizione</h3>
                <div class="summary-row">
                    <span>Evento:</span>
                    <span><?= htmlspecialchars($collective_registration['event_title']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Team:</span>
                    <span><?= htmlspecialchars($collective_registration['team_name']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Partecipanti:</span>
                    <span><?= $collective_registration['total_participants'] ?> persone</span>
                </div>
                <div class="summary-row">
                    <span>Prezzo base per persona:</span>
                    <span>€<?= number_format($collective_registration['base_price_per_person'], 2) ?></span>
                </div>
                <?php if ($collective_registration['discount_percentage'] > 0): ?>
                <div class="summary-row">
                    <span>Sconto gruppo (<?= $collective_registration['discount_percentage'] ?>%):</span>
                    <span style="color: #059669;">
                        -€<?= number_format(($collective_registration['base_price_per_person'] * $collective_registration['total_participants']) - $collective_registration['total_amount'], 2) ?>
                    </span>
                </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span><strong>Totale da Pagare:</strong></span>
                    <span><strong>€<?= number_format($collective_registration['total_amount'], 2) ?></strong></span>
                </div>
            </div>

            <!-- Metodo di Pagamento -->
            <div class="form-group">
                <label class="form-label">Metodo di Pagamento</label>
                <div class="payment-methods">
                    <div class="payment-method">
                        <input type="radio" id="card" name="payment_method" value="card" checked>
                        <label for="card">
                            <i class="fas fa-credit-card"></i><br>
                            Carta di Credito
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                        <label for="bank_transfer">
                            <i class="fas fa-university"></i><br>
                            Bonifico Bancario
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="invoice" name="payment_method" value="invoice">
                        <label for="invoice">
                            <i class="fas fa-file-invoice"></i><br>
                            Fattura
                        </label>
                    </div>
                </div>
            </div>

            <!-- ID Transazione (simulato) -->
            <div class="form-group">
                <label class="form-label" for="transaction_id">
                    ID Transazione (opzionale)
                </label>
                <input type="text" 
                       name="transaction_id" 
                       id="transaction_id" 
                       class="form-control"
                       placeholder="es: TXN-<?= date('Ymd') ?>-<?= rand(1000, 9999) ?>"
                       value="TXN-<?= date('Ymd') ?>-<?= rand(1000, 9999) ?>">
                <small style="color: #64748b; font-size: 0.9em;">
                    ID di riferimento del pagamento (generato automaticamente se non specificato)
                </small>
            </div>

            <div class="actions-section">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle"></i>
                    Conferma Pagamento (€<?= number_format($collective_registration['total_amount'], 2) ?>)
                </button>
                
                <a href="/teams/collective-details/<?= $collective_registration['id'] ?>" 
                   class="btn btn-secondary" style="margin-left: 15px;">
                    <i class="fas fa-arrow-left"></i>
                    Annulla
                </a>
            </div>
        </form>

        <div style="text-align: center; margin-top: 30px; color: #64748b; font-size: 0.9em;">
            <p><i class="fas fa-shield-alt"></i> Pagamento sicuro e protetto</p>
        </div>
    </div>

    <script>
    // Aggiorna il placeholder dell'ID transazione in base al metodo selezionato
    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const transactionInput = document.getElementById('transaction_id');
            const method = this.value;
            const date = '<?= date('Ymd') ?>';
            const rand = Math.floor(Math.random() * 9000) + 1000;
            
            let prefix = 'TXN';
            switch(method) {
                case 'card':
                    prefix = 'CARD';
                    break;
                case 'bank_transfer':
                    prefix = 'BNKT';
                    break;
                case 'invoice':
                    prefix = 'INV';
                    break;
            }
            
            transactionInput.value = prefix + '-' + date + '-' + rand;
            transactionInput.placeholder = 'es: ' + prefix + '-' + date + '-' + rand;
        });
    });
    </script>
</body>
</html>