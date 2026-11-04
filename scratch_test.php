<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Process;

echo "--- Testing Process::run with Python ---\n";
try {
    $res = Process::timeout(5)->run(['C:\\Python310\\python.exe', '-V']);
    echo "Exit code: " . $res->exitCode() . "\n";
    echo "Output: " . $res->output() . "\n";
    echo "Error: " . $res->errorOutput() . "\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "--- Testing Probe Script ---\n";
$probeScript = "import sys, json\n"
    . "data = {'version': sys.version.split()[0]}\n"
    . "try:\n import cv2; data['opencv'] = cv2.__version__\nexcept: data['opencv'] = None\n"
    . "try:\n import torch; data['torch'] = torch.__version__; data['cuda'] = torch.cuda.is_available()\nexcept: data['torch'] = None; data['cuda'] = False\n"
    . "try:\n import pydicom; data['pydicom'] = True\nexcept: data['pydicom'] = False\n"
    . "try:\n import pynetdicom; data['pynetdicom'] = True\nexcept: data['pynetdicom'] = False\n"
    . "print(json.dumps(data))";

try {
    $res = Process::timeout(10)->run(['C:\\Python310\\python.exe', '-c', $probeScript]);
    echo "Exit code: " . $res->exitCode() . "\n";
    echo "Output: " . $res->output() . "\n";
    echo "Error: " . $res->errorOutput() . "\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "--- Testing Start DICOM Process via PowerShell ---\n";
$dicomPort = 4242;
$pyCmd = 'C:\\Python310\\python.exe';
$script = base_path('hyu_dicom_server.py');
$baseDir = base_path();
$startScript = "Start-Process -FilePath '{$pyCmd}' -ArgumentList '\"{$script}\"' -WorkingDirectory '{$baseDir}' -WindowStyle Hidden";
echo "StartScript command: {$startScript}\n";
try {
    $res = Process::timeout(5)->run(['powershell', '-Command', $startScript]);
    echo "PowerShell exit: " . $res->exitCode() . "\n";
    echo "PowerShell out: " . $res->output() . "\n";
    echo "PowerShell err: " . $res->errorOutput() . "\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

