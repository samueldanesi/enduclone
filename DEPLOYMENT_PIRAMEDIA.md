# Deployment su Piramedia - SportEvents

## Informazioni Server
- **Host:** biglietteria.piramedia.it
- **Accesso SFTP/cPanel:** https://sweb.piramedia.it/myadm/
- **Username:** biglietteria_piramedia_it
- **Password:** NI2FjP5TMGtsCF3f

## Database
- **Host:** localhost
- **Nome Database:** biglietteria_piramedia_it
- **Username:** biglietteria_piramedia_it
- **Password:** NI2FjP5TMGtsCF3f

## Istruzioni Deployment

### 1. Caricamento Files
1. Accedi a https://sweb.piramedia.it/myadm/
2. Vai al File Manager
3. Carica tutti i file nella directory principale (public_html)
4. Assicurati che la struttura sia:
   ```
   public_html/
   ├── app/
   ├── assets/
   ├── config/
   ├── database/
   ├── public/
   ├── uploads/
   ├── .htaccess
   └── setup_piramedia.php
   ```

### 2. Setup Database
1. Vai su: https://biglietteria.piramedia.it/setup_piramedia.php
2. Segui le istruzioni per creare il database e le tabelle
3. **ELIMINA** il file setup_piramedia.php dopo l'installazione per sicurezza

### 3. Configurazione Permessi
Assicurati che le cartelle uploads/ abbiano permessi 755 o 777:
- uploads/
- uploads/certificates/
- uploads/events/
- uploads/receipts/
- uploads/cards/
- uploads/collective_registrations/
- uploads/community/
- uploads/gpx/

### 4. Test Funzionalità
Dopo il setup, testa:
- [ ] Registrazione utente
- [ ] Login
- [ ] Creazione eventi
- [ ] Upload documenti
- [ ] Sistema shop
- [ ] Sistema community
- [ ] Sistema team

### 5. Credenziali Demo
Dopo il setup saranno disponibili:
- **Admin:** admin@biglietteria.piramedia.it / admin123
- **Organizzatore:** organizer@biglietteria.piramedia.it / organizer123  
- **Partecipante:** participant@biglietteria.piramedia.it / participant123

### 6. Sicurezza Post-Deployment
- [ ] Elimina setup_piramedia.php
- [ ] Cambia le password demo
- [ ] Configura backup automatici
- [ ] Attiva HTTPS
- [ ] Configura email SMTP reali

## File Modificati per Produzione
- `config/config.php` - Auto-detect ambiente Piramedia
- `config/database.php` - Configurazione database dinamica
- `.htaccess` - Regole Apache per produzione
- `public/.htaccess` - Configurazione PHP e sicurezza

## Troubleshooting
Se ci sono problemi:
1. Verifica permessi cartelle
2. Controlla error log PHP
3. Verifica credenziali database
4. Assicurati che mod_rewrite sia attivo

## URL Finali
- **Sito:** https://biglietteria.piramedia.it
- **Admin Panel:** https://sweb.piramedia.it/myadm/
- **phpMyAdmin:** Disponibile nel cPanel