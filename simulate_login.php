<?php
session_start();

// Simula login utente per test
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_type'] = 'participant';

echo "✅ Sessione utente simulata:\n";
echo "- User ID: " . $_SESSION['user_id'] . "\n";
echo "- Nome: " . $_SESSION['user_name'] . "\n";
echo "- Tipo: " . $_SESSION['user_type'] . "\n";

echo "\n🔗 Ora puoi testare le sezioni community:\n";
echo "- Dashboard: http://localhost:8000/community\n";
echo "- Universal: http://localhost:8000/community/universal\n";
echo "- Eventi: http://localhost:8000/community/events\n";
echo "- Risultati: http://localhost:8000/community/results\n";
?>