<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#2563eb',
                        'primary-dark': '#1d4ed8',
                        'secondary': '#f59e0b',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-inter bg-gray-50 min-h-screen">
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Crea il tuo Account</h1>
                    <p class="text-gray-600">Unisciti alla community SportEvents</p>
                </div>
                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <ul class="space-y-2">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li class="flex items-center text-red-700">
                                    <span class="mr-2">⚠️</span>
                                    <?= htmlspecialchars($error) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <form method="POST" action="/register" enctype="multipart/form-data" class="space-y-6">
                    <!-- Tipo di account -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Tipo di Account</label>
                        <div class="flex space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="user_type" value="participant" <?= ((($_GET['type'] ?? '') !== 'organizer') && (($_SESSION['form_data']['user_type'] ?? '') !== 'organizer')) ? 'checked' : '' ?> 
                                       class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                <span class="ml-2 text-gray-700">🏃‍♂️ Partecipante</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="user_type" value="organizer" <?= ((($_GET['type'] ?? '') === 'organizer') || (($_SESSION['form_data']['user_type'] ?? '') === 'organizer')) ? 'checked' : '' ?> 
                                       class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                <span class="ml-2 text-gray-700">🏛️ Organizzatore</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dati personali -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                            <input type="text" id="nome" name="nome" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                   value="<?= htmlspecialchars($_SESSION['form_data']['nome'] ?? '') ?>"
                                   placeholder="Il tuo nome">
                        </div>

                        <div>
                            <label for="cognome" class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                            <input type="text" id="cognome" name="cognome" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                   value="<?= htmlspecialchars($_SESSION['form_data']['cognome'] ?? '') ?>"
                                   placeholder="Il tuo cognome">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>"
                               placeholder="la-tua-email@esempio.com">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                            <input type="password" id="password" name="password" required minlength="8"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                   placeholder="Minimo 8 caratteri">
                        </div>

                        <div>
                            <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">Conferma Password *</label>
                            <input type="password" id="password_confirm" name="password_confirm" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                   placeholder="Ripeti la password">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="data_nascita" class="form-label">Data di Nascita *</label>
                            <input type="date" id="data_nascita" name="data_nascita" class="form-input" required
                                   value="<?= htmlspecialchars($_SESSION['form_data']['data_nascita'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="sesso" class="form-label">Sesso *</label>
                            <select id="sesso" name="sesso" class="form-select" required>
                                <option value="">Seleziona...</option>
                                <option value="M" <?= (($_SESSION['form_data']['sesso'] ?? '') === 'M') ? 'selected' : '' ?>>Maschio</option>
                                <option value="F" <?= (($_SESSION['form_data']['sesso'] ?? '') === 'F') ? 'selected' : '' ?>>Femmina</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cellulare" class="form-label">Cellulare *</label>
                        <input type="tel" id="cellulare" name="cellulare" class="form-input" required
                               placeholder="+39 123 456 7890"
                               value="<?= htmlspecialchars($_SESSION['form_data']['cellulare'] ?? '') ?>">
                    </div>

                    <!-- Documenti opzionali -->
                    <div id="documents-block" style="background: var(--background-light); padding: 1.5rem; border-radius: var(--border-radius); margin: 1.5rem 0;">
                        <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Documenti (Opzionali)</h3>
                        <p style="color: var(--text-medium); font-size: 0.875rem; margin-bottom: 1rem;">
                            Puoi caricare questi documenti ora o successivamente dal tuo profilo.
                        </p>

                        <div class="form-group">
                            <label for="certificato_medico" class="form-label">Certificato Medico</label>
                            <input type="file" id="certificato_medico" name="certificato_medico" 
                                   class="form-input" accept=".pdf,.jpg,.jpeg,.png">
                            <small style="color: var(--text-medium);">
                                Formato: PDF, JPG, PNG. Max 5MB
                            </small>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="tessera_affiliazione" class="form-label">Tessera di Affiliazione</label>
                            <input type="file" id="tessera_affiliazione" name="tessera_affiliazione" 
                                   class="form-input" accept=".pdf,.jpg,.jpeg,.png">
                            <small style="color: var(--text-medium);">
                                Formato: PDF, JPG, PNG. Max 5MB
                            </small>
                        </div>
                    </div>

                    <!-- Privacy e termini -->
                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; cursor: pointer;">
                            <input type="checkbox" name="privacy" required style="margin-right: 0.5rem; margin-top: 0.25rem;">
                            <span style="font-size: 0.875rem; line-height: 1.4;">
                                Accetto i <a href="#" style="color: var(--primary-color);">Termini di Servizio</a> 
                                e l'<a href="#" style="color: var(--primary-color);">Informativa sulla Privacy</a> *
                            </span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; cursor: pointer;">
                            <input type="checkbox" name="newsletter" style="margin-right: 0.5rem; margin-top: 0.25rem;">
                            <span style="font-size: 0.875rem; line-height: 1.4;">
                                Voglio ricevere aggiornamenti sugli eventi e newsletter (opzionale)
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Crea Account
                    </button>
                </form>
            </div>

            <div class="card-footer text-center">
                <p style="color: var(--text-medium);">
                    Hai già un account? <a href="/login" style="color: var(--primary-color);">Accedi qui</a>
                </p>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
    <script>
        // Validazione password in tempo reale
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.setCustomValidity('Le password non corrispondono');
            } else {
                this.setCustomValidity('');
            }
        });

        // Mostra/nascondi documenti in base al tipo di utente (solo partecipante)
        const docsBlock = document.getElementById('documents-block');
        const userTypeRadios = document.querySelectorAll('input[name="user_type"]');
        function toggleDocuments() {
            const selected = document.querySelector('input[name="user_type"]:checked')?.value || 'participant';
            if (selected === 'organizer') {
                docsBlock.style.display = 'none';
            } else {
                docsBlock.style.display = '';
            }
        }
        userTypeRadios.forEach(r => r.addEventListener('change', toggleDocuments));
        toggleDocuments();
    </script>

    <?php 
    // Pulisci i dati del form dalla sessione
    unset($_SESSION['form_data']); 
    ?>
</body>
</html>
