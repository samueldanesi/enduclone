<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Nuovo Team - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="create-team-container">
        <div class="form-header">
            <h1><i class="fas fa-users"></i> Crea Nuovo Team</h1>
            <p>Registra la tua società sportiva o gruppo per gestire iscrizioni collettive</p>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="team-form">
            <!-- Informazioni Generali -->
            <div class="form-section">
                <h2><i class="fas fa-info-circle"></i> Informazioni Generali</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome Team/Società *</label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               required 
                               class="form-control"
                               placeholder="es. ASD Running Club Milano"
                               value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipologia *</label>
                        <select id="tipo" name="tipo" required class="form-control">
                            <option value="">Seleziona tipologia</option>
                            <option value="società" <?= isset($_POST['tipo']) && $_POST['tipo'] === 'società' ? 'selected' : '' ?>>Società Sportiva</option>
                            <option value="associazione" <?= isset($_POST['tipo']) && $_POST['tipo'] === 'associazione' ? 'selected' : '' ?>>Associazione Sportiva</option>
                            <option value="gruppo" <?= isset($_POST['tipo']) && $_POST['tipo'] === 'gruppo' ? 'selected' : '' ?>>Gruppo Sportivo</option>
                            <option value="azienda" <?= isset($_POST['tipo']) && $_POST['tipo'] === 'azienda' ? 'selected' : '' ?>>Team Aziendale</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="codice_fiscale">Codice Fiscale</label>
                        <input type="text" 
                               id="codice_fiscale" 
                               name="codice_fiscale" 
                               class="form-control"
                               placeholder="Per società sportive"
                               value="<?= isset($_POST['codice_fiscale']) ? htmlspecialchars($_POST['codice_fiscale']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="partita_iva">Partita IVA</label>
                        <input type="text" 
                               id="partita_iva" 
                               name="partita_iva" 
                               class="form-control"
                               placeholder="Se applicabile"
                               value="<?= isset($_POST['partita_iva']) ? htmlspecialchars($_POST['partita_iva']) : '' ?>">
                    </div>
                </div>
            </div>

            <!-- Indirizzo -->
            <div class="form-section">
                <h2><i class="fas fa-map-marker-alt"></i> Indirizzo</h2>
                
                <div class="form-group">
                    <label for="indirizzo">Via/Indirizzo *</label>
                    <input type="text" 
                           id="indirizzo" 
                           name="indirizzo" 
                           required 
                           class="form-control"
                           placeholder="Via Roma, 123"
                           value="<?= isset($_POST['indirizzo']) ? htmlspecialchars($_POST['indirizzo']) : '' ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="citta">Città *</label>
                        <input type="text" 
                               id="citta" 
                               name="citta" 
                               required 
                               class="form-control"
                               placeholder="Milano"
                               value="<?= isset($_POST['citta']) ? htmlspecialchars($_POST['citta']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="provincia">Provincia *</label>
                        <input type="text" 
                               id="provincia" 
                               name="provincia" 
                               required 
                               class="form-control"
                               placeholder="MI"
                               maxlength="2"
                               value="<?= isset($_POST['provincia']) ? htmlspecialchars($_POST['provincia']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="cap">CAP *</label>
                        <input type="text" 
                               id="cap" 
                               name="cap" 
                               required 
                               class="form-control"
                               placeholder="20100"
                               pattern="[0-9]{5}"
                               value="<?= isset($_POST['cap']) ? htmlspecialchars($_POST['cap']) : '' ?>">
                    </div>
                </div>
            </div>

            <!-- Contatti -->
            <div class="form-section">
                <h2><i class="fas fa-phone"></i> Contatti</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Team *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required 
                               class="form-control"
                               placeholder="info@teamname.it"
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Telefono</label>
                        <input type="tel" 
                               id="telefono" 
                               name="telefono" 
                               class="form-control"
                               placeholder="02 1234567"
                               value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="sito_web">Sito Web</label>
                    <input type="url" 
                           id="sito_web" 
                           name="sito_web" 
                           class="form-control"
                           placeholder="https://www.teamname.it"
                           value="<?= isset($_POST['sito_web']) ? htmlspecialchars($_POST['sito_web']) : '' ?>">
                </div>
            </div>

            <!-- Responsabile -->
            <div class="form-section">
                <h2><i class="fas fa-user-tie"></i> Responsabile del Team</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="responsabile_nome">Nome Responsabile *</label>
                        <input type="text" 
                               id="responsabile_nome" 
                               name="responsabile_nome" 
                               required 
                               class="form-control"
                               placeholder="Mario"
                               value="<?= isset($_POST['responsabile_nome']) ? htmlspecialchars($_POST['responsabile_nome']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="responsabile_cognome">Cognome Responsabile *</label>
                        <input type="text" 
                               id="responsabile_cognome" 
                               name="responsabile_cognome" 
                               required 
                               class="form-control"
                               placeholder="Rossi"
                               value="<?= isset($_POST['responsabile_cognome']) ? htmlspecialchars($_POST['responsabile_cognome']) : '' ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="responsabile_email">Email Responsabile *</label>
                        <input type="email" 
                               id="responsabile_email" 
                               name="responsabile_email" 
                               required 
                               class="form-control"
                               placeholder="mario.rossi@email.com"
                               value="<?= isset($_POST['responsabile_email']) ? htmlspecialchars($_POST['responsabile_email']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="responsabile_telefono">Telefono Responsabile *</label>
                        <input type="tel" 
                               id="responsabile_telefono" 
                               name="responsabile_telefono" 
                               required 
                               class="form-control"
                               placeholder="333 1234567"
                               value="<?= isset($_POST['responsabile_telefono']) ? htmlspecialchars($_POST['responsabile_telefono']) : '' ?>">
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div class="form-section">
                <h2><i class="fas fa-sticky-note"></i> Note Aggiuntive</h2>
                
                <div class="form-group">
                    <label for="note">Descrizione/Note</label>
                    <textarea id="note" 
                              name="note" 
                              class="form-control" 
                              rows="4"
                              placeholder="Descrivi brevemente il team, attività principali, obiettivi..."><?= isset($_POST['note']) ? htmlspecialchars($_POST['note']) : '' ?></textarea>
                </div>
            </div>

            <!-- Azioni -->
            <div class="form-actions">
                <a href="/teams" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Annulla
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crea Team
                </button>
            </div>
        </form>
    </div>

    <style>
    .create-team-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .form-header h1 {
        color: #333;
        font-size: 32px;
        margin-bottom: 10px;
    }

    .form-header p {
        color: #666;
        font-size: 16px;
    }

    .team-form {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .form-section {
        padding: 30px;
        border-bottom: 1px solid #e9ecef;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section h2 {
        color: #333;
        font-size: 20px;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
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
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-actions {
        padding: 30px;
        background: #f8f9fa;
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .alert {
        margin-bottom: 30px;
        padding: 15px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108,117,125,0.3);
    }

    @media (max-width: 768px) {
        .create-team-container {
            padding: 10px;
        }

        .form-section {
            padding: 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }
    </style>

    <script>
    // Auto-uppercase per provincia
    document.getElementById('provincia').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Validazione CAP
    document.getElementById('cap').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 5);
    });

    // Validazione form prima dell'invio
    document.querySelector('.team-form').addEventListener('submit', function(e) {
        const requiredFields = ['nome', 'tipo', 'indirizzo', 'citta', 'provincia', 'cap', 'email', 'responsabile_nome', 'responsabile_cognome', 'responsabile_email', 'responsabile_telefono'];
        let valid = true;

        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                input.style.borderColor = '#dc3545';
                valid = false;
            } else {
                input.style.borderColor = '#e9ecef';
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Per favore, compila tutti i campi obbligatori.');
        }
    });
    </script>
</body>
</html>
