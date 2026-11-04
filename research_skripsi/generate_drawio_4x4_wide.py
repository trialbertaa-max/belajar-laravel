import xml.etree.ElementTree as ET

# Generate Draw.io XML for 2-Row Wide Layout (4 Shapes Top, 4 Shapes Bottom)
# Crystal-clear arrows and workflow matching user's exact specification

xml_content = """<mxfile host="app.diagrams.net" modified="2026-10-13T02:00:00.000Z" agent="5.0" version="24.0.0" type="device">
  <diagram id="wide_4x4_hyu_pacs" name="Diagram Alur 4 Atas 4 Bawah Hyu PACS">
    <mxGraphModel dx="1600" dy="900" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1400" pageHeight="700" background="#ffffff" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- Header Judul Diagram -->
        <mxCell id="header_title" value="&lt;b style=&quot;font-size: 18px; color: #0f172a; font-family: 'Times New Roman';&quot;&gt;GAMBAR 3.4. DIAGRAM ALUR KERJA PERANGKAT LUNAK HYU PACS WORKSTATION&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 11px; color: #475569; font-family: 'Times New Roman';&quot;&gt;Alur Integrasi Ingesti Transmisi DICOM, Core Backend EMR, Engine AI, Workstation Diagnostik CTR, dan Pelaporan Medis&lt;/i&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="150" y="25" width="1100" height="40" as="geometry" />
        </mxCell>
        <mxCell id="header_line" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#334155;strokeWidth=1.5;" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="150" y="70" as="sourcePoint" />
            <mxPoint x="1250" y="70" as="targetPoint" />
          </mxGeometry>
        </mxCell>

        <!-- ==================== BARIS ATAS (4 SHAPES) ==================== -->
        <!-- Box 1: Modalitas DR (Teal) -->
        <mxCell id="box1" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="80" y="100" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b1_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;1. MODALITAS RONTGEN DR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#0891b2;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="95" y="112" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b1_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #0891b2; font-weight: bold;&quot;&gt;Konsol Rontgen CDC Surabaya&lt;/i&gt;&lt;br&gt;• Flat-Panel Detector (16-bit)&lt;br&gt;• Matriks 2999 × 2999 Piksel&lt;br&gt;• Layanan DICOM C-STORE SCU&lt;br&gt;• Tag Spacing (0028,0030)&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="85" y="144" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- Box 2: Modul 1 DICOM Listener (Rose/Pink) -->
        <mxCell id="box2" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="385" y="100" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b2_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;2. MODUL 1: DICOM LISTENER&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e11d48;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="400" y="112" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b2_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #e11d48; font-weight: bold;&quot;&gt;Daemon SCP (pynetdicom)&lt;/i&gt;&lt;br&gt;• Background Port 4242&lt;br&gt;• AE: &lt;i&gt;HYU_PACS_SERVER&lt;/i&gt;&lt;br&gt;• Validasi NEMA PS 3.4&lt;br&gt;• Parsing Header Tag Metadata&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="390" y="144" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- Box 3: Modul 2 Backend Laravel (Royal Blue) -->
        <mxCell id="box3" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="690" y="100" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b3_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;3. MODUL 2: BACKEND LARAVEL&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#2563eb;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="705" y="112" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b3_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #2563eb; font-weight: bold;&quot;&gt;Core Framework Web (Laravel 11)&lt;/i&gt;&lt;br&gt;• Otentikasi &amp;amp; Routing API&lt;br&gt;• Manajemen Pasien MCU &amp;amp; RM&lt;br&gt;• Live Templat Ekspertise EMR&lt;br&gt;• Controller Antrean Job AI&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="695" y="144" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- Box 4: Basis Data MySQL & Storage (Slate Gray) -->
        <mxCell id="box4" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#64748b;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="995" y="100" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b4_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;4. BASIS DATA &amp;amp; STORAGE&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#475569;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="1010" y="112" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b4_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #475569; font-weight: bold;&quot;&gt;Penyimpanan Data Terpusat&lt;/i&gt;&lt;br&gt;• MySQL: Metadata &amp;amp; Study UID&lt;br&gt;• Filesystem: storage/raw/ &amp;amp; /clean/&lt;br&gt;• Log Riwayat Restorasi &amp;amp; Audit&lt;br&gt;• Sinkronisasi Jadwal Worklist&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="1000" y="144" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- ==================== CONNECTORS BARIS ATAS ==================== -->
        <!-- Conn 1 -> 2 -->
        <mxCell id="c1_2" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#06b6d4;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box1" target="box2">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p1_2" value="&lt;span style=&quot;font-size: 8.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;TCP/IP LAN&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="330" y="153" width="55" height="22" as="geometry" />
        </mxCell>

        <!-- Conn 2 -> 3 -->
        <mxCell id="c2_3" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#f43f5e;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box2" target="box3">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p2_3" value="&lt;span style=&quot;font-size: 8.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;File &amp;amp; Tag&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="635" y="153" width="55" height="22" as="geometry" />
        </mxCell>

        <!-- Conn 3 <-> 4 -->
        <mxCell id="c3_4" value="" style="endArrow=classic;startArrow=classic;html=1;rounded=0;strokeColor=#3b82f6;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box3" target="box4">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p3_4" value="&lt;span style=&quot;font-size: 8.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;SQL &amp;amp; Arsip&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="940" y="153" width="55" height="22" as="geometry" />
        </mxCell>

        <!-- ==================== PANAH TRANSISI DARI ATAS KE BAWAH ==================== -->
        <!-- Panah dari Box 3 turun ke Box 5 (Restorasi AI) -->
        <mxCell id="c_top_down" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#3b82f6;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box3" target="box5">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="815" y="270" />
              <mxPoint x="205" y="270" />
            </Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="p_top_down" value="&lt;b style=&quot;font-size: 9.5px; color: #1e293b; font-family: 'Times New Roman';&quot;&gt;Dispatch Job Pemrosesan Restorasi &amp;amp; Ekstraksi Matriks Citra 16-bit&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="350" y="258" width="370" height="24" as="geometry" />
        </mxCell>

        <!-- ==================== BARIS BAWAH (4 SHAPES) ==================== -->
        <!-- Box 5: Modul 3 Engine Restorasi AI (Teal) -->
        <mxCell id="box5" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="80" y="310" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b5_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;5. MODUL 3: ENGINE RESTORASI AI&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#0891b2;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="95" y="322" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b5_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #0891b2; font-weight: bold;&quot;&gt;Python Denoising Engine CLI/API&lt;/i&gt;&lt;br&gt;• Bilateral Filter (Preservasi Tepi)&lt;br&gt;• Non-Local Means (NLM Redundansi)&lt;br&gt;• DnCNN Residual (17-Lapisan Conv)&lt;br&gt;• Telemetri PSNR, SSIM, Latensi&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="85" y="354" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- Box 6: Modul 4 Hyu PACS Viewer (Purple) -->
        <mxCell id="box6" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#8b5cf6;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="385" y="310" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b6_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;6. MODUL 4: HYU PACS VIEWER&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7c3aed;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="400" y="322" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b6_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #7c3aed; font-weight: bold;&quot;&gt;Web Diagnostic Workstation&lt;/i&gt;&lt;br&gt;• HTML5 Canvas &amp;amp; SVG Stage Layer&lt;br&gt;• Matriks CTM⁻¹ Lockstep Anchoring&lt;br&gt;• Windowing (W/L), Zoom, Pan, Rotasi&lt;br&gt;• Split-Curtain Comparison Slider&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="390" y="354" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- Box 7: Kaliper CTR Danzer (Coral/Rose) -->
        <mxCell id="box7" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="690" y="310" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b7_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;7. KALIPER BIOMETRI CTR DANZER&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e11d48;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="705" y="322" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b7_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #e11d48; font-weight: bold;&quot;&gt;Analisis Kardiomegali (Danzer, 1919)&lt;/i&gt;&lt;br&gt;• Penarikan 2 Langkah: Garis Jantung A+B&lt;br&gt;• Penarikan Diameter Toraks C&lt;br&gt;• Kalkulasi Rasio: ((A+B)/C) × 100%&lt;br&gt;• Klasifikasi: Normal vs Kardiomegali&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="695" y="354" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- Box 8: Modul 5 Pelaporan & Ekspor (Royal Blue) -->
        <mxCell id="box8" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=2;arcSize=14;" vertex="1" parent="1">
          <mxGeometry x="995" y="310" width="250" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b8_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 11px;&quot;&gt;8. MODUL 5: PELAPORAN &amp;amp; EKSPOR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#2563eb;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="1010" y="322" width="220" height="26" as="geometry" />
        </mxCell>
        <mxCell id="b8_txt" value="&lt;div style=&quot;text-align: center; font-size: 10px; line-height: 1.3; font-family: 'Times New Roman'; color: #1e293b;&quot;&gt;&lt;i style=&quot;color: #2563eb; font-weight: bold;&quot;&gt;Output Medis Resmi Standar PS 3.3&lt;/i&gt;&lt;br&gt;• Penerbitan Laporan PDF Siap Cetak&lt;br&gt;• Ekspor Secondary Capture (DICOM SC)&lt;br&gt;• Burn-in Anotasi &amp;amp; Garis Ukur CTR&lt;br&gt;• Verifikasi Radiolog &amp;amp; Arsip EMR&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=top;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="1000" y="354" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- ==================== CONNECTORS BARIS BAWAH ==================== -->
        <!-- Conn 5 -> 6 -->
        <mxCell id="c5_6" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#06b6d4;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box5" target="box6">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p5_6" value="&lt;span style=&quot;font-size: 8.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;Citra Bersih&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="330" y="363" width="55" height="22" as="geometry" />
        </mxCell>

        <!-- Conn 6 -> 7 -->
        <mxCell id="c6_7" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#8b5cf6;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box6" target="box7">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p6_7" value="&lt;span style=&quot;font-size: 8.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;Anotasi SVG&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#8b5cf6;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="635" y="363" width="55" height="22" as="geometry" />
        </mxCell>

        <!-- Conn 7 -> 8 -->
        <mxCell id="c7_8" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#f43f5e;strokeWidth=2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box7" target="box8">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p7_8" value="&lt;span style=&quot;font-size: 8.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;Rasio CTR %&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="940" y="363" width="55" height="22" as="geometry" />
        </mxCell>

        <!-- Feedback Loop: Box 8 kembali ke Box 4 (MySQL & EMR) -->
        <mxCell id="c_feedback" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#64748b;strokeWidth=1.5;strokeDashed=1;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=1;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box8" target="box4">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1285" y="377" />
              <mxPoint x="1285" y="167" />
            </Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="p_feedback" value="&lt;span style=&quot;font-size: 8.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;Sinkronisasi EMR&lt;/span&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#64748b;strokeWidth=1.2;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="1245" y="258" width="80" height="22" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

with open('Gambar_3_4_Lebar_4x4_Hyu_PACS.drawio', 'w', encoding='utf-8') as f:
    f.write(xml_content.strip())

print("Successfully written 4-top 4-bottom Draw.io file: Gambar_3_4_Lebar_4x4_Hyu_PACS.drawio")
