# Rencana Implementasi: Upload Excel → Tabel Edit (CRUD) → Kirim ke Worklist

Proyek: HYU_PACS (Laravel)
Fitur: modal **"Buat Sesi / Order MCU Baru (Hyu)"** → tombol **Upload Excel**

> Dokumen ini ditulis untuk AI agent. Setiap tahap berisi tujuan, langkah, dan hasil yang harus ada. Kerjakan berurutan. Jangan ubah fitur lain di luar yang disebut di sini.

---

## 1. Ringkasan masalah dan tujuan

**Kondisi sekarang**
Setelah file Excel diupload, sistem membaca semua pasien (contoh: 152 pasien, 88 siap, 64 perlu dicek) lalu menampilkannya sebagai **dropdown**. User memilih satu pasien, datanya mengisi form, lalu dikirim ke worklist satu per satu.

**Masalah**
- Dropdown sulit dipakai untuk ratusan pasien.
- Data yang salah ("perlu dicek") tidak bisa diperbaiki langsung.
- Mengirim pasien satu per satu lambat.

**Yang diinginkan**
1. Hasil baca Excel tampil sebagai **tabel** (bukan dropdown).
2. Tabel bisa **CRUD**: tambah baris, ubah data, hapus baris. Baca (Read) sudah dipenuhi karena data ditampilkan.
3. Setelah semua data dicek dan oke, user klik satu tombol untuk **mengirim semua sekaligus**.
4. Data yang terkirim **muncul di worklist**.

---

## 2. Alur kerja akhir (yang harus terjadi)

```
1. User klik "Upload Excel"
2. File dibaca → muncul tabel staging (belum masuk worklist)
3. Tiap baris dicek otomatis → status "Siap" atau "Perlu dicek"
4. User memperbaiki baris yang bermasalah (edit / hapus / tambah)
5. Saat semua baris yang dipilih berstatus "Siap" → tombol kirim aktif
6. User klik "Kirim X pasien ke Worklist"
7. Server cek ulang, simpan semua dalam satu transaksi
8. Modal tertutup, worklist menampilkan data baru
```

**Prinsip penting:** data di tabel staging **belum ada di database**. Data baru masuk ke worklist hanya setelah langkah 6–7.

---

## 3. Keputusan desain (sudah ditetapkan, ikuti)

| Hal | Keputusan |
|---|---|
| Tempat menyimpan hasil edit sebelum dikirim | Di **memori browser** (array JavaScript / state). Tidak ada tabel draft di database. |
| Cara kirim | **Satu request** `POST` berisi semua baris yang dipilih |
| Pengecekan data | Dilakukan di **browser** (supaya cepat) dan diulang di **server** (supaya aman). Server adalah penentu akhir. |
| Penyimpanan di database | Dalam **satu transaksi**: kalau satu gagal, semua dibatalkan |
| Baris "Perlu dicek" | Tidak boleh terkirim. User harus memperbaikinya, atau menghapusnya, atau menghilangkan centang barisnya |
| Tampilan tabel | Pakai **paginasi** (mis. 25 baris per halaman) karena data bisa ratusan |
| Pemisahan sesi | Tiap pasien dimasukkan ke sesi sesuai tanggalnya lewat `SesiMcu::untukTanggal()` (kalau fitur sesi sudah dibangun; kalau belum, lewati dan tambahkan nanti) |

---

## 4. Kolom tabel staging

