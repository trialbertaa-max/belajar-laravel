<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalScan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;

class SystemHealthController extends Controller
{
    /**
     * Tampilkan Halaman Start Screen / System Readiness Center
     */
    public function index()
    {
        $pacsConfig = [
            'ae_title'    => env('DICOM_AE_TITLE', 'HYU_PACS'),
            'port'        => env('DICOM_PORT', '4242'),
            'ip'          => env('DICOM_HOST', '0.0.0.0'),
            'modality_ae' => env('DICOM_MODALITY_SOURCE', 'FUJIFILM (CR) & MINDRAY (US)'),
        ];

        return view('system.start', compact('pacsConfig'));
    }

    /**
     * API Status Diagnostik Seluruh Subsistem (Database, Python, AI, Storage, DICOM)
     */
    public function status()
    {
        $startTime = microtime(true);

        // 1. Database & EMR Check
        $dbStatus = [
            'connected'    => false,
            'driver'       => config('database.default'),
            'tables_count' => 0,
            'scans_count'  => 0,
            'error'        => null,
        ];
        try {
            DB::connection()->getPdo();
            $dbStatus['connected'] = true;
            $dbStatus['scans_count'] = MedicalScan::count();
            if (config('database.default') === 'sqlite') {
                $tables = DB::select("SELECT count(*) as cnt FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $dbStatus['tables_count'] = $tables[0]->cnt ?? 0;
            } else {
                $dbStatus['tables_count'] = count(Schema::getTableListing());
            }
        } catch (\Throwable $e) {
            $dbStatus['error'] = $e->getMessage();
        }

        // 2. Storage & Assets Check
        $assetsDir = public_path('scan-assets');
        $scansDir  = storage_path('app/public/scans');
        $storageStatus = [
            'assets_dir_exists'   => file_exists($assetsDir),
            'assets_dir_writable' => is_writable($assetsDir),
            'scans_dir_exists'    => file_exists($scansDir),
            'sample_toraks'       => file_exists(base_path('sample_toraks.dcm')),
            'sample_kepala'       => file_exists(base_path('sample_kepala.dcm')),
            'sample_usg'          => file_exists(base_path('sample_usg.dcm')),
            'storage_symlink'     => file_exists(public_path('storage')),
        ];

        // 3. Python & AI Engine Check
        $pythonBinary = $this->getPythonBinary();
        $pythonStatus = [
            'binary'         => $pythonBinary,
            'available'      => false,
            'version'        => null,
            'torch_version'  => null,
            'cuda_available' => false,
            'opencv_version' => null,
            'pydicom'        => false,
            'pynetdicom'     => false,
            'weights_bone'   => file_exists(base_path('medical_engine/weights/bone_suppression.ts')),
            'weights_comm'   => file_exists(base_path('medical_engine/weights/bone_suppression_commercial.ts')),
        ];

        if ($pythonBinary) {
            $probeScript = "import sys, json\n"
                . "data = {'version': sys.version.split()[0]}\n"
                . "try:\n import cv2; data['opencv'] = cv2.__version__\nexcept: data['opencv'] = None\n"
                . "try:\n import torch; data['torch'] = torch.__version__; data['cuda'] = torch.cuda.is_available()\nexcept: data['torch'] = None; data['cuda'] = False\n"
                . "try:\n import pydicom; data['pydicom'] = True\nexcept: data['pydicom'] = False\n"
                . "try:\n import pynetdicom; data['pynetdicom'] = True\nexcept: data['pynetdicom'] = False\n"
                . "print(json.dumps(data))";

            try {
                $probeResult = Process::timeout(5)->run([$pythonBinary, '-c', $probeScript]);
                if ($probeResult->successful()) {
                    $parsed = json_decode(trim($probeResult->output()), true);
                    if (is_array($parsed)) {
                        $pythonStatus['available']      = true;
                        $pythonStatus['version']        = $parsed['version'] ?? null;
                        $pythonStatus['opencv_version'] = $parsed['opencv'] ?? null;
                        $pythonStatus['torch_version']  = $parsed['torch'] ?? null;
                        $pythonStatus['cuda_available'] = (bool) ($parsed['cuda'] ?? false);
                        $pythonStatus['pydicom']        = (bool) ($parsed['pydicom'] ?? false);
                        $pythonStatus['pynetdicom']     = (bool) ($parsed['pynetdicom'] ?? false);
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 4. DICOM Server Listener Check (Port 4242 & PID)
        $dicomPort = (int) env('DICOM_PORT', 4242);
        $dicomHost = env('DICOM_HOST', '0.0.0.0');
        $dicomAe   = env('DICOM_AE_TITLE', 'HYU_PACS');

        $portActive = false;
        $fp = @fsockopen('127.0.0.1', $dicomPort, $errno, $errstr, 0.35);
        if (is_resource($fp)) {
            $portActive = true;
            fclose($fp);
        }

        $pid = null;
        if ($portActive) {
            try {
                $pidRes = Process::timeout(3)->run([
                    'powershell', '-Command',
                    "Get-NetTCPConnection -LocalPort {$dicomPort} -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess -First 1"
                ]);
                $pid = trim($pidRes->output()) ?: null;
            } catch (\Throwable $e) {}
        }

        $dicomStatus = [
            'running'   => $portActive,
            'port'      => $dicomPort,
            'host'      => $dicomHost,
            'ae_title'  => $dicomAe,
            'pid'       => $pid,
            'server_py' => file_exists(base_path('hyu_dicom_server.py')),
        ];

        // Overall Readiness Score
        $score = 0;
        $maxScore = 5;
        if ($dbStatus['connected']) $score++;
        if ($storageStatus['assets_dir_writable']) $score++;
        if ($pythonStatus['available']) $score++;
        if ($pythonStatus['pydicom'] && $pythonStatus['pynetdicom']) $score++;
        if ($dicomStatus['running']) $score++;

        $readinessPercent = round(($score / $maxScore) * 100);

        $executionMs = round((microtime(true) - $startTime) * 1000, 1);

        return response()->json([
            'status'            => 'success',
            'readiness_percent' => $readinessPercent,
            'all_ready'         => ($score === $maxScore),
            'execution_ms'      => $executionMs,
            'database'          => $dbStatus,
            'storage'           => $storageStatus,
            'python'            => $pythonStatus,
            'dicom'             => $dicomStatus,
        ]);
    }

    /**
     * Toggle Kontrol Server DICOM (START, STOP, RESTART)
     */
    public function toggleDicom(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:start,stop,restart',
        ]);

        $action    = $validated['action'];
        $dicomPort = (int) env('DICOM_PORT', 4242);
        $script    = base_path('hyu_dicom_server.py');
        $python    = $this->getPythonBinary();

        if (!file_exists($script)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Berkas hyu_dicom_server.py tidak ditemukan di root proyek.',
            ], 404);
        }

        if ($action === 'stop' || $action === 'restart') {
            // Hentikan proses listener pada port 4242 & proses python hyu_dicom_server
            try {
                Process::timeout(5)->run([
                    'powershell', '-Command',
                    "Get-NetTCPConnection -LocalPort {$dicomPort} -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force -ErrorAction SilentlyContinue }; " .
                    "Get-CimInstance Win32_Process | Where-Object { \$_.CommandLine -like '*hyu_dicom_server.py*' -and \$_.Name -notlike '*powershell*' } | ForEach-Object { Stop-Process -Id \$_.ProcessId -Force -ErrorAction SilentlyContinue }"
                ]);
            } catch (\Throwable $e) {}

            usleep(600000); // 0.6 detik
        }

        if ($action === 'start') {
            // Bersihkan listener lawas jika ada proses orphan yang masih mengunci port
            try {
                Process::timeout(5)->run([
                    'powershell', '-Command',
                    "Get-NetTCPConnection -LocalPort {$dicomPort} -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id \$_.OwningProcess -Force -ErrorAction SilentlyContinue }"
                ]);
            } catch (\Throwable $e) {}

            usleep(300000); // 0.3 detik
        }

        if ($action === 'start' || $action === 'restart') {
            // Jalankan hyu_dicom_server.py di background Windows tanpa window console
            try {
                $pyCmd = $python ?: 'python';
                $baseDir = base_path();
                $startScript = "Start-Process -FilePath '{$pyCmd}' -ArgumentList '{$script}' -WorkingDirectory '{$baseDir}' -WindowStyle Hidden";
                Process::timeout(5)->run(['powershell', '-Command', $startScript]);
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal memicu startup server DICOM: ' . $e->getMessage(),
                ], 500);
            }

            // Polling cek apakah port sudah bind (hingga 2.5 detik)
            $portActive = false;
            for ($attempt = 0; $attempt < 10; $attempt++) {
                usleep(250000); // 250ms
                $fp = @fsockopen('127.0.0.1', $dicomPort, $errno, $errstr, 0.2);
                if (is_resource($fp)) {
                    $portActive = true;
                    fclose($fp);
                    break;
                }
            }
        }

