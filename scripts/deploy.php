<?php
/**
 * Simple FTP/SFTP deploy script for SportEvents
 * - Supports FTP and FTPS (explicit TLS)
 * - Syncs only changed files (size/mtime/sha1 heuristic)
 * - Can run in dry-run mode to preview actions
 * - Skips large/unwanted dirs by default
 *
 * Usage (from project root):
 *   php scripts/deploy.php --config=scripts/deploy.config.php --dry-run
 *   php scripts/deploy.php --config=scripts/deploy.config.php
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

const DEFAULT_EXCLUDES = [
    '.git', '.idea', '.vscode', 'node_modules', 'vendor',
    'uploads/receipts', 'uploads/gpx', 'uploads/certificates',
    'sportevents-piramedia.zip', 'sportevents-production.zip'
];

// Try to load Composer autoloader if available (for phpseclib SFTP)
$autoloadCandidates = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    getcwd() . '/vendor/autoload.php',
];
foreach ($autoloadCandidates as $auto) {
    if (is_file($auto)) { require_once $auto; break; }
}

function usage($msg = '') {
    if ($msg) fwrite(STDERR, "Error: $msg\n");
    echo "\nDeploy usage:\n";
    echo "  php scripts/deploy.php --config=scripts/deploy.config.php [--dry-run] [--only=path1,path2] [--debug] [--ls[=path1,path2]]\n\n";
    exit($msg ? 1 : 0);
}

$options = getopt('', ['config:', 'dry-run', 'only::', 'debug', 'ls::', 'write-test']);
if (!isset($options['config'])) usage('Missing --config');

$configFile = $options['config'];
if (!is_file($configFile)) usage("Config not found: $configFile");

$config = require $configFile;

// Allow environment variables to override config (avoid storing secrets on disk)
// Supported env vars: DEPLOY_FTP_PROTOCOL, DEPLOY_FTP_HOST, DEPLOY_FTP_PORT,
// DEPLOY_FTP_USER, DEPLOY_FTP_PASS, DEPLOY_FTP_ROOT
$envMap = [
    'protocol' => 'DEPLOY_FTP_PROTOCOL',
    'host' => 'DEPLOY_FTP_HOST',
    'port' => 'DEPLOY_FTP_PORT',
    'username' => 'DEPLOY_FTP_USER',
    'password' => 'DEPLOY_FTP_PASS',
    'root' => 'DEPLOY_FTP_ROOT',
];
foreach ($envMap as $key => $env) {
    $val = getenv($env);
    if ($val !== false && $val !== '') {
        // Cast numeric for port
        if ($key === 'port') { $val = (int)$val; }
        $config[$key] = $val;
    }
}

$required = ['host','username','password','root'];
foreach ($required as $k) {
    if (empty($config[$k])) usage("Missing config '$k'");
}

$DRY = isset($options['dry-run']);
$DEBUG = isset($options['debug']);
$ONLY = [];
if (isset($options['only']) && $options['only'] !== false) {
    $ONLY = array_filter(array_map('trim', explode(',', $options['only'])));
}
$LS = [];
if (array_key_exists('ls', $options)) {
    if ($options['ls'] === false) {
        $LS = ['.'];
    } else {
        $LS = array_filter(array_map('trim', explode(',', $options['ls'])));
        if (empty($LS)) $LS = ['.'];
    }
}
$DO_WRITE_TEST = isset($options['write-test']);

$protocol = strtolower($config['protocol'] ?? 'ftp');
// Normalize host if a scheme is included
if (!empty($config['host']) && preg_match('#^(s?ftps?)://(.+)$#i', $config['host'], $m)) {
    $protocol = strtolower($m[1]) === 'sftp' ? 'sftp' : (strtolower($m[1]) === 'ftps' ? 'ftps' : 'ftp');
    $config['host'] = $m[2];
}
$useSftp = $protocol === 'sftp';
$useFtps = $protocol === 'ftps';
$port = $config['port'] ?? ($useSftp ? 22 : 21);
$PATH_MAP = $config['path_map'] ?? [];
$RESTRICT_TO_PUBLIC = (bool)($config['restrict_to_public'] ?? false);

function logi($msg) { echo $msg."\n"; }
function logd($msg) { if ($GLOBALS['DEBUG']) echo "[debug] $msg\n"; }
function shouldExclude($path, $excludes) {
    foreach ($excludes as $ex) {
        if ($ex === '') continue;
        if (str_starts_with($path, rtrim($ex, '/').'/') || $path === rtrim($ex,'/')) return true;
    }
    return false;
}

function listLocalFiles($baseDir, $excludes) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        $rel = str_replace($baseDir.'/', '', $file->getPathname());
        if (shouldExclude($rel, $excludes)) continue;
        $files[$rel] = [
            'size' => $file->getSize(),
            'mtime' => $file->getMTime(),
            'sha1' => sha1_file($file->getPathname()),
        ];
    }
    return $files;
}

function ftp_connect_wrap($host, $port, $timeout = 20, $ftps = false) {
    if ($ftps) {
        if (!function_exists('ftp_ssl_connect')) throw new RuntimeException('FTPS not available');
        return ftp_ssl_connect($host, $port, $timeout);
    }
    return ftp_connect($host, $port, $timeout);
}

function ensureRemoteDir($ftp, $root, $path) {
    $parts = explode('/', trim($path, '/'));
    $cur = rtrim($root, '/');
    foreach ($parts as $p) {
        if ($p === '') continue;
        $cur .= '/'.$p;
        if (!@ftp_chdir($ftp, $cur)) {
            if (!@ftp_mkdir($ftp, $cur)) return false;
            logd("mkdir $cur");
        }
    }
    return true;
}

function getRemoteSizeMtime($ftp, $root, $relPath) {
    $remotePath = rtrim($root,'/').'/'.$relPath;
    $size = ftp_size($ftp, $remotePath);
    $mtime = ftp_mdtm($ftp, $remotePath);
    if ($size < 0) $size = null;
    if ($mtime < 0) $mtime = null;
    return [$size, $mtime];
}

function mapRemoteRel($rel, $map) {
    if (!$map || !is_array($map)) return $rel;
    // Sort by longest prefix first to avoid partial matches overriding longer ones
    uksort($map, function($a, $b){ return strlen($b) <=> strlen($a); });
    foreach ($map as $from => $to) {
        $from = (string)$from;
        if ($from === '') continue;
        // Ensure trailing slash semantics preserved
        if (str_starts_with($rel, $from)) {
            $suffix = substr($rel, strlen($from));
            $to = (string)$to;
            // Normalize slashes
            $to = trim($to, '/');
            return ($to === '' ? '' : $to.'/') . $suffix;
        }
    }
    return $rel;
}

$baseDir = getcwd();
$excludes = array_values(array_unique(array_merge(DEFAULT_EXCLUDES, $config['exclude'] ?? [])));
if ($ONLY) { logi('Only paths: '.implode(', ', $ONLY)); }

logi("Collecting local files...");
$locals = listLocalFiles($baseDir, $excludes);

if ($ONLY) {
    $locals = array_filter($locals, function($k) use ($ONLY) {
        foreach ($ONLY as $o) {
            if (str_starts_with($k, rtrim($o,'/').'/') || $k === rtrim($o,'/')) return true;
        }
        return false;
    }, ARRAY_FILTER_USE_KEY);
}

// If configured, restrict uploads to public/ subtree unless --only is used
if ($RESTRICT_TO_PUBLIC && empty($ONLY)) {
    $locals = array_filter($locals, function($k){
        return str_starts_with($k, 'public/');
    }, ARRAY_FILTER_USE_KEY);
}

if ($useSftp) {
    logi("Connecting to {$config['host']}:$port (SFTP)...");
    if (!class_exists('phpseclib3\\Net\\SFTP')) {
        fwrite(STDERR, "phpseclib not found. Please run 'composer require phpseclib/phpseclib' in project root.\n");
        exit(2);
    }
    $sftp = new \phpseclib3\Net\SFTP($config['host'], $port, 20);
    if (!$sftp->login($config['username'], $config['password'])) die("Auth failed\n");
    if ($DEBUG) {
        $banner = $sftp->getServerIdentification();
        if ($banner) logd("Server: ".$banner);
    }
    // Root existence check for SFTP
    $rootPath = rtrim($config['root'], '/');
    $rootStat = $rootPath === '' ? true : $sftp->stat($rootPath);
    if ($rootPath !== '' && $rootStat === false) {
        logi("[ERROR] Remote root not found: {$config['root']}");
        logi("Listing top-level to help you choose a correct root:");
        $list = $sftp->rawlist('.');
        if (is_array($list)) {
            foreach ($list as $name => $info) {
                if ($name === '.' || $name === '..') continue;
                $type = ($info['type'] ?? 0) === 2 ? 'dir' : 'file';
                logi(" - $name ($type)");
            }
        }
        logi("Try setting DEPLOY_FTP_ROOT to one of the directories above, e.g. '/httpdocs' or '/public_html'.");
        // If --ls provided, continue to LS phase, else exit
        if (empty($LS)) exit(1);
    }
} else {
    logi("Connecting to {$config['host']}:$port (".($useFtps?'FTPS':'FTP').")...");
    $ftp = ftp_connect_wrap($config['host'], $port, 20, $useFtps);
    if (!$ftp) die("Connection failed\n");
    if (!ftp_login($ftp, $config['username'], $config['password'])) die("Auth failed\n");
    ftp_pasv($ftp, true);
}

$root = $config['root'];

// Optional remote listing helper
if (!empty($LS)) {
    logi("Remote listing (relative to root '{$root}'):");
    foreach ($LS as $path) {
        $target = rtrim($root, '/');
        if ($path !== '.' && $path !== '') $target .= '/'.$path;
        logi("-- ls {$path} --");
        if ($useSftp) {
            $list = $sftp->rawlist($target);
            if ($list === false) { logi("(cannot list)" ); continue; }
            foreach ($list as $name => $info) {
                if ($name === '.' || $name === '..') continue;
                $type = ($info['type'] ?? 0) === 2 ? 'dir' : 'file';
                $sz = $info['size'] ?? 0;
                logi("   $name ($type) $sz bytes");
            }
        } else {
            $list = @ftp_nlist($ftp, $target);
            if ($list === false) { logi("(cannot list)" ); continue; }
            foreach ($list as $name) {
                logi("   $name");
            }
        }
    }
    // If only listing requested, exit after listing
    if ($DRY === false && empty($ONLY)) exit(0);
}

// Optional write test to validate root permissions
if ($DO_WRITE_TEST) {
    $testFile = rtrim($root,'/').'/.deploy_write_test_'.time().'.txt';
    $content = "test ".date('c');
    logi("Write-test to $testFile ...");
    $okTest = false;
    if ($useSftp) {
        $okTest = $sftp->put($testFile, $content);
        if (!$okTest) {
            logi("[FAIL] write-test");
            if (method_exists($sftp, 'getSFTPErrors')) {
                $errs = $sftp->getSFTPErrors();
                if ($errs) foreach ($errs as $e) logi("  SFTP: $e");
            } elseif (method_exists($sftp, 'getLastSFTPError')) {
                $e = $sftp->getLastSFTPError(); if ($e) logi("  SFTP: $e");
            }
        }
    } else {
        $tmp = tmpfile();
        fwrite($tmp, $content);
        $meta = stream_get_meta_data($tmp);
        $okTest = ftp_put($ftp, $testFile, $meta['uri'], FTP_ASCII);
        fclose($tmp);
    }
    if ($okTest) {
        logi("Write-test OK. Cleaning up...");
        if ($useSftp) $sftp->delete($testFile); else @ftp_delete($ftp, $testFile);
    } else {
        logi("Write-test failed. Please adjust DEPLOY_FTP_ROOT.");
        exit(3);
    }
}

$toUpload = [];
foreach ($locals as $rel => $meta) {
    $remoteRel = mapRemoteRel($rel, $PATH_MAP);
    if ($useSftp) {
        $stat = $sftp->stat(rtrim($root,'/').'/'.$remoteRel);
        $rsize = $stat && isset($stat['size']) ? (int)$stat['size'] : null;
        $rmtime = $stat && isset($stat['mtime']) ? (int)$stat['mtime'] : null;
    } else {
        [$rsize, $rmtime] = getRemoteSizeMtime($ftp, $root, $remoteRel);
    }
    $need = false;
    if ($rsize === null) $need = true; // new
    else if ($rsize !== $meta['size']) $need = true;
    else if ($rmtime === null || abs($rmtime - $meta['mtime']) > 2) $need = true;
    if ($need) $toUpload[$rel] = $meta;
}

logi("Files to upload: ".count($toUpload));
foreach ($toUpload as $rel => $meta) {
    logi(" - $rel (".$meta['size'].' bytes)');
}

if ($DRY) {
    logi("Dry-run complete. Run without --dry-run to upload.");
    exit(0);
}

$ok = 0; $fail = 0;
foreach ($toUpload as $rel => $meta) {
    $localPath = $baseDir.'/'.$rel;
    $remoteRel = mapRemoteRel($rel, $PATH_MAP);
    $remotePath = rtrim($root,'/').'/'.$remoteRel;
    $remoteDir = dirname($remoteRel);
    if ($remoteDir !== '.' && $remoteDir !== '/') {
        if ($useSftp) {
            if (!$sftp->mkdir(rtrim($root,'/').'/'.$remoteDir, -1, true)) {
                // mkdir may fail if already exists; try to stat a subpath to confirm
                $test = $sftp->stat(rtrim($root,'/').'/'.$remoteDir);
                if ($test === false) {
                    logi("[FAIL] mkdir chain for $remoteDir");
                    $fail++; continue;
                }
            }
        } else {
            if (!ensureRemoteDir($ftp, $root, $remoteDir)) {
                logi("[FAIL] mkdir chain for $remoteDir");
                $fail++; continue;
            }
        }
    }
    logi("Uploading $rel...");
    if ($useSftp) {
        if (!$sftp->put($remotePath, $localPath, \phpseclib3\Net\SFTP::SOURCE_LOCAL_FILE)) {
            logi("[FAIL] $rel");
            if (method_exists($sftp, 'getSFTPErrors')) {
                $errs = $sftp->getSFTPErrors();
                if ($errs) foreach ($errs as $e) logi("  SFTP: $e");
            } elseif (method_exists($sftp, 'getLastSFTPError')) {
                $e = $sftp->getLastSFTPError(); if ($e) logi("  SFTP: $e");
            }
            $fail++;
        } else {
            $ok++;
        }
    } else {
        if (!ftp_put($ftp, $remotePath, $localPath, FTP_BINARY)) {
            logi("[FAIL] $rel");
            $fail++;
        } else {
            $ok++;
        }
    }
}

logi("Done. OK=$ok FAIL=$fail");

if (!empty($config['post_deploy_urls'])) {
    logi("Post-deploy pings:");
    foreach ($config['post_deploy_urls'] as $url) {
        logi(" - $url");
    }
}
if (!$useSftp && isset($ftp) && $ftp) ftp_close($ftp);
