-- MySQL dump 10.13  Distrib 5.7.24, for osx11.1 (x86_64)
--
-- Host: localhost    Database: eventi_sportivi_db
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `calendar_events`
--

DROP TABLE IF EXISTS `calendar_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `event_id` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` enum('evento','allenamento','gara','altro') COLLATE utf8mb4_unicode_ci DEFAULT 'evento',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `calendar_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `calendar_events_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_events`
--

LOCK TABLES `calendar_events` WRITE;
/*!40000 ALTER TABLE `calendar_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `categoria_id` int NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `icona` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `colore` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#667eea',
  `attiva` tinyint(1) DEFAULT '1',
  `data_creazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Running','Eventi di corsa su strada e trail','fas fa-running','#667eea',1,'2025-09-27 08:54:08'),(2,'Ciclismo','Eventi ciclistici','fas fa-bicycle','#667eea',1,'2025-09-27 08:54:08'),(3,'Triathlon','Nuoto, ciclismo e corsa','fas fa-swimmer','#667eea',1,'2025-09-27 08:54:08'),(4,'Trail','Corsa in natura','fas fa-mountain','#667eea',1,'2025-09-27 08:54:08'),(5,'Camminata','Eventi non competitivi','fas fa-walking','#667eea',1,'2025-09-27 08:54:08');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_comments`
--

DROP TABLE IF EXISTS `community_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `contenuto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `community_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`),
  CONSTRAINT `community_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_comments`
--

