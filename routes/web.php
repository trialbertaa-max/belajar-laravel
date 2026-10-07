<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientScanController;

// Halaman utama otomatis diarahkan ke Dashboard Pasien & Viewer
Route::get('/', function () {
    return redirect()->route('scans.index');
});


// URL /scans akan memanggil fungsi 'index' di PatientScanController
Route::get('/scans', [PatientScanController::class, 'index'])->name('scans.index');
Route::post('/scans', [PatientScanController::class, 'store'])->name('scans.store');
Route::delete('/scans/{id}', [PatientScanController::class, 'destroy'])->name('scans.destroy');

// URL /scans/{id} akan memanggil fungsi 'show' di PatientScanController (Viewer)
Route::get('/scans/{id}', [PatientScanController::class, 'show'])->name('scans.show');

// Endpoint: lakukan / simulasi pemeriksaan rontgen (ubah status siap_rontgen → rontgen_selesai)
Route::post('/scans/{id}/perform-exam', [PatientScanController::class, 'performExam'])->name('scans.perform-exam');

// Endpoint simpan catatan diagnosa / ekspertise radiolog
Route::post('/scans/{id}/diagnosis', [PatientScanController::class, 'saveDiagnosis'])->name('scans.diagnosis');

// Endpoint untuk memproses filter denoising via Python
Route::post('/scans/{id}/denoise', [PatientScanController::class, 'runDenoise'])->name('scans.denoise');
Route::post('/scans/{id}/benchmark', [PatientScanController::class, 'runBenchmark'])->name('scans.benchmark');

// Endpoint untuk memicu simulasi transmisi DICOM C-STORE dari mesin Fujifilm
Route::post('/scans/simulate-fuji', [PatientScanController::class, 'simulateFuji'])->name('scans.simulate-fuji');

// Endpoint API Modality Worklist JSON (antrian rontgen)
Route::get('/api/worklist', [PatientScanController::class, 'getWorklistJson'])->name('scans.worklist');
Route::get('/api/scans/{id}/dicom', [PatientScanController::class, 'getDicom'])->name('scans.dicom');

// Endpoint Inspeksi DICOM Tags & Unduh File DICOM (.dcm)
Route::get('/scans/{id}/tags', [PatientScanController::class, 'dicomTags'])->name('scans.tags');
Route::get('/scans/{id}/download-dcm', [PatientScanController::class, 'downloadDicom'])->name('scans.download-dcm');

// Endpoint Konfigurasi DICOM Node (AE Title, Port, Target Modality)
Route::post('/settings/dicom-node', [PatientScanController::class, 'updateDicomNode'])->name('settings.dicom-node');
