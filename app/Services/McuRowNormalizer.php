<?php

namespace App\Services;

class McuRowNormalizer
{
    /**
     * Daftar modalitas yang valid (value persis dari <select> di Blade).
     * Label digunakan untuk fuzzy matching.
     */
    protected static array $modalityMap = [
        'thorax pa'       => 'Thorax PA',
        'thorax ap'       => 'Thorax AP',
        'thorax lateral'  => 'Thorax Lateral',
        'waters'          => 'Waters',
        'usg abdomen'     => 'USG Abdomen',
        'usg pelvic'      => 'USG Pelvic',
        // Label alias (teks yang terlihat user di dropdown)
        'thorax pa (rontgen dada)'         => 'Thorax PA',
        'rontgen dada'                     => 'Thorax PA',
        'waters (sinus paranasal / spn)'   => 'Waters',
        'sinus paranasal'                  => 'Waters',
        'spn'                              => 'Waters',
    ];

    /**
     * Alias header Excel → nama field internal.
     */
    protected static array $headerAliases = [
        'nama_lengkap'     => 'nama_lengkap',
        'nama'             => 'nama_lengkap',
        'patient_name'     => 'nama_lengkap',
        'nama_pasien'      => 'nama_lengkap',

        'mrn_nik'          => 'mrn_nik',
        'nik'              => 'mrn_nik',
        'mrn'              => 'mrn_nik',
        'no_rekam_medis'   => 'mrn_nik',
        'patient_id'       => 'mrn_nik',
        'no_rm'            => 'mrn_nik',

        'usia'             => 'usia',
        'umur'             => 'usia',
        'age'              => 'usia',

        'jenis_kelamin'    => 'jenis_kelamin',
        'gender'           => 'jenis_kelamin',
        'jk'               => 'jenis_kelamin',
        'kelamin'          => 'jenis_kelamin',

        'modalitas'        => 'modalitas',
        'modality'         => 'modalitas',
        'modalitas_pemeriksaan' => 'modalitas',

        'indikasi_klinis'  => 'indikasi_klinis',
        'indikasi'         => 'indikasi_klinis',
        'deskripsi_sesi'   => 'indikasi_klinis',
        'study_description' => 'indikasi_klinis',

        'catatan_petugas'  => 'catatan_petugas',
        'catatan'          => 'catatan_petugas',
        'catatan_tambahan' => 'catatan_petugas',
        'order_notes'      => 'catatan_petugas',
    ];

    /**
     * Mapping gender input → value <select> form.
     */
    protected static array $genderMap = [
        'l'          => 'L',
        'laki-laki'  => 'L',
        'laki'       => 'L',
        'pria'       => 'L',
        'm'          => 'L',
        'male'       => 'L',
        'p'          => 'P',
        'perempuan'  => 'P',
        'wanita'     => 'P',
        'f'          => 'P',
        'female'     => 'P',
    ];

    /**
     * Normalisasi header mentah dari Excel ke nama field internal.
     *
     * @param array $rawHeaders
     * @return array Mapping [original_index => field_name]
     */
    public static function normalizeHeaders(array $rawHeaders): array
    {
        $mapped = [];
        foreach ($rawHeaders as $header) {
            if ($header === null) {
                continue;
            }
            $headerStr = (string) $header;
            // lowercase, trim, spasi → underscore
            $key = strtolower(trim($headerStr));
            $key = preg_replace('/\s+/', '_', $key);

            if (isset(self::$headerAliases[$key])) {
                $mapped[$headerStr] = self::$headerAliases[$key];
            }
        }
        return $mapped;
    }

