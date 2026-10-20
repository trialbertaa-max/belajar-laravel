# Implementation Plan (v5): Dropdown "View" Menggantikan Tombol "Seri Pasien"

Dokumen ini ditulis untuk AI agent (proyek Laravel HYU_PACS, halaman viewer citra DICOM). Ikuti urutan fase. Sebelum mengubah kode, **baca dulu kode yang sudah ada**. Jangan menebak nama file, class, atau fungsi.

Versi ini **menggantikan v1 sampai v4**. Perubahan utama:

1. Dropdown `View` **menempati posisi tombol `◀ Seri Pasien (n)`** di toolbar.
2. Kategori **"Tool Interaksi" tidak dipakai**. `Pan`, `Zoom`, `Alat Ukur` dibiarkan di toolbar seperti sekarang.
3. Isi menu hanya **3 kelompok** sesuai daftar di Bagian 1.

---

## 0. Tujuan

Rapikan toolbar viewer dengan **satu tombol dropdown `View`** berbentuk menu bertingkat seperti menu `View` di VS Code (referensi: `View > Appearance ▸`):

- **Level 1**: daftar **kelompok**, masing-masing ditandai `▸`.
- **Level 2**: saat kelompok di-hover atau diklik, **submenu muncul di samping kanan** berisi item kelompok itu.
- Panel punya tombol **silang (X)** untuk menutup menu setelah dipakai.

---

## 1. Isi Menu (WAJIB persis seperti ini)

Nama kelompok dan nama item memakai teks yang sama dengan daftar yang diberikan pemilik proyek.

```
View ▾  (tombol di toolbar)
├── Window ▸                      (di kanan baris tampil nilai aktif, mis. "Default")
│   ├── Default                   ✓ (radio: hanya satu aktif)
│   ├── Lung
│   ├── Bone
│   └── Soft tissue
│
├── Orientasi ▸
│   ├── Putar 90 clockwise        (aksi)
│   ├── Cermin horizontal         ✓ bila aktif (toggle)
│   ├── Cermin vertikal           ✓ bila aktif (toggle)
│   └── Invert grayscale          ✓ bila aktif (toggle)
│
└── View ▸
    ├── Restorasi & Denoising
    ├── Image processing
    ├── Hasil ukur / CTR
    ├── Expertise radiology
    └── Tags DICOM
```

Catatan:

- Semua item di atas adalah **tombol/fitur yang sudah ada** di viewer. Tugasnya memindahkan penempatan, bukan membuat fitur baru. Jika salah satu item (mis. `Hasil ukur / CTR`, `Expertise radiology`, `Tags DICOM`) ada di bagian lain halaman, temukan dan hubungkan ke item menu, lalu **hapus tombol lamanya** agar tidak dobel.
- Nama kelompok ketiga adalah `View` (sesuai daftar). Jika terasa janggal karena sama dengan nama tombol induk, **biarkan dulu** dan sebutkan di ringkasan akhir agar pemilik proyek bisa memutuskan penggantian nama.
- Item di screenshot referensi memiliki ikon `×` di sebelah kanan beberapa baris. Itu hanya artefak tampilan daftar, **bukan bagian dari desain**. Jangan dibuat.

---

## 2. Perubahan di Toolbar

### Yang dipindah ke dalam dropdown (lalu dihapus dari posisi lamanya)

| Elemen lama | Masuk ke |
|---|---|
| Dropdown `Window: Default` | View > Window |
| Dropdown `Orientasi` | View > Orientasi |
| Tombol `Restorasi & Denoising` | View > View |
| Tombol `Image Processing` | View > View |
| Tombol/akses `Hasil ukur / CTR`, `Expertise radiology`, `Tags DICOM` (bila ada) | View > View |

### Yang diganti

| Elemen lama | Diganti dengan |
|---|---|
| Tombol `◀ Seri Pasien (n)` | Tombol dropdown `View ▾` di **posisi yang sama** (sesudah `← Sesi Pasien`, sebelum info pasien) |

