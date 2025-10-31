#!/bin/bash
# Script di deploy per Render
echo "Starting SportEvents deployment..."

# Crea le directory necessarie se non esistono
mkdir -p uploads/certificates uploads/cards uploads/events uploads/gpx uploads/receipts

# Imposta i permessi
chmod 755 uploads
chmod 755 uploads/*

echo "Deployment completed successfully!"