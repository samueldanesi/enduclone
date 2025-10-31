-- Tabelle per il sistema shop di SportEvents
-- Data creazione: 2025-01-09

-- Tabella prodotti evento
CREATE TABLE IF NOT EXISTS event_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT,
    categoria ENUM('abbigliamento', 'accessori', 'pacco_gara', 'foto', 'donazione', 'altro') NOT NULL DEFAULT 'altro',
    prezzo DECIMAL(10,2) NOT NULL,
    quantita_disponibile INT NOT NULL DEFAULT 0,
    quantita_venduta INT NOT NULL DEFAULT 0,
    immagine VARCHAR(255),
    evento_id INT NOT NULL,
    organizer_id INT NOT NULL,
    attivo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (evento_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_evento (evento_id),
    INDEX idx_organizer (organizer_id),
    INDEX idx_categoria (categoria),
    INDEX idx_attivo (attivo)
);

-- Tabella ordini prodotti
CREATE TABLE IF NOT EXISTS product_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_ordine VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    prodotto_id INT NOT NULL,
    evento_id INT NOT NULL,
    organizer_id INT NOT NULL,
    quantita INT NOT NULL DEFAULT 1,
    prezzo_unitario DECIMAL(10,2) NOT NULL,
    costo_spedizione DECIMAL(10,2) DEFAULT 5.00,
    iva DECIMAL(10,2) DEFAULT 0.00,
    totale DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    metodo_pagamento ENUM('carta', 'paypal', 'bonifico', 'contrassegno') DEFAULT 'carta',
    
    -- Dati spedizione
    nome_destinatario VARCHAR(100) NOT NULL,
    cognome_destinatario VARCHAR(100) NOT NULL,
    indirizzo_spedizione TEXT NOT NULL,
    citta VARCHAR(100) NOT NULL,
    cap VARCHAR(10) NOT NULL,
    provincia VARCHAR(10) NOT NULL,
    telefono VARCHAR(20),
    note_consegna TEXT,
    
    -- Dati pagamento
    transaction_id VARCHAR(100),
    pagato_at TIMESTAMP NULL,
    
    -- Spedizione
    corriere VARCHAR(100),
    tracking_number VARCHAR(100),
    spedito_at TIMESTAMP NULL,
    consegnato_at TIMESTAMP NULL,
    
    data_ordine TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (prodotto_id) REFERENCES event_products(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user (user_id),
    INDEX idx_prodotto (prodotto_id),
    INDEX idx_evento (evento_id),
    INDEX idx_organizer (organizer_id),
    INDEX idx_status (status),
    INDEX idx_data_ordine (data_ordine),
    INDEX idx_numero_ordine (numero_ordine)
);

-- Trigger per generare numero ordine automatico
DELIMITER $$
CREATE TRIGGER generate_order_number 
BEFORE INSERT ON product_orders
FOR EACH ROW
BEGIN
    IF NEW.numero_ordine IS NULL OR NEW.numero_ordine = '' THEN
        SET NEW.numero_ordine = CONCAT('SE-', DATE_FORMAT(NOW(), '%Y%m'), '-', LPAD((
            SELECT COALESCE(MAX(CAST(SUBSTRING(numero_ordine, -4) AS UNSIGNED)), 0) + 1
            FROM product_orders 
            WHERE numero_ordine LIKE CONCAT('SE-', DATE_FORMAT(NOW(), '%Y%m'), '-%')
        ), 4, '0'));
    END IF;
END$$
DELIMITER ;

-- Inserimento dati di esempio per testing
-- Prodotti di esempio per eventi esistenti
INSERT INTO event_products (nome, descrizione, categoria, prezzo, quantita_disponibile, evento_id, organizer_id) 
SELECT 
    'Maglietta Tecnica Evento',
    'Maglietta tecnica traspirante con logo dell\'evento',
    'abbigliamento',
    25.00,
    100,
    e.id,
    e.organizer_id
FROM events e 
ORDER BY e.id 
LIMIT 3;

INSERT INTO event_products (nome, descrizione, categoria, prezzo, quantita_disponibile, evento_id, organizer_id) 
SELECT 
    'Borraccia Sportiva',
    'Borraccia termica da 500ml con logo evento',
    'accessori',
    15.00,
    50,
    e.id,
    e.organizer_id
FROM events e 
ORDER BY e.id 
LIMIT 2;

INSERT INTO event_products (nome, descrizione, categoria, prezzo, quantita_disponibile, evento_id, organizer_id) 
SELECT 
    'Pacco Gara Premium',
    'Pacco gara completo con gadget esclusivi',
    'pacco_gara',
    45.00,
    25,
    e.id,
    e.organizer_id
FROM events e 
ORDER BY e.id 
LIMIT 1;

INSERT INTO event_products (nome, descrizione, categoria, prezzo, quantita_disponibile, evento_id, organizer_id) 
SELECT 
    'Foto Evento Professionale',
    'Servizio fotografico professionale durante l\'evento',
    'foto',
    35.00,
    999,
    e.id,
    e.organizer_id
FROM events e 
ORDER BY e.id 
LIMIT 2;

INSERT INTO event_products (nome, descrizione, categoria, prezzo, quantita_disponibile, evento_id, organizer_id) 
SELECT 
    'Donazione Beneficenza',
    'Contributo volontario per sostenere l\'associazione organizzatrice',
    'donazione',
    10.00,
    999,
    e.id,
    e.organizer_id
FROM events e 
ORDER BY e.id 
LIMIT 3;

-- Statistiche e viste utili
CREATE OR REPLACE VIEW shop_statistics AS
SELECT 
    COUNT(*) as total_products,
    SUM(CASE WHEN attivo = TRUE THEN 1 ELSE 0 END) as active_products,
    SUM(quantita_disponibile) as total_stock,
    SUM(quantita_venduta) as total_sold,
    AVG(prezzo) as avg_price,
    COUNT(DISTINCT organizer_id) as total_sellers,
    COUNT(DISTINCT evento_id) as events_with_products
FROM event_products;

CREATE OR REPLACE VIEW orders_statistics AS
SELECT 
    COUNT(*) as total_orders,
    SUM(totale) as total_revenue,
    AVG(totale) as avg_order_value,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_orders,
    COUNT(CASE WHEN status = 'shipped' THEN 1 END) as shipped_orders,
    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
FROM product_orders;

-- Indici per performance
CREATE INDEX idx_products_search ON event_products (nome, descrizione);
CREATE INDEX idx_products_price ON event_products (prezzo);
CREATE INDEX idx_orders_totale ON product_orders (totale);
CREATE INDEX idx_orders_created ON product_orders (data_ordine DESC);

-- Autorizzazioni per eventuali utenti del database
-- GRANT SELECT, INSERT, UPDATE ON event_products TO 'shop_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE ON product_orders TO 'shop_user'@'localhost';