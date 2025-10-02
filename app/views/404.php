<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Non Trovata - SportEvents</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <!-- Includi navbar unificata -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <div class="container text-center" style="padding: 4rem 1rem;">
        <div style="font-size: 6rem; margin-bottom: 2rem;">🏃‍♂️</div>
        
        <h1 style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-dark);">
            404
        </h1>
        
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--text-medium);">
            Pagina Non Trovata
        </h2>
        
        <p style="font-size: 1.1rem; color: var(--text-medium); margin-bottom: 3rem; max-width: 500px; margin-left: auto; margin-right: auto;">
            Ops! Sembra che questa pagina sia scappata più veloce di un maratoneta. 
            La pagina che stai cercando non esiste o è stata spostata.
        </p>
        
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="/" class="btn btn-primary btn-lg">
                🏠 Torna alla Home
            </a>
            <a href="/events" class="btn btn-secondary btn-lg">
                🏃 Vedi gli Eventi
            </a>
        </div>

        <div style="margin-top: 3rem; padding: 2rem; background: var(--background-light); border-radius: var(--border-radius); max-width: 600px; margin-left: auto; margin-right: auto;">
            <h3 style="margin-bottom: 1rem; color: var(--text-dark);">
                Cosa puoi fare?
            </h3>
            
            <div style="display: grid; gap: 1rem; text-align: left;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <span style="font-size: 1.2rem;">🔍</span>
                    <div>
                        <strong>Cerca eventi</strong> - Usa la barra di ricerca per trovare eventi sportivi nella tua zona
                    </div>
                </div>
                
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <span style="font-size: 1.2rem;">📝</span>
                    <div>
                        <strong>Registrati</strong> - Crea un account per iscriverti agli eventi
                    </div>
                </div>
                
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <span style="font-size: 1.2rem;">📞</span>
                    <div>
                        <strong>Contattaci</strong> - Se il problema persiste, scrivi a info@sportevents.com
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer minimale -->
    <footer style="background: var(--text-dark); color: white; padding: 2rem 0; margin-top: 4rem;">
        <div class="container text-center">
            <p style="color: var(--text-light);">
                &copy; 2024 SportEvents. Tutti i diritti riservati.
            </p>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
