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
</head>
<body>
    <?php renderNavbar('shop'); ?>
    
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-plus-circle"></i> Crea Nuovo Prodotto</h1>
                <p>Aggiungi un nuovo prodotto al tuo shop</p>
            </div>

            <!-- Form con design migliorato -->
            <form action="/shop/store" method="POST" enctype="multipart/form-data" class="product-form">
                <!-- Sezione Informazioni Base -->
                <div class="form-card">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3>Informazioni Prodotto</h3>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="nome">
                                <i class="fas fa-tag"></i> Nome Prodotto*
                            </label>
                            <input type="text" id="nome" name="nome" class="form-control" required 
                                   placeholder="Es: Maglia Running Milano Marathon 2025">
                        </div>

                        <div class="form-group full-width">
                            <label for="descrizione">
                                <i class="fas fa-align-left"></i> Descrizione*
                            </label>
                            <textarea id="descrizione" name="descrizione" rows="3" class="form-control" required 
                                      placeholder="Descrivi il prodotto, materiali, caratteristiche speciali..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="prezzo">
                                <i class="fas fa-euro-sign"></i> Prezzo*
                            </label>
                            <input type="number" id="prezzo" name="prezzo" step="0.01" min="0" class="form-control" required 
                                   placeholder="25.00">
                        </div>

                        <div class="form-group">
                            <label for="categoria">
                                <i class="fas fa-layer-group"></i> Categoria*
                            </label>
                            <select id="categoria" name="categoria" class="form-control" required>
                                <option value="">🏷️ Seleziona categoria</option>
                                <option value="abbigliamento">👕 Abbigliamento</option>
                                <option value="accessori">🎒 Accessori</option>
                                <option value="pacchi_gara">📦 Pacchi Gara</option>
                                <option value="foto_video">📸 Foto & Video</option>
                                <option value="donazioni">💝 Donazioni</option>
                                <option value="altro">🔧 Altro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantita">
                                <i class="fas fa-cubes"></i> Quantità Disponibile*
                            </label>
                            <input type="number" id="quantita" name="quantita_disponibile" min="0" class="form-control" required 
                                   placeholder="100">
                        </div>

                        <div class="form-group">
                            <label for="evento_id">
                                <i class="fas fa-calendar-alt"></i> Evento Associato
                            </label>
                            <select id="evento_id" name="evento_id" class="form-control">
                                <option value="">📅 Seleziona evento (opzionale)</option>
                                <!-- Gli eventi verranno popolati dinamicamente -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sezione Immagine -->
                <div class="form-card">
                    <div class="card-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <h3>Immagine Prodotto</h3>
                    
                    <div class="upload-area">
                        <input type="file" id="immagine" name="immagine" accept="image/*" class="file-input" hidden>
                        <label for="immagine" class="upload-label">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <p class="upload-text">Clicca per caricare l'immagine</p>
                                <small>JPG, PNG - Max 5MB</small>
                            </div>
                        </label>
                        <div class="image-preview" id="preview" style="display: none;"></div>
                    </div>
                </div>

                <!-- Azioni -->
                <div class="form-actions-modern">
                    <a href="/shop/organizer" class="btn-cancel">
                        <i class="fas fa-times"></i> Annulla
                    </a>
                    <button type="submit" class="btn-create">
                        <i class="fas fa-plus"></i> Crea Prodotto
                    </button>
                </div>
            </form>
        </div>
    </main>

    <style>
        .product-form {
            max-width: 800px;
            margin: 0 auto;
            display: grid;
            gap: 2rem;
        }

        .form-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.8);
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .form-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .upload-area {
            margin-top: 1rem;
        }

        .upload-label {
            display: block;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .upload-label:hover {
            border-color: #667eea;
            background: #f1f5f9;
            transform: translateY(-2px);
        }

        .upload-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .upload-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .upload-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .image-preview {
            margin-top: 1rem;
            text-align: center;
        }

        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-actions-modern {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn-cancel, .btn-create {
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            min-width: 150px;
            justify-content: center;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(100, 116, 139, 0.2);
        }

        .btn-create {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-card {
                padding: 1.5rem;
            }
            
            .form-actions-modern {
                flex-direction: column;
            }
        }
    </style>

    <script>
        // Preview immagine
        document.getElementById('immagine').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Animazioni al caricamento
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.form-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
        });

        // Validazione migliorata
        document.querySelector('.product-form').addEventListener('submit', function(e) {
            const requiredFields = ['nome', 'descrizione', 'prezzo', 'categoria', 'quantita'];
            let isValid = true;
            
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.style.borderColor = '#ef4444';
                    isValid = false;
                    
                    setTimeout(() => {
                        field.style.borderColor = '#e5e7eb';
                    }, 3000);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('❌ Per favore compila tutti i campi obbligatori');
            }
        });
    </script>

    <script src="/assets/js/app.js"></script>
</body>
</html>
