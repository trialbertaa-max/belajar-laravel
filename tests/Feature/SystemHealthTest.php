<?php

namespace Tests\Feature;

use Tests\TestCase;

class SystemHealthTest extends TestCase
{
    /**
     * Start Screen renders successfully with HTTP 200.
     */
    public function test_start_screen_renders_successfully(): void
    {
        $response = $this->get(route('system.start'));

        $response->assertStatus(200);
        $response->assertSee('System Readiness Center');
        $response->assertSee('Hyu Standalone DICOM PACS Server');
        $response->assertSee('Python & AI Medical Engine', false);
        $response->assertSee('Buka MCU Central & Viewer', false);
    }

    /**
     * Diagnostic API returns complete structured JSON.
     */
    public function test_system_status_api_returns_complete_json(): void
    {
        $response = $this->get(route('system.status'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'readiness_percent',
            'all_ready',
            'execution_ms',
            'database' => [
                'connected',
                'driver',
                'tables_count',
                'scans_count',
            ],
            'storage' => [
                'assets_dir_exists',
                'assets_dir_writable',
                'scans_dir_exists',
                'sample_toraks',
                'sample_kepala',
                'sample_usg',
            ],
            'python' => [
                'available',
                'weights_bone',
            ],
            'dicom' => [
                'running',
                'port',
                'host',
                'ae_title',
            ],
        ]);
    }

    /**
     * Toggle DICOM rejects invalid actions with HTTP 422.
     */
    public function test_dicom_toggle_rejects_invalid_action(): void
    {
        $response = $this->postJson(route('system.dicom.toggle'), [
            'action' => 'invalid_action_name',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['action']);
    }

    /**
     * Toggle DICOM accepts valid actions (e.g. stop).
     */
    public function test_dicom_toggle_handles_stop_action(): void
    {
        $response = $this->postJson(route('system.dicom.toggle'), [
            'action' => 'stop',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'action' => 'stop',
            'running' => false,
        ]);
    }

    /**
     * Ping DICOM endpoint returns structured status code.
     */
    public function test_dicom_ping_endpoint_returns_json(): void
    {
        $response = $this->postJson(route('system.dicom.ping'));

        // If server is offline, returns 503 with error structure
        // If server is online, returns 200 with success structure
        $this->assertContains($response->status(), [200, 503]);
        $response->assertJsonStructure([
            'status',
            'title',
            'message',
        ]);
    }
}
