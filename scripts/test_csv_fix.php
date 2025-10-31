<?php
// Test rapido per verificare se i parametri fputcsv funzionano
$test_file = tempnam(sys_get_temp_dir(), 'csv_test_');
$handle = fopen($test_file, 'w');

// Test con i nuovi parametri
try {
    fputcsv($handle, ['Nome', 'Cognome'], ',', '"', '\\');
    fputcsv($handle, ['Marco', 'Rossi'], ',', '"', '\\');
    fclose($handle);
    
    echo "✅ fputcsv() test passed\n";
    echo "File contenuto:\n";
    echo file_get_contents($test_file);
    
    unlink($test_file);
} catch (Exception $e) {
    echo "❌ fputcsv() test failed: " . $e->getMessage() . "\n";
    if ($handle) fclose($handle);
    if (file_exists($test_file)) unlink($test_file);
}
?>