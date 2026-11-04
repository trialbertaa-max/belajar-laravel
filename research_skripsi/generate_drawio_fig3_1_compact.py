# -*- coding: utf-8 -*-
import xml.etree.ElementTree as ET

# Ultra-compact, non-tall Draw.io XML for Gambar 3.1 Diagram Alir Penelitian
# Total height is cut down by ~50% (under 500px total!) to prevent vertical stretching

xml_content = """<mxfile host="app.diagrams.net" modified="2026-10-14T08:45:00.000Z" agent="5.0" version="24.0.0" type="device">
  <diagram id="diagram_alir_ringkas_3_1" name="Gambar 3.1 Diagram Alir Ringkas">
    <mxGraphModel dx="1000" dy="700" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="560" pageHeight="510" background="#ffffff" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- 0. START (Mulai Penelitian) -->
        <mxCell id="start_node" value="&lt;b style=&quot;font-size: 13px; font-family: 'Times New Roman'; color: #0f172a;&quot;&gt;Mulai Penelitian&lt;/b&gt;" style="rounded=1;arcSize=50;whiteSpace=wrap;html=1;fillColor=#f8fafc;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="195" y="15" width="170" height="34" as="geometry" />
        </mxCell>

        <!-- 1. KOTAK 1: Persiapan Dataset -->
        <mxCell id="box1" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.3;&quot;&gt;&lt;b style=&quot;font-size: 13px; color: #0f172a;&quot;&gt;1. Persiapan &amp;amp; Kurasi Dataset&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11.5px; color: #334155;&quot;&gt;100 citra DICOM toraks PA (CDC Surabaya) &amp;amp; de-identifikasi data pasien&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=12;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="60" y="64" width="440" height="46" as="geometry" />
        </mxCell>

        <!-- 2. KOTAK 2: Praproses & Derau -->
        <mxCell id="box2" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.3;&quot;&gt;&lt;b style=&quot;font-size: 13px; color: #0f172a;&quot;&gt;2. Praproses &amp;amp; Pemodelan Derau Dosis Rendah&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11.5px; color: #334155;&quot;&gt;Ekstraksi metadata &amp;amp; injeksi derau campuran Poisson-Gaussian dosis rendah&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=12;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="60" y="124" width="440" height="46" as="geometry" />
        </mxCell>

        <!-- 3. KOTAK 3: Restorasi Citra Komparatif -->
        <mxCell id="box3" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.3;&quot;&gt;&lt;b style=&quot;font-size: 13px; color: #0f172a;&quot;&gt;3. Implementasi Restorasi Citra Komparatif&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11.5px; color: #334155;&quot;&gt;Noisy Baseline, Filter (Bilateral, Gaussian, Median) &amp;amp; DnCNN (17-Layer)&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=12;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="60" y="184" width="440" height="46" as="geometry" />
        </mxCell>

        <!-- 4. KOTAK 4: Pengukuran CTR -->
        <mxCell id="box4" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.3;&quot;&gt;&lt;b style=&quot;font-size: 13px; color: #0f172a;&quot;&gt;4. Pengukuran Biometrik CTR Danzer (1919)&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11.5px; color: #334155;&quot;&gt;Pengukuran diameter jantung (&lt;i&gt;D&lt;sub&gt;H&lt;/sub&gt;&lt;/i&gt;) &amp;amp; toraks (&lt;i&gt;D&lt;sub&gt;T&lt;/sub&gt;&lt;/i&gt;) vs ground truth radiolog&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=12;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="60" y="244" width="440" height="46" as="geometry" />
        </mxCell>

        <!-- 5A. KOTAK 5a (Kiri): Evaluasi Kualitas Citra -->
        <mxCell id="box5a" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.3;&quot;&gt;&lt;b style=&quot;font-size: 12.5px; color: #0f172a;&quot;&gt;5a. Evaluasi Kualitas Citra&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11px; color: #334155;&quot;&gt;Metrik: PSNR, SSIM, SNR Gain, &amp;amp; Latensi&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=12;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="60" y="304" width="212" height="46" as="geometry" />
        </mxCell>

        <!-- 5B. KOTAK 5b (Kanan): Evaluasi Akurasi CTR -->
        <mxCell id="box5b" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.3;&quot;&gt;&lt;b style=&quot;font-size: 12.5px; color: #0f172a;&quot;&gt;5b. Evaluasi Akurasi CTR&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11px; color: #334155;&quot;&gt;Biometrik: MAE, &lt;i&gt;Bias&lt;/i&gt;, Pearson &lt;i&gt;r&lt;/i&gt;, &amp;amp; LoA&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=12;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="288" y="304" width="212" height="46" as="geometry" />
        </mxCell>

        <!-- 6. KOTAK 6: Uji Statistik & Integrasi -->
        <mxCell id="box6" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.3;&quot;&gt;&lt;b style=&quot;font-size: 13px; color: #0f172a;&quot;&gt;6. Uji Statistik &amp;amp; Integrasi Sistem&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11.5px; color: #334155;&quot;&gt;Uji ANOVA / Friedman, Tukey HSD, &amp;amp; implementasi ke Web Hyu PACS&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=12;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="60" y="364" width="440" height="46" as="geometry" />
        </mxCell>

        <!-- 7. END (Selesai / Laporan Skripsi) -->
        <mxCell id="end_node" value="&lt;b style=&quot;font-size: 13px; font-family: 'Times New Roman'; color: #0f172a;&quot;&gt;Selesai / Laporan Skripsi&lt;/b&gt;" style="rounded=1;arcSize=50;whiteSpace=wrap;html=1;fillColor=#f8fafc;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="195" y="424" width="170" height="34" as="geometry" />
        </mxCell>

        <!-- ==================== CONNECTORS ==================== -->
        <!-- Start -> Box 1 -->
        <mxCell id="c0_1" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="start_node" target="box1">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 1 -> Box 2 -->
        <mxCell id="c1_2" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box1" target="box2">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 2 -> Box 3 -->
        <mxCell id="c2_3" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box2" target="box3">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 3 -> Box 4 -->
        <mxCell id="c3_4" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box3" target="box4">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 4 -> Split to 5a and 5b -->
        <mxCell id="c4_split" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;" edge="1" parent="1" source="box4">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="280" y="290" as="sourcePoint" />
            <mxPoint x="280" y="297" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c4_bar" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="166" y="297" as="sourcePoint" />
            <mxPoint x="394" y="297" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- Arrow to 5a -->
        <mxCell id="c4_to_5a" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" target="box5a">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="166" y="297" as="sourcePoint" />
            <mxPoint x="166" y="304" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- Arrow to 5b -->
        <mxCell id="c4_to_5b" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" target="box5b">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="394" y="297" as="sourcePoint" />
            <mxPoint x="394" y="304" as="targetPoint" />
          </mxGeometry>
        </mxCell>

        <!-- 5a & 5b -> Merge to Box 6 -->
        <mxCell id="c5a_down" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;" edge="1" parent="1" source="box5a">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="166" y="350" as="sourcePoint" />
            <mxPoint x="166" y="357" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c5b_down" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;" edge="1" parent="1" source="box5b">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="394" y="350" as="sourcePoint" />
            <mxPoint x="394" y="357" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c5_merge_bar" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="166" y="357" as="sourcePoint" />
            <mxPoint x="394" y="357" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c5_to_6" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" target="box6">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="280" y="357" as="sourcePoint" />
            <mxPoint x="280" y="364" as="targetPoint" />
          </mxGeometry>
        </mxCell>

        <!-- Box 6 -> End -->
        <mxCell id="c6_end" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=1.8;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box6" target="end_node">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

with open("Gambar_3_1_Diagram_Alir_Penelitian.drawio", "w", encoding="utf-8") as f:
    f.write(xml_content.strip())

print("Berhasil membuat Gambar 3.1 Draw.io versi super ringkas & tidak kepanjangan!")
