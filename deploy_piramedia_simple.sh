#!/bin/bash

echo "🚀 Deploy SportEvents su Piramedia (FTP)..."

# Variabili
FTP_HOST="ftp.piramedia.it"
FTP_USER="biglietteria_piramedia_it"
FTP_PASS="NI2FjP5TMGtsCF3f"
REMOTE_DIR="/public_html"

# Crea archivio escludendo file non necessari
echo "📦 Creazione archivio..."
tar -czf /tmp/sportevents.tar.gz \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='*.md' \
    --exclude='composer-setup.php' \
    --exclude='test_*.php' \
    --exclude='vendor' \
    --exclude='.DS_Store' \
    .

echo "📤 Upload via FTP..."
echo "
open $FTP_HOST
user $FTP_USER $FTP_PASS
binary
cd $REMOTE_DIR
put /tmp/sportevents.tar.gz
bye
" | ftp -n

echo "✅ File caricato!"
echo "🌍 Ora vai su: https://biglietteria.piramedia.it/setup_piramedia.php"
echo ""
echo "📋 Passi successivi:"
echo "1. Estrai l'archivio sul server (tramite cPanel File Manager)"
echo "2. Vai su: https://biglietteria.piramedia.it/setup_piramedia.php"
echo "3. Dopo il setup, ELIMINA setup_piramedia.php per sicurezza"
echo ""
echo "⚠️  IMPORTANTE: Configura i permessi delle cartelle uploads/"

# Pulisci
rm /tmp/sportevents.tar.gz
