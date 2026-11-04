<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MedicalScan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityHardeningTest extends TestCase
{
    /**
     * Pastikan HTTP Security Headers diterapkan pada setiap response.
     */
    public function test_security_headers_are_present_in_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Pastikan parameter engine pada denoising menolak karakter berbahaya (Command Injection).
     */
    public function test_denoise_rejects_command_injection_characters(): void
    {
        $scan = MedicalScan::create([
            'patient_id'         => 'TEST-RCE-01',
            'patient_name'       => 'Test RCE Subject',
            'modality'           => 'Thorax PA',
            'mcu_status'         => 'rontgen_selesai',
            'preview_image_path' => 'scan-assets/raw_toraks.jpg',
            'scan_image_path'    => 'scan-assets/raw_toraks.jpg',
            'dicom_raw_path'     => 'sample_toraks.dcm',
        ]);

        // Karakter injeksi shell: & ; | && `
        $response = $this->postJson(route('scans.denoise', $scan->id), [
            'engine' => 'bilateral & calc.exe',
            'viewport' => 1
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['engine']);
    }

    /**
     * Pastikan endpoint konfigurasi DICOM menolak newlines (CRLF) dan karakter injeksi .env.
     */
    public function test_update_dicom_node_rejects_newline_injection(): void
    {
        $response = $this->postJson(route('settings.dicom-node'), [
            'ae_title'        => 'VALID_AE',
            'port'            => 4242,
            'modality_source' => "FUJI_FDR\nAPP_KEY=malicious_injected_key",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['modality_source']);
    }

    /**
     * Pastikan update DICOM node menerima nilai yang valid.
     */
    public function test_update_dicom_node_accepts_valid_config(): void
    {
        $response = $this->postJson(route('settings.dicom-node'), [
            'ae_title'        => 'HYU_PACS_TEST',
            'port'            => 4242,
            'modality_source' => 'FUJIFILM (CR) & MINDRAY (US)',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 'success',
            'ae_title' => 'HYU_PACS_TEST',
            'port'     => 4242,
        ]);
    }

    /**
     * Pastikan endpoint download DICOM menolak path traversal.
     */
    public function test_download_dicom_rejects_directory_traversal(): void
    {
        $scan = MedicalScan::create([
            'patient_id'         => 'TEST-TRAVERSAL',
            'patient_name'       => 'Test Traversal',
            'modality'           => 'Thorax PA',
            'mcu_status'         => 'rontgen_selesai',
            'preview_image_path' => 'scan-assets/raw_toraks.jpg',
            'dicom_raw_path'     => '../../../../.env',
        ]);

        $response = $this->get(route('scans.download-dcm', $scan->id));

        $response->assertStatus(403);
    }

    /**
     * Pastikan input diagnosa divalidasi dan tag HTML berbahaya dibersihkan.
     */
    public function test_save_diagnosis_validates_and_sanitizes_input(): void
    {
        $scan = MedicalScan::create([
            'patient_id'         => 'TEST-DIAG-01',
            'patient_name'       => 'Test Diagnosis Patient',
            'modality'           => 'Thorax PA',
            'mcu_status'         => 'rontgen_selesai',
        ]);

        $response = $this->postJson(route('scans.diagnosis', $scan->id), [
            'diagnosis_notes' => '<b>Kondisi paru normal</b><script>alert(1)</script>',
            'doctor_name'     => 'dr. Budi Sp.Rad',
        ]);

        $response->assertStatus(200);

        $scan->refresh();
        $this->assertEquals('Kondisi paru normalalert(1)', $scan->diagnosis_notes);
        $this->assertEquals('selesai_diagnosa', $scan->mcu_status);
    }
}

