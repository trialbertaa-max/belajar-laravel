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
            $table->boolean('is_cine')->default(false)->after('sop_instance_uid');
            $table->integer('frame_count')->default(1)->after('is_cine');
            $table->float('cine_fps')->nullable()->default(15.0)->after('frame_count');
            $table->string('cine_video_path')->nullable()->after('cine_fps');
            $table->string('cine_frames_dir')->nullable()->after('cine_video_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_scans', function (Blueprint $table) {
            $table->dropColumn([
                'is_cine',
                'frame_count',
                'cine_fps',
                'cine_video_path',
                'cine_frames_dir'
            ]);
        });
    }
};
