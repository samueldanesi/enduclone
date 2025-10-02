# SportEvents - Piattaforma Eventi Sportivi

Una moderna web application PHP per la gestione e iscrizione a eventi sportivi, simile a channel.endu.net ma con funzionalità personalizzate.

## 🚀 Caratteristiche Principali

### Per i Partecipanti
- **Registrazione semplice** con dati personali e documenti
- **Ricerca eventi** per sport, località, data e categoria
- **Iscrizioni online** con pagamenti sicuri
- **Area personale** con storico iscrizioni e documenti
- **Notifiche** email/SMS per aggiornamenti

### Per gli Organizzatori
- **Dashboard completa** per gestione eventi
- **Statistiche dettagliate** per età, sesso, squadre
- **Export dati** in formato Excel
- **Import massivo** per iscrizioni di gruppo
- **Gestione prezzi** con price steps e promozioni
- **Comunicazioni** di servizio agli iscritti

### Funzionalità Avanzate
- **Codici sconto** e voucher
- **Gestione squadre** con iscrizioni collettive
- **File GPX** scaricabili per partecipanti
- **Risultati e classifiche** post-gara
- **Sistema recensioni** pubbliche
- **Add-on acquistabili** (maglie, foto, etc.)

## 🛠️ Tecnologie Utilizzate

- **Backend**: PHP 8+ con architettura MVC
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **UI Framework**: CSS Grid/Flexbox con design system custom
- **Pagamenti**: PayPal (con supporto per altri gateway)
- **File Upload**: Gestione sicura di certificati e documenti

## 📁 Struttura del Progetto

```
sportevents/
├── app/
│   ├── controllers/     # Controller MVC
│   ├── models/         # Modelli database
│   └── views/          # Template HTML
├── assets/
│   ├── css/           # Fogli di stile
│   └── js/            # JavaScript
├── config/            # Configurazioni
├── database/          # Schema e migrazioni
├── public/            # Entry point e file pubblici
└── uploads/           # File caricati dagli utenti
```

## 🚦 Setup e Installazione

### Prerequisiti
- PHP 8.0+
- MySQL 5.7+ o MariaDB 10.3+
- Apache/Nginx con mod_rewrite
- Composer (opzionale per dipendenze future)

### Installazione

1. **Clona il repository**
   ```bash
   git clone [repository-url]
   cd sportevents
   ```

2. **Configura il database**
   ```bash
   # Crea il database
   mysql -u root -p
   CREATE DATABASE sportevents_db;
   
   # Importa lo schema
   mysql -u root -p sportevents_db < database/schema.sql
   ```

3. **Configura l'applicazione**
   ```bash
   # Modifica le configurazioni in config/config.php
   # Aggiorna le credenziali database in config/database.php
   ```

4. **Imposta i permessi**
   ```bash
   chmod 755 uploads/
   chmod 644 config/*.php
   ```

5. **Configura il web server**
   
   **Apache (.htaccess)**
   ```apache
   RewriteEngine On
   RewriteRule ^(.*)$ public/index.php [QSA,L]
   ```
   
   **Nginx**
   ```nginx
   location / {
       try_files $uri $uri/ /public/index.php?$query_string;
   }
   ```

## 🔧 Configurazione

### Database
Modifica `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'sportevents_db';
private $username = 'your_username';
private $password = 'your_password';
```

### Email e Pagamenti
Configura in `config/config.php`:
```php
// SMTP per email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// PayPal
define('PAYPAL_CLIENT_ID', 'your-paypal-client-id');
define('PAYPAL_CLIENT_SECRET', 'your-paypal-secret');
```

## 👥 Account Demo

Per testare l'applicazione sono disponibili questi account:

**Organizzatore**
- Email: `organizer@example.com`
- Password: `password123`

**Partecipante**
- Email: `participant@example.com`  
- Password: `password123`

**Admin**
- Email: `admin@sportevents.com`
- Password: `password123`

## 🎨 Design System

L'applicazione utilizza un design system moderno con:

- **Colori**: Schema basato su blu primario (#2563eb) con varianti
- **Typography**: Font Inter per leggibilità ottimale
- **Componenti**: Card, form, bottoni e layout responsive
- **Icone**: Unicode e font icons per leggerezza
- **Responsive**: Mobile-first con breakpoint a 768px

## 📱 Funzionalità Future

- [ ] App mobile React Native/Flutter
- [ ] Live tracking GPS durante eventi
- [ ] Integrazione sistemi cronometraggio
- [ ] Chat di supporto in tempo reale
- [ ] Marketplace per attrezzatura sportiva
- [ ] Social features e community
- [ ] API REST per integrazioni esterne

## 🔒 Sicurezza

- Password hashatediconSha256 con salt
- Validazione input server-side e client-side
- Protezione CSRF per form critici
- Upload file con validazione tipo e dimensione
- Sessioni sicure con token remember-me
- Logs di sicurezza per accessi

## 🤝 Contribuire

1. Fork del progetto
2. Crea un branch per la feature (`git checkout -b feature/AmazingFeature`)
3. Commit delle modifiche (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Apri una Pull Request

## 📄 Licenza

Questo progetto è distribuito sotto licenza MIT. Vedi `LICENSE` per maggiori informazioni.

## 📞 Supporto

Per supporto e domande:
- 📧 Email: info@sportevents.com
- 📞 Telefono: +39 02 1234 5678
- 🌐 Website: https://sportevents.com

---

**SportEvents** - Connettendo atleti e organizzatori dal 2024 🏃‍♂️🚴‍♀️🏊‍♂️