LOCK TABLES `community_comments` WRITE;
/*!40000 ALTER TABLE `community_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_posts`
--

DROP TABLE IF EXISTS `community_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `event_id` int DEFAULT NULL,
  `type` enum('text','photo','video','result') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_caption` text COLLATE utf8mb4_unicode_ci,
  `visibility` enum('public','participants','private') COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `likes_count` int DEFAULT '0',
  `comments_count` int DEFAULT '0',
  `status` enum('active','hidden','deleted') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `community_posts_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_posts`
--

LOCK TABLES `community_posts` WRITE;
/*!40000 ALTER TABLE `community_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `community_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_messages`
--

DROP TABLE IF EXISTS `event_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `organizer_id` int DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organizer_id` (`organizer_id`),
  CONSTRAINT `event_messages_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_messages`
--

LOCK TABLES `event_messages` WRITE;
/*!40000 ALTER TABLE `event_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_products`
--

DROP TABLE IF EXISTS `event_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `categoria` enum('abbigliamento','accessori','pacco_gara','foto','donazione','altro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'altro',
  `prezzo` decimal(10,2) NOT NULL,
  `quantita_disponibile` int NOT NULL DEFAULT '0',
  `quantita_venduta` int NOT NULL DEFAULT '0',
  `immagine` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evento_id` int NOT NULL,
  `organizer_id` int NOT NULL,
  `attivo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_products`
--

LOCK TABLES `event_products` WRITE;
/*!40000 ALTER TABLE `event_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_results`
--

DROP TABLE IF EXISTS `event_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `evento_id` int NOT NULL,
  `posizione` int DEFAULT NULL,
  `tempo_finale` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `event_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `event_results_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `events` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_results`
--

LOCK TABLES `event_results` WRITE;
/*!40000 ALTER TABLE `event_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `titolo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `immagine` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `data_evento` date NOT NULL,
  `ora_inizio` time DEFAULT NULL,
  `luogo_partenza` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distanza_km` decimal(8,2) DEFAULT NULL,
  `citta` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provincia` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitudine` decimal(10,7) DEFAULT NULL,
  `longitudine` decimal(10,7) DEFAULT NULL,
  `prezzo_base` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_partecipanti` int DEFAULT NULL,
  `iscritti_attuali` int DEFAULT '0',
  `categoria_id` int DEFAULT NULL,
  `sport` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organizer_id` int NOT NULL,
  `stato` enum('bozza','pubblicato','chiuso','annullato') COLLATE utf8mb4_unicode_ci DEFAULT 'bozza',
  `data_creazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_modifica` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `organizer_id` (`organizer_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categories` (`categoria_id`),
  CONSTRAINT `events_ibfk_2` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (22,'maratona milano',NULL,'maratona Milano','2025-10-09',NULL,'piazza duomo',42.00,'Milano',NULL,NULL,NULL,99.00,430,0,1,'Corsa',42,'pubblicato','2025-10-02 14:57:27','2025-10-02 15:07:57'),(23,'maratona milano',NULL,'maratona Milano','2025-10-09',NULL,'piazza duomo',42.00,'Milano',NULL,NULL,NULL,99.00,430,0,1,'Corsa',42,'pubblicato','2025-10-02 14:57:35','2025-10-02 15:07:57'),(24,'corsa',NULL,'gufa','2025-10-08',NULL,'gcd',53.00,'Milano',NULL,NULL,NULL,434.00,45,0,5,'Corsa',42,'pubblicato','2025-10-02 15:09:24','2025-10-02 15:09:24'),(25,'re',NULL,'gcd','2025-10-23',NULL,'tfhd',45.00,'Milano',NULL,NULL,NULL,45.00,54,0,5,'Corsa',42,'pubblicato','2025-10-02 15:47:33','2025-10-02 15:47:33'),(26,'maratona milano',NULL,'maratona a milano','2025-10-23',NULL,'piazza duomo',43.00,'Milano',NULL,NULL,NULL,23.00,590,0,5,'Tennis',41,'pubblicato','2025-10-12 20:17:59','2025-10-12 20:17:59');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gpx_files`
--

DROP TABLE IF EXISTS `gpx_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gpx_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `download_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `gpx_files_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gpx_files`
--

LOCK TABLES `gpx_files` WRITE;
/*!40000 ALTER TABLE `gpx_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `gpx_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_recipients`
--

DROP TABLE IF EXISTS `message_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_recipients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message_id` int NOT NULL,
  `recipient_id` int NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `delivery_status` enum('pending','sent','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `registration_id` int DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_message_recipient` (`message_id`,`recipient_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `user_id` (`user_id`),
  KEY `registration_id` (`registration_id`),
  CONSTRAINT `message_recipients_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `event_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_recipients_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `message_recipients_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `message_recipients_ibfk_4` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`registration_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_recipients`
--

LOCK TABLES `message_recipients` WRITE;
/*!40000 ALTER TABLE `message_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `team_id` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `oggetto` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `messaggio` text COLLATE utf8mb4_unicode_ci,
  `stato` enum('pending','inviata','letta') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `data_creazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_invio` timestamp NULL DEFAULT NULL,
  `data_lettura` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `team_id` (`team_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,2,NULL,NULL,'team_message','Messaggio dal team','Il capitano ha condiviso informazioni importanti per la prossima gara.','pending','2025-09-21 20:58:48',NULL,NULL),(2,2,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','inviata','2025-09-26 19:58:48',NULL,NULL),(3,3,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','pending','2025-09-21 19:58:48',NULL,NULL),(4,3,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','letta','2025-09-20 11:58:48',NULL,NULL),(5,3,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','inviata','2025-09-22 07:58:48',NULL,NULL),(6,4,NULL,NULL,'team_message','Messaggio dal team','Il capitano ha condiviso informazioni importanti per la prossima gara.','inviata','2025-09-23 09:58:48',NULL,NULL),(7,4,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','inviata','2025-09-24 23:58:48',NULL,NULL),(8,4,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','letta','2025-09-23 14:58:48',NULL,NULL),(9,5,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','inviata','2025-09-26 10:58:48',NULL,NULL),(10,6,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','letta','2025-09-22 13:58:48',NULL,NULL),(11,7,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','pending','2025-09-20 23:58:48',NULL,NULL),(12,7,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','inviata','2025-09-27 00:58:48',NULL,NULL),(13,7,NULL,NULL,'team_message','Messaggio dal team','Il capitano ha condiviso informazioni importanti per la prossima gara.','inviata','2025-09-25 15:58:48',NULL,NULL),(14,8,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','pending','2025-09-25 11:58:48',NULL,NULL),(15,8,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','inviata','2025-09-25 07:58:48',NULL,NULL),(16,8,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','letta','2025-09-26 20:58:48',NULL,NULL),(17,8,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','pending','2025-09-24 23:58:48',NULL,NULL),(18,9,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','letta','2025-09-24 07:58:48',NULL,NULL),(19,9,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','inviata','2025-09-20 23:58:48',NULL,NULL),(20,10,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','pending','2025-09-26 19:58:48',NULL,NULL),(21,10,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','inviata','2025-09-20 09:58:48',NULL,NULL),(22,11,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','pending','2025-09-24 12:58:48',NULL,NULL),(23,11,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','pending','2025-09-23 06:58:48',NULL,NULL),(24,12,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','letta','2025-09-22 02:58:48',NULL,NULL),(25,12,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','pending','2025-09-25 14:58:48',NULL,NULL),(26,12,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','inviata','2025-09-21 15:58:48',NULL,NULL),(27,13,NULL,NULL,'team_message','Messaggio dal team','Il capitano ha condiviso informazioni importanti per la prossima gara.','inviata','2025-09-24 14:58:48',NULL,NULL),(28,13,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','inviata','2025-09-22 10:58:48',NULL,NULL),(29,13,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','inviata','2025-09-21 03:58:48',NULL,NULL),(30,14,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','inviata','2025-09-20 16:58:48',NULL,NULL),(31,14,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','letta','2025-09-21 13:58:48',NULL,NULL),(32,15,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','letta','2025-09-26 11:58:48',NULL,NULL),(33,15,NULL,NULL,'team_invitation','Invito al team','Sei stato invitato a far parte del team Running Milano City!','inviata','2025-09-24 10:58:48',NULL,NULL),(34,15,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','letta','2025-09-20 12:58:48',NULL,NULL),(35,15,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','pending','2025-09-26 03:58:48',NULL,NULL),(36,16,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','pending','2025-09-26 03:58:48',NULL,NULL),(37,17,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','letta','2025-09-22 13:58:48',NULL,NULL),(38,17,NULL,NULL,'event_reminder','Promemoria evento','Non dimenticare: la Maratona di Milano è tra 3 giorni!','pending','2025-09-21 16:58:48',NULL,NULL),(39,17,NULL,NULL,'payment_confirmation','Pagamento confermato','Il tuo pagamento per il Trail delle Cinque Terre è stato elaborato.','pending','2025-09-22 07:58:48',NULL,NULL),(40,18,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','inviata','2025-09-25 21:58:48',NULL,NULL),(41,18,NULL,NULL,'event_update','Aggiornamento evento','Sono state pubblicate informazioni aggiornate per il tuo evento.','pending','2025-09-26 23:58:48',NULL,NULL);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_orders`
--

DROP TABLE IF EXISTS `product_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_ordine` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `prodotto_id` int NOT NULL,
  `evento_id` int NOT NULL,
  `organizer_id` int NOT NULL,
  `quantita` int NOT NULL DEFAULT '1',
  `prezzo_unitario` decimal(10,2) NOT NULL,
  `totale` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `data_ordine` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_ordine` (`numero_ordine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_orders`
--

LOCK TABLES `product_orders` WRITE;
/*!40000 ALTER TABLE `product_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receipts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `registration_id` int NOT NULL,
  `receipt_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `pdf_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `registration_id` (`registration_id`),
  CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `receipts_ibfk_2` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`registration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipts`
--

LOCK TABLES `receipts` WRITE;
/*!40000 ALTER TABLE `receipts` DISABLE KEYS */;
INSERT INTO `receipts` VALUES (1,39,153,'SE-202510-0001',99.00,NULL,'2025-10-02 15:36:00'),(2,39,154,'SE-202510-0002',99.00,'receipts/receipt_SE-202510-0002.html','2025-10-02 15:40:17'),(3,46,155,'SE-202510-0003',23.00,'receipts/receipt_SE-202510-0003.html','2025-10-12 20:27:10');
/*!40000 ALTER TABLE `receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registrations` (
  `registration_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `event_id` int NOT NULL,
  `data_registrazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `stato` enum('pending','confermata','pagata','annullata') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `prezzo_pagato` decimal(10,2) DEFAULT '0.00',
  `totale_pagato` decimal(10,2) DEFAULT '0.00',
  `metodo_pagamento` enum('contanti','carta','bonifico','paypal','stripe') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`registration_id`),
  UNIQUE KEY `unique_user_event` (`user_id`,`event_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrations`
--

LOCK TABLES `registrations` WRITE;
/*!40000 ALTER TABLE `registrations` DISABLE KEYS */;
INSERT INTO `registrations` VALUES (151,39,24,'2025-10-02 15:12:30','confermata',434.00,NULL,'paypal','bcvsdf'),(153,39,22,'2025-10-02 15:36:00','confermata',99.00,99.00,'paypal','htzdfb'),(154,39,23,'2025-10-02 15:40:17','confermata',99.00,99.00,'paypal','hgj'),(155,46,26,'2025-10-12 20:27:10','confermata',23.00,23.00,'carta','');
/*!40000 ALTER TABLE `registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_invitations`
--

DROP TABLE IF EXISTS `team_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_invitations` (
  `invitation_id` int NOT NULL AUTO_INCREMENT,
  `team_id` int NOT NULL,
  `user_id` int NOT NULL,
  `invited_by` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `messaggio` text COLLATE utf8mb4_unicode_ci,
  `stato` enum('pending','accepted','declined') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `data_invito` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_risposta` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`invitation_id`),
  UNIQUE KEY `token` (`token`),
  KEY `team_id` (`team_id`),
  KEY `user_id` (`user_id`),
  KEY `invited_by` (`invited_by`),
  CONSTRAINT `team_invitations_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  CONSTRAINT `team_invitations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `team_invitations_ibfk_3` FOREIGN KEY (`invited_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_invitations`
--

LOCK TABLES `team_invitations` WRITE;
/*!40000 ALTER TABLE `team_invitations` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_invitations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_join_requests`
--

DROP TABLE IF EXISTS `team_join_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_join_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `team_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `message` text COLLATE utf8mb4_unicode_ci,
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` int DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  UNIQUE KEY `unique_user_team_request` (`user_id`,`team_id`,`status`),
  KEY `processed_by` (`processed_by`),
  KEY `idx_team` (`team_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `team_join_requests_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  CONSTRAINT `team_join_requests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `team_join_requests_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_join_requests`
--

LOCK TABLES `team_join_requests` WRITE;
/*!40000 ALTER TABLE `team_join_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_join_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_members` (
  `member_id` int NOT NULL AUTO_INCREMENT,
  `team_id` int NOT NULL,
  `user_id` int NOT NULL,
  `ruolo` enum('captain','co-captain','member') COLLATE utf8mb4_unicode_ci DEFAULT 'member',
  `stato` enum('attivo','left','removed') COLLATE utf8mb4_unicode_ci DEFAULT 'attivo',
  `data_join` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_uscita` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `unique_team_user` (`team_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `team_members_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  CONSTRAINT `team_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `nome_team` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `captain_id` int NOT NULL,
  `evento_id` int DEFAULT NULL,
  `privacy` enum('public','private') COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `stato` enum('attivo','inattivo') COLLATE utf8mb4_unicode_ci DEFAULT 'attivo',
  `data_creazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`team_id`),
  KEY `captain_id` (`captain_id`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`captain_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `teams_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `events` (`event_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,'erw','sdazi',41,NULL,'public','attivo','2025-10-12 21:59:34');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message_id` int DEFAULT NULL,
  `event_id` int NOT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_notifications` (`user_id`,`is_read`),
  KEY `idx_message_notifications` (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cognome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_nascita` date DEFAULT NULL,
  `sesso` enum('M','F','Altro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo` text COLLATE utf8mb4_unicode_ci,
  `citta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cap` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codice_fiscale` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruolo` enum('atleta','organizzatore','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'atleta',
  `certificato_medico` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_certificato` enum('agonistico','non_agonistico') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scadenza_certificato` date DEFAULT NULL,
  `tessera_affiliazione` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_registrazione` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_accesso` timestamp NULL DEFAULT NULL,
  `attivo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','ENDU','admin@endu.local','$2y$12$AwSWIIAAcy9nDzzarUDEJuAQHxmwtg.BwggTDyFl.SSmjtgpRu79q',NULL,NULL,NULL,NULL,'Milano','MI',NULL,NULL,'admin',NULL,NULL,NULL,NULL,'2025-09-27 08:54:08',NULL,1),(2,'Mario','Rossi','organizer@endu.local','$2y$12$Is.Nkvu1iUKSCW0PI5gMkuGCmFUS5jtmKmljfykaKDTNM2aQOsxLC',NULL,NULL,NULL,NULL,'Roma','RM',NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-09-27 08:54:08',NULL,1),(3,'Luca','Bianchi','athlete@endu.local','$2y$12$iDVoHBtdAFOyu.jna6KBgue.ef5S0dwaOGK7tqDD8MV2zWuB0ISRa',NULL,NULL,NULL,NULL,'Napoli','NA',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:54:09',NULL,1),(4,'Marco','Verdi','marco.verdi@email.com','$2y$12$B13RhANfv1a4FXgoYFCSAOQJWn7USMSQrldOvArRZRMuQeZoeI8AO','1985-03-15','M','3331234567',NULL,'Milano','MI',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:27',NULL,1),(5,'Laura','Bianchi','laura.bianchi@email.com','$2y$12$EFKGdTewlCAG6ipofGDD8ebKOv37CRNHawwDoDNQ2.Ti0xjS3uicS','1990-07-22','F','3337654321',NULL,'Roma','RM',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:27',NULL,1),(6,'Andrea','Rossi','andrea.rossi@email.com','$2y$12$wU2kBvLCMlujjN.viTiLB.Raf7cSkPkn2eU9M84H4WOlu/kTX90V2','1988-11-10','M','3339876543',NULL,'Napoli','NA',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:27',NULL,1),(7,'Giulia','Ferrari','giulia.ferrari@email.com','$2y$12$UxiPUvfiUv8ERF9MFQY2f.fSTrHF8WgnzBROBX59AFYvkV3kNylrW','1992-05-03','F','3335432109',NULL,'Torino','TO',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:27',NULL,1),(8,'Matteo','Romano','matteo.romano@email.com','$2y$12$U4LF2uJbYtmF9eNuyRtUK.h9ZiMKuj75qohgMI41SoTuImH3fi/Di','1987-09-18','M','3332109876',NULL,'Bologna','BO',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:27',NULL,1),(9,'Francesca','Conti','francesca.conti@email.com','$2y$12$.UrR4WnZBG7sp7koPzt05O5gTeNPLCKc5bZfINcYOK3o8/w8mCqxS','1991-12-25','F','3338765432',NULL,'Firenze','FI',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:28',NULL,1),(10,'Alessandro','Ricci','alessandro.ricci@email.com','$2y$12$Qi6f5.VykhbIAukuHpmDBOW2nAzcQYtC46Et.TSMuEMmMALSxyKz6','1986-04-07','M','3334321098',NULL,'Venezia','VE',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:28',NULL,1),(11,'Chiara','Marino','chiara.marino@email.com','$2y$12$1JHftEJXR6OQpvVlQyzpduv3HoY..9yESCoabIAIksLD7cDnrmE1C','1993-08-14','F','3336543210',NULL,'Palermo','PA',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:28',NULL,1),(12,'Davide','Gallo','davide.gallo@email.com','$2y$12$mysQKTUkFL/QstzTwKhZh.NXvn5ypjmKbvmrs6Bk2eCHEXtH0CfDy','1989-01-30','M','3339012345',NULL,'Genova','GE',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:28',NULL,1),(13,'Valentina','Costa','valentina.costa@email.com','$2y$12$/NJW2YK3EyeNdc0hK2Sc1OE/FREdK6uRZIJisFWXHBMeTUUPbzoEa','1994-06-12','F','3335678901',NULL,'Bari','BA',NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 08:58:28',NULL,1),(14,'Roberto','Marchetti','roberto.marchetti@events.it','$2y$12$GYBaNckdM7T2Xaqu0pDYde3huYGeL0YyyCTljXkgNMnhWqH7t5J5O','1975-02-20','M','3401234567',NULL,'Milano','MI',NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-09-27 08:58:29',NULL,1),(15,'Elena','Santoro','elena.santoro@sportevents.it','$2y$12$Sp1n1eWbIc6K0ULr0Kv0keG.dkrQXv10m4a85oWB37GQhCeyzvuBC','1980-10-15','F','3407654321',NULL,'Roma','RM',NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-09-27 08:58:29',NULL,1),(16,'Giuseppe','Leone','giuseppe.leone@trailrun.it','$2y$12$dJGL6ueBaio6JzL/KSBEmuCmsJxrXUxVJskeg366sDvs8Twkn.kCy','1978-06-08','M','3409876543',NULL,'Napoli','NA',NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-09-27 08:58:29',NULL,1),(17,'Silvia','Moretti','silvia.moretti@marathon.it','$2y$12$tsYPDlVtmvf.cOeNV1ONj.NT.1tS.dMK2RwI.rRSeR18hv14PHdxa','1982-12-03','F','3405432109',NULL,'Torino','TO',NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-09-27 08:58:29',NULL,1),(18,'Francesco','Barbieri','francesco.barbieri@cycling.it','$2y$12$TRWvXKZOIfIlL0K2lZsdGuTEgB7KXiN.kvcwIUfQZKQnDCPNCAIci','1976-09-27','M','3402109876',NULL,'Bologna','BO',NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-09-27 08:58:29',NULL,1),(34,'Test','User','test.user@example.com','$2y$12$FDsTirFYw7oxd9iDOPqXKeExUdFSZ09ycfuYIPd6FCNj.0BveAK46','1990-05-15','M','3331234567',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 09:35:17','2025-09-27 13:38:09',1),(35,'samuel','danesi','samuel@gmail.com','$2y$12$SQ9m7mQtCIVZYk2pKW9UqeR5a8lLa6nnBUuRAgCc7DSptakDkMbRC','2005-09-16','M','3516457405',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-09-27 23:50:41','2025-09-28 00:47:42',1),(36,'samuel','danesi','giorno@gmail.com','$2y$12$NCXyFVWIpYis7ioWIz3GoexW1ZlafqrgC17lz5gWMh5IkKo4lPftG','2005-09-19','M','3516457405',NULL,NULL,NULL,NULL,NULL,'atleta','uploads/certificati/68d87a34ea375_1759017524.jpg',NULL,NULL,NULL,'2025-09-27 23:58:44',NULL,1),(37,'samuel','danesi','giorgio@gmail.com','$2y$12$Sei1NGkNsLbx6S5htz820e7uxpo3R.nFWog5v6l6r6NbIEg63VVsu','2005-05-05','M','3516457405',NULL,NULL,NULL,NULL,NULL,'atleta','uploads/certificati/68d87a7a79f4e_1759017594.png',NULL,NULL,NULL,'2025-09-27 23:59:54','2025-09-27 23:59:58',1),(38,'samuel','danesi','samueldanesi11@gmail.com','$2y$12$s4frInaXdjo4E2AsCaosNeDnBRjtINpHSOAOtMsast50SVS.2mUS.','2005-02-02','M','+39 3516457405',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-10-01 12:15:31',NULL,1),(39,'samuel','danesi','samueldanesi1199@gmail.com','$2y$12$GwiJ0E5CM4sIbecjboN7iuh5oUc.00UqHCN9KkkuVrmfPr8HTYxLe','2005-02-02','M','+39 3516457405',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-10-01 12:16:14',NULL,1),(40,'samuel','danesi','samueldanesi1111@gmail.com','$2y$12$pYS7xLA2GlNXhaGsqWjaheD.SRAObKJr7T3YmHuBw7t3TFa8uKe5O','2005-02-02','M','+39 3516457405',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-10-01 12:19:33',NULL,1),(41,'samuel','danesi','sa@gmail.com','$2y$12$dmR0mUwi59YDVE0SO5oDqO/La/hyBhuvglPyGjbTDMmhYngF6y0M6','2003-03-03','M','+39 3516457405',NULL,NULL,NULL,NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-10-01 15:45:08',NULL,1),(42,'giorgio','dallo','giorgi@gmail.com','$2y$12$MY8lyK3r21IEqCTUQ/Bb4e8bR6N2z4ZY6VxWseJRWdIImp/lh4wYS','2001-06-06','M','+393516457405',NULL,NULL,NULL,NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-10-02 10:43:14',NULL,1),(43,'Organizer','Test','organizer@example.com','$2y$12$fF9YlILe/U3tqSctrGYTYeSR1COD5s.l5W9tpH4.fmpNkGsgtWY1C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'organizzatore',NULL,NULL,NULL,NULL,'2025-10-02 14:26:45',NULL,1),(44,'samuel','danesi','ss@gmail.com','$2y$12$y13yLlhreKHQdy1u2LegbemzV3rIg7Bs1ktGUrAnmSMAjfwR/U6lS','2004-03-04','M','+39 3516457405',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-10-02 18:02:09',NULL,1),(45,'giorgio','pino','giorgio00@gmail.com','$2y$12$S0EXabopRmCO4mM/Y8kKZegqtGXV2ZmJntWwH3cb1ut7/FdDcvumy','2004-10-07','M','3516457405',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-10-06 15:34:14',NULL,1),(46,'chiara','gherardi','chiara@gmail.com','$2y$12$cLaGlB0iEjTYjbzJLyIE4.tyM5VTflI2aSapoxVNlCpfj/umGlTIy','2004-10-08','F','3516457405',NULL,NULL,NULL,NULL,NULL,'atleta',NULL,NULL,NULL,NULL,'2025-10-12 20:20:08',NULL,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-20 13:54:56