| Kolom | Isi | Bisa diedit | Aturan |
|---|---|---|---|
| ☑ (centang) | Pilih baris untuk dikirim | Ya | Default tercentang jika status Siap |
| No | Nomor urut | Tidak | Otomatis |
| Status | Siap / Perlu dicek | Tidak | Dihitung otomatis dari aturan di bawah |
| Nama Lengkap | Teks | Ya | Wajib, minimal 2 karakter |
| No. RM / NIK | Teks angka | Ya | Boleh kosong (otomatis dibuat server). Jika diisi sebagai NIK: 16 digit angka |
| Usia (tahun) | Angka | Ya | Wajib, 0–120 |
| Jenis Kelamin | Pilihan | Ya | Wajib: Laki-laki / Perempuan |
| Modalitas Pemeriksaan | Pilihan | Ya | Wajib, harus ada di daftar protokol yang sama dengan form manual |
| Indikasi Klinis / Deskripsi Sesi | Teks | Ya | Opsional |
| Catatan untuk Petugas Rontgen | Teks | Ya | Opsional |
| Aksi | Tombol ✏️ Ubah, 🗑️ Hapus | - | - |

Daftar pilihan **Modalitas** dan **Jenis Kelamin** harus diambil dari sumber yang sama dengan form manual, jangan ditulis ulang di tempat lain.

---

## 5. Aturan status "Siap" dan "Perlu dicek"

Satu baris disebut **Perlu dicek** jika salah satu ini terjadi:

1. Nama kosong atau terlalu pendek.
2. NIK diisi tetapi bukan 16 digit angka.
3. Usia kosong, bukan angka, atau di luar 0–120.
4. Jenis kelamin kosong atau tidak dikenali.
5. Modalitas kosong atau tidak ada di daftar protokol.
6. **Duplikat di dalam file**: NIK sama **dan** modalitas sama dengan baris lain.
7. **Duplikat di worklist**: pasien dengan NIK dan modalitas yang sama sudah ada pada sesi tanggal yang sama (dicek oleh server, lihat Tahap 4).

Selain itu → **Siap**.

Setiap baris yang bermasalah harus menampilkan **alasan singkat** (mis. "NIK harus 16 digit", "Modalitas tidak dikenal"). Tampilkan lewat ikon ⚠ yang jika di-hover/diklik memunculkan teksnya, dan beri warna kuning pada sel yang salah.

Status dihitung ulang **setiap kali** user mengubah sel.

---

## 6. Tahap pengerjaan

### Tahap 1 – Baca Excel menjadi daftar baris

**Tujuan:** hasil baca file menjadi array objek yang rapi.

Langkah:
1. Pakai pembaca Excel yang **sudah ada** di proyek. Jangan ganti library kecuali pembaca lama tidak bisa dipakai.
2. Ubah setiap baris Excel menjadi objek dengan struktur ini:
   ```js
   {
     id: "uuid-sementara",      // id unik di browser, bukan id database
     nama: "",
     nik: "",
     usia: null,
     jenis_kelamin: "",
     modalitas: "",
     indikasi: "",
     catatan: "",
     dipilih: true,
     errors: {}                 // contoh: { nik: "NIK harus 16 digit" }
   }
   ```
3. Abaikan baris Excel yang kosong semua.
4. Rapikan data: hapus spasi di awal/akhir, ubah NIK yang terbaca sebagai angka ilmiah (mis. `3.2E+15`) menjadi teks 16 digit. Ini sering terjadi pada Excel.
5. Kalau kolom wajib tidak ditemukan di file, tampilkan pesan jelas: "Kolom X tidak ditemukan. Gunakan template yang disediakan."

Hasil yang diharapkan: array baris siap dipakai tabel.

### Tahap 2 – Fungsi pengecekan (validasi) di browser

**Tujuan:** satu fungsi yang menghitung `errors` dan status tiap baris.

Langkah:
1. Buat fungsi `validasiBaris(baris, semuaBaris)` yang menerapkan aturan di bagian 5 nomor 1–6.
2. Buat fungsi `hitungRingkasan(semuaBaris)` yang menghasilkan jumlah total, jumlah siap, jumlah perlu dicek, dan jumlah yang dicentang.
3. Panggil validasi setelah Excel dibaca, dan setelah setiap perubahan (ubah, tambah, hapus).
4. Pengecekan duplikat harus dihitung dari **seluruh** array, bukan hanya baris yang berubah. Saat satu baris diubah, baris lain yang duplikat dengannya juga harus diperbarui.

