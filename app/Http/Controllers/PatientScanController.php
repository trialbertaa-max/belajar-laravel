<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalScan;
use Illuminate\Support\Facades\Process;

class PatientScanController extends Controller
{
    // Fungsi untuk menampilkan semua data scan dari MySQL dalam bentuk halaman Web (Blade View)
    public function index()
    {
        // Jika database masih kosong atau perlu data contoh USG & Thorax untuk klinik CDC
        if (MedicalScan::count() < 3) {
            MedicalScan::firstOrCreate(
                ['patient_name' => 'Budi Santoso', 'modality' => 'USG Abdomen'],
                [
                    'scan_image_path' => 'scans/usg_abdomen_01.dcm',
                    'diagnosis_notes' => 'Hepar dan vesica fellea dalam batas normal, tidak tampak batu atau massa.'
                ]
            );

            MedicalScan::firstOrCreate(
                ['patient_name' => 'Siti Rahma', 'modality' => 'Thorax PA'],
                [
                    'scan_image_path' => 'scans/thorax_pa_02.dcm',
                    'diagnosis_notes' => 'Cor dan pulmo dalam batas normal, sinus costophrenicus tajam.'
                ]
            );

            MedicalScan::firstOrCreate(
                ['patient_name' => 'Ahmad Fauzi', 'modality' => 'Thorax Lateral'],
                [
                    'scan_image_path' => 'scans/thorax_lat_03.dcm',
                    'diagnosis_notes' => 'Evaluasi infiltrate pada lobus medius pulmo dextra.'
                ]
            );
        }

        // Mengambil semua data scan dari tabel 'medical_scans' di MySQL
        $scans = MedicalScan::latest()->get();

        // Mengirim data ke tampilan Blade: resources/views/scans/index.blade.php
        return view('scans.index', compact('scans'));
    }


    // Fungsi untuk menampilkan 1 data scan spesifik di halaman DICOM Viewer
    public function show(string $id)
    {
        // Mencari scan di database MySQL berdasarkan ID
        $scan = MedicalScan::find($id);

        // Jika data belum ada, gunakan data contoh default agar viewer tetap tampil
        if (!$scan) {
            $scan = new MedicalScan([
                'patient_name' => ($id == 2) ? 'Siti Rahma' : 'Budi Santoso',
                'modality' => ($id == 2) ? 'Thorax PA' : 'USG Abdomen',
                'scan_image_path' => ($id == 2) ? 'scans/thorax_pa_02.dcm' : 'scans/usg_abdomen_01.dcm',
                'diagnosis_notes' => ($id == 2) ? 'Cor dan pulmo dalam batas normal.' : 'Hepar dan vesica fellea dalam batas normal.'
            ]);
            $scan->id = $id;
        }

        // Mengirim data scan terpilih ke tampilan Blade viewer: resources/views/scans/show.blade.php
        return view('scans.show', compact('scan'));
    }


    // Fungsi Endpoint API untuk mengirimkan file/stream DICOM ke Cornerstone.js
    public function getDicom(string $id)
    {
        $scan = MedicalScan::findOrFail($id);

        // Metadata JSON respon untuk simulasi integrasi WADO-RS PACS teraMedik
        return response()->json([
            'status' => 'success',
            'pacs_server' => 'teraMedik PACS CDC',
            'modality' => $scan->modality,
            'patient_name' => $scan->patient_name,
            'dicom_instance_url' => $scan->scan_image_path
        ]);
    }


    // Fungsi untuk memicu eksekusi Script Python Denoising
    public function runDenoise(string $id)
    {
        // 1. Path eksekusi Python 3.13 asli milikmu
        $pythonBinary = 'C:\\Users\\FLATS-32-46\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';

        // 2. Lokasi script dan file DICOM
        $scriptPath = 'C:\\Users\\FLATS-32-46\\Downloads\\UjiCoba_DICOM\\test_dicom.py';
        $dicomPath  = 'C:\\Users\\FLATS-32-46\\Downloads\\UjiCoba_DICOM\\sample_toraks.dcm';

        // 3. Folder tujuan output di public Laravel
        $outputFolder = public_path('scans');
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }
        $outputPath = $outputFolder . DIRECTORY_SEPARATOR . 'denoised_toraks.jpg';

        // 4. Jalankan Python dengan jalur lengkap
        $command = "\"{$pythonBinary}\" \"{$scriptPath}\" \"{$dicomPath}\" \"{$outputPath}\"";
        $result = Process::timeout(60)->run($command);

        if ($result->successful() && file_exists($outputPath)) {
            return response()->json([
                'status' => 'success',
                'image_url' => asset('scans/denoised_toraks.jpg') . '?t=' . time()
            ]);
        } else {
            $errorMsg = $result->errorOutput() ?: $result->output();
            return response()->json([
                'status' => 'error',
                'message' => $errorMsg ?: 'Gagal memproses via Python.'
            ], 500);
        }
    }
}