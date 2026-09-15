<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalScan extends Model
{
    protected $fillable = [
        'patient_name',
        'modality',
        'scan_image_path',
        'diagnosis_notes'
    ];
}

