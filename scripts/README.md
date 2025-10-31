# 🛠️ Scripts e Utility SportEvents

Questa cartella contiene tutti gli script di test, debug e utility per il progetto SportEvents.

## 📋 Contenuto

### 🔧 Script di Setup e Deployment
- `setup_piramedia.php` - Setup automatico database su Piramedia
- `create_folders.php` - Creazione automatica cartelle uploads
- `test_config.php` - Test configurazione completa sistema

### 🧪 Script di Test Database
- `test_db.php` - Test connessione database base
- `check_tables.php` - Verifica struttura tabelle
- `check_events.php` - Test tabella eventi
- `check_events_columns.php` - Verifica colonne eventi
- `check_event_results.php` - Test risultati eventi

### 👥 Script Gestione Utenti
- `create_demo_users.php` - Creazione utenti demo
- `create_user.php` - Creazione singolo utente
- `simulate_login.php` - Simulazione login utente

### 🏘️ Script Community e Team
- `test_community.php` - Test completo sistema community
- `test_community_simple.php` - Test semplificato community
- `install_collective_schema.php` - Schema iscrizioni collettive

### 📊 Script Import/Export Dati
- `debug_csv.php` - Debug file CSV
- `debug_validation.php` - Debug validazione dati
- `test_csv_fix.php` - Fix problemi CSV
- `insert_test_results.php` - Inserimento risultati test

### 📁 File di Test
- `test_participants.csv` - Dati partecipanti test
- `test_simple.csv` - Dati semplificati test
- `template_test.csv` - Template CSV
- `cookies.txt` / `cookies_new.txt` - Cookie di test

### 🐛 Script Debug
- `debug_template.php` - Debug template
- `test_fix.php` - Fix generici
- `test_simple_debug.php` - Debug semplificato

## 🚀 Utilizzo

### Per Deployment:
```bash
# Setup su Piramedia
php scripts/setup_piramedia.php

# Test configurazione
php scripts/test_config.php

# Creazione cartelle
php scripts/create_folders.php
```

### Per Development:
```bash
# Test database
php scripts/test_db.php

# Creazione utenti demo
php scripts/create_demo_users.php

# Test community
php scripts/test_community.php
```

## ⚠️ Importante

- Questi script sono per **sviluppo e deployment**
- **NON includere** questa cartella in produzione
- Utilizzare solo in ambiente di test/sviluppo

## 📝 Note

- Tutti gli script assumono di essere eseguiti dalla root del progetto
- Verificare sempre le configurazioni database prima dell'uso
- I file CSV contengono dati di test fittizi