    /**
     * Normalisasi satu baris data Excel.
     *
     * @param array $row  Associative array dengan key = field internal
     * @return array ['data' => [...], 'errors' => [...]]
     */
    public static function normalizeRow(array $row): array
    {
        $errors = [];
        $data = [
            'nama_lengkap'    => null,
            'mrn_nik'         => null,
            'usia'            => null,
            'jenis_kelamin'   => null,
            'modalitas'       => null,
            'indikasi_klinis' => null,
            'catatan_petugas' => null,
        ];

        // ── nama_lengkap (wajib) ──
        $nama = self::sanitizeString($row['nama_lengkap'] ?? null);
        if ($nama === null || $nama === '') {
            $errors[] = 'Kolom "nama_lengkap" wajib diisi.';
        } else {
            $data['nama_lengkap'] = mb_substr($nama, 0, 255);
        }

        // ── mrn_nik (string, jaga format) ──
        $mrn = $row['mrn_nik'] ?? null;
        if ($mrn !== null && $mrn !== '') {
            // Paksa string — tangani kasus angka ilmiah dari Excel
            $mrn = self::forceString($mrn);
            $data['mrn_nik'] = mb_substr(trim($mrn), 0, 100);
        }

        // ── usia ──
        $usia = $row['usia'] ?? null;
        if ($usia !== null && $usia !== '') {
            $usiaInt = (int) $usia;
            if ($usiaInt < 0 || $usiaInt > 120) {
                $errors[] = 'Usia harus antara 0 dan 120 tahun.';
            } else {
                $data['usia'] = $usiaInt;
            }
        }

        // ── jenis_kelamin ──
        $jk = $row['jenis_kelamin'] ?? null;
        if ($jk !== null && $jk !== '') {
            $jkNorm = strtolower(trim((string) $jk));
            if (isset(self::$genderMap[$jkNorm])) {
                $data['jenis_kelamin'] = self::$genderMap[$jkNorm];
            } else {
                $errors[] = "Jenis kelamin \"{$jk}\" tidak dikenali. Gunakan: L/P, Laki-laki/Perempuan, Male/Female.";
            }
        }

        // ── modalitas (wajib) ──
        $mod = self::sanitizeString($row['modalitas'] ?? null);
        if ($mod === null || $mod === '') {
            $errors[] = 'Kolom "modalitas" wajib diisi.';
        } else {
            $modKey = strtolower(trim($mod));
            if (isset(self::$modalityMap[$modKey])) {
                $data['modalitas'] = self::$modalityMap[$modKey];
            } else {
                $errors[] = "Modalitas \"{$mod}\" tidak dikenali. Opsi yang tersedia: Thorax PA, Thorax AP, Thorax Lateral, Waters, USG Abdomen, USG Pelvic.";
            }
        }

        // ── indikasi_klinis ──
        $indikasi = self::sanitizeString($row['indikasi_klinis'] ?? null);
        if ($indikasi !== null && $indikasi !== '') {
            $data['indikasi_klinis'] = mb_substr($indikasi, 0, 255);
        }

        // ── catatan_petugas ──
        $catatan = self::sanitizeString($row['catatan_petugas'] ?? null);
        if ($catatan !== null && $catatan !== '') {
            $data['catatan_petugas'] = $catatan;
        }

        return ['data' => $data, 'errors' => $errors];
    }

    /**
     * Sanitasi string: trim, buang karakter kontrol.
     */
    protected static function sanitizeString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $str = trim((string) $value);
        // Buang karakter kontrol kecuali newline/tab
        $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $str);
        return $str === '' ? null : $str;
    }

    /**
     * Paksa value jadi string (tangani kasus float dari Excel untuk NIK 16 digit).
     */
    protected static function forceString(mixed $value): string
    {
        if (is_float($value)) {
            // Cek apakah angka bulat yang besar (kemungkinan NIK)
            if ($value == floor($value) && $value > 0) {
                return number_format($value, 0, '', '');
            }
        }
        return (string) $value;
    }

    /**
     * Daftar semua value modalitas yang valid.
     */
    public static function validModalities(): array
    {
        return array_unique(array_values(self::$modalityMap));
    }
}
