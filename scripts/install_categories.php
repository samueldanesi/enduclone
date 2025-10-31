<?php
// CLI script: php scripts/install_categories.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "Installing categories table...\n";

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if table exists
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'categories'");
    $stmt->execute();
    $exists = (int)($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;

    if (!$exists) {
        $sql = "CREATE TABLE IF NOT EXISTS `categories` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `nome` varchar(100) NOT NULL,
          `descrizione` text DEFAULT NULL,
          `attiva` tinyint(1) DEFAULT 1,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->exec($sql);
        echo "- Table 'categories' created.\n";
    } else {
        echo "- Table 'categories' already exists.\n";
    }

    // Seed defaults if empty
    $count = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count === 0) {
        $seed = "INSERT INTO `categories` (`nome`, `descrizione`, `attiva`) VALUES
            ('Running','Eventi di corsa su strada e trail',1),
            ('Ciclismo','Eventi ciclistici',1),
            ('Triathlon','Nuoto, ciclismo e corsa',1),
            ('Trail','Corsa in natura',1),
            ('Camminata','Eventi non competitivi',1);";
        $db->exec($seed);
        echo "- Inserted default categories.\n";
    } else {
        echo "- Categories table already has data ($count rows).\n";
    }

    echo "Done.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
