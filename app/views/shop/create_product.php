<?php
// Verifica che l'utente sia loggato e sia un organizzatore
if (!isset($_SESSION['user_id']) || (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] !== 'organizzatore')) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '/../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Nuovo Prodotto - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="glass-theme">
    <div class="page-wrapper">
        <?php renderNavbar('shop'); ?>
        
        <main class="main-content">
            <div class="container">
                <div class="glass-card">
                    <div class="card-header">
                        <h1 class="section-title">
                            <span class="icon">🛍️</span>
                            Crea Nuovo Prodotto
                        </h1>
                        <p class="section-subtitle">Aggiungi un nuovo prodotto al tuo negozio</p>
                    </div>

                    <form class="product-form" action="/shop/store" method="POST" enctype="multipart/form-data">
                        <div class="form-grid">
                            <!-- Informazioni di base -->
                            <div class="form-section">
                                <h3 class="form-section-title">Informazioni Prodotto</h3>
                                
                                <div class="form-group">
                                    <label for="nome">Nome Prodotto*</label>
                                    <input type="text" id="nome" name="nome" required 
                                           placeholder="Es: Maglia Tecnica Marathon 2025">
                                </div>

                                <div class="form-group">
                                    <label for="descrizione">Descrizione*</label>
                                    <textarea id="descrizione" name="descrizione" rows="4" required 
                                              placeholder="Descrivi il prodotto, materiali, caratteristiche..."></textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="prezzo">Prezzo (€)*</label>
                                        <input type="number" id="prezzo" name="prezzo" step="0.01" min="0" required 
                                               placeholder="25.00">
                                    </div>

                                    <div class="form-group">
                                        <label for="categoria">Categoria*</label>
                                        <select id="categoria" name="categoria" required>
                                            <option value="">Seleziona categoria</option>
                                            <option value="abbigliamento">Abbigliamento</option>
                                            <option value="accessori">Accessori</option>
                                            <option value="pacchi_gara">Pacchi Gara</option>
                                            <option value="foto_video">Foto & Video</option>
                                            <option value="donazioni">Donazioni</option>
                                            <option value="altro">Altro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Evento associato -->
                            <div class="form-section">
                                <h3 class="form-section-title">Evento Associato</h3>
                                
                                <div class="form-group">
                                    <label for="evento_id">Seleziona Evento*</label>
                                    <select id="evento_id" name="evento_id" required>
                                        <option value="">Seleziona un evento</option>
                                        <?php if (isset($eventi) && !empty($eventi)): ?>
                                            <?php foreach ($eventi as $evento): ?>
                                                <option value="<?= htmlspecialchars($evento['event_id']) ?>">
                                                    <?= htmlspecialchars($evento['titolo']) ?> - <?= date('d/m/Y', strtotime($evento['data_evento'])) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Inventario -->
                            <div class="form-section">
                                <h3 class="form-section-title">Gestione Inventario</h3>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="quantita_disponibile">Quantità Disponibile*</label>
                                        <input type="number" id="quantita_disponibile" name="quantita_disponibile" 
                                               min="1" required placeholder="100">
                                    </div>

                                    <div class="form-group">
                                        <label for="gestione_taglie">
                                            <input type="checkbox" id="gestione_taglie" name="gestione_taglie" value="1">
                                            Gestisci taglie
                                        </label>
                                    </div>
                                </div>

                                <div id="taglie-section" class="form-group" style="display: none;">
                                    <label>Taglie Disponibili</label>
                                    <div class="taglie-grid">
                                        <label><input type="checkbox" name="taglie[]" value="XS"> XS</label>
                                        <label><input type="checkbox" name="taglie[]" value="S"> S</label>
                                        <label><input type="checkbox" name="taglie[]" value="M"> M</label>
                                        <label><input type="checkbox" name="taglie[]" value="L"> L</label>
                                        <label><input type="checkbox" name="taglie[]" value="XL"> XL</label>
                                        <label><input type="checkbox" name="taglie[]" value="XXL"> XXL</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Immagini -->
                            <div class="form-section">
                                <h3 class="form-section-title">Immagini Prodotto</h3>
                                
                                <div class="form-group">
                                    <label for="immagini">Carica Immagini</label>
                                    <input type="file" id="immagini" name="immagini[]" multiple 
                                           accept="image/jpeg,image/png,image/jpg">
                                    <small class="form-help">Formato supportati: JPG, PNG. Max 5 immagini.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="/shop/organizer" class="btn btn-secondary">
                                <span class="icon">←</span>
                                Annulla
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <span class="icon">✓</span>
                                Crea Prodotto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Gestione taglie
        document.getElementById('gestione_taglie').addEventListener('change', function() {
            const taglieSection = document.getElementById('taglie-section');
            taglieSection.style.display = this.checked ? 'block' : 'none';
        });

        // Validazione form
        document.querySelector('.product-form').addEventListener('submit', function(e) {
            const prezzo = document.getElementById('prezzo').value;
            const quantita = document.getElementById('quantita_disponibile').value;
            
            if (parseFloat(prezzo) <= 0) {
                alert('Il prezzo deve essere maggiore di 0');
                e.preventDefault();
                return;
            }
            
            if (parseInt(quantita) <= 0) {
                alert('La quantità deve essere maggiore di 0');
                e.preventDefault();
                return;
            }
        });
    </script>

    <style>
        .product-form {
            max-width: 1200px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            gap: 2rem;
            animation: fadeInUp 0.6s ease-out;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            border-radius: 20px 20px 0 0;
        }

        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.12);
        }

        .form-section-title {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .form-section-title::before {
            content: '✨';
            font-size: 1.2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            transform: translateY(-1px);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .taglie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .taglie-grid label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-size: 0.9rem;
            padding: 0.8rem 1rem;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .taglie-grid label:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .taglie-grid input[type="checkbox"]:checked + span {
            color: #667eea;
        }

        .form-help {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .form-actions {
            display: flex;
            gap: 1.5rem;
            justify-content: flex-end;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 2.5rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .section-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column-reverse;
            }

            .form-section {
                padding: 1.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .taglie-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</body>
</html>