### Tahap 3 – Tampilan tabel staging

**Tujuan:** mengganti dropdown dengan tabel yang mudah dipakai. Pertahankan gaya gelap dan warna yang sudah ada.

Langkah:
1. **Bagian atas** (pertahankan yang sudah ada): pesan sukses ("File … berhasil dibaca"), ringkasan "Ditemukan 152 pasien (88 siap, 64 perlu dicek)", kolom cari nama/NIK, dan tombol **Ganti file**.
2. Tambahkan **filter status**: `Semua | Siap | Perlu dicek`.
3. Tambahkan **toolbar tabel**: tombol `+ Tambah pasien`, dan `Hapus yang dicentang`.
4. **Tabel** sesuai kolom di bagian 4.
   - Edit langsung di sel (inline editing). Klik sel → berubah jadi input; tekan Enter atau klik di luar untuk menyimpan; Esc untuk batal.
   - Kolom Jenis Kelamin dan Modalitas memakai `<select>`.
   - Baris "Perlu dicek" diberi warna kuning lembut dan ikon ⚠.
5. **Paginasi** 25 baris per halaman. Pencarian dan filter bekerja di seluruh data, bukan hanya halaman yang tampil.
6. **Centang semua** di header tabel (hanya menyentang baris berstatus Siap yang terlihat sesuai filter).
7. **Bagian bawah (footer)**:
   - Teks: "X dipilih dari Y pasien".
   - Tombol **Batal**.
   - Tombol **Kirim X pasien ke Worklist**. Tombol ini **nonaktif** kalau tidak ada baris dicentang, atau ada baris dicentang yang statusnya Perlu dicek.

### Tahap 4 – Fungsi CRUD di tabel

**Create (tambah)**
- Klik `+ Tambah pasien` → muncul baris kosong di paling atas halaman pertama, langsung masuk mode edit pada kolom Nama.
- Nilai awal: usia kosong, modalitas kosong. Status awal: Perlu dicek.

**Read (baca)**
- Data tampil di tabel, bisa dicari dan difilter (Tahap 3).

**Update (ubah)**
- Edit inline untuk semua kolom.
- Tombol ✏️ membuka **panel/modal kecil** berisi semua kolom (termasuk Indikasi dan Catatan yang panjang) untuk edit yang lebih nyaman.
- Setiap perubahan langsung memicu validasi ulang (Tahap 2).

**Delete (hapus)**
- Tombol 🗑️ per baris, dan `Hapus yang dicentang` untuk banyak baris.
- Hapus dari tabel **hanya menghapus dari staging di browser**. Tidak ada data database yang ikut terhapus.
- Tampilkan konfirmasi singkat untuk hapus banyak baris.
- Tampilkan tombol **Urungkan (Undo)** selama beberapa detik setelah menghapus, supaya salah klik tidak fatal.

### Tahap 5 – Endpoint kirim ke server (Laravel)

**Tujuan:** menyimpan semua baris terpilih ke worklist dengan aman.

Langkah:
1. Buat route: `POST /worklist/import` (beri nama yang sesuai gaya route di proyek). Lindungi dengan middleware auth dan CSRF yang sama dengan route worklist lainnya.
2. Buat **Form Request** (mis. `ImportWorklistRequest`) yang memvalidasi array `pasien`:
   - `pasien` wajib array, minimal 1, maksimal 500 item (batas aman).
   - Tiap item: `nama` wajib; `nik` opsional, jika ada harus 16 digit angka; `usia` wajib integer 0–120; `jenis_kelamin` wajib salah satu dari daftar; `modalitas` wajib ada di daftar protokol; `indikasi` dan `catatan` opsional (batasi panjang).