> **PERINGATAN untuk agent:** `Seri Pasien` hanya dihilangkan dari **tampilan toolbar** sesuai permintaan. **Jangan menghapus logic/fungsinya** (handler, route, state). Tulis di ringkasan akhir: "Navigasi antar seri tidak lagi punya tombol di UI, perlu keputusan pemilik proyek apakah dipindah ke tempat lain." Jangan mengambil keputusan sendiri untuk menaruhnya di tempat lain.

### Yang TIDAK disentuh sama sekali

`← Sesi Pasien`, info pasien (nama, MRN, DX, usia/jenis kelamin), `1x1`, `1x2`, `Pan`, `Zoom`, `Alat Ukur`, `Undo`, `Hapus`, `Reset View`.

### Susunan toolbar akhir

```
Baris 1: [← Sesi Pasien] [View ▾] [BERTA | MRN | DX | M/21Y] [1x1][1x2]
Baris 2: [Pan] [Zoom] [Alat Ukur ▾] | [Undo] [Hapus] [Reset View]
```

Baris 2 menjadi lebih pendek karena `Window`, `Orientasi`, `Restorasi & Denoising`, dan `Image Processing` pindah ke menu. Rapikan jarak dan pemisah vertikal (`|`) agar tidak ada pemisah yang menggantung.

---

## 3. Fase 1: Discovery (WAJIB, sebelum coding)

Agent harus menemukan dan mencatat:

1. File Blade/komponen yang merender toolbar viewer (cari string `Seri Pasien`, `Alat Ukur`, `Reset View`, `Restorasi`).
2. Stack JavaScript halaman viewer (vanilla JS, Alpine, Livewire, Vue, atau library viewer seperti Cornerstone). **Ikuti stack yang sudah ada, jangan menambah library UI baru.**
3. Cara tiap tombol/dropdown yang akan dipindah terhubung ke logic-nya: `id`, `data-*`, `onclick`, `wire:click`, `@click`, atau event listener di file JS. **Semua harus tetap bekerja persis sama setelah dipindah.**
4. Cara dropdown `Window` dan `Orientasi` bekerja sekarang (nilai terpilih disimpan di mana, fungsi apa yang dipanggil).
5. Untuk `Cermin horizontal`, `Cermin vertikal`, `Invert grayscale`: apakah sudah ada state aktif/nonaktif di kode. Jika belum, **tentukan dan laporkan** cara membaca state-nya dari viewer tanpa mengubah perilaku.
6. Lokasi `Hasil ukur / CTR`, `Expertise radiology`, `Tags DICOM` di halaman saat ini.
7. Apakah item `Restorasi & Denoising`, `Image processing`, dan lainnya membuka modal atau panel samping.
8. Keyboard shortcut yang sudah ada (jika ada, tampilkan di sisi kanan item menu seperti `Ctrl+B` di VS Code).
9. File CSS/Tailwind yang dipakai (agar gaya dark theme cocok).

Output Fase 1: ringkasan singkat temuan sebelum lanjut.

---

## 4. Fase 2: Struktur HTML

Pertahankan elemen/handler yang ada. **Pindahkan** ke dalam menu atau bungkus ulang dengan atribut yang sama. Jangan menulis ulang logic-nya.

```html
<div class="viewer-toolbar">
  <a/button id="btn-sesi-pasien">← Sesi Pasien</a/button>

  <!-- MENGGANTIKAN tombol Seri Pasien -->
  <div class="view-menu" id="viewMenu">
    <button id="viewMenuToggle" aria-haspopup="true" aria-expanded="false">
      View <span class="caret">▾</span>
    </button>

    <!-- LEVEL 1: kelompok -->
    <div id="viewMenuPanel" class="view-menu__panel" role="menu" hidden>
      <div class="view-menu__header">
        <span>View</span>
        <button id="viewMenuClose" aria-label="Tutup menu">×</button>
      </div>

      <div class="menu-item has-submenu" role="menuitem" aria-haspopup="true">
        <span class="menu-label">Window</span>
        <span class="menu-value">Default</span>
        <span class="menu-arrow">▸</span>

        <!-- LEVEL 2: isi kelompok -->
        <div class="submenu" role="menu" hidden>
          <!-- Default, Lung, Bone, Soft tissue (role="menuitemradio") -->
        </div>
      </div>

      <!-- ulangi pola yang sama untuk: Orientasi, View -->
    </div>
  </div>

  <div class="patient-info">BERTA | MRN: ... | DX | M/21Y</div>
  <div class="layout-switch"><!-- 1x1, 1x2 (tetap) --></div>
</div>
```

