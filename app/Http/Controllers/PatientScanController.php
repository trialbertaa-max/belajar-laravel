<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalScan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class PatientScanController extends Controller
{
    /**
     * Normalisasi & validasi ketat string tanggal pencarian (Subbab 3.14)
     * Hanya menerima dua format:
     * 1. YYYY-MM-DD (format tetap standar input type="date")
     * 2. MM/DD/YYYY (format bergaris miring tunggal sesuai tampilan UI date picker)
     * Pola 'd/m/Y' dan 'Y/m/d' telah dihapus untuk menghilangkan ambiguitas (misal: 05/09/2026).
     * Validasi ketat: tanggal diformat ulang dan dibandingkan dengan input asli; tolak jika berbeda
     * (mencegah tanggal meluap, seperti bulan 21).
     */
    private function parseSearchDate(?string $rawDate): ?string
    {
        if (empty($rawDate) || !is_string($rawDate)) {
            return null;
        }
        $trimmed = trim($rawDate);

        // 1. Format tetap standar input type="date": YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $trimmed)) {
            try {
                $d = Carbon::createFromFormat('Y-m-d', $trimmed);
                // Validasi ketat: format ulang harus sama persis dengan input asli
                if ($d && $d->format('Y-m-d') === $trimmed) {
                    return $trimmed;
                }
            } catch (\Throwable $e) {
                return null;
            }
            return null;
        }

        // 2. Format bergaris miring: HANYA m/d/Y (MM/DD/YYYY sesuai tampilan date picker)
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $trimmed, $m)) {
            $padded = sprintf('%02d/%02d/%04d', (int) $m[1], (int) $m[2], (int) $m[3]);
            try {
                $d = Carbon::createFromFormat('m/d/Y', $padded);
                // Validasi ketat: format ulang harus sama persis dengan $padded
                // Jika tanggal meluap (misal bulan 21: '21/09/2026' -> '09/09/2027'), otomatis ditolak!
                if ($d && $d->format('m/d/Y') === $padded) {
                    return $d->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                return null;
            }
            return null;
        }

        // Format selain YYYY-MM-DD dan MM/DD/YYYY ditolak
        return null;
    }

    /**
     * Dashboard Utama MCU & Radiologi Terpadu Hyu
     * Menggantikan fungsi Labsync & teraPACS secara mandiri
     */
    public function index(Request $request)
    {
        // Query & Filter Data
        $query = MedicalScan::latest();

        if ($request->filled('status')) {
            $query->where('mcu_status', $request->status);
        }

        $filterDate = null;
        if ($request->filled('date')) {
            $dateStr = $this->parseSearchDate($request->date);
            if ($dateStr) {
                $filterDate = $dateStr;
                $dateCompact = str_replace('-', '', $dateStr);
                $query->where(function ($q) use ($dateStr, $dateCompact) {
                    $q->whereDate('created_at', $dateStr)
                      ->orWhere('accession_number', 'like', "%{$dateCompact}%");
                });
            }
        }

        if ($request->filled('date_from')) {
            $fromStr = $this->parseSearchDate($request->date_from);
            if ($fromStr) {
                $query->whereDate('created_at', '>=', $fromStr);
            }
        }

        if ($request->filled('date_to')) {
            $toStr = $this->parseSearchDate($request->date_to);
            if ($toStr) {
                $query->whereDate('created_at', '<=', $toStr);
            }
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

        $scans = $query->paginate(10)->withQueryString();

        // Statistik Dashboard Pos MCU (menyesuaikan filter tanggal jika dipilih)
        $statsBase = MedicalScan::query();
        if ($request->filled('date')) {
            $dateStr = $this->parseSearchDate($request->date);
            if ($dateStr) {
                $dateCompact = str_replace('-', '', $dateStr);
                $statsBase->where(function ($q) use ($dateStr, $dateCompact) {
                    $q->whereDate('created_at', $dateStr)
                      ->orWhere('accession_number', 'like', "%{$dateCompact}%");
                });
            }
        }
        if ($request->filled('date_from')) {
            $fromStr = $this->parseSearchDate($request->date_from);
            if ($fromStr) {
                $statsBase->whereDate('created_at', '>=', $fromStr);
            }
        }
        if ($request->filled('date_to')) {
            $toStr = $this->parseSearchDate($request->date_to);
            if ($toStr) {
                $statsBase->whereDate('created_at', '<=', $toStr);
            }
        }

        $stats = [
            'total'             => (clone $statsBase)->count(),
            'siap_rontgen'      => (clone $statsBase)->where('mcu_status', 'siap_rontgen')->count(),
            'rontgen_selesai'   => (clone $statsBase)->where('mcu_status', 'rontgen_selesai')->count(),
            'selesai_diagnosa'  => (clone $statsBase)->where('mcu_status', 'selesai_diagnosa')->count(),
        ];

        // Konfigurasi Parameter DICOM Node Hyu (Port & AE Fleksibel dari .env)
        $pacsConfig = [
            'ae_title'    => env('DICOM_AE_TITLE', 'HYU_PACS'),
            'port'        => env('DICOM_PORT', '4242'),
            'ip'          => env('DICOM_HOST', '0.0.0.0') . ' (Local / LAN)',
            'modality_ae' => env('DICOM_MODALITY_SOURCE', 'FUJIFILM (CR) & MINDRAY (US)'),
        ];

        return view('scans.index', compact('scans', 'stats', 'pacsConfig', 'filterDate'));
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
            'study_description' => !empty($validated['study_description']) ? $validated['study_description'] : $validated['modality'],
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
            $mUpper = strtoupper($scan->modality ?? '');
            $isUsg = str_contains($mUpper, 'USG') || str_contains($mUpper, 'ULTRASOUND') || $mUpper === 'US' || str_starts_with($mUpper, 'US ');
            $primarySeries = [
                'id'          => 'series-1',
                'name'        => $scan->modality ?: ($isUsg ? 'USG Abdomen' : 'Thorax PA (CR)'),
                'modality'    => $isUsg ? 'US / Ultrasonografi' : 'CR / Rontgen Dada',
                'badge'       => $isUsg ? 'US' : 'CR',
                'badge_color' => '#38bdf8',
                'study'       => $scan->study_description ?: ($isUsg ? 'Pemeriksaan USG Hepar & Abdomen Upper' : 'Pemeriksaan Paru & Jantung PA'),
                'station'     => $scan->station_name ?: ($isUsg ? 'USG_MINDRAY_02' : 'FUJIFILM_FDR_01'),
                'matrix'      => $isUsg ? 'B-Mode 3.5MHz (1920x1080)' : '2048 x 2048',
                'image_path'  => $scan->preview_image_path ?: ($isUsg ? 'scan-assets/usg_sample.jpg' : 'scan-assets/raw_toraks.jpg'),
                'has_image'   => true,
                'is_primary'  => true,
            ];

            // Pasien Demo #1 tetap disiapkan 2 seri untuk keperluan demo komparasi multimodal
            // Sedangkan pasien riil (dari alat Fujifilm) akan otomatis menampilkan sejumlah citra riil yang dikirim
            if ($scan->id === 1) {
                $secondarySeries = [
                    'id'          => 'series-2',
                    'name'        => $isUsg ? 'Thorax PA (CR)' : 'USG Abdomen',
                    'modality'    => $isUsg ? 'CR / Rontgen Dada' : 'US / Ultrasonografi',
                    'badge'       => $isUsg ? 'CR' : 'US',
                    'badge_color' => '#38bdf8',
                    'study'       => $isUsg ? 'Pemeriksaan Paru & Jantung PA' : 'Pemeriksaan USG Hepar & Abdomen Upper',
                    'station'     => $isUsg ? 'FUJIFILM_FDR' : 'USG_MINDRAY_02',
                    'matrix'      => $isUsg ? '2048 x 2048' : 'B-Mode 3.5MHz (1920x1080)',
                    'image_path'  => $isUsg ? 'scan-assets/raw_toraks.jpg' : 'scan-assets/usg_sample.jpg',
                    'has_image'   => true,
                    'is_primary'  => false,
                ];
                $patientSeries = [$primarySeries, $secondarySeries];
            } else {
                // Periksa apakah pasien ini memiliki pemeriksaan seri lain yang sudah masuk
                $relatedScans = MedicalScan::where('patient_id', $scan->patient_id)
                    ->where('id', '!=', $scan->id)
                    ->where('mcu_status', '!=', 'siap_rontgen')
                    ->where(function($q) {
                        $q->whereNotNull('preview_image_path')->orWhereNotNull('scan_image_path');
                    })
                    ->get();

                $patientSeries = [$primarySeries];

                foreach ($relatedScans as $rIdx => $rScan) {
                    $rMUpper = strtoupper($rScan->modality ?? '');
                    $rIsUsg = str_contains($rMUpper, 'USG') || str_contains($rMUpper, 'ULTRASOUND') || $rMUpper === 'US' || str_starts_with($rMUpper, 'US ');
                    $patientSeries[] = [
                        'id'          => 'series-' . ($rIdx + 2),
                        'name'        => $rScan->modality ?: 'Seri Tambahan',
                        'modality'    => $rIsUsg ? 'US / Ultrasonografi' : 'CR / Rontgen Dada',
                        'badge'       => $rIsUsg ? 'US' : 'CR',
                        'badge_color' => '#38bdf8',
                        'study'       => $rScan->study_description ?: 'Pemeriksaan Lanjutan',
                        'station'     => $rScan->station_name ?: 'STATION_01',
                        'matrix'      => 'DICOM Series',
                        'image_path'  => $rScan->preview_image_path ?: $rScan->scan_image_path,
                        'has_image'   => true,
                        'is_primary'  => false,
                    ];
                }
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

        return view('scans.show', compact(
            'scan', 'patientSeries', 'defaultVp2Series', 'pacsConfig', 'hasExamined'
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

        $modalityUpper = strtoupper(($scan->modality ?? '') . ' ' . ($scan->study_description ?? ''));
        $isUsg = str_contains($modalityUpper, 'USG') || str_contains($modalityUpper, 'ULTRASOUND') || str_contains($modalityUpper, 'US ') || trim($scan->modality ?? '') === 'US';
        $isHead = str_contains($modalityUpper, 'WATERS') 
            || str_contains($modalityUpper, 'SINUS') 
            || str_contains($modalityUpper, 'KEPALA') 
            || str_contains($modalityUpper, 'SKULL') 
            || str_contains($modalityUpper, 'CRANIUM');

        // Gunakan citra sampel sesuai modalitas (representasi citra DICOM dari alat)
        if ($isUsg) {
            $previewPath = 'scan-assets/usg_sample.jpg';
            $scanPath    = null;
            $dicomRaw    = 'sample_usg.dcm';
        } elseif ($isHead) {
            $previewPath = 'scan-assets/raw_kepala.jpg';
            $scanPath    = 'scan-assets/raw_kepala.jpg';
            $dicomRaw    = 'sample_kepala.dcm';
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
        $validated = $request->validate([
            'diagnosis_notes' => 'required|string|max:10000',
            'doctor_name'     => 'nullable|string|max:100|regex:/^[A-Za-z0-9_\-\.\s,]+$/',
        ]);

        $scan = MedicalScan::findOrFail($id);
        $scan->update([
            'diagnosis_notes' => strip_tags($validated['diagnosis_notes']),
            'doctor_name'     => !empty($validated['doctor_name']) ? trim($validated['doctor_name']) : 'dr. Radiolog Sp.Rad',
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

        $validated = $request->validate([
            'engine'       => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9_:\-]+$/'],
            'level'        => 'nullable|string|in:auto,15,25,50,Auto',
            'sigma_color'  => 'nullable|numeric|min:1|max:250',
            'sigma_space'  => 'nullable|numeric|min:1|max:250',
            'viewport'     => 'nullable|integer|in:1,2',
            'target_image' => 'nullable|string|max:500',
        ]);

        $engine      = $validated['engine'] ?? 'bilateral';
        $level       = strtolower($validated['level'] ?? 'auto');
        $sigmaColor  = (float) ($validated['sigma_color'] ?? 25);
        $sigmaSpace  = (float) ($validated['sigma_space'] ?? 20);

        $viewport    = (int) ($validated['viewport'] ?? 1);
        $targetImage = $validated['target_image'] ?? null;

        // Bersihkan jika formatnya full URL atau memiliki subdirektori web server
        $cleanRelPath = null;
        if ($targetImage) {
            $parsedUrl = parse_url($targetImage, PHP_URL_PATH);
            $rawPath = $parsedUrl ? ltrim($parsedUrl, '/') : '';
            // Pencegahan Directory Traversal
            if (str_contains($rawPath, '..')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Path berkas citra tidak valid.'
                ], 400);
            }
            if (str_contains($rawPath, 'scan-assets/')) {
                $cleanRelPath = 'scan-assets/' . substr($rawPath, strpos($rawPath, 'scan-assets/') + strlen('scan-assets/'));
            } elseif (str_contains($rawPath, 'storage/')) {
                $cleanRelPath = 'storage/' . substr($rawPath, strpos($rawPath, 'storage/') + strlen('storage/'));
            } else {
                $cleanRelPath = $rawPath;
            }
        }

        $pythonBinary = $this->getPythonBinary();
        $scriptPath   = base_path('test_dicom.py');

        // Prioritaskan file DICOM asli jika tersedia untuk mendapatkan akurasi bit-depth 12/16-bit
        $dicomPath = null;
        if ($viewport === 1 && $scan->dicom_raw_path && !str_contains($scan->dicom_raw_path, '..')) {
            $p1 = storage_path('app/public/' . $scan->dicom_raw_path);
            $p2 = base_path($scan->dicom_raw_path);
            if (file_exists($p1)) {
                $dicomPath = $p1;
            } elseif (file_exists($p2)) {
                $dicomPath = $p2;
            }
        }

        if (!$dicomPath) {
            if ($cleanRelPath && file_exists(public_path($cleanRelPath))) {
                $dicomPath = public_path($cleanRelPath);
            } elseif ($cleanRelPath && str_starts_with($cleanRelPath, 'storage/') && file_exists(storage_path('app/public/' . substr($cleanRelPath, 8)))) {
                $dicomPath = storage_path('app/public/' . substr($cleanRelPath, 8));
            } elseif ($cleanRelPath && file_exists(base_path($cleanRelPath))) {
                $dicomPath = base_path($cleanRelPath);
            } elseif ($viewport === 2 && file_exists(public_path('scan-assets/usg_sample.jpg'))) {
                $dicomPath = public_path('scan-assets/usg_sample.jpg');
            } elseif ($scan->dicom_raw_path && !str_contains($scan->dicom_raw_path, '..')) {
                $p1 = storage_path('app/public/' . $scan->dicom_raw_path);
                $p2 = base_path($scan->dicom_raw_path);
                if (file_exists($p1)) $dicomPath = $p1;
                elseif (file_exists($p2)) $dicomPath = $p2;
            }
        }

        if (!$dicomPath || !file_exists($dicomPath)) {
            $dicomPath = file_exists(public_path('scan-assets/raw_toraks.jpg')) 
                ? public_path('scan-assets/raw_toraks.jpg') 
                : base_path('sample_toraks.dcm');
        }

        // Folder output denoised JPG & Residual Map (permission aman 0755)
        $outputFolder = public_path('scan-assets');
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0755, true);
        }
        $outputPath   = $outputFolder . DIRECTORY_SEPARATOR . "denoised_scan_{$id}_vp{$viewport}.jpg";
        $residualPath = $outputFolder . DIRECTORY_SEPARATOR . "residual_scan_{$id}_vp{$viewport}.jpg";

        // Siapkan Environment Variables Windows
        $env = [
            'SystemRoot'     => getenv('SystemRoot') ?: 'C:\\Windows',
            'SYSTEMROOT'     => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'WINDIR'         => getenv('WINDIR') ?: 'C:\\Windows',
            'PATH'           => getenv('PATH') ?: 'C:\\Windows\\system32;C:\\Windows',
            'TEMP'           => getenv('TEMP') ?: 'C:\\Windows\\Temp',
            'TMP'            => getenv('TMP') ?: 'C:\\Windows\\Temp',
            'PYTHONHASHSEED' => '0',
        ];

        // Eksekusi subprocess Python secara aman dengan array arguments (kebal Command Injection)
        $command = [
            $pythonBinary,
            $scriptPath,
            $dicomPath,
            $outputPath,
            $engine,
            (string) $sigmaColor,
            (string) $sigmaSpace,
            $residualPath,
            (string) $level
        ];
        $tStart = microtime(true);
        $result = Process::env($env)->timeout(90)->run($command);
        $totalProcMs = round((microtime(true) - $tStart) * 1000, 1);

        $stdout = trim($result->output());

        // 2. Baca JSON pada baris terakhir stdout
        $lines = array_values(array_filter(preg_split('/\r\n|\r|\n/', $stdout), fn($line) => trim($line) !== ''));
        $lastLine = count($lines) > 0 ? end($lines) : '{}';
        $jsonRes = json_decode($lastLine, true);

        // Jika "ok" bernilai false, tampilkan isi "error" di UI
        if (is_array($jsonRes) && isset($jsonRes['ok']) && $jsonRes['ok'] === false) {
            $errorMsg = $jsonRes['error'] ?? 'Terjadi kesalahan pada inferensi model DnCNN.';
            return response()->json([
                'status'  => 'error',
                'ok'      => false,
                'message' => $errorMsg,
                'error'   => $errorMsg
            ], 422);
        }

        if ($result->successful() && file_exists($outputPath) && is_array($jsonRes) && ($jsonRes['status'] ?? '') === 'success') {
            $relDenoisedPath = "scan-assets/denoised_scan_{$id}_vp{$viewport}.jpg";
            $relResidualPath = "scan-assets/residual_scan_{$id}_vp{$viewport}.jpg";

            if ($viewport === 1) {
                $scan->update(['denoised_image_path' => $relDenoisedPath]);
            }

            // 4. Simpan "level", "device", "infer_ms", dan "total_ms" ke log untuk pengukuran latensi
            if (isset($jsonRes['level']) || isset($jsonRes['device']) || isset($jsonRes['infer_ms'])) {
                Log::info('DnCNN inference measurement (Subbab 3.13)', [
                    'scan_id'   => $id,
                    'viewport'  => $viewport,
                    'level'     => $jsonRes['level'] ?? null,
                    'device'    => $jsonRes['device'] ?? null,
                    'infer_ms'  => $jsonRes['infer_ms'] ?? null,
                    'total_ms'  => $totalProcMs,
                    'load_ms'   => $jsonRes['load_ms'] ?? null,
                    'method'    => $jsonRes['method_name'] ?? $jsonRes['engine'] ?? null,
                ]);
            }

            return response()->json([
                'status'         => 'success',
                'ok'             => true,
                'viewport'       => $viewport,
                'image_url'      => asset($relDenoisedPath) . '?t=' . time(),
                'residual_url'   => file_exists($residualPath) ? (asset($relResidualPath) . '?t=' . time()) : null,
                'engine'         => $jsonRes['method_name'] ?? $jsonRes['engine'] ?? 'Medical Processing',
                'category'       => $jsonRes['category'] ?? 'restoration_denoising',
                'method_name'    => $jsonRes['method_name'] ?? $jsonRes['engine'] ?? 'Medical Processing',
                'parameters'     => $jsonRes['parameters'] ?? $jsonRes['details'] ?? [],
                'metrics'        => $jsonRes['metrics'] ?? [],
                'level'          => $jsonRes['level'] ?? null,
                'device'         => $jsonRes['device'] ?? null,
                'infer_ms'       => $jsonRes['infer_ms'] ?? ($jsonRes['metrics']['inference_time_ms'] ?? null),
                'total_ms'       => $totalProcMs,
                'dicom_metadata' => $jsonRes['dicom_metadata'] ?? null,
                'message'        => 'Pemrosesan citra radiologi berhasil diaplikasikan.'
            ]);
        } elseif ($result->successful() && is_array($jsonRes) && ($jsonRes['status'] ?? '') === 'info') {
            return response()->json([
                'status'      => 'info',
                'category'    => $jsonRes['category'] ?? 'bone_suppression',
                'method_name' => $jsonRes['method_name'] ?? 'AI Bone Suppression',
                'message'     => $jsonRes['message'] ?? 'Informasi audit model AI.',
                'audit'       => $jsonRes['audit'] ?? null
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

        $validated = $request->validate([
            'sigma_color'  => 'nullable|numeric|min:1|max:250',
            'sigma_space'  => 'nullable|numeric|min:1|max:250',
            'viewport'     => 'nullable|integer|in:1,2',
            'target_image' => 'nullable|string|max:500',
        ]);

        $sigmaColor  = (float) ($validated['sigma_color'] ?? 25);
        $sigmaSpace  = (float) ($validated['sigma_space'] ?? 20);
        $viewport    = (int) ($validated['viewport'] ?? 1);
        $targetImage = $validated['target_image'] ?? null;

        if ($targetImage) {
            $parsedUrl = parse_url($targetImage, PHP_URL_PATH);
            $rawPath = $parsedUrl ? ltrim($parsedUrl, '/') : '';
            if (str_contains($rawPath, '..')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Path berkas tidak valid.'
                ], 400);
            }
            $cleanRelPath = $rawPath;
        } else {
            $cleanRelPath = null;
        }

        $pythonBinary = $this->getPythonBinary();
        $scriptPath   = base_path('test_dicom.py');

        $dicomPath = base_path('sample_toraks.dcm');
        if ($cleanRelPath && file_exists(public_path($cleanRelPath))) {
            $dicomPath = public_path($cleanRelPath);
        } elseif ($viewport === 2 && file_exists(public_path('scan-assets/usg_sample.jpg'))) {
            $dicomPath = public_path('scan-assets/usg_sample.jpg');
        } elseif ($scan->dicom_raw_path && !str_contains($scan->dicom_raw_path, '..') && file_exists(base_path($scan->dicom_raw_path))) {
            $dicomPath = base_path($scan->dicom_raw_path);
        }

        $outputFolder = public_path('scan-assets');
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0755, true);
        }
        $outputPath   = $outputFolder . DIRECTORY_SEPARATOR . "benchmark_scan_{$id}_vp{$viewport}.jpg";

        $env = [
            'SystemRoot'     => getenv('SystemRoot') ?: 'C:\\Windows',
            'SYSTEMROOT'     => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'WINDIR'         => getenv('WINDIR') ?: 'C:\\Windows',
            'PATH'           => getenv('PATH') ?: 'C:\\Windows\\system32;C:\\Windows',
            'TEMP'           => getenv('TEMP') ?: 'C:\\Windows\\Temp',
            'TMP'            => getenv('TMP') ?: 'C:\\Windows\\Temp',
            'PYTHONHASHSEED' => '0',
        ];

        // Eksekusi subprocess Python secara aman dengan array arguments
        $command = [
            $pythonBinary,
            $scriptPath,
            $dicomPath,
            $outputPath,
            'benchmark',
            (string) $sigmaColor,
            (string) $sigmaSpace
        ];
        $tStart = microtime(true);
        $result = Process::env($env)->timeout(120)->run($command);
        $totalProcMs = round((microtime(true) - $tStart) * 1000, 1);

        $stdout = trim($result->output());

        // 2. Baca JSON pada baris terakhir stdout
        $lines = array_values(array_filter(preg_split('/\r\n|\r|\n/', $stdout), fn($line) => trim($line) !== ''));
        $lastLine = count($lines) > 0 ? end($lines) : '{}';
        $jsonRes = json_decode($lastLine, true);

        // Jika "ok" bernilai false, tampilkan isi "error" di UI
        if (is_array($jsonRes) && isset($jsonRes['ok']) && $jsonRes['ok'] === false) {
            $errorMsg = $jsonRes['error'] ?? 'Gagal menjalankan benchmark algoritma.';
            return response()->json([
                'status'  => 'error',
                'ok'      => false,
                'message' => $errorMsg,
                'error'   => $errorMsg
            ], 422);
        }

        if ($result->successful() && is_array($jsonRes) && ($jsonRes['status'] ?? '') === 'success') {
            // 4. Simpan "level", "device", "infer_ms", dan "total_ms" ke log untuk pengukuran latensi
            $dncnnRow = collect($jsonRes['benchmark_rows'] ?? [])->firstWhere('id', 'dl_dncnn');
            if ($dncnnRow) {
                Log::info('DnCNN benchmark latency measurement (Subbab 3.13)', [
                    'scan_id'  => $id,
                    'viewport' => $viewport,
                    'level'    => $dncnnRow['level'] ?? ($jsonRes['level'] ?? null),
                    'device'   => $dncnnRow['device'] ?? ($jsonRes['device'] ?? null),
                    'infer_ms' => $dncnnRow['infer_ms'] ?? ($jsonRes['infer_ms'] ?? null),
                    'total_ms' => $totalProcMs,
                ]);
            }

            return response()->json([
                'status'         => 'success',
                'ok'             => true,
                'viewport'       => $viewport,
                'benchmark_rows' => $jsonRes['benchmark_rows'] ?? [],
                'recommended'    => $jsonRes['recommended_engine'] ?? 'dl_dncnn',
                'level'          => $jsonRes['level'] ?? null,
                'device'         => $jsonRes['device'] ?? null,
                'infer_ms'       => $jsonRes['infer_ms'] ?? null,
                'total_ms'       => $totalProcMs,
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
     * Dapatkan path executable Python yang tersedia di sistem
     */
    private function getPythonBinary(): string
    {
        $candidates = [
            env('PYTHON_BINARY'),
            'C:\\Python310\\python.exe',
            'python',
            'py',
            'C:\\Users\\user\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
            'C:\\laragon\\bin\\python\\python-3.13\\python.exe'
        ];

        foreach ($candidates as $cand) {
            if ($cand && (file_exists($cand) || in_array($cand, ['py', 'python']))) {
                return $cand;
            }
        }

        return 'py';
    }

    /**
     * Memicu Simulasi Transmisi DICOM C-STORE dari Mesin Fujifilm ke Hyu PACS
     */
    public function simulateFuji(Request $request)
    {
        $pythonBinary = $this->getPythonBinary();

        $scriptPath = base_path('test_fujifilm_sender.py');
        $dcmFile    = base_path('sample_toraks.dcm');
        $executedViaPython = false;

        if ($pythonBinary && file_exists($scriptPath) && file_exists($dcmFile)) {
            $env = [
                'SystemRoot'     => getenv('SystemRoot') ?: 'C:\\Windows',
                'SYSTEMROOT'     => getenv('SYSTEMROOT') ?: 'C:\\Windows',
                'PATH'           => getenv('PATH') ?: 'C:\\Windows\\system32;C:\\Windows',
                'PYTHONHASHSEED' => '0',
            ];

            try {
                $command = [$pythonBinary, $scriptPath, $dcmFile];
                $result = Process::env($env)->timeout(8)->run($command);
                if ($result->successful()) {
                    $executedViaPython = true;
                }
            } catch (\Throwable $e) {
                // Lanjutkan ke simulasi internal PACS jika background socket/python offline
            }
        }

        // Lakukan simulasi penerimaan C-STORE di database Hyu PACS
        try {
            // Cari order MCU yang sedang menunggu rontgen (siap_rontgen)
            $targetScan = MedicalScan::where('mcu_status', 'siap_rontgen')->orderBy('id', 'asc')->first();

            if ($targetScan) {
                $targetScan->update([
                    'mcu_status'         => 'rontgen_selesai',
                    'station_name'       => 'FUJIFILM_FDR_D-EVO',
                    'scan_image_path'    => 'scan-assets/raw_toraks.jpg',
                    'preview_image_path' => 'scan-assets/raw_toraks.jpg',
                    'dicom_raw_path'     => 'sample_toraks.dcm',
                    'study_instance_uid' => '1.2.392.200036.9125.0.' . time(),
                    'sop_instance_uid'   => '1.2.392.200036.9125.1.' . time(),
                ]);
            } else {
                // Jika belum ada antrian yang menunggu, buat record pemeriksaan baru dari stasiun Fuji
                $latestId = (MedicalScan::max('id') ?: 0) + 1;
                $targetScan = MedicalScan::create([
                    'patient_id'         => 'CDC-' . str_pad($latestId, 5, '0', STR_PAD_LEFT),
                    'accession_number'   => 'ACC-' . date('Ymd') . '-' . str_pad($latestId, 3, '0', STR_PAD_LEFT),
                    'patient_name'       => 'Budi Santoso (Simulasi Fuji)',
                    'age'                => 38,
                    'gender'             => 'L',
                    'modality'           => 'Thorax PA',
                    'study_description'  => 'Thorax PA (Simulasi Fujifilm C-STORE)',
                    'mcu_status'         => 'rontgen_selesai',
                    'station_name'       => 'FUJIFILM_FDR_D-EVO',
                    'scan_image_path'    => 'scan-assets/raw_toraks.jpg',
                    'preview_image_path' => 'scan-assets/raw_toraks.jpg',
                    'dicom_raw_path'     => 'sample_toraks.dcm',
                    'study_instance_uid' => '1.2.392.200036.9125.0.' . time(),
                    'sop_instance_uid'   => '1.2.392.200036.9125.1.' . time(),
                    'order_notes'        => 'Diterima otomatis dari workstation akuisisi Fujifilm FDR D-EVO via DICOM C-STORE SCP.',
                ]);
            }

            return response()->json([
                'status'           => 'success',
                'title'            => 'TRANSMISI DICOM C-STORE BERHASIL',
                'message'          => 'Citra rontgen dari mesin Fujifilm FDR D-EVO berhasil diterima dan diarsipkan oleh Hyu PACS SCP.',
                'patient_name'     => $targetScan->patient_name,
                'patient_id'       => $targetScan->patient_id,
                'accession_number' => $targetScan->accession_number,
                'modality'         => $targetScan->modality,
                'station'          => $targetScan->station_name ?: 'FUJIFILM_FDR_D-EVO',
                'sop_class'        => 'Digital X-Ray Image Storage (1.2.840.10008.5.1.4.1.1.1)',
                'status_code'      => '0x0000 (Success)',
                'mode'             => $executedViaPython ? 'DICOM Network Protocol (Python)' : 'Hyu PACS Storage Ingestion'
            ]);
        } catch (\Throwable $ex) {
            return response()->json([
                'status'  => 'error',
                'title'   => 'GAGAL TRANSMISI C-STORE',
                'message' => 'Terjadi kesalahan saat memproses transmisi C-STORE: ' . $ex->getMessage()
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

        if (!$hasExamined || empty($scan->dicom_raw_path)) {
            return back()->with('error', 'Pemeriksaan belum dilakukan. File DICOM belum tersedia.');
        }

        $rawPath = $scan->dicom_raw_path;
        // Pencegahan Directory Traversal
        if (str_contains($rawPath, '..')) {
            abort(403, 'Akses berkas tidak diizinkan.');
        }

        $cleanRel = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $rawPath), DIRECTORY_SEPARATOR);
        $candidates = [
            storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $cleanRel),
            base_path($cleanRel),
        ];

        $targetFile = null;
        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_file($cand)) {
                $real = realpath($cand);
                $baseReal = realpath(base_path());
                $storageReal = realpath(storage_path());
                if ($real && (($baseReal && str_starts_with($real, $baseReal)) || ($storageReal && str_starts_with($real, $storageReal)))) {
                    $targetFile = $real;
                    break;
                }
            }
        }

        if (!$targetFile) {
            return back()->with('error', 'File DICOM mentah tidak ditemukan di server.');
        }

        $cleanPid = preg_replace('/[^A-Za-z0-9_\-]/', '', $scan->patient_id) ?: 'PASIEN';
        $cleanAcc = preg_replace('/[^A-Za-z0-9_\-]/', '', $scan->accession_number) ?: 'ACC';
        $filename = "DICOM_{$cleanPid}_{$cleanAcc}.dcm";

        return response()->download($targetFile, $filename, [
            'Content-Type' => 'application/dicom',
        ]);
    }

    /**
     * Memperbarui Konfigurasi DICOM Node (AE Title, Port, Target Modality) langsung dari UI Web
     */
    public function updateDicomNode(Request $request)
    {
        $validated = $request->validate([
            'ae_title'        => 'required|string|max:16|regex:/^[A-Za-z0-9_\-]+$/',
            'port'            => 'required|integer|min:1|max:65535',
            'modality_source' => 'nullable|string|max:100|regex:/^[A-Za-z0-9_\-\(\)\s\/\.,&]+$/',
        ]);

        $aeTitle = strtoupper(preg_replace('/[^A-Za-z0-9_\-]/', '', $validated['ae_title']));
        $port = (int) $validated['port'];
        $rawModality = $validated['modality_source'] ?? '';
        $modality = preg_replace('/[\r\n"\'\\\\]/', '', $rawModality) ?: 'FUJIFILM (CR) & MINDRAY (US)';

        // Update file .env secara aman tanpa risiko injeksi baris baru
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