3. Buat **Controller/Service** yang melakukan:
   1. Cek duplikat dalam kiriman (NIK + modalitas sama).
   2. Cek duplikat di database (NIK + modalitas yang sama di sesi tanggal yang sama).
   3. Jika ada masalah, **jangan simpan apa pun**. Kembalikan error per baris (lihat format di bawah).
   4. Jika semua baik, jalankan dalam `DB::transaction()`:
      - Untuk tiap pasien: buat/cari data pasien, buat nomor accession, buat studi/order worklist dengan status awal yang sama seperti order manual (mis. "Menunggu"), isi `sesi_id` lewat `SesiMcu::untukTanggal()` jika ada.
   5. Kembalikan ringkasan sukses.
4. Gunakan **logika pembuatan order yang sudah ada** (sama dengan tombol "Simpan & Kirim ke Worklist" pada form manual). Jangan menulis ulang logika pembuatan accession atau pengisian data. Panggil ulang saja lewat method/service yang sama, agar hasil upload Excel dan input manual **identik**.
5. Jika No. RM kosong, buat otomatis dengan cara yang sama seperti form manual ("Otomatis jika kosong").

**Format respons error** (HTTP 422):
```json
{
  "message": "Ada data yang perlu diperbaiki.",
  "errors": {
    "pasien.3.nik": ["NIK harus 16 digit."],
    "pasien.7.modalitas": ["Pasien ini sudah ada di worklist tanggal ini."]
  }
}
```
Angka setelah `pasien.` adalah **urutan** dalam array yang dikirim. Browser harus memetakannya kembali ke baris yang tepat (kirim juga `id` sementara tiap baris, atau simpan peta urutan→id).

**Format respons sukses** (HTTP 200):
```json
{
  "message": "88 pasien berhasil dikirim ke worklist.",
  "jumlah": 88
}
```

### Tahap 6 – Hubungkan tombol Kirim di browser

Langkah:
1. Saat tombol **Kirim** diklik: kumpulkan hanya baris yang dicentang, buang field yang hanya dipakai di browser (`errors`, `dipilih`), lalu kirim ke endpoint Tahap 5.
2. Selama menunggu: tombol berubah jadi loading dan nonaktif (cegah klik ganda), tabel tidak bisa diedit.
3. Jika **sukses**: tutup modal, tampilkan notifikasi "88 pasien berhasil dikirim", dan **segarkan worklist** supaya data baru tampil (muat ulang daftar, atau tambahkan baris baru tanpa reload halaman, ikuti cara yang dipakai worklist sekarang).
4. Jika **gagal 422**: jangan tutup modal. Petakan error ke baris yang sesuai, tandai baris itu sebagai Perlu dicek dengan alasan dari server, lalu bawa user ke halaman tabel yang berisi baris bermasalah pertama.
5. Jika **gagal lain** (jaringan/500): tampilkan pesan umum "Gagal mengirim, data Anda masih aman di tabel. Coba lagi." Jangan hapus data staging.

### Tahap 7 – Pengaman tambahan (ikuti semua)

- **Peringatan keluar:** jika ada data staging dan user menutup modal, klik Batal, atau menutup tab, tampilkan konfirmasi "Data yang belum dikirim akan hilang."
- **Ganti file:** jika user klik "Ganti file" saat sudah ada perubahan, tampilkan konfirmasi bahwa hasil edit akan hilang.
- **Batas ukuran:** tolak file yang terlalu besar atau lebih dari 500 baris, dengan pesan jelas.
- **Data sensitif:** jangan mencatat (log) nama atau NIK pasien di log aplikasi. Ini data medis.
- **Hak akses:** hanya pengguna yang boleh membuat order manual yang boleh memakai import ini.

### Tahap 8 – Pengujian

Uji manual dan, jika proyek sudah memakai test otomatis, buat juga Feature Test untuk endpoint.

