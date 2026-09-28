<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalScan extends Model
{
    protected $fillable = [
        'patient_id',
        'accession_number',
        'patient_name',
        'gender',
        'birth_date',
        'age',
        'modality',
        'study_description',
        'scan_image_path',
        'diagnosis_notes',
        'mcu_status',
        'station_name',
        'study_instance_uid',
        'sop_instance_uid',
        'dicom_raw_path',
        'preview_image_path',
        'denoised_image_path',
        'doctor_name',
        'order_notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // Helper untuk status badge di antarmuka web
    public function getStatusLabelAttribute(): string
    {
        return match ($this->mcu_status) {
            'antrian_pendaftaran' => 'Antrian Pendaftaran',
            'siap_rontgen'        => 'Siap di Ruang Rontgen',
            'rontgen_selesai'     => 'Citra Masuk (Siap Baca)',
            'selesai_diagnosa'    => 'Selesai Diagnosa',
            default               => 'Dalam Proses'
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->mcu_status) {
            'antrian_pendaftaran' => 'badge-pending',
            'siap_rontgen'        => 'badge-waiting',
            'rontgen_selesai'     => 'badge-ready',
            'selesai_diagnosa'    => 'badge-done',
            default               => 'badge-waiting'
        };
    }
}

