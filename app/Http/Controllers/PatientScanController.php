<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalScan;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class PatientScanController extends Controller
{
    /**
     * Dashboard Utama MCU & Radiologi Terpadu Hyu
     * Menggantikan fungsi Labsync & teraPACS secara mandiri
     */
    public function index(Request $request)
    {
        // Inisialisasi data contoh MCU jika database masih kosong
        if (MedicalScan::count() === 0) {
            MedicalScan::create([
                'patient_id'        => 'CDC-00001',
                'accession_number'  => 'ACC-20260921-001',
                'patient_name'      => 'Budi Santoso',
                'gender'            => 'L',
                'age'               => 45,
                'modality'          => 'Thorax PA',
                'study_description' => 'Evaluasi Pulmo & Cor MCU Tahunan',
                'scan_image_path'   => 'scan-assets/raw_toraks.jpg',
                'preview_image_path'=> 'scan-assets/raw_toraks.jpg',
                'dicom_raw_path'    => 'sample_toraks.dcm',
                'mcu_status'        => 'rontgen_selesai',
                'station_name'      => 'FUJIFILM_FDR_01',
                'diagnosis_notes'   => 'Cor dan pulmo dalam batas normal, sinus costophrenicus tajam, tidak tampak infiltrat aktif.'
            ]);

            MedicalScan::create([
                'patient_id'        => 'CDC-00002',
                'accession_number'  => 'ACC-20260921-002',
                'patient_name'      => 'Siti Rahma',
                'gender'            => 'P',
                'age'               => 38,
                'modality'          => 'USG Abdomen',
                'study_description' => 'Pemeriksaan USG Upper Abdomen',
                'scan_image_path'   => 'scan-assets/usg_sample.jpg',
                'preview_image_path'=> 'scan-assets/usg_sample.jpg',
                'dicom_raw_path'    => 'sample_usg.dcm',
                'mcu_status'        => 'rontgen_selesai',
                'station_name'      => 'USG_MINDRAY_02',
                'diagnosis_notes'   => 'Hepar dan vesica fellea dalam batas normal, tidak tampak batu empedu atau massa abnormal.'
            ]);

            MedicalScan::create([
                'patient_id'        => 'CDC-00003',
                'accession_number'  => 'ACC-20260921-003',
                'patient_name'      => 'Ahmad Fauzi (MCU Gresik)',
                'gender'            => 'L',
                'age'               => 29,
                'modality'          => 'Thorax PA',
                'study_description' => 'Screening Kesehatan Paru Karyawan',
                'mcu_status'        => 'siap_rontgen',
                'station_name'      => 'FUJIFILM_FDR_01',
                'order_notes'       => 'Order otomatis dari Sesi MCU Hyu, menunggu antrian ekspos rontgen.'
            ]);
        }

        // Query & Filter Data
        $query = MedicalScan::latest();

        if ($request->filled('status')) {
            $query->where('mcu_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('accession_number', 'like', "%{$search}%")
                  ->orWhere('modality', 'like', "%{$search}%");
            });
        }

        $scans = $query->get();

        // Statistik Dashboard Pos MCU
        $stats = [
            'total'             => MedicalScan::count(),
            'siap_rontgen'      => MedicalScan::where('mcu_status', 'siap_rontgen')->count(),
            'rontgen_selesai'   => MedicalScan::where('mcu_status', 'rontgen_selesai')->count(),
            'selesai_diagnosa'  => MedicalScan::where('mcu_status', 'selesai_diagnosa')->count(),
        ];

        // Konfigurasi Parameter DICOM Node Hyu (Port & AE Fleksibel dari .env)
        $pacsConfig = [
            'ae_title'    => env('DICOM_AE_TITLE', 'HYU_PACS'),
            'port'        => env('DICOM_PORT', '4242'),
            'ip'          => env('DICOM_HOST', '0.0.0.0') . ' (Local / LAN)',
            'modality_ae' => env('DICOM_MODALITY_SOURCE', 'FUJIFILM (CR) & MINDRAY (US)'),
        ];

        return view('scans.index', compact('scans', 'stats', 'pacsConfig'));
    }


    /**
     * Membuat Sesi Order MCU / Pemeriksaan Rontgen Baru di Hyu
     * (Menggantikan alur manual Labsync)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name'      => 'required|string|max:255',
            'patient_id'        => 'nullable|string|max:100',
            'gender'            => 'nullable|string|in:L,P',
            'age'               => 'nullable|integer|min:1|max:120',
            'modality'          => 'required|string|max:100',
            'study_description' => 'nullable|string|max:255',
            'order_notes'       => 'nullable|string',
        ]);

        $count = MedicalScan::count() + 1;
        $patientId = $validated['patient_id'] ?: 'CDC-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        $accessionNo = 'ACC-' . date('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        MedicalScan::create([
            'patient_id'        => $patientId,
            'accession_number'  => $accessionNo,
            'patient_name'      => $validated['patient_name'],
            'gender'            => $validated['gender'] ?? 'L',
            'age'               => $validated['age'] ?? 30,
            'modality'          => $validated['modality'],
            'study_description' => $validated['study_description'] ?: $validated['modality'],
            'mcu_status'        => 'siap_rontgen', // Otomatis siap di-query Modality Worklist alat Fujifilm
            'order_notes'       => $validated['order_notes'] ?? 'Order Sesi MCU Baru di Hyu',
        ]);

        return redirect()->route('scans.index')->with('success', "Order MCU untuk {$validated['patient_name']} ({$accessionNo}) berhasil dibuat dan siap di alat rontgen!");
    }


    /**
     * Menampilkan PACS Multimodal DICOM Viewer untuk 1 Pasien
     * Hanya menampilkan citra & alat klinis jika pemeriksaan SUDAH dilakukan.
     * Pasien dengan status 'siap_rontgen' hanya akan melihat layar disclaimer.
     */
    public function show(string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        // Pastikan nama pasien tidak kosong
        if (empty($scan->patient_name) || $scan->patient_name === 'UNKNOWN') {
            $scan->patient_name = 'Pasien MCU CDC #' . str_pad($scan->id, 4, '0', STR_PAD_LEFT);
        }

        // ─── LOGIKA UTAMA: Periksa apakah pemeriksaan SUDAH dilakukan ─────────────
        // Pasien dianggap sudah diperiksa JIKA:
        //   1. Status bukan 'siap_rontgen', DAN
        //   2. Terdapat path gambar yang nyata (bukan kosong)
        $hasExamined = ($scan->mcu_status !== 'siap_rontgen')
            && (!empty($scan->preview_image_path) || !empty($scan->scan_image_path));

        // ─── SERIES DATA ──────────────────────────────────────────────────────────
        // Jika belum diperiksa: kirim array kosong — JANGAN tampilkan gambar dummy.
        // Blade akan mendeteksi ini dan menampilkan layar disclaimer.
        if (!$hasExamined) {
            $patientSeries    = [];
            $defaultVp2Series = null;
        } else {
            // Susun seri citra nyata berdasarkan modalitas
            $isUsg = str_contains(strtoupper($scan->modality), 'USG');

            if ($isUsg) {
                $patientSeries = [
                    [
                        'id'          => 'series-1',
                        'name'        => $scan->modality ?: 'USG Abdomen',
                        'modality'    => 'US / Ultrasonografi',
                        'badge'       => 'US',
                        'badge_color' => '#38bdf8',
                        'study'       => $scan->study_description ?: 'Pemeriksaan USG Hepar & Abdomen Upper',
                        'station'     => $scan->station_name ?: 'USG_MINDRAY_02',
                        'matrix'      => 'B-Mode 3.5MHz (1920x1080)',
                        'image_path'  => $scan->preview_image_path ?: 'scan-assets/usg_sample.jpg',
                        'has_image'   => true,
                        'is_primary'  => true,
                    ],
                    [
                        'id'          => 'series-2',
                        'name'        => 'Thorax PA (CR)',
                        'modality'    => 'CR / Rontgen Dada',
                        'badge'       => 'CR',
                        'badge_color' => '#38bdf8',
                        'study'       => 'Pemeriksaan Paru & Jantung PA',
                        'station'     => 'FUJIFILM_FDR',
                        'matrix'      => '2048 x 2048',
                        'image_path'  => 'scan-assets/raw_toraks.jpg',
                        'has_image'   => true,
                        'is_primary'  => false,
                    ],
                ];
            } else {
                $patientSeries = [
                    [
                        'id'          => 'series-1',
                        'name'        => $scan->modality ?: 'Thorax PA (CR)',
                        'modality'    => 'CR / Rontgen Dada',
                        'badge'       => 'CR',
                        'badge_color' => '#38bdf8',
                        'study'       => $scan->study_description ?: 'Pemeriksaan Paru & Jantung PA',
                        'station'     => $scan->station_name ?: 'FUJIFILM_FDR_01',
                        'matrix'      => '2048 x 2048',
                        'image_path'  => $scan->preview_image_path ?: 'scan-assets/raw_toraks.jpg',
                        'has_image'   => true,
                        'is_primary'  => true,
                    ],
                    [
                        'id'          => 'series-2',
                        'name'        => 'USG Abdomen',
                        'modality'    => 'US / Ultrasonografi',
                        'badge'       => 'US',
                        'badge_color' => '#38bdf8',
                        'study'       => 'Pemeriksaan USG Hepar & Abdomen Upper',
                        'station'     => 'USG_MINDRAY_02',
                        'matrix'      => 'B-Mode 3.5MHz (1920x1080)',
                        'image_path'  => 'scan-assets/usg_sample.jpg',
                        'has_image'   => true,
                        'is_primary'  => false,
                    ],
                ];
            }

            $defaultVp2Series = $patientSeries[1] ?? $patientSeries[0];
        }

        // Konfigurasi Parameter DICOM Node Hyu
        $pacsConfig = [
            'ae_title'    => env('DICOM_AE_TITLE', 'HYU_PACS'),
            'port'        => env('DICOM_PORT', '4242'),
            'ip'          => env('DICOM_HOST', '0.0.0.0') . ' (Local / LAN)',
            'modality_ae' => env('DICOM_MODALITY_SOURCE', 'FUJIFILM (CR) & MINDRAY (US)'),
        ];

        // Daftar pasien historis lainnya untuk drawer navigasi cepat
        $otherScans = MedicalScan::where('id', '!=', $id)->orderBy('created_at', 'desc')->take(10)->get();

        return view('scans.show', compact(
            'scan', 'patientSeries', 'defaultVp2Series', 'otherScans', 'pacsConfig', 'hasExamined'
        ));
    }


    /**
     * Menjalankan / Mensimulasikan Pemeriksaan Rontgen pada Pasien yang Belum Diperiksa
     * Digunakan untuk men-trigger perubahan status dari 'siap_rontgen' → 'rontgen_selesai'
     * dan mengisi path gambar hasil pemeriksaan nyata.
     */
    public function performExam(Request $request, string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        // Hanya pasien dengan status siap_rontgen yang bisa diperiksa via endpoint ini
        if ($scan->mcu_status !== 'siap_rontgen') {
            return redirect()->route('scans.show', $id)
                ->with('info', 'Pasien ini sudah pernah menjalani pemeriksaan.');
        }

        $isUsg = str_contains(strtoupper($scan->modality), 'USG');

        // Gunakan citra sampel sesuai modalitas (representasi citra DICOM dari alat)
        if ($isUsg) {
            $previewPath = 'scan-assets/usg_sample.jpg';
            $scanPath    = null;
            $dicomRaw    = 'sample_usg.dcm';
        } else {
            $previewPath = 'scan-assets/raw_toraks.jpg';
            $scanPath    = 'scan-assets/raw_toraks.jpg';
            $dicomRaw    = 'sample_toraks.dcm';
        }

        $scan->update([
            'mcu_status'         => 'rontgen_selesai',
            'preview_image_path' => $previewPath,
            'scan_image_path'    => $scanPath,
            'dicom_raw_path'     => $dicomRaw,
            'station_name'       => $scan->station_name ?: ($isUsg ? 'USG_MINDRAY_02' : 'FUJIFILM_FDR_01'),
        ]);

        return redirect()->route('scans.show', $id)
            ->with('success', 'Pemeriksaan selesai! Citra DICOM berhasil masuk ke Hyu PACS. Viewer siap digunakan.');
    }


    /**
     * Menyimpan Catatan Diagnosa / Ekspertise Radiolog ke Database Hyu
     */
    public function saveDiagnosis(Request $request, string $id)
    {
        $request->validate([
            'diagnosis_notes' => 'required|string',
            'doctor_name'     => 'nullable|string|max:255',
        ]);

        $scan = MedicalScan::findOrFail($id);
        $scan->update([
            'diagnosis_notes' => $request->diagnosis_notes,
            'doctor_name'     => $request->doctor_name ?: 'dr. Radiolog Sp.Rad',
            'mcu_status'      => 'selesai_diagnosa',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Catatan diagnosa & ekspertise radiolog berhasil disimpan ke Rekam Medis Hyu!',
            'scan'    => $scan
        ]);
    }


    /**
     * Eksekusi Filter Image Processing & Denoising Berbasis Python
     * Mendukung multi-engine: Bilateral, Non-Local Means, Deep CNN DnCNN, Gaussian
     */
    public function runDenoise(Request $request, string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        // Tolak permintaan denoising jika pasien belum menjalani pemeriksaan
        $hasExamined = ($scan->mcu_status !== 'siap_rontgen')
            && (!empty($scan->preview_image_path) || !empty($scan->scan_image_path));
        if (!$hasExamined) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pasien belum menjalani pemeriksaan. Hasil citra tidak tersedia untuk diproses.'
            ], 400);
        }

        $engine      = $request->input('engine', 'bilateral');
        $sigmaColor  = (float) $request->input('sigma_color', 25);
        $sigmaSpace  = (float) $request->input('sigma_space', 20);

        $viewport    = (int) $request->input('viewport', 1);
        $targetImage = $request->input('target_image');

        // Bersihkan jika formatnya full URL
        if ($targetImage) {
            $parsedUrl = parse_url($targetImage, PHP_URL_PATH);
            $cleanRelPath = ltrim($parsedUrl, '/');
        } else {
            $cleanRelPath = null;
        }

        $pythonBinary = env('PYTHON_BINARY', 'C:\\Python310\\python.exe');
        $scriptPath   = base_path('test_dicom.py');

        // Tentukan path DICOM file atau image file pasien
        $dicomPath = base_path('sample_toraks.dcm');
        if ($cleanRelPath && file_exists(public_path($cleanRelPath))) {
            $dicomPath = public_path($cleanRelPath);
        } elseif ($viewport === 2) {
            if (file_exists(public_path('scan-assets/usg_sample.jpg'))) {
                $dicomPath = public_path('scan-assets/usg_sample.jpg');
            }
        } elseif ($scan->dicom_raw_path) {
            $possiblePath1 = storage_path('app/public/' . $scan->dicom_raw_path);
            $possiblePath2 = base_path($scan->dicom_raw_path);
            if (file_exists($possiblePath1)) {
                $dicomPath = $possiblePath1;
            } elseif (file_exists($possiblePath2)) {
                $dicomPath = $possiblePath2;
            }
        }

        // Folder output denoised JPG & Residual Map
        $outputFolder = public_path('scan-assets');
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }
        $outputPath   = $outputFolder . DIRECTORY_SEPARATOR . "denoised_scan_{$id}_vp{$viewport}.jpg";
        $residualPath = $outputFolder . DIRECTORY_SEPARATOR . "residual_scan_{$id}_vp{$viewport}.jpg";

        // Siapkan Environment Variables Windows
        $env = [
            'SystemRoot'     => getenv('SystemRoot') ?: 'C:\\Windows',
            'SYSTEMROOT'     => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'WINDIR'         => getenv('WINDIR') ?: 'C:\\Windows',
            'PATH'           => getenv('PATH') ?: 'C:\\Python310;C:\\Windows\\system32;C:\\Windows',
            'TEMP'           => getenv('TEMP') ?: 'C:\\Windows\\Temp',
            'TMP'            => getenv('TMP') ?: 'C:\\Windows\\Temp',
            'PYTHONHASHSEED' => '0',
        ];

        $command = "\"{$pythonBinary}\" \"{$scriptPath}\" \"{$dicomPath}\" \"{$outputPath}\" \"{$engine}\" \"{$sigmaColor}\" \"{$sigmaSpace}\" \"{$residualPath}\"";
        $result = Process::env($env)->timeout(60)->run($command);

        $stdout = trim($result->output());
        $jsonRes = json_decode($stdout, true);

        if ($result->successful() && file_exists($outputPath) && is_array($jsonRes) && ($jsonRes['status'] ?? '') === 'success') {
            $relDenoisedPath = "scan-assets/denoised_scan_{$id}_vp{$viewport}.jpg";
            $relResidualPath = "scan-assets/residual_scan_{$id}_vp{$viewport}.jpg";

            if ($viewport === 1) {
                $scan->update(['denoised_image_path' => $relDenoisedPath]);
            }

            return response()->json([
                'status'       => 'success',
                'viewport'     => $viewport,
                'image_url'    => asset($relDenoisedPath) . '?t=' . time(),
                'residual_url' => asset($relResidualPath) . '?t=' . time(),
                'engine'       => $jsonRes['engine'] ?? 'Bilateral Filter',
                'parameters'   => $jsonRes['parameters'] ?? [],
                'metrics'      => $jsonRes['metrics'] ?? [],
                'message'      => 'Filter restorasi citra berhasil diaplikasikan.'
            ]);
        } else {
            $errorMsg = $result->errorOutput() ?: $stdout;
            return response()->json([
                'status'  => 'error',
                'message' => $errorMsg ?: 'Gagal memproses restorasi citra via Python.'
            ], 500);
        }
    }


    /**
     * Menjalankan Benchmark Multi-Algoritma Komparatif untuk Evaluasi Kualitas Citra Medis
     */
    public function runBenchmark(Request $request, string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        // Tolak permintaan benchmark jika pasien belum menjalani pemeriksaan
        $hasExamined = ($scan->mcu_status !== 'siap_rontgen')
            && (!empty($scan->preview_image_path) || !empty($scan->scan_image_path));
        if (!$hasExamined) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pasien belum menjalani pemeriksaan. Tidak ada citra untuk di-benchmark.'
            ], 400);
        }

        $sigmaColor  = (float) $request->input('sigma_color', 25);
        $sigmaSpace  = (float) $request->input('sigma_space', 20);
        $viewport    = (int) $request->input('viewport', 1);
        $targetImage = $request->input('target_image');

        if ($targetImage) {
            $parsedUrl = parse_url($targetImage, PHP_URL_PATH);
            $cleanRelPath = ltrim($parsedUrl, '/');
        } else {
            $cleanRelPath = null;
        }

        $pythonBinary = env('PYTHON_BINARY', 'C:\\Python310\\python.exe');
        $scriptPath   = base_path('test_dicom.py');

        $dicomPath = base_path('sample_toraks.dcm');
        if ($cleanRelPath && file_exists(public_path($cleanRelPath))) {
            $dicomPath = public_path($cleanRelPath);
        } elseif ($viewport === 2 && file_exists(public_path('scan-assets/usg_sample.jpg'))) {
            $dicomPath = public_path('scan-assets/usg_sample.jpg');
        } elseif ($scan->dicom_raw_path && file_exists(base_path($scan->dicom_raw_path))) {
            $dicomPath = base_path($scan->dicom_raw_path);
        }

        $outputFolder = public_path('scan-assets');
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }
        $outputPath   = $outputFolder . DIRECTORY_SEPARATOR . "benchmark_scan_{$id}_vp{$viewport}.jpg";

        $env = [
            'SystemRoot'     => getenv('SystemRoot') ?: 'C:\\Windows',
            'SYSTEMROOT'     => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'WINDIR'         => getenv('WINDIR') ?: 'C:\\Windows',
            'PATH'           => getenv('PATH') ?: 'C:\\Python310;C:\\Windows\\system32;C:\\Windows',
            'TEMP'           => getenv('TEMP') ?: 'C:\\Windows\\Temp',
            'TMP'            => getenv('TMP') ?: 'C:\\Windows\\Temp',
            'PYTHONHASHSEED' => '0',
        ];

        $command = "\"{$pythonBinary}\" \"{$scriptPath}\" \"{$dicomPath}\" \"{$outputPath}\" \"benchmark\" \"{$sigmaColor}\" \"{$sigmaSpace}\"";
        $result = Process::env($env)->timeout(60)->run($command);

        $stdout = trim($result->output());
        $jsonRes = json_decode($stdout, true);

        if ($result->successful() && is_array($jsonRes) && ($jsonRes['status'] ?? '') === 'success') {
            return response()->json([
                'status'         => 'success',
                'viewport'       => $viewport,
                'benchmark_rows' => $jsonRes['benchmark_rows'] ?? [],
                'recommended'    => $jsonRes['recommended_engine'] ?? 'dl_dncnn',
                'message'        => 'Benchmark komparasi 4 algoritma berhasil dieksekusi.'
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => $result->errorOutput() ?: $stdout ?: 'Gagal menjalankan benchmark.'
            ], 500);
        }
    }


    /**
     * Memicu Simulasi Transmisi DICOM C-STORE dari Mesin Fujifilm ke Hyu PACS
     */
    public function simulateFuji(Request $request)
    {
        $pythonBinary = env('PYTHON_BINARY', 'C:\\Python310\\python.exe');
        $scriptPath   = base_path('test_fujifilm_sender.py');
        $dcmFile      = base_path('sample_toraks.dcm');

        $env = [
            'SystemRoot'     => getenv('SystemRoot') ?: 'C:\\Windows',
            'SYSTEMROOT'     => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'PATH'           => getenv('PATH') ?: 'C:\\Python310;C:\\Windows\\system32;C:\\Windows',
            'PYTHONHASHSEED' => '0',
        ];

        $command = "\"{$pythonBinary}\" \"{$scriptPath}\" \"{$dcmFile}\"";
        $result = Process::env($env)->timeout(30)->run($command);

        if ($result->successful()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Simulasi transmisi C-STORE dari mesin Fujifilm berhasil diterima oleh Hyu PACS!',
                'output'  => $result->output()
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal simulasi C-STORE: ' . ($result->errorOutput() ?: $result->output())
            ], 500);
        }
    }


    /**
     * Endpoint API Modality Worklist (JSON)
     */
    public function getWorklistJson()
    {
        $orders = MedicalScan::where('mcu_status', 'siap_rontgen')
            ->select('id', 'patient_id', 'accession_number', 'patient_name', 'gender', 'age', 'modality', 'study_description')
            ->get();

        return response()->json([
            'status' => 'success',
            'source' => 'Hyu Modality Worklist (MCU Central)',
            'total'  => $orders->count(),
            'orders' => $orders
        ]);
    }


    /**
     * Hapus Data Pemeriksaan
     */
    public function destroy(string $id)
    {
        $scan = MedicalScan::findOrFail($id);
        $scan->delete();

        return redirect()->route('scans.index')->with('success', 'Data pemeriksaan pasien berhasil dihapus.');
    }


    /**
     * Endpoint API DICOM Metadata untuk Viewer
     */
    public function getDicom(string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        $hasExamined = ($scan->mcu_status !== 'siap_rontgen')
            && (!empty($scan->preview_image_path) || !empty($scan->scan_image_path));

        if (!$hasExamined) {
            return response()->json([
                'status'             => 'error',
                'message'            => 'Pemeriksaan belum dilakukan. Citra medis belum tersedia.',
                'dicom_instance_url' => null
            ], 404);
        }

        return response()->json([
            'status'             => 'success',
            'pacs_server'        => 'Hyu Standalone PACS (Port 4242)',
            'modality'           => $scan->modality,
            'patient_name'       => $scan->patient_name,
            'accession_number'   => $scan->accession_number,
            'dicom_instance_url' => $scan->preview_image_path ?: $scan->scan_image_path
        ]);
    }

    /**
     * DICOM Tag Inspector & Data Dictionary Browser (NEMA DICOM PS 3.6 Compliance)
     */
    public function dicomTags(string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        $tags = [
            [
                'group'       => '0010',
                'element'     => '0010',
                'vr'          => 'PN',
                'description' => "Patient's Name",
                'value'       => strtoupper($scan->patient_name)
            ],
            [
                'group'       => '0010',
                'element'     => '0020',
                'vr'          => 'LO',
                'description' => 'Patient ID (MRN)',
                'value'       => $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT)
            ],
            [
                'group'       => '0010',
                'element'     => '0040',
                'vr'          => 'CS',
                'description' => "Patient's Sex",
                'value'       => $scan->gender === 'P' ? 'F' : 'M'
            ],
            [
                'group'       => '0010',
                'element'     => '1010',
                'vr'          => 'AS',
                'description' => "Patient's Age",
                'value'       => ($scan->age ?? 30) . 'Y'
            ],
            [
                'group'       => '0008',
                'element'     => '0050',
                'vr'          => 'SH',
                'description' => 'Accession Number',
                'value'       => $scan->accession_number ?: 'ACC-' . date('Ymd') . '-' . str_pad($scan->id, 3, '0', STR_PAD_LEFT)
            ],
            [
                'group'       => '0008',
                'element'     => '0060',
                'vr'          => 'CS',
                'description' => 'Modality',
                'value'       => str_contains(strtoupper($scan->modality), 'USG') ? 'US' : 'DX'
            ],
            [
                'group'       => '0008',
                'element'     => '0080',
                'vr'          => 'LO',
                'description' => 'Institution Name',
                'value'       => 'CAHAYA DIAGNOSTIC CENTRE (CDC MCU)'
            ],
            [
                'group'       => '0008',
                'element'     => '1010',
                'vr'          => 'SH',
                'description' => 'Station Name',
                'value'       => $scan->station_name ?: 'FUJIFILM_FDR_01'
            ],
            [
                'group'       => '0008',
                'element'     => '1030',
                'vr'          => 'LO',
                'description' => 'Study Description',
                'value'       => $scan->study_description ?: 'Thorax PA View'
            ],
            [
                'group'       => '0018',
                'element'     => '0015',
                'vr'          => 'CS',
                'description' => 'Body Part Examined',
                'value'       => str_contains(strtoupper($scan->modality), 'USG') ? 'ABDOMEN' : 'CHEST'
            ],
            [
                'group'       => '0020',
                'element'     => '000D',
                'vr'          => 'UI',
                'description' => 'Study Instance UID',
                'value'       => $scan->study_instance_uid ?: '1.2.826.0.1.3680043.8.498.' . time() . '.' . $scan->id
            ],
            [
                'group'       => '0020',
                'element'     => '000E',
                'vr'          => 'UI',
                'description' => 'Series Instance UID',
                'value'       => '1.2.826.0.1.3680043.8.498.' . (time() + 1) . '.' . $scan->id
            ],
            [
                'group'       => '0028',
                'element'     => '0004',
                'vr'          => 'CS',
                'description' => 'Photometric Interpretation',
                'value'       => 'MONOCHROME2'
            ],
            [
                'group'       => '0028',
                'element'     => '0010',
                'vr'          => 'US',
                'description' => 'Rows (Height)',
                'value'       => '2048'
            ],
            [
                'group'       => '0028',
                'element'     => '0011',
                'vr'          => 'US',
                'description' => 'Columns (Width)',
                'value'       => '2048'
            ],
            [
                'group'       => '0028',
                'element'     => '0030',
                'vr'          => 'DS',
                'description' => 'Pixel Spacing (Spatial Calibration)',
                'value'       => '0.8000\\0.8000 (mm/px)'
            ],
            [
                'group'       => '0028',
                'element'     => '0100',
                'vr'          => 'US',
                'description' => 'Bits Allocated',
                'value'       => '16'
            ],
            [
                'group'       => '0028',
                'element'     => '1050',
                'vr'          => 'DS',
                'description' => 'Window Center (WL)',
                'value'       => '2047'
            ],
            [
                'group'       => '0028',
                'element'     => '1051',
                'vr'          => 'DS',
                'description' => 'Window Width (WW)',
                'value'       => '4095'
            ],
        ];

        return response()->json([
            'status'  => 'success',
            'scan_id' => $scan->id,
            'tags'    => $tags
        ]);
    }

    /**
     * Unduh File Mentah DICOM (.dcm)
     */
    public function downloadDicom(string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        $hasExamined = ($scan->mcu_status !== 'siap_rontgen')
            && (!empty($scan->dicom_raw_path) || !empty($scan->preview_image_path));

        if (!$hasExamined) {
            return back()->with('error', 'Pemeriksaan belum dilakukan. File DICOM belum tersedia.');
        }

        if ($scan->dicom_raw_path && file_exists(base_path($scan->dicom_raw_path))) {
            return response()->download(base_path($scan->dicom_raw_path), "DICOM_{$scan->patient_id}_{$scan->accession_number}.dcm");
        }

        return back()->with('error', 'File DICOM mentah tidak ditemukan di server.');
    }

    /**
     * Memperbarui Konfigurasi DICOM Node (AE Title, Port, Target Modality) langsung dari UI Web
     */
    public function updateDicomNode(Request $request)
    {
        $validated = $request->validate([
            'ae_title'        => 'required|string|max:16',
            'port'            => 'required|integer|min:1|max:65535',
            'modality_source' => 'nullable|string|max:100',
        ]);

        $aeTitle = strtoupper(preg_replace('/[^A-Za-z0-9_\-]/', '', $validated['ae_title']));
        $port = (int) $validated['port'];
        $modality = $validated['modality_source'] ?: 'FUJIFILM (CR) & MINDRAY (US)';

        // Update file .env
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);

            $replacements = [
                '/^DICOM_AE_TITLE=.*$/m'        => "DICOM_AE_TITLE={$aeTitle}",
                '/^DICOM_PORT=.*$/m'            => "DICOM_PORT={$port}",
                '/^DICOM_MODALITY_SOURCE=.*$/m' => "DICOM_MODALITY_SOURCE=\"{$modality}\"",
            ];

            foreach ($replacements as $pattern => $replacement) {
                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            file_put_contents($envPath, $envContent);
        }

        return response()->json([
            'status'          => 'success',
            'message'         => 'Konfigurasi DICOM Node berhasil diperbarui!',
            'ae_title'        => $aeTitle,
            'port'            => $port,
            'modality_source' => $modality,
        ]);
    }
}