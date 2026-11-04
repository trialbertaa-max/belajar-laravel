import xml.etree.ElementTree as ET

# Generate Draw.io XML matching the user's uploaded "FLOW DIAGRAM" example
# 4 Columns x 3 Rows with Circle Start, Diamond Decision, and Capsule End

xml_content = """<mxfile host="app.diagrams.net" modified="2026-10-13T01:55:00.000Z" agent="5.0" version="24.0.0" type="device">
  <diagram id="flow_diagram_hyu_pacs" name="Flow Diagram Hyu PACS">
    <mxGraphModel dx="1400" dy="900" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" background="#ffffff" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- Header Judul Diagram -->
        <mxCell id="header_title" value="&lt;b style=&quot;font-size: 22px; color: #1e293b; font-family: 'Times New Roman';&quot;&gt;FLOW DIAGRAM SISTEM HYU PACS&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11px; color: #64748b; font-family: 'Times New Roman'; letter-spacing: 1px;&quot;&gt;ALUR LOGIKA INGESTI DICOM, RESTORASI CITRA AI, PENGUKURAN CTR, DAN PELAPORAN MEDIS&lt;/span&gt;" style="text;html=1;align=left;verticalAlign=middle;whiteSpace=wrap;rounded=0;" vertex="1" parent="1">
          <mxGeometry x="80" y="35" width="800" height="45" as="geometry" />
        </mxCell>

        <!-- ==================== KOLOM 1 ==================== -->
        <!-- Node 1: START (Circle Purple) -->
        <mxCell id="node_start" value="&lt;b style=&quot;color: #ffffff; font-size: 13px; font-family: 'Times New Roman';&quot;&gt;START&lt;/b&gt;" style="ellipse;whiteSpace=wrap;html=1;fillColor=#8b5cf6;strokeColor=none;fontColor=#ffffff;" vertex="1" parent="1">
          <mxGeometry x="110" y="115" width="85" height="85" as="geometry" />
        </mxCell>

        <!-- Node 2: Input Modalitas DR (Pink Rect) -->
        <mxCell id="node_modality" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Modalitas Rontgen DR&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Transmisi berkas citra toraks mentah 16-bit via protokol C-STORE SCU&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ec4899;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="60" y="240" width="185" height="75" as="geometry" />
        </mxCell>

        <!-- Node 3: Modul 1 DICOM Listener (Deep Purple Rect) -->
        <mxCell id="node_listener" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Modul 1: DICOM Listener&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Daemon SCP (Port 4242) menangkap stream data &amp;amp; parsing header tag&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7c3aed;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="60" y="355" width="185" height="75" as="geometry" />
        </mxCell>

        <!-- ==================== KOLOM 2 ==================== -->
        <!-- Node 4: Modul 2 Backend & Storage (Pink Rect) -->
        <mxCell id="node_backend" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Modul 2: Backend EMR&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Simpan metadata pasien ke MySQL &amp;amp; arsip file raw .dcm ke server&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ec4899;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="300" y="355" width="185" height="75" as="geometry" />
        </mxCell>

        <!-- Node 5: Validasi Standar Medis (Lavender Rect) -->
        <mxCell id="node_val_dicom" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Validasi NEMA PS 3.4&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Verifikasi SOP Class UID &amp;amp; integritas transfer syntax berkas&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#a78bfa;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="300" y="240" width="185" height="75" as="geometry" />
        </mxCell>

        <!-- ==================== KOLOM 3 ==================== -->
        <!-- Node 6: Fallback / Log Error (Magenta Rect) -->
        <mxCell id="node_fallback" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Bypass / Raw Display&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Tampilkan citra asli tanpa filter atau catat log error transmisi&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#c026d3;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="540" y="145" width="185" height="70" as="geometry" />
        </mxCell>

        <!-- Node 7: Modul 3 Engine Restorasi AI (Deep Purple Rect) -->
        <mxCell id="node_ai_engine" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Modul 3: Engine Restorasi AI&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Reduksi derau DnCNN 17-Lapisan / NLM / Bilateral &amp;amp; metrik PSNR/SSIM&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7c3aed;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="540" y="240" width="185" height="75" as="geometry" />
        </mxCell>

        <!-- Node 9: Diamond Decision CTR (Magenta Diamond) -->
        <mxCell id="node_decision_ctr" value="&lt;b style=&quot;font-size: 10.5px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Uji Biometri CTR&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.2;&quot;&gt;Apakah rasio CTR&lt;br&gt;(A+B)/C &amp;gt; 50%?&lt;/div&gt;" style="rhombus;whiteSpace=wrap;html=1;fillColor=#db2777;strokeColor=none;fontColor=#ffffff;spacing=4;" vertex="1" parent="1">
          <mxGeometry x="567" y="340" width="130" height="105" as="geometry" />
        </mxCell>

        <!-- ==================== KOLOM 4 ==================== -->
        <!-- Node 8: Modul 4 Workstation Viewer (Lavender Rect) -->
        <mxCell id="node_viewer" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Modul 4: Hyu PACS Viewer&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Canvas/SVG Stage Layer, Windowing (W/L), &amp;amp; Kaliper CTR Danzer&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#a78bfa;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="780" y="145" width="185" height="70" as="geometry" />
        </mxCell>

        <!-- Node 10: Modul 5 Pelaporan Medis (Pink Rect) -->
        <mxCell id="node_report" value="&lt;b style=&quot;font-size: 11px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Modul 5: Pelaporan &amp;amp; Ekspor&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman'; line-height: 1.25;&quot;&gt;Cetak Laporan PDF &amp;amp; Secondary Capture DICOM (NEMA PS 3.3)&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#ec4899;strokeColor=none;arcSize=18;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="780" y="240" width="185" height="75" as="geometry" />
        </mxCell>

        <!-- Node 11: END / FINISH (Capsule Deep Purple) -->
        <mxCell id="node_end" value="&lt;b style=&quot;font-size: 11.5px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;SELESAI (Arsip EMR Radiologi)&lt;/b&gt;&lt;br&gt;&lt;div style=&quot;font-size: 9.5px; color: #ffffff; font-family: 'Times New Roman';&quot;&gt;Verifikasi Dokter Spesialis Radiologi&lt;/div&gt;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#6d28d9;strokeColor=none;arcSize=50;fontColor=#ffffff;spacing=6;" vertex="1" parent="1">
          <mxGeometry x="772" y="360" width="200" height="65" as="geometry" />
        </mxCell>

        <!-- ==================== CONNECTORS ==================== -->
        <!-- Conn: Start -> Modality (Down) -->
        <mxCell id="c_start_mod" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_start" target="node_modality">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Modality -> Listener (Down) -->
        <mxCell id="c_mod_list" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_modality" target="node_listener">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Listener -> Backend (Right) -->
        <mxCell id="c_list_back" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_listener" target="node_backend">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Backend -> Val Dicom (Up) -->
        <mxCell id="c_back_val" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=0;exitDx=0;exitDy=0;entryX=0.5;entryY=1;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_backend" target="node_val_dicom">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Val Dicom -> Fallback (NO - Up & Right) -->
        <mxCell id="c_val_fall" value="&lt;span style=&quot;font-size: 9px; font-weight: bold; color: #64748b; font-family: 'Times New Roman'; background-color: #ffffff;&quot;&gt;NO&lt;/span&gt;" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=0;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_val_dicom" target="node_fallback">
          <mxGeometry relative="1" as="geometry">
            <Array as="points">
              <mxPoint x="392" y="180" />
            </Array>
          </mxGeometry>
        </mxCell>

        <!-- Conn: Val Dicom -> AI Engine (YES - Right) -->
        <mxCell id="c_val_ai" value="&lt;span style=&quot;font-size: 9px; font-weight: bold; color: #64748b; font-family: 'Times New Roman'; background-color: #ffffff;&quot;&gt;YES&lt;/span&gt;" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_val_dicom" target="node_ai_engine">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: AI Engine -> Fallback (NO - Up) -->
        <mxCell id="c_ai_fall" value="&lt;span style=&quot;font-size: 9px; font-weight: bold; color: #64748b; font-family: 'Times New Roman'; background-color: #ffffff;&quot;&gt;NO&lt;/span&gt;" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=0;exitDx=0;exitDy=0;entryX=0.5;entryY=1;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_ai_engine" target="node_fallback">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Fallback -> Viewer (Right) -->
        <mxCell id="c_fall_view" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_fallback" target="node_viewer">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: AI Engine -> Decision CTR (YES - Down) -->
        <mxCell id="c_ai_dec" value="&lt;span style=&quot;font-size: 9px; font-weight: bold; color: #64748b; font-family: 'Times New Roman'; background-color: #ffffff;&quot;&gt;YES&lt;/span&gt;" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_ai_engine" target="node_decision_ctr">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Viewer -> Report (Down) -->
        <mxCell id="c_view_rep" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_viewer" target="node_report">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Report -> End (Down Arrow) -->
        <mxCell id="c_rep_end" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_report" target="node_end">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Conn: Decision CTR -> End (Right Arrow) -->
        <mxCell id="c_dec_end" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#cbd5e1;strokeWidth=1.5;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="node_decision_ctr" target="node_end">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

with open('Gambar_3_4_Flowchart_Hyu_PACS.drawio', 'w', encoding='utf-8') as f:
    f.write(xml_content.strip())

print("Successfully written matching Flow Diagram Draw.io file: Gambar_3_4_Flowchart_Hyu_PACS.drawio")
