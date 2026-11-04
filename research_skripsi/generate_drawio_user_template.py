import xml.etree.ElementTree as ET

# Generate Draw.io XML mirroring the user's "Process Flow Diagram Template"
# Horizontal pipeline: 5 main boxes + Top satellite box + Bottom satellite box + Dashed description pills

xml_content = """<mxfile host="app.diagrams.net" modified="2026-10-13T01:45:00.000Z" agent="5.0" version="24.0.0" type="device">
  <diagram id="process_flow_hyu_pacs" name="Process Flow Hyu PACS Template">
    <mxGraphModel dx="1600" dy="1000" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1654" pageHeight="1169" background="#ffffff" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- ==================== HEADER ==================== -->
        <mxCell id="header_title" value="&lt;b style=&quot;font-size: 20px; color: #1e293b;&quot;&gt;Diagram Alur Kerja &amp;amp; Arsitektur Sistem Hyu PACS Workstation&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px; color: #64748b;&quot;&gt;Integrasi Modalitas Radiografi Digital, Core Backend EMR, Engine Restorasi AI, Workstation Diagnostik CTR, dan Pelaporan Medis&lt;/span&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="277" y="30" width="1100" height="50" as="geometry" />
        </mxCell>

        <!-- ==================== 1. TOP BOX (ENGINE RESTORASI AI) ==================== -->
        <!-- Outer Box: Cyan Theme -->
        <mxCell id="top_box_outer" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="470" y="110" width="240" height="110" as="geometry" />
        </mxCell>
        <!-- Pill Badge Header -->
        <mxCell id="top_box_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;MODUL 3: ENGINE RESTORASI AI&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#0891b2;strokeColor=none;arcSize=50;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="485" y="122" width="210" height="26" as="geometry" />
        </mxCell>
        <!-- Content Text -->
        <mxCell id="top_box_text" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: Times New Roman; color: #1e293b;&quot;&gt;Python Denoising Engine CLI/API&lt;br&gt;• Bilateral Filter &amp;amp; Non-Local Means (NLM)&lt;br&gt;• Deep Residual CNN (DnCNN 17-Lapisan)&lt;br&gt;• Telemetri Real-Time: PSNR, SSIM, Latensi&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="475" y="154" width="230" height="60" as="geometry" />
        </mxCell>

        <!-- ==================== 2. MAIN HORIZONTAL PIPELINE (5 BOXES) ==================== -->

        <!-- ----- BOX 1 (FAR LEFT): MODALITAS DR ----- -->
        <!-- Outer Box: Teal / Cyan Theme -->
        <mxCell id="box1_outer" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="100" y="380" width="230" height="110" as="geometry" />
        </mxCell>
        <!-- Pill Badge Header -->
        <mxCell id="box1_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;MODALITAS RONTGEN DR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#0891b2;strokeColor=none;arcSize=50;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="115" y="392" width="200" height="26" as="geometry" />
        </mxCell>
        <!-- Content Text -->
        <mxCell id="box1_text" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: Times New Roman; color: #1e293b;&quot;&gt;Konsol Rontgen Digital (CDC Surabaya)&lt;br&gt;• Flat-Panel Detector (16-bit Grayscale)&lt;br&gt;• Resolusi Matriks: 2999 × 2999 Piksel&lt;br&gt;• DICOM C-STORE SCU Service&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="105" y="424" width="220" height="60" as="geometry" />
        </mxCell>

        <!-- ----- BOX 2: MODUL 1 DICOM LISTENER ----- -->
        <!-- Outer Box: Coral / Rose Pink Theme -->
        <mxCell id="box2_outer" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="410" y="380" width="240" height="110" as="geometry" />
        </mxCell>
        <!-- Pill Badge Header -->
        <mxCell id="box2_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;MODUL 1: DICOM LISTENER&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e11d48;strokeColor=none;arcSize=50;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="425" y="392" width="210" height="26" as="geometry" />
        </mxCell>
        <!-- Content Text -->
        <mxCell id="box2_text" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: Times New Roman; color: #1e293b;&quot;&gt;C-STORE SCP Daemon (pynetdicom)&lt;br&gt;• Service Latar Belakang (Port 4242)&lt;br&gt;• AE Title: &lt;i&gt;HYU_PACS_SERVER&lt;/i&gt;&lt;br&gt;• Parsing Header Tag &amp;amp; Validasi NEMA&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="415" y="424" width="230" height="60" as="geometry" />
        </mxCell>

        <!-- ----- BOX 3: MODUL 2 CORE BACKEND LARAVEL ----- -->
        <!-- Outer Box: Blue Theme -->
        <mxCell id="box3_outer" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="725" y="380" width="240" height="110" as="geometry" />
        </mxCell>
        <!-- Pill Badge Header -->
        <mxCell id="box3_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;MODUL 2: CORE BACKEND EMR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#2563eb;strokeColor=none;arcSize=50;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="740" y="392" width="210" height="26" as="geometry" />
        </mxCell>
        <!-- Content Text -->
        <mxCell id="box3_text" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: Times New Roman; color: #1e293b;&quot;&gt;Web Application Core (Laravel 11)&lt;br&gt;• Manajemen Pasien MCU &amp;amp; Study UID&lt;br&gt;• Routing REST API &amp;amp; Otentikasi Role&lt;br&gt;• Dispatch Job Restorasi AI &amp;amp; Live EMR&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="730" y="424" width="230" height="60" as="geometry" />
        </mxCell>

        <!-- ----- BOX 4: MODUL 4 HYU PACS VIEWER ----- -->
        <!-- Outer Box: Teal / Cyan Theme -->
        <mxCell id="box4_outer" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="1040" y="380" width="250" height="110" as="geometry" />
        </mxCell>
        <!-- Pill Badge Header -->
        <mxCell id="box4_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;MODUL 4: HYU PACS VIEWER&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#0891b2;strokeColor=none;arcSize=50;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="1055" y="392" width="220" height="26" as="geometry" />
        </mxCell>
        <!-- Content Text -->
        <mxCell id="box4_text" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: Times New Roman; color: #1e293b;&quot;&gt;Web Diagnostic Workstation (Canvas/SVG)&lt;br&gt;• Lockstep Inverse Affine Mapping (CTM⁻¹)&lt;br&gt;• Windowing (W/L), Zoom/Pan, Rotasi/Flip&lt;br&gt;• Kaliper CTR Danzer ((A+B)/C) &amp;amp; Comparison&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="1045" y="424" width="240" height="60" as="geometry" />
        </mxCell>

        <!-- ----- BOX 5 (FAR RIGHT): MODUL 5 PELAPORAN & EKSPOR ----- -->
        <!-- Outer Box: Rose Pink Theme -->
        <mxCell id="box5_outer" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="1360" y="380" width="240" height="110" as="geometry" />
        </mxCell>
        <!-- Pill Badge Header -->
        <mxCell id="box5_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;MODUL 5: PELAPORAN &amp;amp; EKSPOR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e11d48;strokeColor=none;arcSize=50;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="1375" y="392" width="210" height="26" as="geometry" />
        </mxCell>
        <!-- Content Text -->
        <mxCell id="box5_text" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: Times New Roman; color: #1e293b;&quot;&gt;Output Medis Resmi &amp;amp; Rekam Medis&lt;br&gt;• Cetak Lembar Hasil Pemeriksaan (PDF)&lt;br&gt;• Secondary Capture DICOM (NEMA PS 3.3)&lt;br&gt;• Burn-in Anotasi &amp;amp; Verifikasi Radiolog&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="1365" y="424" width="230" height="60" as="geometry" />
        </mxCell>

        <!-- ==================== 3. BOTTOM BOX (DATABASE & STORAGE) ==================== -->
        <!-- Outer Box: Cool Slate / Gray Theme -->
        <mxCell id="bottom_box_outer" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#64748b;strokeWidth=2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="470" y="650" width="240" height="110" as="geometry" />
        </mxCell>
        <!-- Pill Badge Header -->
        <mxCell id="bottom_box_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;BASIS DATA &amp;amp; STORAGE ARSIP&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#475569;strokeColor=none;arcSize=50;fontFamily=Times New Roman;" vertex="1" parent="1">
          <mxGeometry x="485" y="662" width="210" height="26" as="geometry" />
        </mxCell>
        <!-- Content Text -->
        <mxCell id="bottom_box_text" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: Times New Roman; color: #1e293b;&quot;&gt;Penyimpanan Data Terpusat&lt;br&gt;• Database MySQL: Metadata Pasien &amp;amp; Studi&lt;br&gt;• Filesystem Server: storage/app/dicom/raw/&lt;br&gt;• Log Riwayat Restorasi &amp;amp; Nilai PSNR/SSIM&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="475" y="694" width="230" height="60" as="geometry" />
        </mxCell>

        <!-- ==================== 4. CONNECTORS & DESCRIPTION PILLS ==================== -->

        <!-- Conn: Box 1 -> Box 2 (Horizontal) -->
        <mxCell id="conn_1_2" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#06b6d4;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box1_outer" target="box2_outer">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Box 2 -> Box 3 (Horizontal with circular node) -->
        <mxCell id="conn_2_3" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#f43f5e;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;startArrow=oval;startFill=0;startSize=6;" edge="1" parent="1" source="box2_outer" target="box3_outer">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Box 3 -> Box 4 (Horizontal with circular node) -->
        <mxCell id="conn_3_4" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#3b82f6;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;startArrow=oval;startFill=0;startSize=6;" edge="1" parent="1" source="box3_outer" target="box4_outer">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Box 4 -> Box 5 (Horizontal with circular node) -->
        <mxCell id="conn_4_5" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#06b6d4;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;startArrow=oval;startFill=0;startSize=6;" edge="1" parent="1" source="box4_outer" target="box5_outer">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Loop Conn: Box 2 -> Box 3 (Overhead arch) -->
        <mxCell id="arch_2_3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#06b6d4;strokeWidth=1.5;exitX=0.75;exitY=0;exitDx=0;exitDy=0;entryX=0.25;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box2_outer" target="box3_outer">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="590" y="320" />
              <mxPoint x="785" y="320" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- Pill on arch 2-3 -->
        <mxCell id="pill_arch_2_3" value="&lt;span style=&quot;font-size: 9.5px; color: #475569; font-family: Times New Roman;&quot;&gt;Ekstraksi Tag &amp;amp; Ingesti Berkas&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="612" y="308" width="150" height="24" as="geometry" />
        </mxCell>

        <!-- Loop Conn: Box 3 -> Box 4 (Overhead arch) -->
        <mxCell id="arch_3_4" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#06b6d4;strokeWidth=1.5;exitX=0.75;exitY=0;exitDx=0;exitDy=0;entryX=0.25;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box3_outer" target="box4_outer">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="905" y="320" />
              <mxPoint x="1102" y="320" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- Pill on arch 3-4 -->
        <mxCell id="pill_arch_3_4" value="&lt;span style=&quot;font-size: 9.5px; color: #475569; font-family: Times New Roman;&quot;&gt;Streaming Citra &amp;amp; Tag Kalibrasi&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="930" y="308" width="150" height="24" as="geometry" />
        </mxCell>

        <!-- Top Box Connection: Box 4 -> Top Box (Long return line overhead) -->
        <mxCell id="conn_4_top" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#06b6d4;strokeWidth=1.5;exitX=0.5;exitY=0;exitDx=0;exitDy=0;entryX=1;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box4_outer" target="top_box_outer">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1165" y="165" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- Pill on long return overhead line -->
        <mxCell id="pill_conn_4_top" value="&lt;span style=&quot;font-size: 9.5px; color: #475569; font-family: Times New Roman;&quot;&gt;Evaluasi Citra Restorasi &amp;amp; Telemetri PSNR/SSIM&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="840" y="153" width="230" height="24" as="geometry" />
        </mxCell>

        <!-- Top Box Connection: Top Box -> Box 2 (Downward arrow) -->
        <mxCell id="conn_top_box2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#06b6d4;strokeWidth=1.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;startArrow=oval;startFill=0;startSize=6;" edge="1" parent="1" source="top_box_outer" target="box2_outer">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- Pill on downward line from Top Box -->
        <mxCell id="pill_top_box2" value="&lt;span style=&quot;font-size: 9.5px; color: #475569; font-family: Times New Roman;&quot;&gt;Dispatch Restorasi AI&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="515" y="270" width="150" height="24" as="geometry" />
        </mxCell>

        <!-- Bottom Box Connection: Box 2 -> Bottom Box (Downward pink line) -->
        <mxCell id="conn_box2_bottom" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#f43f5e;strokeWidth=1.5;exitX=0.35;exitY=1;exitDx=0;exitDy=0;entryX=0.35;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box2_outer" target="bottom_box_outer">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>

        <!-- Bottom Box Connection: Bottom Box -> Box 2 (Upward gray line) -->
        <mxCell id="conn_bottom_box2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#64748b;strokeWidth=1.5;exitX=0.65;exitY=0;exitDx=0;exitDy=0;entryX=0.65;entryY=1;entryDx=0;entryDy=0;" edge="1" parent="1" source="bottom_box_outer" target="box2_outer">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- Pill on vertical connection between Box 2 and Bottom Box -->
        <mxCell id="pill_box2_bottom" value="&lt;span style=&quot;font-size: 9.5px; color: #475569; font-family: Times New Roman;&quot;&gt;Read/Write MySQL &amp;amp; Storage&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#64748b;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="515" y="550" width="160" height="24" as="geometry" />
        </mxCell>

        <!-- Bottom Box Connection: Box 4 -> Bottom Box (Long return line below) -->
        <mxCell id="conn_4_bottom" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#06b6d4;strokeWidth=1.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=1;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box4_outer" target="bottom_box_outer">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1165" y="705" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- Pill on long return bottom line -->
        <mxCell id="pill_conn_4_bottom" value="&lt;span style=&quot;font-size: 9.5px; color: #475569; font-family: Times New Roman;&quot;&gt;Sinkronisasi Hasil Rasio CTR &amp;amp; Anotasi ke EMR&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="840" y="693" width="230" height="24" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

with open('Gambar_3_4_Formasi_Template_Hyu_PACS.drawio', 'w', encoding='utf-8') as f:
    f.write(xml_content.strip())

print("Successfully written Draw.io matching user template: Gambar_3_4_Formasi_Template_Hyu_PACS.drawio")