Aturan:

- Satu pola komponen `has-submenu` dipakai untuk ketiga kelompok.
- Setiap item = satu baris penuh: **kolom centang `✓` (atau ruang kosong sejajar) di kiri, label di tengah, shortcut/nilai/`▸` di kanan**, mengikuti layout menu VS Code.
- Jangan mengganti `id` tombol lama yang dipakai JavaScript.

---

## 5. Fase 3: Perilaku (JavaScript)

### 5.1 Buka dan tutup menu

| Aksi | Hasil |
|---|---|
| Klik tombol `View` | Panel Level 1 muncul (toggle), `aria-expanded="true"` |
| Hover atau klik kelompok (`▸`) | Submenu muncul di samping kanan. Hanya **satu submenu terbuka** pada satu waktu |
| Klik tombol `×` di panel | **Seluruh menu** tertutup |
| Tekan `Esc` | Menutup submenu yang terbuka; jika tidak ada, menutup seluruh menu |
| Klik item di submenu | Aksi dijalankan, **menu tetap terbuka** agar bisa memakai beberapa item berurutan |

### 5.2 Hal yang SENGAJA tidak dilakukan

- **Jangan menutup menu saat klik di luar panel.** Area luar adalah canvas citra yang dipakai untuk Pan, Zoom, dan Alat Ukur.
- Menu tidak menutup otomatis setelah satu item dipakai. Penutupan hanya lewat `×`, `Esc`, atau klik ulang tombol `View`.
- **Pengecualian:** jika item membuka modal/panel yang menutupi menu (mis. `Restorasi & Denoising`, `Image processing`, `Tags DICOM`), tutup menu otomatis **hanya untuk item tersebut**, dan catat daftar itemnya di ringkasan akhir.

### 5.3 Perilaku per kelompok

| Kelompok | Jenis item | Perilaku |
|---|---|---|
| Window | Pilih satu (radio) | Memanggil logic preset window yang sudah ada. Centang `✓` pindah ke item terpilih. Nilai di baris `Window ▸` ikut berubah. Sinkron juga bila preset berubah lewat cara lain (mis. shortcut) |
| Orientasi | `Putar 90 clockwise` = aksi; sisanya toggle | `Putar 90 clockwise` hanya menjalankan aksi (tanpa centang). `Cermin horizontal`, `Cermin vertikal`, `Invert grayscale` menampilkan `✓` saat aktif dan menghilang saat nonaktif, mengikuti state viewer yang sebenarnya |
| View | Aksi pembuka fitur | Menjalankan fungsi yang sama seperti tombol lamanya |

### 5.4 Posisi submenu

- Default: muncul di **kanan** item induk, sejajar vertikal.
- Jika ruang kanan tidak cukup, buka ke **kiri**.
- Jika tinggi tidak cukup, geser ke atas, beri `max-height` + `overflow-y: auto`.
- Beri jeda kecil (sekitar 150ms) saat kursor pindah dari induk ke submenu agar tidak langsung tertutup.

### 5.5 Aksesibilitas dan keyboard

- Tombol `View`: `aria-haspopup="true"`, `aria-expanded` mengikuti status.
- Panel dan submenu: `role="menu"`; item biasa `menuitem`; Window `menuitemradio`; toggle Orientasi `menuitemcheckbox`.
- Panah atas/bawah memindahkan fokus; panah kanan membuka submenu; panah kiri menutupnya (nilai tambah, jangan merusak shortcut yang ada).
- Shortcut keyboard viewer yang sudah ada **harus tetap berfungsi walau menu tertutup**.

---

## 6. Fase 4: Styling

