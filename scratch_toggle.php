<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\SystemHealthController;

$controller = new SystemHealthController();
$req = Request::create('/system/dicom/toggle', 'POST', ['action' => 'start']);
$t0 = microtime(true);
$res = $controller->toggleDicom($req);
$t1 = microtime(true);

echo "Execution time: " . round(($t1 - $t0), 2) . "s\n";
echo "Response: " . $res->getContent() . "\n";

