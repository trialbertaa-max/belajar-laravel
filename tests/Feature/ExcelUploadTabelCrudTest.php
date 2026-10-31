<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MedicalScan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArrayExport;

class ExcelUploadTabelCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper untuk membuat file Excel dummy di memory.
     */
    protected function createExcelFile(string $filename, array $headers, array $rows): UploadedFile
    {
        $export = new class([$headers, ...$rows]) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected array $data;
            public function __construct(array $data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        };

        $tempPath = tempnam(sys_get_temp_dir(), 'test_mcu_') . '.xlsx';
        Excel::store($export, basename($tempPath), 'local');
        $fullPath = storage_path('app/' . basename($tempPath));

        return new UploadedFile($fullPath, $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    /**
     * 1. Preview Excel mengembalikan struktur baris staging (id, status, errors).
     */
    public function test_excel_preview_returns_staging_structure(): void
    {
        $file = $this->createExcelFile('staging_test.xlsx', [
            'Nama Lengkap', 'No. RM / NIK', 'Usia', 'Jenis Kelamin', 'Modalitas', 'Indikasi Klinis', 'Catatan'
        ], [
            // Baris 1: valid
            ['Budi Santoso', '3201234567890001', 35, 'L', 'Thorax PA', 'Checkup rutin', 'Alergi dingin'],
            // Baris 2: NIK bukan 16 digit (invalid)
            ['Siti Rahma', '12345', 28, 'P', 'USG Abdomen', 'MCU Tahunan', ''],
            // Baris 3: Modalitas tidak dikenal (invalid)
            ['Ahmad Fauzi', '3201234567890003', 40, 'L', 'MRI Brain Ultra', '', ''],
        ]);

        $response = $this->post(route('worklist.excel.preview'), ['file' => $file]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total',
            'valid',
            'bermasalah',
            'pasien' => [
                '*' => [
                    'id',
                    'index',
                    'nama',
                    'nik',
                    'usia',
                    'jenis_kelamin',
                    'modalitas',
                    'indikasi',
                    'catatan',
                    'dipilih',
                    'status',
                    'errors',
                ]
            ]
        ]);

        $json = $response->json();
        $this->assertEquals(3, $json['total']);
        $this->assertEquals(1, $json['valid']);
        $this->assertEquals(2, $json['bermasalah']);

        // Baris 1: Siap
        $this->assertEquals('Siap', $json['pasien'][0]['status']);
        $this->assertTrue($json['pasien'][0]['dipilih']);
        $this->assertEmpty($json['pasien'][0]['errors']);

        // Baris 2: Perlu dicek karena NIK tidak 16 digit
        $this->assertEquals('Perlu dicek', $json['pasien'][1]['status']);
        $this->assertFalse($json['pasien'][1]['dipilih']);
        $this->assertArrayHasKey('nik', $json['pasien'][1]['errors']);

        // Baris 3: Perlu dicek karena modalitas tidak dikenal
        $this->assertEquals('Perlu dicek', $json['pasien'][2]['status']);
        $this->assertFalse($json['pasien'][2]['dipilih']);
        $this->assertArrayHasKey('modalitas', $json['pasien'][2]['errors']);
    }

    /**
     * 2. Kirim batch valid tersimpan semua dalam 1 transaksi database (HTTP 200).
     */
    public function test_import_batch_valid_saves_all_in_single_transaction(): void
    {
        $initialCount = MedicalScan::count();

        $payload = [
            'pasien' => [
                [
                    'id'            => 'row-1',
                    'nama'          => 'Dewi Sartika',
                    'nik'           => '3271012345670001',
                    'usia'          => 29,
                    'jenis_kelamin' => 'P',
                    'modalitas'     => 'Thorax AP',
                    'indikasi'      => 'MCU Karyawan PT ABC',
                    'catatan'       => 'Puasa 8 jam',
                ],
                [
                    'id'            => 'row-2',
                    'nama'          => 'Ahmad Dahlan',
                    'nik'           => '3271012345670002',
                    'usia'          => 35,
                    'jenis_kelamin' => 'L',
                    'modalitas'     => 'Waters',
                    'indikasi'      => 'Evaluasi SPN',
                    'catatan'       => '',
                ],
                [
                    'id'            => 'row-3',
                    'nama'          => 'Kartini Putri',
                    'nik'           => null, // NIK opsional (dibuatkan server CDC-xxxxx)
                    'usia'          => 24,
                    'jenis_kelamin' => 'P',
                    'modalitas'     => 'USG Pelvic',
                    'indikasi'      => '',
                    'catatan'       => '',
                ]
            ]
        ];

        $response = $this->postJson(route('worklist.import'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => '3 pasien berhasil dikirim ke worklist.',
            'jumlah'  => 3,
        ]);

        $this->assertEquals($initialCount + 3, MedicalScan::count());

        $first = MedicalScan::where('patient_name', 'Dewi Sartika')->first();
        $this->assertNotNull($first);
        $this->assertEquals('3271012345670001', $first->patient_id);
        $this->assertEquals('Thorax AP', $first->modality);
        $this->assertEquals('siap_rontgen', $first->mcu_status);
        $this->assertStringStartsWith('ACC-', $first->accession_number);

        // Pasien tanpa NIK dibuatkan ID otomatis
        $third = MedicalScan::where('patient_name', 'Kartini Putri')->first();
        $this->assertNotNull($third);
        $this->assertStringStartsWith('CDC-', $third->patient_id);
    }

    /**
     * 3. Jika satu baris invalid, seluruh transaksi dibatalkan (rollback) dan error mengacu pada baris yang tepat.
     */
    public function test_import_batch_invalid_row_rolls_back_entire_transaction(): void
    {
        $initialCount = MedicalScan::count();

        $payload = [
            'pasien' => [
                [
                    'id'            => 'row-1',
                    'nama'          => 'Pasien Valid Satu',
                    'nik'           => '3201234567890001',
                    'usia'          => 30,
                    'jenis_kelamin' => 'L',
                    'modalitas'     => 'Thorax PA',
                ],
                [
                    'id'            => 'row-2',
                    'nama'          => 'Pasien Invalid Dua',
                    'nik'           => '12345', // NIK bukan 16 digit angka
                    'usia'          => 25,
                    'jenis_kelamin' => 'P',
                    'modalitas'     => 'Thorax PA',
                ],
            ]
        ];

        $response = $this->postJson(route('worklist.import'), $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'pasien.1.nik'
            ]
        ]);

        // Pastikan tidak ada data yang tersimpan sama sekali (transaksi dibatalkan)
        $this->assertEquals($initialCount, MedicalScan::count());
    }

    /**
     * 4. Deteksi duplikat intra-payload (NIK + Modalitas sama dalam satu kiriman) ditolak dengan HTTP 422.
     */
    public function test_import_batch_intra_payload_duplicate_is_rejected(): void
    {
        $payload = [
            'pasien' => [
                [
                    'id'            => 'row-1',
                    'nama'          => 'Pasien Asli',
                    'nik'           => '3201234567890001',
                    'usia'          => 30,
                    'jenis_kelamin' => 'L',
                    'modalitas'     => 'Thorax PA',
                ],
                [
                    'id'            => 'row-2',
                    'nama'          => 'Pasien Duplikat',
                    'nik'           => '3201234567890001', // NIK sama & modalitas sama
                    'usia'          => 30,
                    'jenis_kelamin' => 'L',
                    'modalitas'     => 'Thorax PA',
                ],
            ]
        ];

        $response = $this->postJson(route('worklist.import'), $payload);

        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('pasien.1.nik', $errors);
        $this->assertStringContainsString('Duplikat', $errors['pasien.1.nik'][0]);
    }

    /**
     * 5. Deteksi duplikat dengan database untuk tanggal/sesi yang sama ditolak dengan HTTP 422.
     */
    public function test_import_batch_database_duplicate_for_same_date_is_rejected(): void
    {
        // Buat data di database pada hari ini
        MedicalScan::create([
            'patient_id'        => '3201234567890001',
            'accession_number'  => 'ACC-' . date('Ymd') . '-999',
            'patient_name'      => 'Pasien Existing',
            'gender'            => 'L',
            'age'               => 30,
            'modality'          => 'Thorax PA',
            'study_description' => 'Thorax PA',
            'mcu_status'        => 'siap_rontgen',
            'created_at'        => now(),
        ]);

        $payload = [
            'pasien' => [
                [
                    'id'                  => 'row-1',
                    'nama'                => 'Pasien Baru',
                    'nik'                 => '3201234567890001',
                    'usia'                => 30,
                    'jenis_kelamin'       => 'L',
                    'modalitas'           => 'Thorax PA',
                    'tanggal_pemeriksaan' => date('Y-m-d'),
                ]
            ]
        ];

        $response = $this->postJson(route('worklist.import'), $payload);

        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('pasien.0.modalitas', $errors);
        $this->assertStringContainsString('sudah ada di worklist', $errors['pasien.0.modalitas'][0]);
    }

    /**
     * 6. Kirim lebih dari 500 baris ditolak dengan HTTP 422.
     */
    public function test_import_batch_exceeding_max_500_rows_is_rejected(): void
    {
        $largeBatch = [];
        for ($i = 0; $i < 501; $i++) {
            $largeBatch[] = [
                'id'            => 'row-' . $i,
                'nama'          => 'Pasien ' . $i,
                'nik'           => sprintf('3201%012d', $i),
                'usia'          => 30,
                'jenis_kelamin' => 'L',
                'modalitas'     => 'Thorax PA',
            ];
        }

        $response = $this->postJson(route('worklist.import'), ['pasien' => $largeBatch]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['pasien']);
    }

    /**
     * 7. Order manual melalui POST /scans tetap berfungsi normal tanpa terganggu.
     */
    public function test_manual_order_submission_still_works_normally(): void
    {
        $response = $this->post(route('scans.store'), [
            'patient_name'      => 'Budi Hartono Manual',
            'patient_id'        => 'RM-MANUAL-001',
            'age'               => 42,
            'gender'            => 'L',
            'modality'          => 'Thorax PA',
            'study_description' => 'Pemeriksaan Rutin',
            'order_notes'       => 'Pasien batuk 3 hari',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('scans.index'));

        $scan = MedicalScan::where('patient_name', 'Budi Hartono Manual')->first();
        $this->assertNotNull($scan);
        $this->assertEquals('RM-MANUAL-001', $scan->patient_id);
        $this->assertEquals('Thorax PA', $scan->modality);
        $this->assertEquals('siap_rontgen', $scan->mcu_status);
    }
}
