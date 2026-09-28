<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medical_scans', function (Blueprint $table) {
            $table->string('patient_id')->nullable()->after('id'); // MRN / No. Rekam Medis / NIK
            $table->string('accession_number')->nullable()->after('patient_id'); // Order ID untuk Worklist
            $table->string('gender', 10)->nullable()->after('patient_name'); // L / P
            $table->date('birth_date')->nullable()->after('gender');
            $table->integer('age')->nullable()->after('birth_date');
            $table->string('study_description')->nullable()->after('modality'); // Deskripsi Pemeriksaan
            $table->string('mcu_status')->default('siap_rontgen')->after('diagnosis_notes'); // Status Pos MCU
            $table->string('station_name')->nullable()->after('mcu_status'); // Nama Alat, misal: FUJIFILM_FDR
            $table->string('study_instance_uid')->nullable()->after('station_name');
            $table->string('sop_instance_uid')->nullable()->after('study_instance_uid');
            $table->string('dicom_raw_path')->nullable()->after('sop_instance_uid');
            $table->string('preview_image_path')->nullable()->after('dicom_raw_path');
            $table->string('denoised_image_path')->nullable()->after('preview_image_path');
            $table->string('doctor_name')->nullable()->after('denoised_image_path');
            $table->text('order_notes')->nullable()->after('doctor_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_scans', function (Blueprint $table) {
            $table->dropColumn([
                'patient_id',
                'accession_number',
                'gender',
                'birth_date',
                'age',
                'study_description',
                'mcu_status',
                'station_name',
                'study_instance_uid',
                'sop_instance_uid',
                'dicom_raw_path',
                'preview_image_path',
                'denoised_image_path',
                'doctor_name',
                'order_notes'
            ]);
        });
    }
};
