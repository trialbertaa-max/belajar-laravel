<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class McuImportTest extends TestCase
{
    /**
     * Helper untuk membuat UploadedFile Excel secara in-memory.
     */
    protected function createExcelFile(string $filename, array $headers, array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tulis header (baris 1)
        $colIdx = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($colIdx, 1, $header);
            $colIdx++;
        }

        // Tulis data (mulai baris 2)
        $rowIdx = 2;
        foreach ($rows as $row) {
            $colIdx = 1;
            foreach ($row as $val) {
                if (is_string($val) && preg_match('/^\d{10,}$/', $val)) {
                    $sheet->setCellValueExplicitByColumnAndRow($colIdx, $rowIdx, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueByColumnAndRow($colIdx, $rowIdx, $val);
                }
                $colIdx++;
            }
            $rowIdx++;
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    /**
     * Test download template Excel berfungsi.
     */
    public function test_template_download_returns_excel_file(): void
    {
        $response = $this->get(route('mcu.import.template'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test upload file valid 1 baris → JSON sesuai, errors kosong.
     */
    public function test_upload_single_valid_row_returns_success(): void
    {
        $file = $this->createExcelFile('single_valid.xlsx', [
            'nama_lengkap', 'mrn_nik', 'usia', 'jenis_kelamin', 'modalitas', 'indikasi_klinis', 'catatan_petugas'
        ], [
            ['Budi Santoso', '3201234567890001', 35, 'L', 'Thorax PA', 'Checkup rutin', 'Alergi dingin']
        ]);

        $response = $this->post(route('mcu.import.preview'), ['file' => $file]);

        $response->assertStatus(200);
        $response->assertJson([
            'summary' => [
                'total'   => 1,
                'valid'   => 1,
                'invalid' => 0,
            ],
            'rows' => [
                [
                    'index' => 2,
                    'data'  => [
                        'nama_lengkap'    => 'Budi Santoso',
                        'mrn_nik'         => '3201234567890001',
                        'usia'            => 35,
                        'jenis_kelamin'   => 'L',
                        'modalitas'       => 'Thorax PA',
                        'indikasi_klinis' => 'Checkup rutin',
                        'catatan_petugas' => 'Alergi dingin',
                    ],
                    'errors' => [],
                ]
            ]
        ]);
    }

    /**
     * Test upload banyak baris campuran valid dan invalid.
     */
    public function test_upload_mixed_rows_summary(): void
    {
        $file = $this->createExcelFile('mixed.xlsx', [
            'nama_lengkap', 'mrn_nik', 'usia', 'jenis_kelamin', 'modalitas'
        ], [
            // Baris 1: valid
            ['Siti Rahma', '123456', 28, 'P', 'USG Abdomen'],
            // Baris 2: nama kosong (invalid)
            ['', '123457', 30, 'L', 'Thorax PA'],
            // Baris 3: modalitas tidak valid
            ['Ahmad Yani', '123458', 40, 'L', 'MRI Brain Ultra'],
        ]);

        $response = $this->post(route('mcu.import.preview'), ['file' => $file]);

        $response->assertStatus(200);
        $response->assertJson([
            'summary' => [
                'total'   => 3,
                'valid'   => 1,
                'invalid' => 2,
            ],
        ]);

        $json = $response->json();
        $this->assertNotEmpty($json['rows'][1]['errors']);
        $this->assertNotEmpty($json['rows'][2]['errors']);
    }

    /**
     * Test header alias (nama, nik, jk) terbaca dengan benar.
     */
    public function test_header_aliases_work_correctly(): void
    {
        $file = $this->createExcelFile('aliases.xlsx', [
            'Nama', 'NIK', 'Umur', 'Gender', 'Modality'
        ], [
            ['Dewi Sartika', '3271012345670002', 45, 'Perempuan', 'Thorax AP']
        ]);

        $response = $this->post(route('mcu.import.preview'), ['file' => $file]);

        $response->assertStatus(200);
        $data = $response->json('rows.0.data');
        $this->assertEquals('Dewi Sartika', $data['nama_lengkap']);
        $this->assertEquals('3271012345670002', $data['mrn_nik']);
        $this->assertEquals(45, $data['usia']);
        $this->assertEquals('P', $data['jenis_kelamin']);
        $this->assertEquals('Thorax AP', $data['modalitas']);
    }

    /**
     * Test NIK 16 digit tetap utuh sebagai string dan tidak menjadi scientific notation.
     */
    public function test_nik_16_digits_preserves_string(): void
    {
        $file = $this->createExcelFile('nik_test.xlsx', [
            'nama_lengkap', 'mrn_nik', 'modalitas'
        ], [
            ['Joko Widodo', '3301010101010001', 'Waters']
        ]);

        $response = $this->post(route('mcu.import.preview'), ['file' => $file]);

        $response->assertStatus(200);
        $this->assertSame('3301010101010001', $response->json('rows.0.data.mrn_nik'));
    }

    /**
     * Test modalitas tidak dikenali ditandai error.
     */
    public function test_unrecognized_modality_returns_error(): void
    {
        $file = $this->createExcelFile('bad_modality.xlsx', [
            'nama_lengkap', 'modalitas'
        ], [
            ['Budi', 'X-Ray Gigi 3D']
        ]);

        $response = $this->post(route('mcu.import.preview'), ['file' => $file]);

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('summary.invalid'));
        $this->assertStringContainsString('Modalitas', $response->json('rows.0.errors.0'));
    }

    /**
     * Test file non-Excel ditolak (422).
     */
    public function test_non_excel_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->post(route('mcu.import.preview'), ['file' => $file]);

        $response->assertStatus(302); // Redirect back with validation error (standard Laravel form request)
        $response->assertSessionHasErrors('file');
    }

    /**
     * Test file .xlsm (macro-enabled) ditolak (422).
     */
    public function test_xlsm_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('macro.xlsm', 100);

        $response = $this->postJson(route('mcu.import.preview'), ['file' => $file]);

        $response->assertStatus(422);
    }
}
