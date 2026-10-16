import xml.etree.ElementTree as ET

# Generate valid Draw.io XML
xml_content = """<mxfile host="app.diagrams.net" modified="2026-10-12T08:00:00.000Z" agent="5.0" version="24.0.0" type="device">
  <diagram id="hyu_pacs_architecture" name="Hyu PACS Architecture Diagram">
    <mxGraphModel dx="1422" dy="794" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" background="#ffffff" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        
        <!-- TITLE BLOCK -->
        <mxCell id="title" value="&lt;b style=&quot;font-size: 16px;&quot;&gt;DIAGRAM ARSITEKTUR &amp;amp; ALUR KERJA SISTEM HYU PACS WORKSTATION&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 11px; color: #475569;&quot;&gt;Arsitektur Integrasi Modalitas Radiografi Digital, Core Backend EMR, Engine AI, dan Workstation Diagnostik Klinis&lt;/i&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="60" y="25" width="1050" height="40" as="geometry" />
        </mxCell>
        <mxCell id="title_line" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#64748b;strokeWidth=1.5;" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="60" y="75" as="sourcePoint" />
            <mxPoint x="1110" y="75" as="targetPoint" />
          </mxGeometry>
        </mxCell>

        <!-- ==================== TIER 1 ==================== -->
        <mxCell id="tier1_container" value="&lt;b&gt;TIER 1: LAPISAN AKUISISI CITRA &amp;amp; INGESTI TRANSMISI MEDIS (HARDWARE &amp;amp; DICOM SCP)&lt;/b&gt;" style="swimlane;whiteSpace=wrap;html=1;startSize=26;rounded=0;arcSize=0;fillColor=#f8fafc;strokeColor=#94a3b8;fontFamily=Times New Roman;fontSize=11;fontColor=#0f172a;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="60" y="90" width="1050" height="180" as="geometry" />
        </mxCell>

        <!-- Card 0: Modalitas DR -->
        <mxCell id="card_mod0" value="&lt;b style=&quot;font-size: 12px;&quot;&gt;MODALITAS RADIOGRAFI DIGITAL (DR)&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 10px; color: #334155;&quot;&gt;Konsol Radiografi Digital (Klinik CDC Surabaya)&lt;/i&gt;&lt;hr style=&quot;margin: 4px 0;&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 11px; line-height: 1.3;&quot;&gt;• &lt;b&gt;Detektor:&lt;/b&gt; Flat-Panel Detector (FPD) Standar Medis&lt;br&gt;• &lt;b&gt;Format Citra:&lt;/b&gt; DICOM 3.0 (Koneksi Kabel LAN TCP/IP)&lt;br&gt;• &lt;b&gt;SOP Class:&lt;/b&gt; Computed Radiography (CR) / Digital X-Ray (DX)&lt;br&gt;• &lt;b&gt;Matriks Sensor:&lt;/b&gt; 2999 × 2999 Piksel, 16-bit Grayscale&lt;br&gt;• &lt;b&gt;Tag Kalibrasi:&lt;/b&gt; (0028,0030) ImagerPixelSpacing = 0.140 mm/px&lt;/div&gt;" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#1e293b;strokeWidth=1.5;fontFamily=Times New Roman;align=center;verticalAlign=top;spacingTop=6;" vertex="1" parent="tier1_container">
          <mxGeometry x="20" y="38" width="440" height="125" as="geometry" />
        </mxCell>

        <!-- Card 1: Modul 1 DICOM Listener -->
        <mxCell id="card_mod1" value="&lt;b style=&quot;font-size: 12px;&quot;&gt;MODUL 1: DICOM LISTENER (C-STORE SCP)&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 10px; color: #0369a1;&quot;&gt;Daemon Service Latar Belakang (pynetdicom &amp;amp; pydicom)&lt;/i&gt;&lt;hr style=&quot;margin: 4px 0;&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 11px; line-height: 1.3;&quot;&gt;• &lt;b&gt;Layanan Medis:&lt;/b&gt; C-STORE SCP Standar NEMA PS 3.4&lt;br&gt;• &lt;b&gt;Parameter:&lt;/b&gt; Port Jaringan 4242 | AE Title: HYU_PACS_SERVER&lt;br&gt;• &lt;b&gt;Validasi:&lt;/b&gt; Verifikasi SOP Class UID &amp;amp; Transfer Syntax Medis&lt;br&gt;• &lt;b&gt;Ekstraksi Tag:&lt;/b&gt; Pixel Spacing, Rescale Slope/Intercept, Window W/L&lt;br&gt;• &lt;b&gt;Repositori:&lt;/b&gt; Pengarsipan Berkas Mentah .dcm ke Direktori Server&lt;/div&gt;" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#0369a1;strokeWidth=1.5;fontFamily=Times New Roman;align=center;verticalAlign=top;spacingTop=6;" vertex="1" parent="tier1_container">
          <mxGeometry x="590" y="38" width="440" height="125" as="geometry" />
        </mxCell>

        <!-- Connector Card 0 -> Card 1 -->
        <mxCell id="conn_0_1" value="&lt;b style=&quot;font-size: 10px;&quot;&gt;DICOM C-STORE SCP&lt;/b&gt;&lt;br&gt;(Port 4242 LAN)" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;strokeColor=#0284c7;strokeWidth=2;fontFamily=Times New Roman;fontColor=#0369a1;labelBackgroundColor=#e0f2fe;" edge="1" parent="tier1_container" source="card_mod0" target="card_mod1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- ==================== TIER 2 ==================== -->
        <mxCell id="tier2_container" value="&lt;b&gt;TIER 2: LAPISAN PENGELOLAAN DATA EMR &amp;amp; KOMPUTASI RESTORASI CITRA AI&lt;/b&gt;" style="swimlane;whiteSpace=wrap;html=1;startSize=26;rounded=0;arcSize=0;fillColor=#f8fafc;strokeColor=#94a3b8;fontFamily=Times New Roman;fontSize=11;fontColor=#0f172a;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="60" y="315" width="1050" height="195" as="geometry" />
        </mxCell>

        <!-- Card 3: Modul 3 Engine Restorasi AI -->
        <mxCell id="card_mod3" value="&lt;b style=&quot;font-size: 12px;&quot;&gt;MODUL 3: ENGINE RESTORASI CITRA &amp;amp; AI&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 10px; color: #065f46;&quot;&gt;Multi-Algorithm Restoration Engine (Python CLI &amp;amp; API)&lt;/i&gt;&lt;hr style=&quot;margin: 4px 0;&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 11px; line-height: 1.3;&quot;&gt;• &lt;b&gt;Bilateral Filter:&lt;/b&gt; Domain Spasial (σ_s) &amp;amp; Range (σ_r) Preservasi Tepi Batas&lt;br&gt;• &lt;b&gt;Non-Local Means (NLM):&lt;/b&gt; Redundansi Patch Citra (Self-Exemplar)&lt;br&gt;• &lt;b&gt;Deep Residual CNN (DnCNN):&lt;/b&gt; 17-Lapisan Konvolusi (Conv+BN+ReLU)&lt;br&gt;  Residual Noise Learning: Rekonstruksi Citra Bersih x = y - R(y)&lt;br&gt;• &lt;b&gt;Telemetri Real-Time:&lt;/b&gt; PSNR (dB), SSIM, SNR Gain (dB), Latensi (ms)&lt;/div&gt;" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#047857;strokeWidth=1.5;fontFamily=Times New Roman;align=center;verticalAlign=top;spacingTop=6;" vertex="1" parent="tier2_container">
          <mxGeometry x="20" y="38" width="450" height="140" as="geometry" />
        </mxCell>

        <!-- Card 2: Modul 2 Laravel Backend -->
        <mxCell id="card_mod2" value="&lt;b style=&quot;font-size: 12px;&quot;&gt;MODUL 2: BACKEND &amp;amp; BASIS DATA RELASIONAL&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 10px; color: #991b1b;&quot;&gt;Framework Web EMR &amp;amp; Manajemen Pasien (Laravel 11)&lt;/i&gt;&lt;hr style=&quot;margin: 4px 0;&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 11px; line-height: 1.3;&quot;&gt;• &lt;b&gt;Basis Data Pasien MCU:&lt;/b&gt; Nama, NIK, No. RM, Usia, Gender, Study UID&lt;br&gt;• &lt;b&gt;Keamanan Sistem:&lt;/b&gt; Otentikasi &amp;amp; Otorisasi Pengguna (Role-Based)&lt;br&gt;• &lt;b&gt;Integrasi Alur Kerja:&lt;/b&gt; Sinkronisasi Jadwal Modality Worklist (MWL)&lt;br&gt;• &lt;b&gt;Layanan Distribusi:&lt;/b&gt; RESTful API Endpoint &amp;amp; DICOM Image Stream&lt;br&gt;• &lt;b&gt;Controller Logik:&lt;/b&gt; Manajemen &amp;amp; Penyimpanan Hasil Ekspertise Dokter&lt;/div&gt;" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#b91c1c;strokeWidth=1.5;fontFamily=Times New Roman;align=center;verticalAlign=top;spacingTop=6;" vertex="1" parent="tier2_container">
          <mxGeometry x="580" y="38" width="450" height="140" as="geometry" />
        </mxCell>

        <!-- Connector Card 3 <-> Card 2 -->
        <mxCell id="conn_3_2" value="&lt;b style=&quot;font-size: 10px;&quot;&gt;Request Filter &amp;amp;&lt;/b&gt;&lt;br&gt;Citra + Telemetri" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;startArrow=classic;startFill=1;strokeColor=#047857;strokeWidth=2;fontFamily=Times New Roman;fontColor=#065f46;labelBackgroundColor=#ecfdf5;" edge="1" parent="tier2_container" source="card_mod3" target="card_mod2">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- ==================== TIER 3 ==================== -->
        <mxCell id="tier3_container" value="&lt;b&gt;TIER 3: LAPISAN WORKSTATION DIAGNOSTIK KLINIS &amp;amp; PELAPORAN MEDIS&lt;/b&gt;" style="swimlane;whiteSpace=wrap;html=1;startSize=26;rounded=0;arcSize=0;fillColor=#f8fafc;strokeColor=#94a3b8;fontFamily=Times New Roman;fontSize=11;fontColor=#0f172a;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="60" y="555" width="1050" height="235" as="geometry" />
        </mxCell>

        <!-- Card 4: Web Viewer Workstation -->
        <mxCell id="card_mod4" value="&lt;b style=&quot;font-size: 12px;&quot;&gt;MODUL 4: WEB DIAGNOSTIC WORKSTATION (HYU PACS VIEWER)&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 10px; color: #312e81;&quot;&gt;Zero-Footprint Clinical Viewer (HTML5 + CSS + Custom Modern JavaScript)&lt;/i&gt;" style="swimlane;whiteSpace=wrap;html=1;startSize=34;rounded=0;arcSize=0;fillColor=#ffffff;strokeColor=#3730a3;strokeWidth=1.5;fontFamily=Times New Roman;fontColor=#0f172a;" vertex="1" parent="tier3_container">
          <mxGeometry x="20" y="38" width="650" height="180" as="geometry" />
        </mxCell>

        <!-- Subcard 4A: SVG Stage Layer -->
        <mxCell id="subcard_4a" value="&lt;b style=&quot;font-size: 11px; color: #312e81;&quot;&gt;A. SVG Stage Layer (.vp-stage-layer)&lt;/b&gt;&lt;hr style=&quot;margin: 3px 0;&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 10.5px; line-height: 1.25;&quot;&gt;• &lt;b&gt;Inverse Affine Mapping:&lt;/b&gt; Matriks CTM⁻¹&lt;br&gt;• &lt;b&gt;Lockstep Anchoring:&lt;/b&gt; Garis ukur terkunci&lt;br&gt;  mutlak saat Zoom-in/out &amp;amp; Pan/drag&lt;br&gt;• &lt;b&gt;Manipulasi Klinis:&lt;/b&gt;&lt;br&gt;  - Rotasi (90°, 180°, 270°) &amp;amp; Flip (H/V)&lt;br&gt;  - Windowing Kontras/Brightness (W/L)&lt;br&gt;• &lt;b&gt;Komparasi:&lt;/b&gt; Split-Curtain Comparison Slider&lt;br&gt;• &lt;b&gt;Difference Map:&lt;/b&gt; |I_noisy - I_clean|&lt;/div&gt;" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#f8fafc;strokeColor=#cbd5e1;fontFamily=Times New Roman;align=center;verticalAlign=top;spacingTop=4;" vertex="1" parent="card_mod4">
          <mxGeometry x="15" y="42" width="295" height="126" as="geometry" />
        </mxCell>

        <!-- Subcard 4B: Kaliper CTR -->
        <mxCell id="subcard_4b" value="&lt;b style=&quot;font-size: 11px; color: #065f46;&quot;&gt;B. Kaliper Biometri CTR (Danzer, 1919)&lt;/b&gt;&lt;hr style=&quot;margin: 3px 0;&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 10.5px; line-height: 1.25;&quot;&gt;• &lt;b&gt;Kalibrasi Fisik:&lt;/b&gt; Tag (0028,0030) Spacing (mm)&lt;br&gt;• &lt;b&gt;Penarikan 2 Langkah:&lt;/b&gt;&lt;br&gt;  - Step 1: Garis Transversal Jantung A+B&lt;br&gt;  - Step 2: Diameter Transversal Toraks C&lt;br&gt;• &lt;b&gt;Formula CTR:&lt;/b&gt; ((A+B) / C) × 100%&lt;br&gt;• &lt;b&gt;Klasifikasi Status Diagnostik:&lt;/b&gt;&lt;br&gt;  - Normal (CTR ≤ 50%) | Kardiomegali (&amp;gt; 50%)&lt;br&gt;• &lt;b&gt;Visual HUD Badge&lt;/b&gt; &amp;amp; Sync ke Lembar EMR&lt;/div&gt;" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#f8fafc;strokeColor=#cbd5e1;fontFamily=Times New Roman;align=center;verticalAlign=top;spacingTop=4;" vertex="1" parent="card_mod4">
          <mxGeometry x="340" y="42" width="295" height="126" as="geometry" />
        </mxCell>

        <!-- Card 5: Pelaporan & Ekspor -->
        <mxCell id="card_mod5" value="&lt;b style=&quot;font-size: 12px;&quot;&gt;MODUL 5: PELAPORAN &amp;amp; EKSPOR&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 10px; color: #92400e;&quot;&gt;Output Medis (Standar NEMA PS 3.3)&lt;/i&gt;&lt;hr style=&quot;margin: 4px 0;&quot;&gt;&lt;div style=&quot;text-align: left; font-size: 11px; line-height: 1.3;&quot;&gt;• &lt;b&gt;Live Insertion Template:&lt;/b&gt; Ringkasan ekspertise CTR ke lembar rekam medis EMR&lt;br&gt;• &lt;b&gt;Penerbitan Dokumen:&lt;/b&gt; Lembar Hasil Pemeriksaan Radiologi Siap Cetak (PDF)&lt;br&gt;• &lt;b&gt;Ekspor Secondary Capture (SC):&lt;/b&gt;&lt;br&gt;  - Burn-In Overlay Data Identitas Pasien&lt;br&gt;  - Burn-In Garis Ukur CTR (A, B, C)&lt;br&gt;  - Burn-In Nilai Rasio (%) &amp;amp; Status Klinis&lt;br&gt;• &lt;b&gt;Kepatuhan Standar Medis NEMA PS 3.3&lt;/b&gt;&lt;/div&gt;" style="rounded=0;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#b45309;strokeWidth=1.5;fontFamily=Times New Roman;align=center;verticalAlign=top;spacingTop=6;" vertex="1" parent="tier3_container">
          <mxGeometry x="720" y="38" width="310" height="180" as="geometry" />
        </mxCell>

        <!-- Connector Card 4 -> Card 5 -->
        <mxCell id="conn_4_5" value="&lt;b style=&quot;font-size: 10px;&quot;&gt;Hasil Ukur CTR &amp;amp;&lt;/b&gt;&lt;br&gt;Citra Anotasi Final" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;strokeColor=#b45309;strokeWidth=2;fontFamily=Times New Roman;fontColor=#78350f;labelBackgroundColor=#fef3c7;" edge="1" parent="tier3_container" source="card_mod4" target="card_mod5">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- ==================== INTER-TIER CONNECTORS ==================== -->
        <!-- Vertical: Modul 1 (Tier 1) -> Modul 2 (Tier 2) -->
        <mxCell id="conn_t1_t2" value="&lt;b style=&quot;font-size: 10px;&quot;&gt;Berkas .dcm &amp;amp; Metadata Header Terurai&lt;/b&gt;" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;strokeColor=#0369a1;strokeWidth=2;fontFamily=Times New Roman;fontColor=#0369a1;labelBackgroundColor=#ffffff;" edge="1" parent="1" source="card_mod1" target="card_mod2">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- Vertical: Modul 3 (Tier 2) -> Modul 4 (Tier 3) -->
        <mxCell id="conn_t2_t3_a" value="&lt;b style=&quot;font-size: 10px;&quot;&gt;Citra Terestorasi &amp;amp; Telemetri PSNR/SSIM&lt;/b&gt;" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.35;entryY=0;entryDx=0;entryDy=0;strokeColor=#047857;strokeWidth=2;fontFamily=Times New Roman;fontColor=#065f46;labelBackgroundColor=#ffffff;" edge="1" parent="1" source="card_mod3" target="card_mod4">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- Vertical: Modul 2 (Tier 2) -> Modul 4 (Tier 3) -->
        <mxCell id="conn_t2_t3_b" value="&lt;b style=&quot;font-size: 10px;&quot;&gt;Stream Citra Raw &amp;amp; Tag Kalibrasi&lt;/b&gt;" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;exitX=0.25;exitY=1;exitDx=0;exitDy=0;entryX=0.85;entryY=0;entryDx=0;entryDy=0;strokeColor=#b91c1c;strokeWidth=2;fontFamily=Times New Roman;fontColor=#991b1b;labelBackgroundColor=#ffffff;" edge="1" parent="1" source="card_mod2" target="card_mod4">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- Vertical: Modul 2 (Tier 2) -> Modul 5 (Tier 3) -->
        <mxCell id="conn_t2_t3_c" value="&lt;b style=&quot;font-size: 10px;&quot;&gt;Demografi Pasien MCU &amp;amp; EMR&lt;/b&gt;" style="edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;exitX=0.75;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;strokeColor=#b91c1c;strokeWidth=2;fontFamily=Times New Roman;fontColor=#991b1b;labelBackgroundColor=#ffffff;" edge="1" parent="1" source="card_mod2" target="card_mod5">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

with open('Gambar_3_4_Arsitektur_Hyu_PACS.drawio', 'w', encoding='utf-8') as f:
    f.write(xml_content)

print("Successfully written Draw.io file: Gambar_3_4_Arsitektur_Hyu_PACS.drawio")
