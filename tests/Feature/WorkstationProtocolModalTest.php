<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\MedicalScan;

class WorkstationProtocolModalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. View /scans renders the workstation protocol modal and interactive skeleton elements.
     */
    public function test_scans_page_renders_workstation_protocol_modal_and_skeleton(): void
    {
        $response = $this->get(route('scans.index'));

        $response->assertStatus(200);

        // Modal container
        $response->assertSee('id="modalProtocolPicker"', false);
        $response->assertSee('openProtocolPickerModal()', false);

        // Skeleton SVG and glowing layer images
        $response->assertSee('id="skeletonSvg"', false);
        $response->assertSee('workstation_skeleton.jpg', false);
        $response->assertSee('id="glow-head"', false);
        $response->assertSee('id="glow-spine"', false);
        $response->assertSee('id="glow-thorax"', false);
        $response->assertSee('id="glow-pelvis"', false);
        $response->assertSee('id="glow-right_arm"', false);
        $response->assertSee('id="glow-left_arm"', false);
        $response->assertSee('id="glow-right_leg"', false);
        $response->assertSee('id="glow-left_leg"', false);

        // Interactive anatomical hitboxes
        $response->assertSee('id="skel-head"', false);
        $response->assertSee('id="skel-right-arm"', false);
        $response->assertSee('id="skel-left-arm"', false);
        $response->assertSee('id="skel-thorax"', false);
        $response->assertSee('id="skel-pelvis"', false);
        $response->assertSee('id="skel-right-leg"', false);
        $response->assertSee('id="skel-left-leg"', false);
        $response->assertSee('id="skel-spine"', false);

        // Protocol UI components
        $response->assertSee('id="protocolGridContainer"', false);
        $response->assertSee('id="protocolSearchInput"', false);
        $response->assertSee('id="tabOftenUsed"', false);
        $response->assertSee('id="tabAdvanced"', false);
        $response->assertSee('id="pickerSelectedName"', false);
        $response->assertSee('id="selectedModalityInput"', false);
    }

    /**
     * 2. Manual order with selected workstation protocol successfully saves to database.
     */
    public function test_manual_order_with_chosen_skeleton_protocol_saves_successfully(): void
    {
        $response = $this->post(route('scans.store'), [
            'patient_name'      => 'Raden Arya',
            'patient_id'        => 'RM-SKELETON-001',
            'age'               => 35,
            'gender'            => 'L',
            'modality'          => 'Skull AP Axial',
            'study_description' => 'Evaluasi Cranium',
            'order_notes'       => 'Pemeriksaan protokol Head/Skull',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('scans.index'));

        $scan = MedicalScan::where('patient_id', 'RM-SKELETON-001')->first();
        $this->assertNotNull($scan);
        $this->assertEquals('Raden Arya', $scan->patient_name);
        $this->assertEquals('Skull AP Axial', $scan->modality);
        $this->assertEquals('siap_rontgen', $scan->mcu_status);
    }
}

