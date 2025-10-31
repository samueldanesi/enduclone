-- ============================================
-- SPORTEVENTS - DATABASE COMPLETO
-- Schema unificato con dati demo
-- ============================================

-- Creazione database
CREATE DATABASE IF NOT EXISTS `biglietteria_piramedia_it` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `biglietteria_piramedia_it`;

-- ============================================
-- TABELLE PRINCIPALI
-- ============================================

-- Tabella utenti
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `ruolo` enum('admin','organizzatore','partecipante') DEFAULT 'partecipante',
  `telefono` varchar(20) DEFAULT NULL,
  `data_nascita` date DEFAULT NULL,
  `citta` varchar(100) DEFAULT NULL,
  `provincia` varchar(2) DEFAULT NULL,
  `codice_fiscale` varchar(16) DEFAULT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  `email_verificata` tinyint(1) DEFAULT 0,
  `token_verifica` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `tipo_certificato` enum('agonistico','non_agonistico','') DEFAULT '',
  `scadenza_certificato` date DEFAULT NULL,
  `tessera_affiliazione` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella categorie
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `attiva` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella eventi
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text NOT NULL,
  `data_evento` datetime NOT NULL,
  `data_scadenza_iscrizione` datetime NOT NULL,
  `luogo` varchar(255) NOT NULL,
  `coordinate` varchar(100) DEFAULT NULL,
  `prezzo` decimal(8,2) DEFAULT 0.00,
  `posti_disponibili` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `organizzatore_id` int(11) NOT NULL,
  `stato` enum('bozza','pubblicato','annullato','completato') DEFAULT 'pubblicato',
  `immagine` varchar(255) DEFAULT NULL,
  `regolamento` text DEFAULT NULL,
  `info_aggiuntive` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_events_organizzatore` (`organizzatore_id`),
  KEY `fk_events_categoria` (`categoria_id`),
  CONSTRAINT `fk_events_organizzatore` FOREIGN KEY (`organizzatore_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_events_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella iscrizioni
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `stato` enum('confermata','in_attesa','annullata') DEFAULT 'confermata',
  `data_iscrizione` timestamp DEFAULT CURRENT_TIMESTAMP,
  `note` text DEFAULT NULL,
  `numero_pettorale` varchar(20) DEFAULT NULL,
  `tempo_gara` time DEFAULT NULL,
  `posizione` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_registration` (`evento_id`,`utente_id`),
  KEY `fk_registrations_utente` (`utente_id`),
  CONSTRAINT `fk_registrations_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_registrations_utente` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella ricevute
CREATE TABLE IF NOT EXISTS `receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utente_id` int(11) NOT NULL,
  `evento_id` int(11) DEFAULT NULL,
  `numero_ricevuta` varchar(50) NOT NULL UNIQUE,
  `descrizione` text NOT NULL,
  `importo` decimal(10,2) NOT NULL,
  `data_emissione` date NOT NULL,
  `tipo` enum('iscrizione','prodotto','donazione','altro') DEFAULT 'iscrizione',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `stato` enum('pagata','in_attesa','annullata') DEFAULT 'pagata',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_receipts_utente` (`utente_id`),
  KEY `fk_receipts_evento` (`evento_id`),
  CONSTRAINT `fk_receipts_utente` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_receipts_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SISTEMA COMMUNITY
-- ============================================