- Pakai **dark theme** yang sama dengan viewer (warna latar, border, radius, font).
- Tombol `View`: gaya sama dengan tombol toolbar lain yang berdekatan, ukuran sebanding dengan tombol `Seri Pasien` yang digantikan.
- Panel Level 1: `position: absolute` tepat di bawah tombol `View`, `z-index` di atas canvas, lebar sekitar 240-280px, bayangan halus.
- Submenu: gaya sama dengan panel, sedikit menimpa tepi panel induk agar terlihat menyambung.
- Item: tinggi nyaman diklik (sekitar 32-36px), **highlight biru solid saat hover/aktif** seperti baris `Appearance` di menu VS Code.
- Kolom kiri untuk centang `✓` dengan lebar tetap agar label sejajar.
- Teks nilai/shortcut di kanan memakai warna lebih redup.
- Tombol `×` di pojok kanan atas panel Level 1, area klik minimal 32x32px.

---

## 7. Fase 5: Pengujian

**Manual checklist:**

- [ ] Tombol `View ▾` berada di posisi bekas `Seri Pasien`; tombol `Seri Pasien` tidak tampil di toolbar, tetapi kodenya tidak dihapus.
- [ ] `Sesi Pasien`, info pasien, `1x1`, `1x2`, `Pan`, `Zoom`, `Alat Ukur`, `Undo`, `Hapus`, `Reset View` tampil dan berfungsi persis seperti sebelumnya.
- [ ] Klik `View` → muncul 3 kelompok: Window, Orientasi, View.
- [ ] Window: 4 preset berfungsi, centang dan nilai di baris `Window ▸` selalu sesuai preset aktif.
- [ ] Orientasi: `Putar 90 clockwise` berfungsi; ketiga toggle berfungsi dan centangnya sesuai kondisi citra.
- [ ] View: kelima item membuka fitur yang sama seperti tombol lamanya.
- [ ] Tidak ada tombol lama yang tertinggal dobel di toolbar atau halaman.
- [ ] Hanya satu submenu terbuka pada satu waktu.
- [ ] `×` menutup seluruh menu; `Esc` menutup bertahap; klik item biasa tidak menutup menu.
- [ ] Klik atau drag di canvas tidak menutup menu dan tidak merusak tool aktif.
- [ ] Submenu tidak terpotong di tepi layar.
- [ ] Tampilan rapi di layar sempit (laptop kecil/tablet); tidak ada pemisah `|` yang menggantung di baris 2.
- [ ] Tidak ada error di console browser; shortcut lama tetap bekerja.

---

## 8. Aturan untuk Agent

1. **Jangan mengubah logic fungsi tombol.** Ini hanya perombakan tampilan dan penempatan.
2. Jangan menambah framework atau library UI baru.
3. Jangan mengubah `id`/`data-*` yang sudah dipakai JavaScript.
4. Jangan menyentuh elemen yang tercantum di "Yang TIDAK disentuh sama sekali".
5. Jangan membuat kelompok "Tool Interaksi" atau memasukkan `Pan`, `Zoom`, `Alat Ukur` ke menu.
6. Kerjakan bertahap dan cek tampilan di tiap fase.
7. Jika ada keputusan yang ambigu, **tandai asumsi** di ringkasan akhir. Asumsi dalam dokumen ini:
   - "Tombol silang" = tombol `×` di panel yang menutup seluruh menu.
   - "Diganti" untuk `Seri Pasien` = dihilangkan dari toolbar, logic dipertahankan.
   - Kelompok ketiga bernama `View` mengikuti daftar yang diberikan.
8. Tutup dengan ringkasan: file yang diubah, elemen apa dipindah ke mana, item yang menutup menu otomatis (jika ada), dan hasil checklist pengujian.

---

## 9. Definition of Done

- [ ] Dropdown `View` menggantikan posisi tombol `Seri Pasien`.
- [ ] Menu bertingkat berisi tepat 3 kelompok (Window, Orientasi, View) dengan isi sesuai Bagian 1.
- [ ] `Pan`, `Zoom`, `Alat Ukur`, dan tombol lain di luar daftar tetap di toolbar tanpa perubahan.
- [ ] `×` menutup seluruh menu; `Esc` menutup bertahap; klik item biasa tidak menutup menu.
- [ ] Status aktif (preset Window dan toggle Orientasi) selalu akurat.
- [ ] Semua fungsi viewer berjalan seperti sebelumnya, tanpa error console.