        // Cek status akhir port setelah aksi
        $portActive = false;
        $fp = @fsockopen('127.0.0.1', $dicomPort, $errno, $errstr, 0.4);
        if (is_resource($fp)) {
            $portActive = true;
            fclose($fp);
        }

        $pid = null;
        if ($portActive) {
            try {
                $pidRes = Process::timeout(3)->run([
                    'powershell', '-Command',
                    "Get-NetTCPConnection -LocalPort {$dicomPort} -State Listen -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess -First 1"
                ]);
                $pid = trim($pidRes->output()) ?: null;
            } catch (\Throwable $e) {}
        }

        $pidSuffix = $pid ? " (PID: {$pid})" : "";
        $msgMap = [
            'start'   => $portActive ? "Hyu DICOM PACS Server berhasil dinyalakan pada Port {$dicomPort}{$pidSuffix}!" : "Server gagal mengikat port {$dicomPort}.",
            'stop'    => !$portActive ? "Hyu DICOM PACS Server berhasil dihentikan." : "Server masih terdeteksi aktif di port {$dicomPort}.",
            'restart' => $portActive ? "Hyu DICOM PACS Server berhasil di-restart pada Port {$dicomPort}{$pidSuffix}!" : "Server gagal di-restart.",
        ];

        return response()->json([
            'status'     => ($action === 'stop' && !$portActive) || (($action === 'start' || $action === 'restart') && $portActive) ? 'success' : 'warning',
            'action'     => $action,
            'running'    => $portActive,
            'port'       => $dicomPort,
            'pid'        => $pid,
            'message'    => $msgMap[$action],
        ]);
    }

    /**
     * Tes Koneksi DICOM C-ECHO (Ping) Langsung ke Port Listener
     */
    public function pingDicom(Request $request)
    {
        $dicomPort = (int) env('DICOM_PORT', 4242);
        $dicomAe   = env('DICOM_AE_TITLE', 'HYU_PACS');
        $python    = $this->getPythonBinary();

        if (!$python) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Python binary tidak ditemukan di sistem.',
            ], 500);
        }

        $pingScript = "import sys, time\n"
            . "from pynetdicom import AE\n"
            . "from pynetdicom.sop_class import Verification\n"
            . "ae = AE(ae_title='PING_CLIENT')\n"
            . "ae.add_requested_context(Verification)\n"
            . "t0 = time.time()\n"
            . "assoc = ae.associate('127.0.0.1', {$dicomPort}, ae_title='{$dicomAe}')\n"
            . "if assoc.is_established:\n"
            . "    status = assoc.send_c_echo()\n"
            . "    assoc.release()\n"
            . "    latency = round((time.time() - t0) * 1000, 1)\n"
            . "    print(f'SUCCESS:{status.Status:04X}:{latency}')\n"
            . "else:\n"
            . "    print('FAILED:CONNECT_REJECTED:0')\n";

        try {
            $result = Process::timeout(6)->run([$python, '-c', $pingScript]);
            $out = trim($result->output());

            if (str_starts_with($out, 'SUCCESS:')) {
                $parts = explode(':', $out);
                $statusCode = $parts[1] ?? '0000';
                $latency = $parts[2] ?? '0';

                return response()->json([
                    'status'       => 'success',
                    'title'        => 'DICOM C-ECHO VERIFIKASI SUKSES',
                    'message'      => "Server Hyu PACS ({$dicomAe} di Port {$dicomPort}) merespons dengan status 0x{$statusCode} dalam {$latency} ms.",
                    'latency_ms'   => (float) $latency,
                    'status_code'  => "0x{$statusCode}",
                ]);
            } else {
                return response()->json([
                    'status'  => 'error',
                    'title'   => 'C-ECHO GAGAL',
                    'message' => "Tidak dapat menghubungi server DICOM di Port {$dicomPort}. Pastikan toggle server dalam kondisi AKTIF.",
                ], 503);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'title'   => 'TIMEOUT ATAU ERROR',
                'message' => 'Terjadi kesalahan saat menguji C-ECHO: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dapatkan path executable Python
     */
    private function getPythonBinary(): string
    {
        $candidates = [
            env('PYTHON_BINARY'),
            'C:\\Python310\\python.exe',
            'python',
            'py',
            'C:\\Users\\user\\AppData\\Local\\Programs\\Python\\Python313\\python.exe',
            'C:\\laragon\\bin\\python\\python-3.13\\python.exe',
        ];

        foreach ($candidates as $cand) {
            if ($cand && (file_exists($cand) || in_array($cand, ['py', 'python']))) {
                return $cand;
            }
        }

        return 'python';
    }
}