-- Tabella post community
CREATE TABLE IF NOT EXISTS `community_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) NOT NULL,
  `contenuto` text NOT NULL,
  `tipo` enum('post','evento','universale') DEFAULT 'post',
  `evento_id` int(11) DEFAULT NULL,
  `autore_id` int(11) NOT NULL,
  `immagine` varchar(255) DEFAULT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_community_posts_autore` (`autore_id`),
  KEY `fk_community_posts_evento` (`evento_id`),
  CONSTRAINT `fk_community_posts_autore` FOREIGN KEY (`autore_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_community_posts_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella commenti community
CREATE TABLE IF NOT EXISTS `community_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `autore_id` int(11) NOT NULL,
  `contenuto` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_community_comments_post` (`post_id`),
  KEY `fk_community_comments_autore` (`autore_id`),
  CONSTRAINT `fk_community_comments_post` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_community_comments_autore` FOREIGN KEY (`autore_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SISTEMA TEAMS
-- ============================================

-- Tabella teams
CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `evento_id` int(11) NOT NULL,
  `capitano_id` int(11) NOT NULL,
  `max_membri` int(11) DEFAULT 10,
  `stato` enum('aperto','chiuso','completo') DEFAULT 'aperto',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_teams_evento` (`evento_id`),
  KEY `fk_teams_capitano` (`capitano_id`),
  CONSTRAINT `fk_teams_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_teams_capitano` FOREIGN KEY (`capitano_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella membri team
CREATE TABLE IF NOT EXISTS `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `stato` enum('attivo','in_attesa','rimosso') DEFAULT 'attivo',
  `ruolo` enum('capitano','membro') DEFAULT 'membro',
  `data_ingresso` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_team_member` (`team_id`,`utente_id`),
  KEY `fk_team_members_utente` (`utente_id`),
  CONSTRAINT `fk_team_members_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_team_members_utente` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella messaggi team
CREATE TABLE IF NOT EXISTS `team_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `messaggio` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_team_messages_team` (`team_id`),
  KEY `fk_team_messages_utente` (`utente_id`),
  CONSTRAINT `fk_team_messages_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_team_messages_utente` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SISTEMA SHOP
-- ============================================

-- Tabella prodotti shop
CREATE TABLE IF NOT EXISTS `event_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `categoria` enum('abbigliamento','accessori','pacco_gara','foto','donazione','altro') NOT NULL,
  `quantita_disponibile` int(11) DEFAULT 0,
  `quantita_venduta` int(11) DEFAULT 0,
  `immagine` varchar(255) DEFAULT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_event_products_evento` (`evento_id`),
  CONSTRAINT `fk_event_products_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella ordini prodotti
CREATE TABLE IF NOT EXISTS `product_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prodotto_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `quantita` int(11) NOT NULL DEFAULT 1,
  `prezzo_unitario` decimal(10,2) NOT NULL,
  `totale` decimal(10,2) NOT NULL,
  `stato` enum('in_attesa','confermato','spedito','completato','annullato') DEFAULT 'in_attesa',
  `note` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_product_orders_prodotto` (`prodotto_id`),
  KEY `fk_product_orders_utente` (`utente_id`),
  CONSTRAINT `fk_product_orders_prodotto` FOREIGN KEY (`prodotto_id`) REFERENCES `event_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_orders_utente` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SISTEMA NOTIFICHE E MESSAGGI
-- ============================================

-- Tabella notifiche utenti
CREATE TABLE IF NOT EXISTS `user_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utente_id` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `messaggio` text NOT NULL,
  `tipo` enum('info','successo','avviso','errore') DEFAULT 'info',
  `letta` tinyint(1) DEFAULT 0,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_notifications_utente` (`utente_id`),
  CONSTRAINT `fk_user_notifications_utente` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella messaggi eventi
CREATE TABLE IF NOT EXISTS `event_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_id` int(11) NOT NULL,
  `mittente_id` int(11) NOT NULL,
  `destinatario_id` int(11) DEFAULT NULL,
  `titolo` varchar(255) NOT NULL,
  `messaggio` text NOT NULL,
  `tipo` enum('pubblico','privato','broadcast') DEFAULT 'pubblico',
  `letto` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_event_messages_evento` (`evento_id`),
  KEY `fk_event_messages_mittente` (`mittente_id`),
  KEY `fk_event_messages_destinatario` (`destinatario_id`),
  CONSTRAINT `fk_event_messages_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_messages_mittente` FOREIGN KEY (`mittente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_messages_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SISTEMA GPX E RISULTATI
-- ============================================

-- Tabella file GPX
CREATE TABLE IF NOT EXISTS `gpx_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_id` int(11) NOT NULL,
  `nome_file` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `distanza_km` decimal(6,2) DEFAULT NULL,
  `dislivello_m` int(11) DEFAULT NULL,
  `tipo` enum('percorso','traccia_gps') DEFAULT 'percorso',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_gpx_files_evento` (`evento_id`),
  CONSTRAINT `fk_gpx_files_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabella risultati eventi
CREATE TABLE IF NOT EXISTS `event_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evento_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `posizione` int(11) DEFAULT NULL,
  `tempo` time DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `posizione_categoria` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_event_result` (`evento_id`,`utente_id`),
  KEY `fk_event_results_utente` (`utente_id`),
  CONSTRAINT `fk_event_results_evento` FOREIGN KEY (`evento_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_results_utente` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATI DEMO
-- ============================================

-- Inserimento categorie
INSERT IGNORE INTO `categories` (`nome`, `descrizione`) VALUES
('Corsa', 'Eventi di corsa su strada e trail'),
('Ciclismo', 'Gare ciclistiche e mountain bike'),
('Triathlon', 'Competizioni di triathlon e multisport'),
('Nuoto', 'Gare di nuoto in piscina e acque libere'),
('Altri Sport', 'Altri eventi sportivi');

-- Inserimento utenti demo
INSERT IGNORE INTO `users` (`nome`, `cognome`, `email`, `password`, `ruolo`, `attivo`, `email_verificata`) VALUES
('Admin', 'SportEvents', 'admin@biglietteria.piramedia.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 1),
('Organizzatore', 'Demo', 'organizer@biglietteria.piramedia.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'organizzatore', 1, 1),
('Partecipante', 'Demo', 'participant@biglietteria.piramedia.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'partecipante', 1, 1),
('Mario', 'Rossi', 'mario.rossi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'partecipante', 1, 1),
('Giulia', 'Verdi', 'giulia.verdi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'partecipante', 1, 1);

-- Inserimento eventi demo
INSERT IGNORE INTO `events` (`titolo`, `descrizione`, `data_evento`, `data_scadenza_iscrizione`, `luogo`, `prezzo`, `posti_disponibili`, `categoria_id`, `organizzatore_id`, `stato`) VALUES
('Maratona di Milano Demo', 'Maratona cittadina di 42km nel centro di Milano', '2025-04-15 09:00:00', '2025-04-10 23:59:59', 'Milano, Piazza Duomo', 25.00, 1000, 1, 2, 'pubblicato'),
('Gran Fondo delle Dolomiti', 'Gara ciclistica di 120km tra le Dolomiti', '2025-06-20 08:00:00', '2025-06-15 23:59:59', 'Cortina d\'Ampezzo', 45.00, 500, 2, 2, 'pubblicato'),
('Triathlon Sprint Lake', 'Triathlon sprint: 750m nuoto, 20km bici, 5km corsa', '2025-07-10 07:30:00', '2025-07-05 23:59:59', 'Lago di Como', 35.00, 200, 3, 2, 'pubblicato');

-- Inserimento iscrizioni demo
INSERT IGNORE INTO `registrations` (`evento_id`, `utente_id`, `stato`, `numero_pettorale`) VALUES
(1, 3, 'confermata', 'M001'),
(1, 4, 'confermata', 'M002'),
(2, 3, 'confermata', 'C001'),
(3, 5, 'confermata', 'T001');

-- Inserimento post community demo
INSERT IGNORE INTO `community_posts` (`titolo`, `contenuto`, `tipo`, `evento_id`, `autore_id`) VALUES
('Benvenuti nella Community SportEvents!', 'Questo è il primo post della nostra community. Qui potrete condividere esperienze, consigli e fare domande sui nostri eventi sportivi.', 'universale', NULL, 1),
('Preparazione Maratona di Milano', 'Qualche consiglio per prepararsi al meglio alla maratona. Chi ha già partecipato negli anni scorsi?', 'evento', 1, 2),
('Condizioni meteo Gran Fondo', 'Come sono le previsioni per domenica? Speriamo nel bel tempo per la Gran Fondo!', 'evento', 2, 3);

-- Inserimento prodotti shop demo
INSERT IGNORE INTO `event_products` (`evento_id`, `nome`, `descrizione`, `prezzo`, `categoria`, `quantita_disponibile`) VALUES
(1, 'Maglietta Ufficiale Maratona', 'Maglietta tecnica ufficiale dell\'evento', 25.00, 'abbigliamento', 100),
(1, 'Pettorale Ricordo', 'Pettorale commemorativo dell\'evento', 10.00, 'altro', 50),
(2, 'Borraccia Gran Fondo', 'Borraccia termica ufficiale', 15.00, 'accessori', 75),
(3, 'Pacco Gara Triathlon', 'Pacco gara completo con gadget', 30.00, 'pacco_gara', 200);

-- Inserimento notifiche demo
INSERT IGNORE INTO `user_notifications` (`utente_id`, `titolo`, `messaggio`, `tipo`) VALUES
(3, 'Iscrizione Confermata', 'La tua iscrizione alla Maratona di Milano è stata confermata!', 'successo'),
(4, 'Nuovo Evento Disponibile', 'È stato pubblicato un nuovo evento: Gran Fondo delle Dolomiti', 'info'),
(5, 'Promemoria Pagamento', 'Ricordati di completare il pagamento per il Triathlon Sprint Lake', 'avviso');

-- ============================================
-- FINE SETUP DATABASE
-- ============================================

-- Messaggio di conferma
SELECT 'Database SportEvents creato con successo!' as messaggio;
SELECT COUNT(*) as utenti_creati FROM users;
SELECT COUNT(*) as eventi_creati FROM events;
SELECT COUNT(*) as tabelle_create FROM information_schema.tables WHERE table_schema = 'biglietteria_piramedia_it';