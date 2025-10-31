<?php
// Ricerca testuale nei file del progetto (uso: /find.php?debug=1&q=e.stato)
header('Content-Type: text/html; charset=utf-8');

$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
if (!$debug) {
    http_response_code(403);
    echo 'Richiesta non autorizzata. Aggiungi ?debug=1';
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo '<p>Parametro q mancante. Esempio: /find.php?debug=1&q=e.stato</p>';
    exit;
}

$root = dirname(__DIR__);
$excludeDirs = ['.git', 'node_modules', 'vendor', 'uploads', 'storage', 'cache'];
$allowedExt = ['php', 'html', 'htaccess'];

function iterFiles($base, $excludeDirs, $allowedExt) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if (!$file->isFile()) continue;
        $path = $file->getPathname();
        // Exclude dirs
        foreach ($excludeDirs as $ex) {
            if (strpos($path, DIRECTORY_SEPARATOR . $ex . DIRECTORY_SEPARATOR) !== false) {
                continue 2;
            }
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) continue;
        yield $path;
    }
}

$matches = [];
foreach (iterFiles($root, $excludeDirs, $allowedExt) as $path) {
    $content = @file_get_contents($path);
    if ($content === false) continue;
    if (stripos($content, $q) !== false) {
        $matches[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
    }
}

echo '<h1>Ricerca: ' . htmlspecialchars($q, ENT_QUOTES, 'UTF-8') . '</h1>';
echo '<p>Root: ' . htmlspecialchars($root, ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>Trovati: ' . count($matches) . '</p>';
echo '<ul>';
foreach ($matches as $m) {
    echo '<li>' . htmlspecialchars($m, ENT_QUOTES, 'UTF-8') . '</li>';
}
echo '</ul>';

?>