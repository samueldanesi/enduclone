#!/bin/bash

echo "🚀 Preparazione Deploy SportEvents per Piramedia..."
echo ""

# Crea cartella temporanea
DEPLOY_DIR="/tmp/sportevents_deploy_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$DEPLOY_DIR"

echo "📦 Copia file nel pacchetto di deploy..."

# Copia file necessari escludendo quelli non necessari
rsync -av \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='*.md' \
    --exclude='README.md' \
    --exclude='composer-setup.php' \
    --exclude='test_*.php' \
    --exclude='vendor' \
    --exclude='.DS_Store' \
    --exclude='*.log' \
    --exclude='deploy_*.sh' \
    --exclude='scripts/' \
    ./ "$DEPLOY_DIR/"

# Assicurati che config.piramedia.php sia copiato come config.php
cp config/config.piramedia.php "$DEPLOY_DIR/config/config.php"

echo "✅ File preparati in: $DEPLOY_DIR"
echo ""
echo "📦 Creazione archivio ZIP..."

cd /tmp
ZIP_FILE="sportevents_piramedia_$(date +%Y%m%d_%H%M%S).zip"
zip -r "$ZIP_FILE" "$(basename $DEPLOY_DIR)" -q

echo "✅ Archivio creato: /tmp/$ZIP_FILE"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📤 ISTRUZIONI PER IL DEPLOY:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "1️⃣  CARICA IL FILE"
echo "   • Vai su: https://sweb.piramedia.it/myadm/"
echo "   • Login: biglietteria_piramedia_it"
echo "   • Password: NI2FjP5TMGtsCF3f"
echo "   • File Manager → Upload → Seleziona: /tmp/$ZIP_FILE"
echo ""
echo "2️⃣  ESTRAI L'ARCHIVIO"
echo "   • Click destro sul file .zip → Extract"
echo "   • Sposta tutti i file dalla cartella estratta a public_html/"
echo ""
echo "3️⃣  CONFIGURA PERMESSI"
echo "   • Seleziona cartella 'uploads' → Permissions → 755"
echo "   • Applica ricorsivamente a tutte le sottocartelle"
echo ""
echo "4️⃣  SETUP DATABASE"
echo "   • Vai su: https://biglietteria.piramedia.it/setup_piramedia.php"
echo "   • Segui le istruzioni automatiche"
echo ""
echo "5️⃣  SICUREZZA"
echo "   • ELIMINA il file setup_piramedia.php dopo l'installazione"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌍 URL FINALE: https://biglietteria.piramedia.it"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "👥 CREDENZIALI DEMO (dopo il setup):"
echo "   • Admin: admin@biglietteria.piramedia.it / admin123"
echo "   • Organizzatore: organizer@biglietteria.piramedia.it / organizer123"
echo "   • Partecipante: participant@biglietteria.piramedia.it / participant123"
echo ""
echo "✅ Pronto per il deploy!"
