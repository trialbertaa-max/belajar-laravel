# -*- coding: utf-8 -*-
"""
Generate concise, high-legibility Draw.io XML for Gambar 3.4 (Hyu PACS).
Designed specifically to prevent tiny unreadable text when exported to PDF or placed in A4 Word.
"""

xml_content = """<mxfile host="app.diagrams.net" modified="2026-10-14T08:20:00.000Z" agent="5.0" version="24.0.0" type="device">
  <diagram id="wide_4x4_hyu_pacs" name="Diagram Alur 4 Atas 4 Bawah Hyu PACS">
    <mxGraphModel dx="1400" dy="750" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1380" pageHeight="500" background="#ffffff" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- Header Judul Diagram -->
        <mxCell id="header_title" value="&lt;b style=&quot;font-size: 17px; color: #0f172a; font-family: 'Times New Roman';&quot;&gt;GAMBAR 3.4. DIAGRAM ALUR KERJA PERANGKAT LUNAK HYU PACS WORKSTATION&lt;/b&gt;&lt;br&gt;&lt;i style=&quot;font-size: 12px; color: #475569; font-family: 'Times New Roman';&quot;&gt;Alur Integrasi Ingesti Transmisi DICOM, Core Backend EMR, Engine AI, Workstation Diagnostik CTR, dan Pelaporan Medis&lt;/i&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="90" y="12" width="1200" height="38" as="geometry" />
        </mxCell>
        <mxCell id="header_line" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#334155;strokeWidth=1.5;" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="90" y="55" as="sourcePoint" />
            <mxPoint x="1290" y="55" as="targetPoint" />
          </mxGeometry>
        </mxCell>

        <!-- ==================== BARIS ATAS (4 SHAPES) ==================== -->
        <!-- Box 1: Modalitas DR (Teal) -->
        <mxCell id="box1" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="60" y="75" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b1_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;1. MODALITAS RONTGEN DR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#0891b2;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="70" y="85" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b1_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #0891b2; font-size: 13px;&quot;&gt;Fujifilm FDR Xair &amp;amp; D-EVO II&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Citra Toraks PA (Format DICOM)&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Transmisi DICOM C-STORE SCU&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Kalibrasi &lt;i&gt;Pixel Spacing&lt;/i&gt; Fisik&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="65" y="118" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- Box 2: Modul 1 DICOM Listener (Rose/Pink) -->
        <mxCell id="box2" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="390" y="75" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b2_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;2. DICOM LISTENER (MODUL 1)&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e11d48;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="400" y="85" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b2_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #e11d48; font-size: 13px;&quot;&gt;Daemon SCP (pynetdicom)&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Background Port Jaringan 4242&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• AE Title: &lt;i&gt;HYU_PACS_SERVER&lt;/i&gt;&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Ekstraksi Tag &amp;amp; Matriks Citra&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="395" y="118" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- Box 3: Modul 2 Backend Laravel (Royal Blue) -->
        <mxCell id="box3" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="720" y="75" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b3_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;3. CORE BACKEND (MODUL 2)&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#2563eb;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="730" y="85" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b3_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #2563eb; font-size: 13px;&quot;&gt;Framework Laravel 11&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Manajemen Pasien MCU &amp;amp; RM&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Routing API &amp;amp; Otentikasi Role&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Controller Antrean Dispatch AI&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="725" y="118" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- Box 4: Basis Data MySQL & Storage (Slate Gray) -->
        <mxCell id="box4" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#64748b;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="1050" y="75" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b4_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;4. BASIS DATA &amp;amp; STORAGE&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#475569;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="1060" y="85" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b4_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #475569; font-size: 13px;&quot;&gt;MySQL &amp;amp; Filesystem&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Metadata Pasien &amp;amp; Study UID&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Penyimpanan Berkas DICOM&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Log Audit &amp;amp; Riwayat Restorasi&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="1055" y="118" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- ==================== CONNECTORS BARIS ATAS ==================== -->
        <!-- Conn 1 -> 2 -->
        <mxCell id="c1_2" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#06b6d4;strokeWidth=2.2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box1" target="box2">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p1_2" value="&lt;b style=&quot;font-size: 11px; color: #0891b2; font-family: 'Times New Roman';&quot;&gt;TCP/IP LAN&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="332" y="128" width="56" height="28" as="geometry" />
        </mxCell>

        <!-- Conn 2 -> 3 -->
        <mxCell id="c2_3" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#f43f5e;strokeWidth=2.2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box2" target="box3">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p2_3" value="&lt;b style=&quot;font-size: 11px; color: #e11d48; font-family: 'Times New Roman';&quot;&gt;File &amp;amp; Tag&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="662" y="128" width="56" height="28" as="geometry" />
        </mxCell>

        <!-- Conn 3 <-> 4 -->
        <mxCell id="c3_4" value="" style="endArrow=classic;startArrow=classic;html=1;rounded=0;strokeColor=#3b82f6;strokeWidth=2.2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box3" target="box4">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p3_4" value="&lt;b style=&quot;font-size: 11px; color: #2563eb; font-family: 'Times New Roman';&quot;&gt;SQL &amp;amp; File&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="992" y="128" width="56" height="28" as="geometry" />
        </mxCell>

        <!-- ==================== PANAH TRANSISI DARI ATAS KE BAWAH ==================== -->
        <!-- Panah dari Box 3 turun ke Box 5 (Restorasi AI) -->
        <mxCell id="c_top_down" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#2563eb;strokeWidth=2.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box3" target="box5">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="855" y="240" />
              <mxPoint x="195" y="240" />
            </Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="p_top_down" value="&lt;b style=&quot;font-size: 11.5px; color: #1e293b; font-family: 'Times New Roman';&quot;&gt;Dispatch Job Pemrosesan Restorasi AI &amp;amp; Ekstraksi Matriks Citra 16-bit&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#2563eb;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="395" y="226" width="430" height="28" as="geometry" />
        </mxCell>

        <!-- ==================== BARIS BAWAH (4 SHAPES) ==================== -->
        <!-- Box 5: Modul 3 Engine Restorasi AI (Teal) -->
        <mxCell id="box5" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="60" y="275" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b5_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;5. ENGINE RESTORASI AI (MOD 3)&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#0891b2;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="70" y="285" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b5_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #0891b2; font-size: 13px;&quot;&gt;Python Denoising Engine&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Bilateral Filter (Preservasi Tepi)&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• DnCNN Residual (17-Layer)&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Telemetri PSNR &amp;amp; SSIM Real-Time&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="65" y="318" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- Box 6: Modul 4 Hyu PACS Viewer (Purple) -->
        <mxCell id="box6" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#8b5cf6;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="390" y="275" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b6_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;6. HYU PACS VIEWER (MODUL 4)&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7c3aed;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="400" y="285" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b6_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #7c3aed; font-size: 13px;&quot;&gt;Web Diagnostic Workstation&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• SVG Layer &amp;amp; Matriks CTM⁻¹&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Windowing (W/L), Pan, &amp;amp; Zoom&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Split-Curtain Comparison Slider&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="395" y="318" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- Box 7: Kaliper CTR Danzer (Coral/Rose) -->
        <mxCell id="box7" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="720" y="275" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b7_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;7. KALIPER BIOMETRI CTR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e11d48;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="730" y="285" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b7_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #e11d48; font-size: 13px;&quot;&gt;Analisis Kardiomegali (Danzer, 1919)&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Garis Jantung (A+B) &amp;amp; Toraks (C)&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Kalkulasi Rasio: ((A+B)/C) × 100%&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Klasifikasi Otomatis Kardiomegali&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="725" y="318" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- Box 8: Modul 5 Pelaporan & Ekspor (Royal Blue) -->
        <mxCell id="box8" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#3b82f6;strokeWidth=2.2;arcSize=12;" vertex="1" parent="1">
          <mxGeometry x="1050" y="275" width="270" height="135" as="geometry" />
        </mxCell>
        <mxCell id="b8_pill" value="&lt;b style=&quot;color: #ffffff; font-size: 12.5px;&quot;&gt;8. PELAPORAN &amp;amp; EKSPOR (MOD 5)&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#2563eb;strokeColor=none;arcSize=50;fontFamily='Times New Roman';" vertex="1" parent="1">
          <mxGeometry x="1060" y="285" width="250" height="28" as="geometry" />
        </mxCell>
        <mxCell id="b8_txt" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; color: #1e293b; line-height: 1.45;&quot;&gt;&lt;b style=&quot;color: #2563eb; font-size: 13px;&quot;&gt;Output Medis Resmi PS 3.3&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Penerbitan Laporan PDF Siap Cetak&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Ekspor Secondary Capture (DICOM SC)&lt;/span&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px;&quot;&gt;• Burn-in Anotasi &amp;amp; Hasil Rasio CTR&lt;/span&gt;&lt;/div&gt;" style="text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="1055" y="318" width="260" height="85" as="geometry" />
        </mxCell>

        <!-- ==================== CONNECTORS BARIS BAWAH ==================== -->
        <!-- Conn 5 -> 6 -->
        <mxCell id="c5_6" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#06b6d4;strokeWidth=2.2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box5" target="box6">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p5_6" value="&lt;b style=&quot;font-size: 11px; color: #0891b2; font-family: 'Times New Roman';&quot;&gt;Citra Bersih&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#06b6d4;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="332" y="328" width="56" height="28" as="geometry" />
        </mxCell>

        <!-- Conn 6 -> 7 -->
        <mxCell id="c6_7" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#8b5cf6;strokeWidth=2.2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box6" target="box7">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p6_7" value="&lt;b style=&quot;font-size: 11px; color: #7c3aed; font-family: 'Times New Roman';&quot;&gt;Anotasi SVG&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#8b5cf6;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="662" y="328" width="56" height="28" as="geometry" />
        </mxCell>

        <!-- Conn 7 -> 8 -->
        <mxCell id="c7_8" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#f43f5e;strokeWidth=2.2;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box7" target="box8">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>
        <mxCell id="p7_8" value="&lt;b style=&quot;font-size: 11px; color: #e11d48; font-family: 'Times New Roman';&quot;&gt;Rasio CTR %&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#f43f5e;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="992" y="328" width="56" height="28" as="geometry" />
        </mxCell>

        <!-- Feedback Loop: Box 8 kembali ke Box 4 (MySQL & EMR) -->
        <mxCell id="c_feedback" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#475569;strokeWidth=1.8;strokeDashed=1;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=1;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="box8" target="box4">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="1350" y="342" />
              <mxPoint x="1350" y="142" />
            </Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="p_feedback" value="&lt;b style=&quot;font-size: 10.5px; color: #475569; font-family: 'Times New Roman';&quot;&gt;Sinkronisasi EMR&lt;/b&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#64748b;strokeWidth=1.5;strokeDashed=1;arcSize=50;" vertex="1" parent="1">
          <mxGeometry x="1302" y="232" width="96" height="26" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

with open("Gambar_3_4_Lebar_4x4_Hyu_PACS.drawio", "w", encoding="utf-8") as f:
    f.write(xml_content.strip())

print("Successfully written concise, large-font Draw.io file: Gambar_3_4_Lebar_4x4_Hyu_PACS.drawio")