**Browser**
1. Upload file 152 pasien → tabel tampil, ringkasan 88 siap / 64 perlu dicek sesuai.
2. Ubah NIK salah menjadi 16 digit → baris berubah ke Siap dan angka ringkasan ikut berubah.
3. Hapus baris → hilang dari tabel, bisa di-undo.
4. Tambah pasien baru → baris muncul, bisa diisi lalu berstatus Siap.
5. Dua baris dengan NIK dan modalitas sama → keduanya ditandai duplikat.
6. Pencarian dan filter bekerja pada seluruh data (bukan hanya halaman aktif).
7. Tombol Kirim nonaktif jika ada baris tercentang yang masih Perlu dicek.
8. NIK di Excel yang terbaca seperti `3.2E+15` berhasil dirapikan.

**Server**
1. Kirim data valid → tersimpan semua, muncul di worklist.
2. Kirim 1 baris tidak valid di antara 50 baris → tidak ada yang tersimpan, respons 422 menunjuk baris yang benar.
3. Kirim pasien duplikat dengan data yang sudah ada di sesi tanggal itu → ditolak dengan pesan jelas.
4. Kirim 501 baris → ditolak.
5. Kirim tanpa login → ditolak.
6. Hasil order dari import identik dengan order dari form manual (status, accession, sesi).

---

## 7. Daftar file yang kemungkinan dibuat/diubah

Sesuaikan dengan struktur proyek yang sebenarnya. Cari dulu file yang sudah ada sebelum membuat baru.

| Jenis | Perubahan |
|---|---|
| Blade/View modal order | Ganti blok dropdown pasien menjadi tabel staging |
| JavaScript modal order | Tambah state `stagingRows`, validasi, CRUD, paginasi, kirim |
| Route web | Tambah `POST /worklist/import` |
| Form Request | `ImportWorklistRequest` |
| Controller / Service | Method import yang memanggil logika pembuatan order yang sudah ada |
| Test | Feature test untuk import (jika proyek memakai test) |

---

## 8. Urutan pengerjaan yang disarankan

1. Tahap 1–2: baca Excel dan validasi (tanpa mengubah tampilan dulu, cek di console)
2. Tahap 3: tampilan tabel (read-only dulu)
3. Tahap 4: CRUD
4. Tahap 5: endpoint server
5. Tahap 6: hubungkan tombol Kirim
6. Tahap 7–8: pengaman dan pengujian

Setelah tiap tahap, jalankan aplikasi dan pastikan fitur lama (form manual, upload lama) masih berfungsi.

---

## 9. Kriteria selesai (Definition of Done)

- [ ] Dropdown pasien diganti tabel staging.
- [ ] Baris bisa ditambah, diubah, dan dihapus tanpa menyimpan ke database.
- [ ] Status Siap / Perlu dicek ter-update otomatis dan ada alasan jelas untuk baris bermasalah.
- [ ] Pencarian, filter status, dan paginasi bekerja untuk 150+ baris tanpa lambat.
- [ ] Tombol Kirim hanya aktif jika semua baris yang dicentang berstatus Siap.
- [ ] Semua baris terpilih terkirim dalam satu request dan tersimpan dalam satu transaksi.
- [ ] Data yang terkirim muncul di worklist, sama seperti order manual.
- [ ] Error dari server tampil di baris yang tepat dan data staging tidak hilang.
- [ ] Ada konfirmasi sebelum data belum terkirim hilang.
- [ ] Fitur form manual dan fitur lain tidak rusak.

---

## 10. Hal yang perlu dikonfirmasi ke pemilik proyek

Jika belum jelas, AI agent boleh memakai nilai bawaan di kolom kanan, lalu menyebutkannya di laporan akhir.

| Pertanyaan | Nilai bawaan |
|---|---|
| Boleh kirim sebagian saja (baris Siap) sambil mengabaikan yang Perlu dicek? | Ya, dengan mencentang hanya baris yang Siap |
| Tanggal sesi memakai tanggal apa untuk data Excel? | Tanggal hari upload, kecuali ada kolom tanggal di Excel |
| Batas maksimal baris per upload | 500 |
| Apakah perlu menyimpan draft jika modal ditutup? | Tidak (data hilang setelah konfirmasi) |
