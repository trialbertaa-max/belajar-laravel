<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = new App\Http\Controllers\PatientScanController();
// Call show(1) and inspect the view data
$response = $controller->show('1');
$viewData = $response->getData();
echo "patientSeries count: " . count($viewData['patientSeries']) . "\n";
foreach ($viewData['patientSeries'] as $i => $s) {
    echo "Series $i:\n";
    echo "  name: " . $s['name'] . "\n";
    echo "  image_path: " . $s['image_path'] . "\n";
}
echo "scan->preview_image_path: " . $viewData['scan']->preview_image_path . "\n";
echo "scan->denoised_image_path: " . $viewData['scan']->denoised_image_path . "\n";
