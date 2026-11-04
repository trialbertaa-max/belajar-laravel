# -*- coding: utf-8 -*-
"""
Generate Draw.io XML for Gambar 3.1: Diagram Alir Tahapan Penelitian.
Optimized for academic skripsi with concise, high-impact text, large typography,
and balanced A4-friendly proportions so text remains crisp and highly legible in PDF.
"""

xml_content = """<mxfile host="app.diagrams.net" modified="2026-10-14T08:30:00.000Z" agent="5.0" version="24.0.0" type="device">
  <diagram id="diagram_alir_penelitian_3_1" name="Gambar 3.1 Diagram Alir Penelitian">
    <mxGraphModel dx="1000" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="640" pageHeight="870" background="#ffffff" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />

        <!-- 0. START (Mulai Penelitian) -->
        <mxCell id="start_node" value="&lt;b style=&quot;font-size: 13.5px; font-family: 'Times New Roman'; color: #0f172a;&quot;&gt;Mulai Penelitian&lt;/b&gt;" style="rounded=1;arcSize=50;whiteSpace=wrap;html=1;fillColor=#f8fafc;strokeColor=#334155;strokeWidth=2;" vertex="1" parent="1">
          <mxGeometry x="225" y="25" width="190" height="42" as="geometry" />
        </mxCell>

        <!-- 1. KOTAK 1: Persiapan & Kurasi Dataset -->
        <mxCell id="box1" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.4;&quot;&gt;&lt;b style=&quot;font-size: 13.5px; color: #0f172a;&quot;&gt;1. Persiapan &amp;amp; Kurasi Dataset&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px; color: #334155;&quot;&gt;• Pengumpulan 100 citra DICOM toraks PA (CDC Surabaya)&lt;br&gt;• De-identifikasi metadata &amp;amp; data pribadi pasien&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=10;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="120" y="95" width="400" height="74" as="geometry" />
        </mxCell>

        <!-- 2. KOTAK 2: Praproses & Pemodelan Derau -->
        <mxCell id="box2" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.4;&quot;&gt;&lt;b style=&quot;font-size: 13.5px; color: #0f172a;&quot;&gt;2. Praproses &amp;amp; Pemodelan Derau Dosis Rendah&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px; color: #334155;&quot;&gt;• Ekstraksi Rescale Slope/Intercept &amp;amp; Pixel Spacing&lt;br&gt;• Injeksi derau campuran Poisson-Gaussian pada citra&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=10;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="120" y="195" width="400" height="74" as="geometry" />
        </mxCell>

        <!-- 3. KOTAK 3: Implementasi Restorasi Citra Komparatif -->
        <mxCell id="box3" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.4;&quot;&gt;&lt;b style=&quot;font-size: 13.5px; color: #0f172a;&quot;&gt;3. Implementasi Restorasi Citra Komparatif&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px; color: #334155;&quot;&gt;• Kondisi Kontrol: &lt;i&gt;Noisy Baseline&lt;/i&gt;&lt;br&gt;• Filter Pembanding: Gaussian &amp;amp; Median Filter&lt;br&gt;• Metode Restorasi: Bilateral Filter &amp;amp; DnCNN (17-Layer)&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=10;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="120" y="295" width="400" height="84" as="geometry" />
        </mxCell>

        <!-- 4. KOTAK 4: Pengukuran Biometrik CTR -->
        <mxCell id="box4" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.4;&quot;&gt;&lt;b style=&quot;font-size: 13.5px; color: #0f172a;&quot;&gt;4. Pengukuran Biometrik CTR Danzer (1919)&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px; color: #334155;&quot;&gt;• Penarikan diameter jantung (&lt;i&gt;D&lt;sub&gt;H&lt;/sub&gt;&lt;/i&gt;) dan toraks (&lt;i&gt;D&lt;sub&gt;T&lt;/sub&gt;&lt;/i&gt;) terkalibrasi&lt;br&gt;• Komparasi terhadap &lt;i&gt;ground truth&lt;/i&gt; Dokter Spesialis Radiologi&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=10;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="120" y="405" width="400" height="74" as="geometry" />
        </mxCell>

        <!-- 5A. KOTAK 5a (Kiri): Evaluasi Kualitas Citra -->
        <mxCell id="box5a" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.35;&quot;&gt;&lt;b style=&quot;font-size: 13px; color: #0f172a;&quot;&gt;5a. Evaluasi Kualitas Citra&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11.5px; color: #334155;&quot;&gt;Kalkulasi metrik objektif:&lt;br&gt;PSNR, SSIM, SNR Gain,&lt;br&gt;&amp;amp; Latensi Komputasi (ms)&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=10;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="120" y="515" width="190" height="82" as="geometry" />
        </mxCell>

        <!-- 5B. KOTAK 5b (Kanan): Evaluasi Akurasi Biometrik -->
        <mxCell id="box5b" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.35;&quot;&gt;&lt;b style=&quot;font-size: 13px; color: #0f172a;&quot;&gt;5b. Evaluasi Akurasi CTR&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 11.5px; color: #334155;&quot;&gt;Kalkulasi biometrik:&lt;br&gt;MAE, &lt;i&gt;Bias&lt;/i&gt;, Pearson &lt;i&gt;r&lt;/i&gt;,&lt;br&gt;&amp;amp; Bland-Altman LoA&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=10;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="330" y="515" width="190" height="82" as="geometry" />
        </mxCell>

        <!-- 6. KOTAK 6: Uji Statistik & Integrasi Sistem -->
        <mxCell id="box6" value="&lt;div style=&quot;text-align: center; font-family: 'Times New Roman'; line-height: 1.4;&quot;&gt;&lt;b style=&quot;font-size: 13.5px; color: #0f172a;&quot;&gt;6. Uji Statistik &amp;amp; Integrasi Sistem&lt;/b&gt;&lt;br&gt;&lt;span style=&quot;font-size: 12px; color: #334155;&quot;&gt;• Uji komparasi multikelompok: ANOVA / Friedman &amp;amp; Tukey HSD&lt;br&gt;• Hilirisasi purwarupa Web DICOM Workstation (Hyu PACS)&lt;/span&gt;&lt;/div&gt;" style="rounded=1;arcSize=10;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#334155;strokeWidth=1.8;" vertex="1" parent="1">
          <mxGeometry x="120" y="625" width="400" height="74" as="geometry" />
        </mxCell>

        <!-- 7. END (Selesai / Laporan Skripsi) -->
        <mxCell id="end_node" value="&lt;b style=&quot;font-size: 13.5px; font-family: 'Times New Roman'; color: #0f172a;&quot;&gt;Selesai / Laporan Skripsi&lt;/b&gt;" style="rounded=1;arcSize=50;whiteSpace=wrap;html=1;fillColor=#f8fafc;strokeColor=#334155;strokeWidth=2;" vertex="1" parent="1">
          <mxGeometry x="225" y="730" width="190" height="42" as="geometry" />
        </mxCell>

        <!-- ==================== CONNECTORS ==================== -->
        <!-- Start -> Box 1 -->
        <mxCell id="c0_1" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="start_node" target="box1">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 1 -> Box 2 -->
        <mxCell id="c1_2" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box1" target="box2">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 2 -> Box 3 -->
        <mxCell id="c2_3" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box2" target="box3">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 3 -> Box 4 -->
        <mxCell id="c3_4" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box3" target="box4">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

        <!-- Box 4 -> Split to 5a and 5b -->
        <mxCell id="c4_split" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;" edge="1" parent="1" source="box4">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="320" y="480" as="sourcePoint" />
            <mxPoint x="320" y="498" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c4_bar" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="215" y="498" as="sourcePoint" />
            <mxPoint x="425" y="498" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- Arrow to 5a -->
        <mxCell id="c4_to_5a" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" target="box5a">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="215" y="498" as="sourcePoint" />
            <mxPoint x="215" y="515" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- Arrow to 5b -->
        <mxCell id="c4_to_5b" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" target="box5b">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="425" y="498" as="sourcePoint" />
            <mxPoint x="425" y="515" as="targetPoint" />
          </mxGeometry>
        </mxCell>

        <!-- 5a & 5b -> Merge to Box 6 -->
        <mxCell id="c5a_down" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;" edge="1" parent="1" source="box5a">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="215" y="597" as="sourcePoint" />
            <mxPoint x="215" y="612" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c5b_down" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;" edge="1" parent="1" source="box5b">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="425" y="597" as="sourcePoint" />
            <mxPoint x="425" y="612" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c5_merge_bar" value="" style="endArrow=none;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="215" y="612" as="sourcePoint" />
            <mxPoint x="425" y="612" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <mxCell id="c5_to_6" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" target="box6">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="320" y="612" as="sourcePoint" />
            <mxPoint x="320" y="625" as="targetPoint" />
          </mxGeometry>
        </mxCell>

        <!-- Box 6 -> End -->
        <mxCell id="c6_end" value="" style="endArrow=classic;html=1;rounded=0;strokeColor=#1e293b;strokeWidth=2;exitX=0.5;exitY=1;exitDx=0;exitDy=0;entryX=0.5;entryY=0;entryDx=0;entryDy=0;" edge="1" parent="1" source="box6" target="end_node">
          <mxGeometry width="50" height="50" relative="1" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

with open("Gambar_3_1_Diagram_Alir_Penelitian.drawio", "w", encoding="utf-8") as f:
    f.write(xml_content.strip())

print("Successfully generated Gambar_3_1_Diagram_Alir_Penelitian.drawio")
