<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Process;

$t0 = microtime(true);
$res1 = Process::timeout(5)->run([
    'powershell', '-Command',
    "Get-NetTCPConnection -LocalPort 4242 -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force -ErrorAction SilentlyContinue }"
]);
$t1 = microtime(true);
echo "Step 1 (Cleanup powershell): " . round(($t1 - $t0) * 1000) . " ms\n";

$pyCmd = 'C:\\Python310\\python.exe';
$script = base_path('hyu_dicom_server.py');
$baseDir = base_path();
$startScript = "Start-Process -FilePath '{$pyCmd}' -ArgumentList '{$script}' -WorkingDirectory '{$baseDir}' -WindowStyle Hidden";

$t2 = microtime(true);
$res2 = Process::timeout(5)->run(['powershell', '-Command', $startScript]);
$t3 = microtime(true);
echo "Step 2 (Start powershell): " . round(($t3 - $t2) * 1000) . " ms\n";

$t4 = microtime(true);
$portActive = false;
$attempts = 0;
for ($attempt = 0; $attempt < 10; $attempt++) {
    $attempts++;
    usleep(250000);
    $fp = @fsockopen('127.0.0.1', 4242, $errno, $errstr, 0.2);
    if (is_resource($fp)) {
        $portActive = true;
        fclose($fp);
        break;
    }
}
$t5 = microtime(true);
echo "Step 3 (Polling $attempts attempts): " . round(($t5 - $t4) * 1000) . " ms, Active: " . ($portActive ? 'YES' : 'NO') . "\n";

