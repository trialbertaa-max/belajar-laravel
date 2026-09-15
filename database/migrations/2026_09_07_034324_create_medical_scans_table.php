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
        Schema::create('medical_scans', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name'); // Nama Pasien
            $table->string('modality');     // Contoh: MRI, CT Scan, X-Ray
            $table->string('scan_image_path')->nullable(); // Lokasi file foto scan
            $table->text('diagnosis_notes')->nullable();   // Catatan diagnosa dokter
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_scans');
    }
};
