# 🏃‍♂️ SportEvents - Sistema di Gestione Eventi Sportivi

Sistema completo per la gestione di eventi sportivi con funzionalità avanzate per organizzatori e partecipanti.

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-F7DF1E?style=flat-square&logo=javascript)
![CSS3](https://img.shields.io/badge/CSS3-Modern-1572B6?style=flat-square&logo=css3)

## 🚀 Funzionalità Principali

### 👥 Sistema Utenti
- **Registrazione e Login** con sistema di ruoli (Partecipante, Organizzatore, Admin)
- **Profili utente** completi con gestione documenti
- **Sistema "Ricordami"** per sessioni persistenti
- **Upload certificati medici e tessere sportive**

### 🏆 Gestione Eventi
- **CRUD completo** per eventi sportivi
- **Filtri avanzati** per ricerca eventi (sport, località, data, prezzo)
- **Categorie personalizzabili** per ogni disciplina
- **Upload immagini** e file GPX per percorsi
- **Sistema iscrizioni** con pagamenti e ricevute automatiche

### 👫 Sistema Community
- **Community universale** per tutti gli utenti
- **Community specifiche** per ogni evento
- **Post e commenti** con sistema di interazione
- **Feed in tempo reale** delle attività

### 🤝 Gestione Team
- **Creazione e gestione team**
- **Iscrizioni collettive** tramite CSV
- **Chat team** per comunicazione interna
- **Gestione richieste** di partecipazione

### 📱 Dashboard Organizzatori
- **Statistiche eventi** complete
- **Gestione iscrizioni** e partecipanti
- **Sistema messaggi** ai membri
- **Shop integrato** per prodotti evento

### 🛒 Sistema E-commerce
- **Prodotti personalizzabili** per ogni evento
- **Gestione ordini** e pagamenti
- **Dashboard vendite** per organizzatori

## 🛠️ Tecnologie Utilizzate

### Backend
- **PHP 8+** con pattern MVC
- **MySQL** per database
- **PDO** per sicurezza database
- **Session management** avanzato

### Frontend
- **HTML5 semantico**
- **CSS3 moderno** con Grid/Flexbox
- **JavaScript ES6+** vanilla
- **Design responsive** mobile-first

### Architettura
- **MVC Pattern** ben strutturato
- **Router personalizzato** per URL SEO-friendly
- **Sistema di template** modulare
- **Validazione dati** lato server e client

## 📦 Installazione

### Prerequisiti
- PHP 8.0 o superiore
- MySQL 5.7 o superiore
- Server web (Apache/Nginx) o PHP built-in server

### Setup Rapido

1. **Clona il repository**
   ```bash
   git clone [url-repository]
   cd sportevents
   ```

2. **Configura il database**
   ```bash
   # Crea il database MySQL
   mysql -u root -p -e "CREATE DATABASE eventi_sportivi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   
   # Importa lo schema
   mysql -u root -p eventi_sportivi_db < database/schema.sql
   ```

3. **Configura l'applicazione**
   ```bash
   # Copia il file di configurazione
   cp config/config.example.php config/config.php
   
   # Modifica le credenziali database in config/config.php
   ```

4. **Avvia il server**
   ```bash
   php -S localhost:8001 -t public/
   ```

5. **Accedi all'applicazione**
   - Apri http://localhost:8001
   - Usa gli account demo o registra nuovi utenti

## 🔧 Configurazione

### Database
Modifica `config/config.php`:
```php
'database' => [
    'host' => 'localhost',
    'database' => 'eventi_sportivi_db',
    'username' => 'your_username',
    'password' => 'your_password'
]
```

### Upload Files
- **Certificati**: `uploads/certificates/`
- **Tessere**: `uploads/cards/`
- **Immagini eventi**: `uploads/events/`
- **File GPX**: `uploads/gpx/`

## 👤 Account Demo

Per testare rapidamente tutte le funzionalità:

```
Organizzatore:
Email: organizer@example.com
Password: password123

Partecipante:
Email: participant@example.com  
Password: password123

Admin:
Email: admin@sportevents.com
Password: password123
```

## 📁 Struttura Progetto

```
sportevents/
├── app/
│   ├── controllers/     # Controller MVC
│   ├── models/         # Modelli database
│   └── views/          # Template HTML
├── assets/
│   ├── css/           # Stili CSS
│   └── js/            # JavaScript
├── config/            # Configurazioni
├── database/          # Schema e migrazioni
├── public/            # Entry point web
└── uploads/           # File caricati
```

## 🎯 Roadmap

### Versione 2.0
- [ ] API REST completa
- [ ] App mobile React Native
- [ ] Sistema pagamenti Stripe/PayPal
- [ ] Notifiche push
- [ ] Sistema recensioni avanzato
- [ ] Integrazione social login

### Versione 2.1
- [ ] Sistema classifiche automatiche
- [ ] Esportazione dati Excel/PDF
- [ ] Multi-lingua i18n
- [ ] Sistema backup automatico
- [ ] Analytics avanzati

## 🤝 Contribuire

1. Fork del repository
2. Crea un branch feature (`git checkout -b feature/AmazingFeature`)
3. Commit delle modifiche (`git commit -m 'Add AmazingFeature'`)
4. Push del branch (`git push origin feature/AmazingFeature`)
5. Apri una Pull Request

## 📄 Licenza

Distribuito sotto licenza MIT. Vedi `LICENSE` per maggiori informazioni.

## 🆘 Supporto

- **Issues**: Apri un issue su GitHub
- **Email**: support@sportevents.com
- **Wiki**: Consulta la documentazione completa

## 📊 Features Completate

✅ Sistema autenticazione completo  
✅ Gestione eventi con CRUD  
✅ Community e social features  
✅ Sistema team e iscrizioni collettive  
✅ Upload documenti sicuro  
✅ Dashboard organizzatori  
✅ Sistema messaggi e notifiche  
✅ E-commerce integrato  
✅ Design responsive moderno  
✅ SEO-friendly URLs  
✅ Validazione dati robusta  
✅ Sistema sessioni sicuro  

---

**Sviluppato con ❤️ per la community sportiva italiana**

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
