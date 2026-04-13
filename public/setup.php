<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

/**
 * DEPLOYMENT HELPER — DELETE THIS FILE AFTER SETUP!
 *
 * Access this file via browser: https://your-domain.com/setup.php
 * It will run essential artisan commands for shared hosting without SSH.
 *
 * IMPORTANT: Delete this file immediately after running all commands!
 */
$envPath = dirname(__DIR__).'/.env';
$secret = '';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        if (! str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        if (trim($key) === 'DEPLOY_SETUP_KEY') {
            $secret = trim($value, " \t\n\r\0\x0B\"'");
            break;
        }
    }
}

if ($secret === '' || ! isset($_GET['key']) || ! hash_equals($secret, (string) $_GET['key'])) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

$commands = [
    'key:generate --force' => 'Generate APP_KEY',
    'migrate --force' => 'Run database migrations',
    'optimize:clear' => 'Clear all caches',
    'config:cache' => 'Cache config for performance',
    'view:cache' => 'Cache views',
];

$runCommand = isset($_GET['cmd']) ? $_GET['cmd'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;
$results = [];

if ($action === 'delete_setup') {
    $deleted = @unlink(__FILE__);
    $results[] = [
        'command' => 'delete setup.php',
        'output' => $deleted ? 'setup.php berhasil dihapus.' : 'Gagal menghapus setup.php. Hapus manual dari file manager.',
        'success' => $deleted,
    ];
}

if ($runCommand && $action === null) {
    $allowed = array_keys($commands);
    if (! in_array($runCommand, $allowed)) {
        $results[] = ['command' => $runCommand, 'output' => 'ERROR: Command not allowed', 'success' => false];
    } else {
        $returnCode = 1;
        $output = '';

        try {
            require_once __DIR__.'/../vendor/autoload.php';

            /** @var Application $app */
            $app = require __DIR__.'/../bootstrap/app.php';

            /** @var Kernel $kernel */
            $kernel = $app->make(Kernel::class);

            $returnCode = $kernel->call($runCommand);
            $output = $kernel->output();
        } catch (Throwable $e) {
            $output = $e->getMessage();
            $returnCode = 1;
        }

        $results[] = [
            'command' => $runCommand,
            'output' => $output,
            'success' => $returnCode === 0,
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — DELETE AFTER USE</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: monospace; background: #1a1a2e; color: #eee; padding: 20px; }
        h1 { color: #e94560; margin-bottom: 20px; }
        .warning { background: #e94560; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .card { background: #16213e; border-radius: 8px; padding: 15px; margin-bottom: 10px; }
        .card h3 { color: #0f3460; background: #eee; display: inline-block; padding: 2px 8px; border-radius: 4px; margin-bottom: 8px; }
        .btn { display: inline-block; background: #0f3460; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-family: monospace; }
        .btn:hover { background: #e94560; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        pre { background: #0a0a1a; padding: 10px; border-radius: 4px; overflow-x: auto; margin-top: 8px; color: #a8d8; }
        .success { color: #4ecca3; }
        .error { color: #e94560; }
        .php-info { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Laravel Deployment Setup</h1>
    <div class="warning">DELETE THIS FILE (setup.php) IMMEDIATELY AFTER RUNNING ALL COMMANDS!</div>
    
    <div class="card">
        <h3>Server Info</h3>
        <p>PHP Version: <strong><?php echo PHP_VERSION; ?></strong></p>
        <p>Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
        <p>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
        <p>Base Path: <?php echo dirname(__DIR__); ?></p>
    </div>

    <?php foreach ($results as $result) { ?>
    <div class="card">
        <h3>Result: <?php echo htmlspecialchars($result['command']); ?></h3>
        <p class="<?php echo $result['success'] ? 'success' : 'error'; ?>">
            Status: <?php echo $result['success'] ? '✓ SUCCESS' : '✗ FAILED'; ?>
        </p>
        <pre><?php echo htmlspecialchars($result['output']); ?></pre>
    </div>
    <?php } ?>

    <div class="card">
        <h3>Run Commands (click each button in order)</h3>
        <p style="margin-bottom: 10px;">Pastikan <strong>DEPLOY_SETUP_KEY</strong> sudah diisi di file <strong>.env</strong>.</p>
        <?php foreach ($commands as $cmd => $label) { ?>
        <div style="margin: 8px 0;">
            <a href="?key=<?php echo $secret; ?>&cmd=<?php echo urlencode($cmd); ?>" class="btn">
                ▶ <?php echo htmlspecialchars($label); ?> <small>(php artisan <?php echo htmlspecialchars($cmd); ?>)</small>
            </a>
        </div>
        <?php } ?>
    </div>

    <div class="card">
        <h3>Final Step</h3>
        <p style="margin-bottom: 10px;">Setelah semua command sukses dan website bisa dibuka, hapus file setup ini.</p>
        <a href="?key=<?php echo $secret; ?>&action=delete_setup" class="btn" style="background:#e94560;">Delete setup.php now</a>
    </div>

    <div class="card">
        <h3>Quick Diagnostics</h3>
        <p>Storage writable: <strong class="<?php echo is_writable(dirname(__DIR__).'/storage') ? 'success' : 'error'; ?>">
            <?php echo is_writable(dirname(__DIR__).'/storage') ? 'YES' : 'NO — FIX PERMISSIONS'; ?>
        </strong></p>
        <p>Bootstrap cache writable: <strong class="<?php echo is_writable(dirname(__DIR__).'/bootstrap/cache') ? 'success' : 'error'; ?>">
            <?php echo is_writable(dirname(__DIR__).'/bootstrap/cache') ? 'YES' : 'NO — FIX PERMISSIONS'; ?>
        </strong></p>
        <p>.env file exists: <strong class="<?php echo file_exists(dirname(__DIR__).'/.env') ? 'success' : 'error'; ?>">
            <?php echo file_exists(dirname(__DIR__).'/.env') ? 'YES' : 'NO — CREATE .env FILE'; ?>
        </strong></p>
        <?php
        $envPath = dirname(__DIR__).'/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    $hasKey = preg_match('/APP_KEY=base64:/', $envContent);
    $dbConn = '';
    if (preg_match('/DB_CONNECTION=(\S+)/', $envContent, $m)) {
        $dbConn = $m[1];
    }
    echo '<p>APP_KEY set: <strong class="'.($hasKey ? 'success' : 'error').'">'.($hasKey ? 'YES' : 'NO — RUN key:generate').'</strong></p>';
    echo '<p>DB_CONNECTION: <strong>'.htmlspecialchars($dbConn).'</strong></p>';
}
?>
    </div>
</body>
</html>
