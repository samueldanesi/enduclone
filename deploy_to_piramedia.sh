#!/bin/bash

echo "🚀 Deploy SportEvents su Piramedia..."

# Variabili
HOST="ftp.piramedia.it"
USER="biglietteria_piramedia_it"
PASS="NI2FjP5TMGtsCF3f"
REMOTE_DIR="/public_html"

# Escludi file non necessari
echo "📦 Preparazione files..."
rsync -avz --exclude 'node_modules' \
           --exclude '.git' \
           --exclude '*.md' \
           --exclude 'composer-setup.php' \
           --exclude 'test_*.php' \
           --exclude 'vendor' \
           ./ /tmp/sportevents_deploy/

echo "📤 Upload via FTP..."
lftp -c "
set ssl:verify-certificate no
open -u $USER,$PASS $HOST
mirror -R /tmp/sportevents_deploy $REMOTE_DIR --verbose
bye
"

echo "✅ Deploy completato!"
echo "🌍 Vai su: https://biglietteria.piramedia.it/setup_piramedia.php"
echo "⚠️  Dopo il setup, elimina setup_piramedia.php per sicurezza!"

rm -rf /tmp/sportevents_deploy
