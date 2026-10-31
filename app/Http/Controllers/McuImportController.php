<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImportWorklistRequest;
use App\Imports\PasienExcelImport;
use App\Services\McuRowNormalizer;
use App\Models\MedicalScan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class McuImportController extends Controller
{
    /**
     * Batas maksimum baris yang bisa diimport (tidak termasuk header).
     */
    protected const MAX_ROWS = 500;

    /**
     * Preview: parse file Excel dan kembalikan JSON untuk tabel staging CRUD di browser.
     * Endpoint ini TIDAK menyimpan ke database.
     */
    public function preview(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file' => 'required|file|max:5120',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.file'     => 'File yang diupload tidak valid.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $validator->errors()->first('file'),
                    'errors'  => $validator->errors(),
                ], 422);
            }
            $validator->validate();
        }

        $file = $request->file('file');

        // Tolak selain .xlsx, .xls, atau .csv (termasuk .xlsm makro)
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'], true) || $ext === 'xlsm') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Hanya file .xlsx, .xls, atau .csv.',
                    'errors'  => ['file' => ['Hanya file .xlsx, .xls, atau .csv.']],
                ], 422);
            }

            throw \Illuminate\Validation\ValidationException::withMessages([
                'file' => 'Format file tidak didukung. Gunakan file .xlsx, .xls, atau .csv.',
            ]);
        }

        try {
            $import = new PasienExcelImport();
            Excel::import($import, $file);
            $collection = $import->rows;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Excel Import Preview Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Format file tidak sesuai template. Silakan unduh template terbaru.',
            ], 422);
        }

        if ($collection->isEmpty()) {
            return response()->json([
                'message' => 'File tidak berisi data pasien.',
            ], 422);
        }

        if ($collection->count() > self::MAX_ROWS) {
            return response()->json([
                'message' => 'Jumlah baris melebihi batas maksimum (' . self::MAX_ROWS . ' baris). Silakan bagi data ke beberapa file.',
            ], 422);
        }

        // Ambil header dari baris pertama
        $rawHeaders = $collection->first()->keys()->toArray();
        $headerMapping = McuRowNormalizer::normalizeHeaders($rawHeaders);

        // Periksa header wajib (nama dan modalitas)
        $mappedFields = array_values($headerMapping);
        $hasNama = in_array('nama_lengkap', $mappedFields, true);
        $hasModality = in_array('modalitas', $mappedFields, true);

        if (!$hasNama || !$hasModality) {
            return response()->json([
                'message' => 'Format file tidak sesuai template. Silakan unduh template terbaru.',
            ], 422);
        }

        $rawParsedRows = [];
        $intraCounts = [];

        foreach ($collection as $idx => $rawRow) {
            $mappedRow = [];
            foreach ($headerMapping as $rawKey => $fieldName) {
                $value = $rawRow[$rawKey] ?? null;
                if ($value === '' || $value === null) {
                    $value = null;
                }
                $mappedRow[$fieldName] = $value;
            }

            // Skip baris yang seluruh kolomnya kosong
            $allNull = true;
            foreach ($mappedRow as $v) {
                if ($v !== null && trim((string)$v) !== '') {
                    $allNull = false;
                    break;
                }
            }
            if ($allNull) {
                continue;
            }

            $result = McuRowNormalizer::normalizeRow($mappedRow);
            $nik = trim((string)($result['data']['nik'] ?? ''));
            $mod = trim((string)($result['data']['modalitas'] ?? ''));

            if ($nik !== '') {
                $key = $nik . '|' . strtolower($mod);
                $intraCounts[$key] = ($intraCounts[$key] ?? 0) + 1;
            }

            $rawParsedRows[] = [
                'excel_row'  => $idx + 2,
                'data'       => $result['data'],
                'errors'     => $result['errors'],
                'errors_map' => $result['errors_map'],
            ];
        }

        if (empty($rawParsedRows)) {
            return response()->json([
                'message' => 'File tidak berisi data pasien.',
            ], 422);
        }

        $pasien = [];
        $rows = [];
        $validCount = 0;
        $invalidCount = 0;

        foreach ($rawParsedRows as $i => $item) {
            $data = $item['data'];
            $errors = $item['errors'];
            $errorsMap = $item['errors_map'];

            $nik = trim((string)($data['nik'] ?? ''));
            $mod = trim((string)($data['modalitas'] ?? ''));

            // Cek duplikasi di dalam file (NIK + modalitas sama)
            if ($nik !== '') {
                $key = $nik . '|' . strtolower($mod);
                if (($intraCounts[$key] ?? 0) > 1) {
                    $dupMsg = 'Duplikat NIK dan modalitas di file.';
                    $errors[] = $dupMsg;
                    $errorsMap['nik'] = $dupMsg;
                    $errorsMap['modalitas'] = $dupMsg;
                }
            }

            $isReady = empty($errors);
            if ($isReady) {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $rowId = 'row-' . ($i + 1) . '-' . substr(md5(uniqid((string)($i + 1), true)), 0, 6);

            $pasien[] = [
                'id'                  => $rowId,
                'index'               => $i + 1,
                'nama'                => $data['nama'],
                'nik'                 => $data['nik'],
                'usia'                => $data['usia'],
                'jenis_kelamin'       => $data['jenis_kelamin'],
                'modalitas'           => $data['modalitas'],
                'indikasi'            => $data['indikasi'],
                'catatan'             => $data['catatan'],
                'tanggal_pemeriksaan' => $data['tanggal_pemeriksaan'],
                'dipilih'             => $isReady,
                'status'              => $isReady ? 'Siap' : 'Perlu dicek',
                'errors'              => $errorsMap,
                'masalah'             => array_values(array_unique($errors)),
                // Backward compatibility
                'nama_lengkap'        => $data['nama'],
                'mrn_nik'             => $data['nik'],
                'catatan_petugas'     => $data['catatan'],
                'indikasi_klinis'     => $data['indikasi'],
            ];

            // Backward compatibility untuk tes lama
            $rows[] = [
                'index'  => $item['excel_row'],
                'data'   => $data,
                'errors' => $errors,
            ];
        }

        return response()->json([
            'total'      => count($pasien),
            'valid'      => $validCount,
            'bermasalah' => $invalidCount,
            'pasien'     => $pasien,
            'rows'       => $rows,
            'summary'    => [
                'total'   => count($pasien),
                'valid'   => $validCount,
                'invalid' => $invalidCount,
            ],
        ]);
    }

    /**
     * Kirim batch pasien dari tabel staging ke database Worklist (Tahap 5).
     * Disimpan dalam satu transaksi database.
     */
    public function importBatch(ImportWorklistRequest $request)
    {
        $items = $request->validated()['pasien'];
        $errors = [];
        $seen = [];

        // 1. Cek duplikasi di dalam batch pengiriman (NIK + Modalitas sama)
        foreach ($items as $idx => $p) {
            $nik = trim((string)($p['nik'] ?? ''));
            $mod = trim((string)($p['modalitas'] ?? ''));
            if ($nik !== '') {
                $key = $nik . '|' . strtolower($mod);
                if (isset($seen[$key])) {
                    $errors["pasien.{$idx}.nik"] = ["Duplikat di dalam data yang dikirim (NIK dan modalitas sama dengan baris " . ($seen[$key] + 1) . ")."];
                } else {
                    $seen[$key] = $idx;
                }
            }
        }

        // 2. Cek duplikasi di database (NIK + Modalitas sama pada tanggal/sesi yang sama)
        foreach ($items as $idx => $p) {
            $nik = trim((string)($p['nik'] ?? ''));
            $mod = trim((string)($p['modalitas'] ?? ''));
            if ($nik !== '') {
                $examDate = !empty($p['tanggal_pemeriksaan'])
                    ? date('Y-m-d', strtotime($p['tanggal_pemeriksaan']))
                    : date('Y-m-d');

                $exists = MedicalScan::where('patient_id', $nik)
                    ->where('modality', $mod)
                    ->whereDate('created_at', $examDate)
                    ->exists();

                if ($exists) {
                    $errors["pasien.{$idx}.modalitas"] = ["Pasien ini sudah ada di worklist pada tanggal ini."];
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Ada data yang perlu diperbaiki.',
                'errors'  => $errors,
            ], 422);
        }

        // 3. Simpan semua dalam satu transaksi database
        DB::transaction(function () use ($items) {
            $startCount = MedicalScan::count();

            foreach ($items as $idx => $p) {
                $count = $startCount + $idx + 1;
                $examDate = !empty($p['tanggal_pemeriksaan'])
                    ? date('Y-m-d', strtotime($p['tanggal_pemeriksaan']))
                    : date('Y-m-d');

                $patientId = !empty($p['nik']) ? $p['nik'] : ('CDC-' . str_pad($count, 5, '0', STR_PAD_LEFT));
                $accessionDate = date('Ymd', strtotime($examDate));
                $accessionNo = 'ACC-' . $accessionDate . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

                $jk = strtolower(trim((string)($p['jenis_kelamin'] ?? 'L')));
                $gender = ($jk === 'p' || $jk === 'perempuan') ? 'P' : 'L';

                // Hook sesi jika model SesiMcu sudah ada
                $sesiId = null;
                if (class_exists(\App\Models\SesiMcu::class) && method_exists(\App\Models\SesiMcu::class, 'untukTanggal')) {
                    try {
                        $sesi = \App\Models\SesiMcu::untukTanggal($examDate);
                        if ($sesi) {
                            $sesiId = $sesi->id;
                        }
                    } catch (\Throwable $e) {}
                }

                $createdAt = Carbon::parse($examDate)->setTime(now()->hour, now()->minute, now()->second);

                $scanData = [
                    'patient_id'        => $patientId,
                    'accession_number'  => $accessionNo,
                    'patient_name'      => $p['nama'],
                    'gender'            => $gender,
                    'age'               => $p['usia'] ?? 30,
                    'modality'          => $p['modalitas'],
                    'study_description' => !empty($p['indikasi']) ? $p['indikasi'] : $p['modalitas'],
                    'mcu_status'        => 'siap_rontgen',
                    'order_notes'       => !empty($p['catatan']) ? $p['catatan'] : 'Order Sesi MCU Baru di Hyu',
                    'created_at'        => $createdAt,
                ];

                if ($sesiId && Schema::hasColumn('medical_scans', 'sesi_id')) {
                    $scanData['sesi_id'] = $sesiId;
                }

                MedicalScan::create($scanData);
            }
        });

        return response()->json([
            'message' => count($items) . ' pasien berhasil dikirim ke worklist.',
            'jumlah'  => count($items),
        ], 200);
    }

    /**
     * Download file template Excel contoh resmi HYU PACS MCU.
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template MCU');

        // Header
        $headers = [
            'A1' => 'nama_pasien',
            'B1' => 'no_rm_nik',
            'C1' => 'usia',
            'D1' => 'jenis_kelamin',
            'E1' => 'modalitas',
            'F1' => 'indikasi_klinis',
            'G1' => 'catatan',
            'H1' => 'tanggal_pemeriksaan',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('0284C7'); // Cyan primary
        }

        // Baris contoh 1
        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValueExplicit('B2', '3201234567890001', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C2', 30);
        $sheet->setCellValue('D2', 'Laki-laki');
        $sheet->setCellValue('E2', 'Thorax PA');
        $sheet->setCellValue('F2', 'MCU Karyawan PT Semen Gresik');
        $sheet->setCellValue('G2', 'Riwayat alergi kontras');
        $sheet->setCellValueExplicit('H2', date('Y-m-d'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        // Baris contoh 2
        $sheet->setCellValue('A3', 'Siti Aminah');
        $sheet->setCellValueExplicit('B3', '3201234567890002', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C3', 28);
        $sheet->setCellValue('D3', 'Perempuan');
        $sheet->setCellValue('E3', 'Thorax AP');
        $sheet->setCellValue('F3', 'MCU Karyawan PT Semen Gresik');
        $sheet->setCellValue('G3', 'Pemeriksaan rutin tahunan');
        $sheet->setCellValueExplicit('H3', date('Y-m-d'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        // Format kolom B (NIK) & H (Tanggal) sebagai teks murni
        $sheet->getStyle('B:B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('H:H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Auto-size kolom A sampai H
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'template_mcu_pasien.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'mcu_tpl_');

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
