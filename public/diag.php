<?php
// Simple diagnostics to verify routing, paths, and code version on server
header('Content-Type: text/html; charset=utf-8');

$root = dirname(__DIR__);

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$sections = [];

// 1) Server/Request info
$sections['Server/Request'] = [
  'DATE' => date('c'),
  'PHP_VERSION' => PHP_VERSION,
  'PHP_SAPI' => php_sapi_name(),
  'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? '(n/a)',
  'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'] ?? '(n/a)',
  'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? '(n/a)',
  'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '(n/a)',
  'PWD (dirname(__DIR__))' => $root,
];

// 2) Config
$env = 'unknown';
$base = 'unknown';
try {
  require_once $root . '/config/config.php';
  $env = defined('ENVIRONMENT') ? ENVIRONMENT : 'not defined';
  $base = defined('BASE_URL') ? BASE_URL : 'not defined';
} catch (Throwable $e) {
  $sections['Config Error'] = $e->getMessage();
}
$sections['Config'] = [
  'ENVIRONMENT' => $env,
  'BASE_URL' => $base,
  'DEBUG_MODE' => defined('DEBUG_MODE') && DEBUG_MODE ? 'true' : 'false',
];

// 3) File checks
$files = [
  'public/index.php',
  'index.php',
  'app/models/Event.php',
  'app/controllers/EventController.php',
  'config/config.php',
  'config/database.php',
];
$fileInfo = [];
foreach ($files as $rel) {
  $abs = $root . '/' . $rel;
  $exists = file_exists($abs);
  $size = $exists ? filesize($abs) : 0;
  $mtime = $exists ? date('Y-m-d H:i:s', filemtime($abs)) : '(missing)';
  $sha1 = $exists ? sha1_file($abs) : '(missing)';
  $fileInfo[$rel] = [
    'exists' => $exists ? 'yes' : 'no',
    'size' => $size,
    'mtime' => $mtime,
    'sha1' => $sha1,
  ];
}
$sections['Files'] = $fileInfo;

// 4) Inspect Event.php content for legacy patterns
$eventPath = $root . '/app/models/Event.php';
$eventChecks = [];
if (is_file($eventPath)) {
  $content = file_get_contents($eventPath);
  $eventChecks['contains e.stato'] = (strpos($content, 'e.stato') !== false) ? 'YES' : 'NO';
  $eventChecks['contains resolveColumns'] = (strpos($content, 'resolveColumns') !== false) ? 'YES' : 'NO';
  $eventChecks['contains readAll('] = (strpos($content, 'function readAll(') !== false) ? 'YES' : 'NO';
  $eventChecks['first 200 chars'] = substr($content, 0, 200);
}
$sections['Event.php scan'] = $eventChecks;

// 5) Opcache status (if any)
$opcache = function_exists('opcache_get_status') ? @opcache_get_status(false) : null;
$sections['OPcache enabled'] = $opcache && !empty($opcache['opcache_enabled']) ? 'YES' : 'NO';

// 6) Output neatly
?>
<!doctype html>
<html lang="it"><head><meta charset="utf-8"><title>Diag</title>
<style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;padding:20px}
pre{background:#f7f7f7;padding:10px;border-radius:6px;overflow:auto}
section{margin-bottom:24px}h2{margin-bottom:8px}
.table{border-collapse:collapse} .table td,.table th{border:1px solid #ddd;padding:6px 8px}
</style></head><body>
<h1>Diagnostics</h1>
<?php foreach ($sections as $title => $data): ?>
  <section>
    <h2><?=h($title)?></h2>
    <?php if (is_array($data)): ?>
      <table class="table">
        <tbody>
        <?php foreach ($data as $k=>$v): ?>
          <tr><th><?=h($k)?></th><td>
            <?php if (is_array($v)) { echo '<pre>'.h(print_r($v,true)).'</pre>'; }
            else { echo h((string)$v); } ?>
          </td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <pre><?=h((string)$data)?></pre>
    <?php endif; ?>
  </section>
<?php endforeach; ?>
<p><em>Tip:</em> appendi <code>?debug=1</code> all'URL per forzare la pulizia opcache.</p>
</body></html>
