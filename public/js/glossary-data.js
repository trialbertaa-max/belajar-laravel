/**
 * glossary-data.js
 * Sumber data terpusat untuk semua singkatan dan istilah teknis
 * yang digunakan di sistem Hyu PACS / MCU.
 *
 * Struktur setiap entri:
 *   id              – identifier unik (digunakan pada URL hash, e.g. /glosarium#psnr)
 *   abbreviation    – singkatan atau nama pendek
 *   fullName        – nama lengkap
 *   category        – kategori entri (lihat GLOSSARY_CATEGORIES)
 *   summary         – ringkasan 2–3 kalimat (bahasa Indonesia, untuk modal)
 *   detailedExplanation – penjelasan lebih panjang (untuk halaman glosarium)
 *   formula         – rumus (string, null jika tidak ada)
 *   howToRead       – panduan membaca nilai ("semakin tinggi semakin baik", dst.)
 *   references      – referensi ilmiah atau teknis
 */

const GLOSSARY_CATEGORIES = {
    image_quality: 'Kualitas Citra',
    algorithm:     'Algoritma Denoising',
    radiology:     'Radiologi & DICOM',
    system:        'Sistem & Umum',
};

const GLOSSARY_DATA = [

    /* ──────────────────────────────────────────────────────────────
     * KATEGORI: KUALITAS CITRA
     * ────────────────────────────────────────────────────────────── */
    {
        id: 'psnr',
        abbreviation: 'PSNR',
        fullName: 'Peak Signal-to-Noise Ratio',
        category: 'image_quality',
        summary:
            'PSNR mengukur seberapa mirip citra hasil pemrosesan dengan citra referensi aslinya, dinyatakan dalam satuan desibel (dB). ' +
            'Semakin tinggi nilai PSNR, semakin sedikit noise yang tersisa dan semakin baik kualitas restorasi citra. ' +
            'Nilai di atas ±40 dB umumnya dianggap sangat baik untuk citra medis.',
        detailedExplanation:
            'PSNR (Peak Signal-to-Noise Ratio) adalah metrik standar dalam pemrosesan citra untuk mengukur kemiripan antara dua gambar — ' +
            'biasanya antara citra asli/referensi dan citra hasil denoising/kompresi. ' +
            'PSNR dihitung dari MSE (Mean Squared Error): semakin kecil MSE, semakin besar PSNR. ' +
            'Dalam radiologi digital, PSNR yang tinggi berarti algoritma berhasil mengurangi noise tanpa merusak informasi diagnostik citra. ' +
            'Nilai PSNR di atas 40 dB umumnya diterima sebagai "sangat baik" untuk citra medis resolusi tinggi. ' +
            'Catatan penting: PSNR adalah metrik teknis kualitas citra — bukan penilaian diagnostik klinis.',
        formula: 'PSNR = 10 · log₁₀(MAX² / MSE)',
        howToRead: 'Semakin tinggi semakin baik. Nilai > 40 dB = sangat baik; 30–40 dB = baik; < 30 dB = perlu peningkatan.',
        references: ['Wang et al. (2004) – IEEE Trans. Image Process.', 'Hore & Ziou (2010) – ICPR'],
    },
    {
        id: 'mse',
        abbreviation: 'MSE',
        fullName: 'Mean Squared Error',
        category: 'image_quality',
        summary:
            'MSE adalah rata-rata dari kuadrat perbedaan intensitas piksel antara dua citra. ' +
            'MSE merupakan dasar perhitungan PSNR; nilai MSE yang lebih kecil berarti dua citra lebih identik. ' +
            'Nilai MSE = 0 berarti kedua citra identik sempurna.',
        detailedExplanation:
            'Mean Squared Error (MSE) mengukur perbedaan rata-rata kuadrat antara setiap piksel citra hasil dengan citra referensi. ' +
            'Nilainya selalu ≥ 0, dan semakin kecil berarti semakin baik. ' +
            'MSE digunakan sebagai komponen utama dalam rumus PSNR dan merupakan fungsi kerugian (loss function) umum dalam pelatihan model deep learning untuk pemrosesan citra.',
        formula: 'MSE = (1/MN) · Σ[I(x,y) − K(x,y)]²',
        howToRead: 'Semakin rendah semakin baik. MSE = 0 berarti identik sempurna.',
        references: ['Gonzalez & Woods – Digital Image Processing, 4th Ed.'],
    },
    {
        id: 'ssim',
        abbreviation: 'SSIM',
        fullName: 'Structural Similarity Index Measure',
        category: 'image_quality',
        summary:
            'SSIM mengukur kemiripan struktural antara dua citra berdasarkan tiga aspek: luminansi, kontras, dan struktur. ' +
            'Nilainya berkisar antara 0 hingga 1; semakin mendekati 1 berarti citra hampir identik. ' +
            'SSIM dianggap lebih mendekati persepsi visual manusia dibandingkan PSNR.',
        detailedExplanation:
            'SSIM (Structural Similarity Index Measure) dikembangkan oleh Wang et al. (2004) sebagai alternatif yang lebih baik dari PSNR dalam mengevaluasi kualitas citra. ' +
            'SSIM memodelkan distorsi citra sebagai kombinasi dari tiga faktor: luminansi (l), kontras (c), dan struktur (s). ' +
            'Nilainya berkisar antara -1 hingga 1, namun dalam praktik denoising selalu mendekati 1 untuk hasil yang baik. ' +
            'SSIM > 0.95 umumnya dianggap sangat baik untuk citra medis. ' +
            'Catatan: SSIM adalah metrik teknis kualitas citra — bukan penilaian diagnostik klinis.',
        formula: 'SSIM(x,y) = [l(x,y)]α · [c(x,y)]β · [s(x,y)]γ',
        howToRead: 'Semakin mendekati 1.0 semakin baik. Nilai > 0.95 = sangat baik; 0.90–0.95 = baik; < 0.90 = perlu evaluasi.',
        references: ['Wang, Z., Bovik, A. C., Sheikh, H. R., & Simoncelli, E. P. (2004). IEEE Trans. Image Process., 13(4), 600–612.'],
    },
    {
        id: 'snr',
        abbreviation: 'SNR',
        fullName: 'Signal-to-Noise Ratio',
        category: 'image_quality',
        summary:
            'SNR adalah rasio antara kekuatan sinyal (informasi citra) terhadap kekuatan noise (gangguan), dinyatakan dalam desibel (dB). ' +
            'SNR yang lebih tinggi berarti kualitas sinyal lebih baik dan noise lebih sedikit. ' +
            'Dalam konteks denoising, SNR Gain menunjukkan peningkatan SNR setelah proses restorasi citra.',
        detailedExplanation:
            'Signal-to-Noise Ratio (SNR) adalah ukuran mendasar kualitas sinyal di bidang elektronik, telekomunikasi, dan pemrosesan citra. ' +
            'Dalam pencitraan medis, SNR tinggi sangat penting agar struktur anatomi terlihat jelas dan tidak tertutupi oleh noise. ' +
            'SNR Gain (dalam tabel Matriks Komparasi) menunjukkan seberapa besar peningkatan SNR yang dicapai oleh setiap algoritma denoising dibandingkan citra asli yang belum diproses. ' +
            'Nilai SNR Gain positif berarti algoritma berhasil meningkatkan rasio sinyal terhadap noise.',
        formula: 'SNR = 10 · log₁₀(P_sinyal / P_noise) ; SNR Gain = SNR_hasil − SNR_asli',
        howToRead: 'Semakin tinggi semakin baik. SNR Gain positif (+dB) berarti ada peningkatan kualitas.',
        references: ['Bushberg et al. – The Essential Physics of Medical Imaging, 3rd Ed.'],
    },
    {
        id: 'snr-gain',
        abbreviation: 'SNR Gain',
        fullName: 'Signal-to-Noise Ratio Gain',
        category: 'image_quality',
        summary:
            'SNR Gain adalah selisih antara SNR citra hasil denoising dengan SNR citra asli, dinyatakan dalam dB. ' +
            'Nilai positif berarti algoritma berhasil meningkatkan kualitas sinyal dibanding citra mentah. ' +
            'Semakin besar SNR Gain, semakin efektif algoritma dalam menekan noise.',
        detailedExplanation:
            'SNR Gain mengukur "keuntungan" yang diperoleh dari proses denoising. ' +
            'Dalam tabel Matriks Komparasi 4 Algoritma, kolom SNR Gain menampilkan perbedaan (dalam dB) antara SNR citra yang telah diproses oleh masing-masing algoritma dengan SNR citra Raw (baseline). ' +
            'Nilai + berarti peningkatan; nilai – berarti penurunan (yang tidak diinginkan).',
        formula: 'SNR Gain = SNR_output − SNR_input (dB)',
        howToRead: 'Semakin tinggi (positif) semakin baik. Nilai negatif berarti kualitas menurun.',
        references: ['Immerkaer, J. (1996) – Fast noise estimation, CVIU.'],
    },
    {
        id: 'latency',
        abbreviation: 'Latency',
        fullName: 'Inference Latency (Waktu Pemrosesan)',
        category: 'image_quality',
        summary:
            'Latency adalah waktu yang dibutuhkan oleh suatu algoritma untuk memproses satu citra dari masukan hingga keluaran, diukur dalam milidetik (ms). ' +
            'Latency yang rendah berarti algoritma lebih cepat dan lebih responsif untuk penggunaan klinis. ' +
            'Ada trade-off antara kualitas hasil dan kecepatan pada berbagai algoritma.',
        detailedExplanation:
            'Dalam sistem real-time seperti PACS, latency pemrosesan sangat penting untuk memastikan alur kerja radiologis tidak terhambat. ' +
            'Latency dipengaruhi oleh kompleksitas algoritma, ukuran citra, dan kemampuan hardware (CPU/GPU). ' +
            'Sebagai contoh: DnCNN (deep learning) menggunakan GPU acceleration sehingga bisa lebih cepat dari NLM meskipun komputasinya lebih kompleks.',
        formula: 'Latency = t_selesai − t_mulai (ms)',
        howToRead: 'Semakin rendah semakin baik untuk responsivitas klinis.',
        references: ['Zhang et al. (2017) – DnCNN, IEEE Trans. Image Process.'],
    },
    {
        id: 'db',
        abbreviation: 'dB',
        fullName: 'Desibel (Decibel)',
        category: 'image_quality',
        summary:
            'Desibel (dB) adalah satuan logaritmik yang digunakan untuk menyatakan rasio antara dua nilai (seperti daya, amplitudo, atau intensitas). ' +
            'Dalam konteks PSNR dan SNR, dB digunakan karena skala logaritmik lebih sesuai dengan persepsi manusia. ' +
            'Setiap kenaikan 10 dB berarti peningkatan 10× pada rasio daya.',
        detailedExplanation:
            'Satuan desibel (dB) banyak digunakan dalam pemrosesan sinyal dan citra karena rentang nilai yang sangat lebar dapat direpresentasikan dalam skala yang lebih mudah dibaca. ' +
            'Dalam PSNR: 40 dB ≈ 10.000× rasio daya terhadap noise; 60 dB ≈ 1.000.000×.',
        formula: 'dB = 10 · log₁₀(P₁/P₂)',
        howToRead: 'Kontekstual: untuk PSNR dan SNR, nilai dB yang lebih tinggi = kualitas lebih baik.',
        references: ['IEEE Standard 100 – The Authoritative Dictionary of IEEE Standards Terms'],
    },
    {
        id: 'ms',
        abbreviation: 'ms',
        fullName: 'Milidetik (Millisecond)',
        category: 'image_quality',
        summary:
            'Milidetik (ms) adalah satuan waktu yang setara dengan 1/1000 detik. ' +
            'Digunakan dalam sistem ini untuk mengukur latency (waktu pemrosesan) algoritma denoising. ' +
            '1000 ms = 1 detik.',
        detailedExplanation:
            'Milidetik adalah satuan waktu standar untuk mengukur kecepatan komputasi dalam sistem real-time. ' +
            'Dalam konteks sistem PACS, latency < 100 ms umumnya dianggap responsif untuk pengguna.',
        formula: '1 ms = 10⁻³ detik',
        howToRead: 'Semakin kecil angkanya, semakin cepat pemrosesannya.',
        references: ['SI Units – Bureau International des Poids et Mesures (BIPM)'],
    },

    /* ──────────────────────────────────────────────────────────────
     * KATEGORI: ALGORITMA DENOISING
     * ────────────────────────────────────────────────────────────── */
    {
        id: 'raw',
        abbreviation: 'Raw / Citra Asli',
        fullName: 'Citra Asli Tanpa Pemrosesan (Baseline)',
        category: 'algorithm',
        summary:
            'Citra Raw adalah gambar medis asli yang belum melalui proses denoising atau restorasi apapun. ' +
            'Digunakan sebagai baseline (pembanding) dalam evaluasi kinerja algoritma. ' +
            'Semua metrik SNR Gain dihitung relatif terhadap citra Raw ini.',
        detailedExplanation:
            'Dalam Matriks Komparasi 4 Algoritma, baris "Citra Asli (Raw)" mewakili citra masukan asli dari alat radiologi (Fujifilm FDR / USG Mindray) sebelum pemrosesan apapun. ' +
            'SSIM-nya selalu 1.0000 (identik dengan dirinya sendiri) dan SNR Gain-nya 0.00 dB (tidak ada perubahan). ' +
            'Ini adalah titik referensi untuk mengukur efektivitas tiap algoritma.',
        formula: null,
        howToRead: 'Baseline — semua algoritma dibandingkan terhadap nilai ini.',
        references: [],
    },
    {
        id: 'bilateral',
        abbreviation: 'Bilateral Filter',
        fullName: 'Bilateral Filter (Tomasi & Manduchi, 1998)',
        category: 'algorithm',
        summary:
            'Bilateral Filter adalah metode denoising yang mengurangi noise sambil mempertahankan tepi (edges) gambar secara efektif. ' +
            'Ia memperhitungkan kedekatan spasial dan kedekatan intensitas piksel secara bersamaan. ' +
            'Hasilnya: noise berkurang tetapi batas struktur anatomi tetap tajam.',
        detailedExplanation:
            'Bilateral Filter (diperkenalkan oleh Tomasi & Manduchi, 1998) adalah filter non-linear yang menghitung rata-rata tertimbang piksel tetangga. ' +
            'Dua faktor penentu bobot: (1) jarak spasial dari piksel pusat (σ_space) dan (2) perbedaan intensitas (σ_color). ' +
            'Ini menjadikannya "edge-preserving" — piksel di sisi tepi yang berbeda intensitas tidak dirata-ratakan bersama. ' +
            'Catatan teknis: beberapa kode sumber juga menyebut Immerkaer (1996) — itu adalah referensi untuk estimasi noise, bukan untuk Bilateral Filter itu sendiri.',
        formula: 'BF[I]_p = (1/W_p) · Σ_{q∈S} G_σs(||p−q||) · G_σr(|I_p−I_q|) · I_q',
        howToRead: 'Lihat kolom PSNR dan SSIM di tabel komparasi untuk mengevaluasi hasilnya.',
        references: [
            'Tomasi, C., & Manduchi, R. (1998). Bilateral Filtering for Gray and Color Images. ICCV.',
            'Immerkaer, J. (1996). Fast noise variance estimation. CVIU. (Referensi estimasi noise, bukan filter)',
        ],
    },
    {
        id: 'nlm',
        abbreviation: 'NLM',
        fullName: 'Non-Local Means (Buades et al., 2005)',
        category: 'algorithm',
        summary:
            'Non-Local Means (NLM) adalah algoritma denoising yang merata-ratakan piksel dari seluruh citra berdasarkan kemiripan patch (area lokal), bukan hanya tetangga terdekat. ' +
            'Metode ini umumnya menghasilkan PSNR dan SSIM yang sangat tinggi karena memanfaatkan redundansi struktur pada seluruh citra. ' +
            'Kelemahannya adalah waktu pemrosesan (latency) yang relatif lebih lama.',
        detailedExplanation:
            'Non-Local Means diperkenalkan oleh Buades, Coll, & Morel (2005). ' +
            'Idenya: setiap piksel diperkirakan nilainya dengan rata-rata tertimbang dari semua piksel lain dalam citra — bobotnya ditentukan oleh kemiripan patch (area kecil) di sekitar masing-masing piksel. ' +
            'Metode ini unggul dalam mempertahankan tekstur halus (seperti trabekula tulang atau pola pembuluh darah paru) sambil mengurangi noise secara signifikan. ' +
            'Kelemahannya: kompleksitas komputasi O(N²) menjadikannya lambat tanpa akselerasi khusus.',
        formula: 'NLM[u](x) = (1/C(x)) · ∫ e^{−(G_a★|u(x+·)−u(y+·)|²)(0)/h²} u(y) dy',
        howToRead: 'PSNR dan SSIM umumnya tertinggi di antara semua algoritma. Perhatikan trade-off dengan latency yang lebih tinggi.',
        references: ['Buades, A., Coll, B., & Morel, J. M. (2005). A Non-Local Algorithm for Image Denoising. CVPR.'],
    },
    {
        id: 'dncnn',
        abbreviation: 'DnCNN',
        fullName: 'Denoising Convolutional Neural Network (Zhang et al., 2017)',
        category: 'algorithm',
        summary:
            'DnCNN adalah model deep learning berbasis jaringan saraf konvolusional yang dilatih khusus untuk menghilangkan noise dari citra. ' +
            'Keunggulannya: inferensi sangat cepat (terendah latency-nya jika menggunakan GPU) dan kualitas hasil yang sangat baik. ' +
            'DnCNN belajar secara otomatis dari data, tanpa perlu pengaturan parameter manual seperti Bilateral Filter.',
        detailedExplanation:
            'DnCNN (Beyond a Gaussian Denoiser, Zhang et al., 2017) menggunakan arsitektur jaringan konvolusional dalam (20 lapisan) dengan teknik residual learning. ' +
            'Model mempelajari "peta noise" dari citra, lalu menguranginya dari gambar asli. ' +
            'Keunggulan utama: (1) tidak memerlukan parameter tuning manual, (2) dapat digeneralisasi ke berbagai tingkat noise, (3) sangat cepat dengan akselerasi GPU. ' +
            'Saat ini digunakan sebagai salah satu dari 4 algoritma dalam sistem Hyu PACS.',
        formula: 'f(y) = y − R(y) ; R(y) = noise residual yang dipelajari CNN',
        howToRead: 'Perhatikan keseimbangan antara PSNR/SSIM dan Latency — DnCNN sering menjadi rekomendasi otomatis sistem.',
        references: ['Zhang, K., Zuo, W., Chen, Y., Meng, D., & Zhang, L. (2017). DnCNN. IEEE Trans. Image Process., 26(7).'],
    },
    {
        id: 'ai',
        abbreviation: 'AI',
        fullName: 'Artificial Intelligence (Kecerdasan Buatan)',
        category: 'algorithm',
        summary:
            'AI merujuk pada kemampuan mesin untuk melakukan tugas yang biasanya memerlukan kecerdasan manusia, seperti mengenali pola, mengambil keputusan, dan belajar dari data. ' +
            'Dalam sistem Hyu PACS, AI digunakan dalam bentuk DnCNN (jaringan saraf konvolusional) untuk denoising citra medis. ' +
            'AI tidak menggantikan penilaian diagnostik radiolog — ia hanya membantu meningkatkan kualitas citra.',
        detailedExplanation:
            'Kecerdasan Buatan (AI) adalah cabang ilmu komputer yang berfokus pada pembuatan sistem yang dapat belajar dan beradaptasi. ' +
            'Dalam konteks pencitraan medis, AI umumnya mengacu pada machine learning dan deep learning. ' +
            'Penting untuk dipahami: AI dalam sistem ini adalah alat bantu teknis untuk meningkatkan kualitas citra — seluruh interpretasi diagnostik tetap menjadi tanggung jawab radiolog bersertifikat.',
        formula: null,
        howToRead: 'Kontekstual — dalam sistem ini AI merujuk pada algoritma DnCNN.',
        references: ['LeCun, Y., Bengio, Y., & Hinton, G. (2015). Deep learning. Nature, 521, 436–444.'],
    },
    {
        id: 'cnn',
        abbreviation: 'CNN',
        fullName: 'Convolutional Neural Network (Jaringan Saraf Konvolusional)',
        category: 'algorithm',
        summary:
            'CNN adalah arsitektur jaringan saraf tiruan yang dirancang khusus untuk memproses data grid seperti citra. ' +
            'CNN menggunakan lapisan konvolusi untuk mendeteksi fitur seperti tepi, tekstur, dan pola kompleks secara hierarkis. ' +
            'DnCNN yang digunakan dalam sistem ini adalah salah satu implementasi CNN untuk denoising.',
        detailedExplanation:
            'Convolutional Neural Network (CNN) terdiri dari lapisan-lapisan: konvolusional (mendeteksi fitur), pooling (mengurangi dimensi), dan fully-connected (klasifikasi/regresi). ' +
            'Dalam pemrosesan citra medis, CNN telah terbukti sangat efektif untuk deteksi lesi, segmentasi organ, dan peningkatan kualitas citra (denoising, super-resolution).',
        formula: null,
        howToRead: 'CNN adalah komponen teknis; untuk evaluasi kinerja, lihat metrik PSNR/SSIM/Latency.',
        references: ['LeCun, Y., et al. (1998). Gradient-based learning applied to document recognition. Proc. IEEE.'],
    },

    /* ──────────────────────────────────────────────────────────────
     * KATEGORI: RADIOLOGI & DICOM
     * ────────────────────────────────────────────────────────────── */
    {
        id: 'pacs',
        abbreviation: 'PACS',
        fullName: 'Picture Archiving and Communication System',
        category: 'radiology',
        summary:
            'PACS adalah sistem komputer yang digunakan di fasilitas kesehatan untuk menyimpan, mengambil, mendistribusikan, dan menampilkan citra medis digital. ' +
            'Sistem Hyu merupakan implementasi PACS mandiri (standalone) yang terintegrasi dengan alat Fujifilm (CR) dan USG Mindray. ' +
            'PACS memungkinkan radiolog mengakses citra dari mana saja dalam jaringan klinik.',
        detailedExplanation:
            'PACS menggantikan film radiologi konvensional dengan sistem digital yang memungkinkan penyimpanan, pengambilan, dan distribusi citra secara cepat. ' +
            'Komponen utama PACS: modalitas (alat penghasil citra), server penyimpanan, workstation diagnostik, dan jaringan. ' +
            'Hyu PACS dibangun menggunakan standar DICOM untuk kompatibilitas dengan berbagai modalitas.',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3 – Digital Imaging and Communications in Medicine (DICOM) Standard'],
    },
    {
        id: 'dicom',
        abbreviation: 'DICOM',
        fullName: 'Digital Imaging and Communications in Medicine',
        category: 'radiology',
        summary:
            'DICOM adalah standar internasional untuk penyimpanan, pertukaran, dan transmisi citra medis dan informasi terkait. ' +
            'Standar ini memastikan kompatibilitas antara perangkat dari berbagai produsen (Fujifilm, Mindray, Siemens, dll.). ' +
            'Sistem Hyu menggunakan DICOM 3.0 (NEMA PS 3) untuk komunikasi dengan alat radiologi.',
        detailedExplanation:
            'DICOM (Digital Imaging and Communications in Medicine) dikembangkan bersama oleh NEMA (National Electrical Manufacturers Association) dan ACR (American College of Radiology). ' +
            'Setiap file DICOM (.dcm) mengandung metadata pasien (nama, ID, modalitas) dan data piksel citra dalam satu paket. ' +
            'Protokol komunikasi DICOM mendefinisikan layanan seperti C-STORE (mengirim citra), C-ECHO (uji koneksi), dan MWL (Modality Worklist).',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.1–3.19 – DICOM Standard (dicom.nema.org)'],
    },
    {
        id: 'ae-title',
        abbreviation: 'AE Title',
        fullName: 'Application Entity Title',
        category: 'radiology',
        summary:
            'AE Title adalah nama unik yang diberikan kepada setiap entitas aplikasi DICOM dalam jaringan (seperti server PACS, alat CT, atau workstation). ' +
            'Panjang maksimum 16 karakter alfanumerik sesuai standar NEMA. ' +
            'AE Title Hyu PACS default adalah "HYU_PACS".',
        detailedExplanation:
            'Application Entity Title (AE Title) berfungsi seperti alamat jaringan logis dalam komunikasi DICOM. ' +
            'Ketika alat Fujifilm mengirimkan citra via C-STORE, ia harus mengetahui AE Title server tujuan (Hyu PACS). ' +
            'Konfigurasi AE Title yang salah akan menyebabkan kegagalan transmisi citra.',
        formula: null,
        howToRead: 'Pastikan AE Title di Hyu PACS sesuai dengan yang dikonfigurasi pada alat radiologi.',
        references: ['NEMA PS 3.8 – DICOM Network Communication'],
    },
    {
        id: 'scp',
        abbreviation: 'SCP',
        fullName: 'Service Class Provider',
        category: 'radiology',
        summary:
            'SCP adalah pihak yang menyediakan layanan dalam komunikasi DICOM — dalam konteks Hyu, SCP adalah server yang menerima citra (C-STORE) dari alat radiologi. ' +
            'Lawannya adalah SCU (Service Class User) — yaitu alat yang mengirimkan citra. ' +
            'Hyu PACS berperan sebagai C-STORE SCP dan C-ECHO SCP.',
        detailedExplanation:
            'Dalam arsitektur DICOM, setiap transaksi melibatkan dua pihak: Service Class User (SCU) yang meminta layanan, dan Service Class Provider (SCP) yang menyediakan layanan. ' +
            'Contoh: alat Fujifilm (SCU) mengirimkan citra via C-STORE ke Hyu PACS (SCP).',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.4 – DICOM Service Class Specifications'],
    },
    {
        id: 'c-store',
        abbreviation: 'C-STORE',
        fullName: 'DICOM C-STORE (Composite Object Store)',
        category: 'radiology',
        summary:
            'C-STORE adalah layanan DICOM yang digunakan untuk mengirimkan (mentransmisikan) citra medis dari satu entitas ke entitas lain dalam jaringan. ' +
            'Alat Fujifilm menggunakan C-STORE untuk mengirimkan citra rontgen ke server Hyu PACS setelah pemeriksaan selesai. ' +
            'Fitur "Simulasi Kirim Citra Fujifilm (C-STORE)" di dashboard mensimulasikan proses ini.',
        detailedExplanation:
            'C-STORE adalah operasi dasar dalam DICOM untuk transfer citra. ' +
            'Setelah pasien menjalani pemeriksaan X-ray di alat Fujifilm FDR, alat tersebut secara otomatis mengirimkan file DICOM (.dcm) ke server PACS tujuan melalui jaringan LAN menggunakan protokol C-STORE.',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.7 – DICOM Message Exchange'],
    },
    {
        id: 'c-echo',
        abbreviation: 'C-ECHO',
        fullName: 'DICOM C-ECHO (Verification)',
        category: 'radiology',
        summary:
            'C-ECHO adalah layanan DICOM yang digunakan untuk menguji konektivitas antara dua entitas aplikasi DICOM — mirip dengan perintah "ping" di jaringan komputer. ' +
            'C-ECHO memverifikasi bahwa server Hyu PACS dapat dicapai oleh alat radiologi melalui jaringan. ' +
            'Jika C-ECHO berhasil, komunikasi DICOM siap digunakan.',
        detailedExplanation:
            'C-ECHO (Verification Service) mengirimkan permintaan verifikasi dan mengharapkan respons "sukses" dari SCP tujuan. ' +
            'Ini adalah langkah diagnostik pertama ketika ada masalah koneksi antara alat radiologi dan server PACS.',
        formula: null,
        howToRead: 'Respons C-ECHO sukses = koneksi DICOM aktif dan siap.',
        references: ['NEMA PS 3.7 – DICOM Message Exchange'],
    },
    {
        id: 'mwl',
        abbreviation: 'MWL',
        fullName: 'Modality Worklist (DICOM MWL)',
        category: 'radiology',
        summary:
            'MWL adalah layanan DICOM yang memungkinkan alat radiologi mengambil daftar pasien yang dijadwalkan untuk pemeriksaan secara otomatis dari server. ' +
            'Dengan MWL, teknisi tidak perlu mengetikkan data pasien secara manual di alat — informasi diambil langsung dari sistem Hyu. ' +
            'Ini mengurangi kesalahan entri data dan mempercepat alur kerja.',
        detailedExplanation:
            'Modality Worklist (MWL) adalah salah satu layanan paling penting dalam DICOM untuk efisiensi klinik. ' +
            'Alur kerja: dokter membuat order pemeriksaan di Hyu → alat Fujifilm/Mindray mengambil daftar pasien via MWL → teknisi memilih pasien dari daftar → pemeriksaan dilakukan → citra dikirim via C-STORE.',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.4 – DICOM Modality Worklist Service'],
    },
    {
        id: 'cr',
        abbreviation: 'CR',
        fullName: 'Computed Radiography (Rontgen Digital)',
        category: 'radiology',
        summary:
            'CR adalah teknologi pencitraan rontgen digital yang menggunakan plate fosfor untuk menangkap gambar, kemudian didigitalisasi menjadi file DICOM. ' +
            'Alat Fujifilm FDR di Cahaya Diagnostic Centre menggunakan teknologi CR/DR untuk menghasilkan citra rontgen thorax. ' +
            'Citra CR/DR disimpan dalam format DICOM dan ditransmisikan ke server Hyu PACS.',
        detailedExplanation:
            'Computed Radiography (CR) adalah penerus film rontgen konvensional. ' +
            'Keunggulan CR: tidak memerlukan proses pencucian film kimiawi, dapat didigitalisasi langsung, mudah disimpan dan dibagi secara digital melalui PACS.',
        formula: null,
        howToRead: null,
        references: ['Bushberg et al. – The Essential Physics of Medical Imaging, 3rd Ed.'],
    },
    {
        id: 'fdr',
        abbreviation: 'FDR / DR',
        fullName: 'Flat Panel Detector Radiography (Fujifilm FDR)',
        category: 'radiology',
        summary:
            'FDR (Flat Panel Detector Radiography) adalah teknologi rontgen digital langsung menggunakan detektor panel datar. ' +
            'Fujifilm FDR adalah salah satu alat X-ray digital yang digunakan di Cahaya Diagnostic Centre. ' +
            'Citra yang dihasilkan langsung tersedia secara digital dalam hitungan detik setelah eksposur.',
        detailedExplanation:
            'FDR menggunakan detektor panel datar (amorphous silicon/amorphous selenium) untuk mengkonversi sinar-X langsung menjadi sinyal digital. ' +
            'Dibandingkan CR, FDR memberikan resolusi lebih tinggi, latency lebih rendah, dan kualitas citra yang lebih konsisten.',
        formula: null,
        howToRead: null,
        references: ['Fujifilm Medical – FDR Series Technical Specifications'],
    },
    {
        id: 'us',
        abbreviation: 'US',
        fullName: 'Ultrasonografi (Ultrasound)',
        category: 'radiology',
        summary:
            'US atau USG (Ultrasonografi) adalah modalitas pencitraan menggunakan gelombang suara frekuensi tinggi untuk menghasilkan gambar organ internal. ' +
            'Sistem Hyu mendukung citra USG dari alat Mindray (USG_MINDRAY_02). ' +
            'Citra USG disimpan dalam format DICOM dan dapat dilihat di PACS Viewer.',
        detailedExplanation:
            'Ultrasonografi adalah modalitas yang tidak menggunakan radiasi ionisasi, sehingga aman untuk berbagai kelompok pasien. ' +
            'Alat USG Mindray yang digunakan di Cahaya Diagnostic Centre terhubung ke Hyu PACS via DICOM C-STORE.',
        formula: null,
        howToRead: null,
        references: ['ACR–AIUM–SRU Practice Parameter for the Performance of Diagnostic Ultrasound'],
    },
    {
        id: 'port',
        abbreviation: 'Port',
        fullName: 'Nomor Port Jaringan DICOM',
        category: 'radiology',
        summary:
            'Port adalah nomor yang mengidentifikasi layanan jaringan tertentu pada sebuah server. ' +
            'Port default DICOM standar adalah 104; Hyu menggunakan port 4242 secara default. ' +
            'Port harus sama antara konfigurasi server Hyu dan alat radiologi agar komunikasi berhasil.',
        detailedExplanation:
            'Nomor port DICOM standar (Well-Known Port) adalah 104 berdasarkan RFC dan NEMA. ' +
            'Port alternatif yang umum digunakan dalam pengembangan/pengujian adalah 4242 dan 11112. ' +
            'Pastikan firewall tidak memblokir port DICOM yang dikonfigurasi.',
        formula: null,
        howToRead: 'Port harus sesuai antara Hyu PACS dan alat radiologi. Default: 4242.',
        references: ['NEMA PS 3.8 – Network Communication Support'],
    },
    {
        id: 'lan',
        abbreviation: 'LAN',
        fullName: 'Local Area Network (Jaringan Area Lokal)',
        category: 'radiology',
        summary:
            'LAN adalah jaringan komputer yang menghubungkan perangkat-perangkat dalam area terbatas (seperti satu gedung klinik). ' +
            'Sistem Hyu PACS dan alat radiologi (Fujifilm, Mindray) berkomunikasi melalui LAN klinik. ' +
            'Kecepatan LAN mempengaruhi kecepatan transfer citra DICOM.',
        detailedExplanation:
            'Dalam konfigurasi Hyu PACS, server mendengarkan koneksi DICOM pada alamat 0.0.0.0 (semua antarmuka jaringan), sehingga dapat dijangkau dari semua perangkat dalam LAN.',
        formula: null,
        howToRead: null,
        references: [],
    },
    {
        id: 'nema-ps3',
        abbreviation: 'NEMA PS 3',
        fullName: 'NEMA Publication Series 3 (Standar DICOM)',
        category: 'radiology',
        summary:
            'NEMA PS 3 adalah dokumen standar resmi yang mendefinisikan format dan protokol DICOM secara lengkap. ' +
            'Standar ini terdiri dari banyak bagian (PS 3.1 hingga PS 3.19) yang mencakup format file, protokol jaringan, dan layanan. ' +
            'Sistem Hyu PACS dibangun untuk memenuhi persyaratan NEMA PS 3.4 (layanan) dan NEMA PS 3.7 (jaringan).',
        detailedExplanation:
            'NEMA (National Electrical Manufacturers Association) menerbitkan dan memelihara standar DICOM. ' +
            'Standar ini gratis dan dapat diakses publik di dicom.nema.org.',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.1–3.19 (dicom.nema.org)'],
    },
    {
        id: 'tls',
        abbreviation: 'TLS',
        fullName: 'Transport Layer Security',
        category: 'radiology',
        summary:
            'TLS adalah protokol kriptografi yang mengamankan komunikasi data melalui jaringan (termasuk DICOM). ' +
            'TLS memastikan bahwa citra medis dan data pasien yang dikirimkan melalui jaringan tidak dapat disadap atau dimodifikasi. ' +
            'Implementasi TLS direkomendasikan untuk lingkungan klinik yang terhubung ke internet.',
        detailedExplanation:
            'Tanpa TLS, komunikasi DICOM bersifat plaintext dan rentan terhadap penyadapan (eavesdropping). ' +
            'DICOM PS 3.15 mendefinisikan profil keamanan termasuk penggunaan TLS untuk komunikasi DICOM.',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.15 – Security and System Management Profiles'],
    },

    /* ──────────────────────────────────────────────────────────────
     * KATEGORI: SISTEM & UMUM
     * ────────────────────────────────────────────────────────────── */
    {
        id: 'csv',
        abbreviation: 'CSV',
        fullName: 'Comma-Separated Values',
        category: 'system',
        summary:
            'CSV adalah format file teks sederhana di mana nilai-nilai dipisahkan oleh koma. ' +
            'Sistem Hyu menggunakan format CSV untuk ekspor data Matriks Komparasi Algoritma. ' +
            'File CSV dapat dibuka langsung dengan Microsoft Excel atau Google Sheets.',
        detailedExplanation:
            'File CSV berisi data tabular dalam format teks biasa, di mana setiap baris mewakili satu record dan kolom dipisahkan oleh koma (atau karakter lain seperti titik koma). ' +
            'Format ini universal dan dapat dibaca oleh hampir semua perangkat lunak spreadsheet dan database.',
        formula: null,
        howToRead: null,
        references: ['RFC 4180 – Common Format and MIME Type for CSV Files'],
    },
    {
        id: 'api',
        abbreviation: 'API',
        fullName: 'Application Programming Interface',
        category: 'system',
        summary:
            'API adalah antarmuka yang memungkinkan dua aplikasi perangkat lunak berkomunikasi satu sama lain. ' +
            'Sistem Hyu menyediakan API endpoint untuk worklist DICOM (/api/worklist) dan metadata citra (/api/scans/{id}/dicom). ' +
            'API ini dapat diintegrasikan dengan sistem rumah sakit lain (HIS/RIS).',
        detailedExplanation:
            'API Hyu PACS menyediakan akses programatik ke data worklist dan metadata DICOM dalam format JSON, memudahkan integrasi dengan sistem informasi rumah sakit (Hospital Information System) yang ada.',
        formula: null,
        howToRead: null,
        references: [],
    },
    {
        id: 'mrn',
        abbreviation: 'MRN / No. RM',
        fullName: 'Medical Record Number (Nomor Rekam Medis)',
        category: 'system',
        summary:
            'MRN adalah nomor identifikasi unik yang diberikan kepada setiap pasien di fasilitas kesehatan. ' +
            'Dalam sistem Hyu, MRN digunakan bersama dengan Accession Number untuk mengidentifikasi setiap order pemeriksaan. ' +
            'Format default MRN di Hyu: CDC-XXXXX.',
        detailedExplanation:
            'Nomor Rekam Medis (MRN) adalah kunci utama untuk mengidentifikasi pasien dalam sistem informasi kesehatan. ' +
            'Dalam standar DICOM, MRN disimpan dalam tag (0010,0020) – Patient ID.',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.5 – DICOM Data Structures (Patient ID tag)'],
    },
    {
        id: 'accession-number',
        abbreviation: 'Accession Number',
        fullName: 'Nomor Aksesi Pemeriksaan (DICOM Accession Number)',
        category: 'system',
        summary:
            'Accession Number adalah nomor unik yang mengidentifikasi satu sesi/order pemeriksaan radiologi. ' +
            'Berbeda dengan MRN (yang menidentifikasi pasien), Accession Number mengidentifikasi setiap pemeriksaan spesifik. ' +
            'Format di Hyu: ACC-YYYYMMDD-NNN.',
        detailedExplanation:
            'Accession Number disimpan dalam tag DICOM (0008,0050) dan digunakan untuk mengaitkan citra yang diterima dengan order pemeriksaan yang ada di worklist. ' +
            'Pencocokan Accession Number yang akurat sangat penting untuk memastikan citra terhubung ke pasien yang benar.',
        formula: null,
        howToRead: null,
        references: ['NEMA PS 3.5 – DICOM Data Structures (Accession Number tag)'],
    },
    {
        id: 'roi',
        abbreviation: 'ROI',
        fullName: 'Region of Interest (Area Kajian)',
        category: 'system',
        summary:
            'ROI adalah area tertentu pada citra medis yang dipilih oleh radiolog untuk dianalisis lebih lanjut. ' +
            'Sistem Hyu menyediakan alat ukur ROI berupa box, ellipse, dan polygon untuk penggambaran dan analisis area spesifik. ' +
            'ROI membantu dalam pengukuran densitas, luas, atau karakteristik lain pada area yang menjadi perhatian klinis.',
        detailedExplanation:
            'Region of Interest (ROI) adalah konsep fundamental dalam pencitraan medis digital. ' +
            'Pengukuran ROI dapat mencakup: rata-rata intensitas piksel (Hounsfield Unit pada CT), standar deviasi, luas area, dan perimeter.',
        formula: null,
        howToRead: null,
        references: [],
    },
    {
        id: 'ctr',
        abbreviation: 'CTR',
        fullName: 'Cardiothoracic Ratio (Rasio Kardiotoraks)',
        category: 'system',
        summary:
            'CTR adalah rasio antara diameter terlebar jantung terhadap diameter terlebar rongga dada pada foto rontgen PA. ' +
            'CTR normal pada orang dewasa umumnya < 0.5 (50%). Nilai > 0.5 dapat mengindikasikan kardiomegali. ' +
            'Sistem Hyu menyediakan alat ukur CTR otomatis pada PACS Viewer.',
        detailedExplanation:
            'Cardiothoracic Ratio (CTR) adalah pengukuran standar yang pertama kali diajukan oleh Danzer (1919). ' +
            'Cara pengukuran: (1) ukur diameter transversal jantung terlebar (D_jantung), (2) ukur diameter dalam rongga dada terlebar (D_dada), (3) CTR = D_jantung / D_dada. ' +
            'Catatan: interpretasi CTR harus dilakukan oleh radiolog bersertifikat.',
        formula: 'CTR = D_jantung / D_dada (normal < 0.5)',
        howToRead: 'CTR < 0.5 umumnya normal. CTR ≥ 0.5 perlu evaluasi klinis lebih lanjut oleh dokter.',
        references: ['Danzer, C.S. (1919). The cardiothoracic ratio. Am J Med Sci, 157, 513–521.'],
    },
];

// Buat lookup map untuk akses cepat berdasarkan ID
const GLOSSARY_MAP = Object.fromEntries(GLOSSARY_DATA.map(e => [e.id, e]));
