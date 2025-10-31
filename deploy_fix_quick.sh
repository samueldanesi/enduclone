#!/bin/bash

echo "🔧 Creazione pacchetto FIX rapido per Piramedia..."

# Crea cartella temporanea
FIX_DIR="/tmp/piramedia_fix_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$FIX_DIR/app/controllers"
mkdir -p "$FIX_DIR/app/models"
mkdir -p "$FIX_DIR/config"

# Copia solo i file fixati
echo "📦 Copiando file aggiornati..."
cp app/controllers/EventController.php "$FIX_DIR/app/controllers/"
cp app/models/Event.php "$FIX_DIR/app/models/"
cp app/models/EventMessage.php "$FIX_DIR/app/models/"
cp app/models/Receipt.php "$FIX_DIR/app/models/"
cp config/config.piramedia.php "$FIX_DIR/config/config.php"

# Crea ZIP
cd /tmp
ZIP_NAME="piramedia_fix_$(date +%Y%m%d_%H%M%S).zip"
zip -r "$ZIP_NAME" "$(basename $FIX_DIR)" -q

echo "✅ Pacchetto FIX creato: /tmp/$ZIP_NAME"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📤 ISTRUZIONI RAPIDE:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "1. Vai su: https://sweb.piramedia.it/myadm/"
echo "2. File Manager → Upload → /tmp/$ZIP_NAME"
echo "3. Extract il file in public_html/"
echo "4. Sovrascrivi i file quando richiesto"
echo "5. Ricarica: https://biglietteria.piramedia.it/events"
echo ""
echo "✅ Problema 'organizzatore_id' risolto!"

# Apri la cartella
open /tmp
