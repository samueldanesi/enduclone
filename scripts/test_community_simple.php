<?php
/**
 * Test semplice per la community
 */
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Test query semplice sulla tabella community_posts
    $query = "SELECT COUNT(*) as total FROM community_posts";
    $stmt = $db->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ Community Posts Table: " . $result['total'] . " records\n";
    
    // Test query con JOIN verso users
    $query = "SELECT u.nome, u.cognome FROM users u LIMIT 1";
    $stmt = $db->query($query);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Users Table accessible: " . $user['nome'] . " " . $user['cognome'] . "\n";
    } else {
        echo "❌ No users found\n";
    }
    
    // Test query con JOIN verso events
    $query = "SELECT e.nome FROM events e LIMIT 1";
    $stmt = $db->query($query);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($event) {
        echo "✅ Events Table accessible: " . $event['nome'] . "\n";
    } else {
        echo "❌ No events found\n";
    }
    
    // Test query completa come nel CommunityPost
    $query = "SELECT p.*, 
                    u.nome as user_nome, 
                    u.cognome as user_cognome,
                    e.nome as event_title
             FROM community_posts p
             LEFT JOIN users u ON p.user_id = u.id
             LEFT JOIN events e ON p.event_id = e.id
             ORDER BY p.created_at DESC
             LIMIT 5";
    
    $stmt = $db->query($query);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Community Feed Query: " . count($posts) . " posts found\n";
    echo "🎉 Community ready to use!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>