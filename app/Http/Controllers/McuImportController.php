<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\McuPatientsImport;
use App\Services\McuRowNormalizer;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class McuImportController extends Controller
{
    /**
     * Batas maksimum baris yang bisa diimport (tidak termasuk header).
     */
    protected const MAX_ROWS = 200;

    /**
     * Preview: parse file Excel dan kembalikan JSON untuk auto-fill form.
     * Endpoint ini TIDAK menyimpan ke database.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $file = $request->file('file');

        // Tolak .xlsm (bisa mengandung makro)
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext === 'xlsm') {
            return response()->json([
                'message' => 'File .xlsm (dengan makro) tidak diizinkan. Gunakan format .xlsx, .xls, atau .csv.',
            ], 422);
        }

        try {
            $import = new McuPatientsImport();
            Excel::import($import, $file);
            $collection = $import->rows;
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal membaca file Excel. Pastikan format file benar dan tidak rusak.',
            ], 422);
        }

        if ($collection->isEmpty()) {
            return response()->json([
                'message' => 'File Excel kosong atau tidak memiliki data di bawah header.',
            ], 422);
        }

        if ($collection->count() > self::MAX_ROWS) {
            return response()->json([
                'message' => 'Jumlah baris melebihi batas maksimum (' . self::MAX_ROWS . ' baris). Silakan bagi data ke beberapa file.',
            ], 422);
        }

        // Ambil header dari key pertama
        $rawHeaders = $collection->first()->keys()->toArray();
        $headerMapping = McuRowNormalizer::normalizeHeaders($rawHeaders);

        // Periksa header wajib
        $mappedFields = array_values($headerMapping);
        $missingRequired = [];
        if (!in_array('nama_lengkap', $mappedFields)) {
            $missingRequired[] = 'nama_lengkap';
        }
        if (!in_array('modalitas', $mappedFields)) {
            $missingRequired[] = 'modalitas';
        }

        if (!empty($missingRequired)) {
            return response()->json([
                'message' => 'Header wajib tidak ditemukan di file Excel: ' . implode(', ', $missingRequired) . '. Pastikan baris pertama file berisi nama kolom yang sesuai.',
            ], 422);
        }

        $rows = [];
        $validCount = 0;
        $invalidCount = 0;

        foreach ($collection as $idx => $rawRow) {
            // Map dari raw header ke field internal
            $mappedRow = [];
            foreach ($headerMapping as $rawKey => $fieldName) {
                $value = $rawRow[$rawKey] ?? null;
                // Kolom kosong → null
                if ($value === '' || $value === null) {
                    $value = null;
                }
                $mappedRow[$fieldName] = $value;
            }

            // Skip baris yang sepenuhnya kosong
            $allNull = true;
            foreach ($mappedRow as $v) {
                if ($v !== null) {
                    $allNull = false;
                    break;
                }
            }
            if ($allNull) {
                continue;
            }

            $result = McuRowNormalizer::normalizeRow($mappedRow);

            $excelRowNumber = $idx + 2; // +1 header, +1 zero-index

            if (empty($result['errors'])) {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $rows[] = [
                'index'  => $excelRowNumber,
                'data'   => $result['data'],
                'errors' => $result['errors'],
            ];
        }

        return response()->json([
            'rows'    => $rows,
            'summary' => [
                'total'   => count($rows),
                'valid'   => $validCount,
                'invalid' => $invalidCount,
            ],
        ]);
    }

    /**
     * Download file template Excel contoh.
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template MCU');

        // Header
        $headers = [
            'A1' => 'nama_lengkap',
            'B1' => 'mrn_nik',
            'C1' => 'usia',
            'D1' => 'jenis_kelamin',
            'E1' => 'modalitas',
            'F1' => 'indikasi_klinis',
            'G1' => 'catatan_petugas',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Baris contoh
        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValueExplicit('B2', '3201234567890001', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C2', 30);
        $sheet->setCellValue('D2', 'L');
        $sheet->setCellValue('E2', 'Thorax PA');
        $sheet->setCellValue('F2', 'MCU Karyawan PT Semen Gresik');
        $sheet->setCellValue('G2', 'Riwayat alergi kontras');

        // Format kolom B (NIK) sebagai teks
        $sheet->getStyle('B:B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Auto-size kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'template_mcu_upload.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'mcu_tpl_');

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
