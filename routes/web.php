<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientScanController;

// Halaman utama otomatis diarahkan ke Dashboard Pasien & Viewer
Route::get('/', function () {
    return redirect()->route('scans.index');
});


// URL /scans akan memanggil fungsi 'index' di PatientScanController
Route::get('/scans', [PatientScanController::class, 'index'])->name('scans.index');

// URL /scans/{id} akan memanggil fungsi 'show' di PatientScanController (Viewer)
Route::get('/scans/{id}', [PatientScanController::class, 'show'])->name('scans.show');

// Endpoint API untuk mengambil stream/metadata DICOM untuk Cornerstone.js
Route::get('/api/scans/{id}/dicom', [PatientScanController::class, 'getDicom'])->name('scans.dicom');

// Endpoint untuk memproses filter denoising via Python
Route::post('/scans/{id}/denoise', [PatientScanController::class, 'runDenoise'])->name('scans.denoise');
