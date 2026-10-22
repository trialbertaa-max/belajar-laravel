# Panduan Menjalankan Training Model di Google Colab (100% Gratis)

Panduan ini memandu Anda melatih model **In-House AI Bone Suppression** menggunakan **Google Colab** tanpa membebani laptop atau kuota internet lokal Anda.

---

### Langkah 1: Buka Google Colab
1. Buka browser web (Google Chrome atau Microsoft Edge).
2. Kunjungi situs resmi Google Colab:  
   👉 [https://colab.research.google.com](https://colab.research.google.com)
3. Masuk (*Sign in*) menggunakan akun Google biasa Anda.

---

### Langkah 2: Upload File Notebook Training
1. Pada jendela pop-up awal di Colab, pilih tab **"Upload"** (atau klik menu **File** $\rightarrow$ **Upload notebook**).
2. Klik tombol **"Browse"** dan pilih file dari laptop Anda di lokasi:  
   `d:\laragon\www\belajar-laravel\medical_engine\training\hyu_bone_suppression_lidc_training.ipynb`
3. Notebook akan terbuka di layar browser Anda.

---

### Langkah 3: Aktifkan GPU Gratis (NVIDIA T4 16 GB)
1. Klik menu di bagian atas: **Runtime** $\rightarrow$ **Change runtime type** (Ubah jenis runtime).
2. Di bagian *Hardware accelerator*, pilih **T4 GPU**.
3. Klik **Save**.
4. Di pojok kanan atas Colab, Anda akan melihat status *Connected* dengan ikon RAM/Disk dan GPU siap digunakan.

---

### Langkah 4: Jalankan Training (Sekali Klik atau Lanjutkan Sesi Aktif)
> **💡 Catatan Penting jika Sesi Colab Anda Sudah Aktif:**  
> Jika 10 volume CT sudah ada di `/content/lidc_raw/`, notebook v2.0 secara otomatis **melewati pengunduhan ulang**, sehingga Anda langsung menghemat waktu dan kuota!
>
> **Pilihan A (Upload Ulang Notebook):**  
> Di Colab, klik **File** $\rightarrow$ **Upload notebook** $\rightarrow$ pilih `hyu_bone_suppression_lidc_training.ipynb` yang baru, lalu klik **Runtime** $\rightarrow$ **Run all**.
>
> **Pilihan B (Ganti Cell 4 ke Bawah pada Tab yang Sedang Terbuka):**  
> Salin kode baru Langkah 4 (Generator DRR v2.0), Langkah 5, Langkah 6, Langkah 7, dan Langkah 8 ke tab Colab Anda yang sedang terbuka, lalu jalankan bertahap.

1. Jalankan proses:
   * **Langkah 4 (DRR v2.0):** Menghasilkan 140 pasangan citra berakurasi sub-piksel dengan Shared Normalization.
   * **Langkah 5 (Inspeksi):** Verifikasi bahwa panel 3 (X - Y) memancarkan kerangka tulang iga tajam berwarna emas/merah, sedangkan jaringan paru hitam pekat (0.0).
   * **Langkah 6 & 7 (Training):** Melatih UNet dengan Dual Supervision (L1 Bone + L1 Soft + Grad Loss) selama 40 epoch (~2 menit di GPU T4).
   * **Langkah 8 (Export):** Menyimpan model biner TorchScript `bone_suppression_commercial.ts`.
2. Di akhir proses (Langkah 9), browser akan otomatis mengunduh file bobot `bone_suppression_commercial.ts` ke folder Downloads laptop Anda.

---

### Langkah 5: Pindahkan File Bobot ke Hyu PACS
1. Ambil file `bone_suppression_commercial.ts` dari folder *Downloads* laptop Anda.
2. Pindahkan file tersebut ke folder proyek:  
   `d:\laragon\www\belajar-laravel\medical_engine\weights\bone_suppression_commercial.ts`
3. Buka Hyu PACS di browser Anda:  
   `http://127.0.0.1:8000/scans/1`
4. Pada dropdown Filter Engine:
   * Pilih **`[AI-01] Bone Suppression (In-House Commercial Engine)`**.
   * Klik tombol **"▶ Jalankan In-House AI Bone Suppression"**.
5. **Selesai!** Model in-house komersial Anda kini berjalan 100% penuh secara mandiri dan legal untuk dipresentasikan ke dokter atau atasan klinik.
