<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalScan;

class MedicalScanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                'diagnosis_notes'   => 'Cor dan pulmo dalam batas normal, sinus costophrenicus tajam, tidak tampak infiltrat aktif.',
                'created_at'        => '2026-09-21 08:30:00',
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
                'diagnosis_notes'   => 'Hepar dan vesica fellea dalam batas normal, tidak tampak batu empedu atau massa abnormal.',
                'created_at'        => '2026-09-21 09:15:00',
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
                'order_notes'       => 'Order otomatis dari Sesi MCU Hyu, menunggu antrian ekspos rontgen.',
                'created_at'        => '2026-09-21 10:00:00',
            ]);
        }
    }
}

