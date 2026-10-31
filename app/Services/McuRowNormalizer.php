<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class McuRowNormalizer
{
    /**
     * Daftar modalitas yang valid (value persis dari <select> di Blade).
     * Label digunakan untuk fuzzy matching.
     */
    protected static array $modalityMap = [
        'thorax pa'                     => 'Thorax PA',
        'thorax ap'                     => 'Thorax AP',
        'thorax lateral'                => 'Thorax Lateral',
        'thorax lat'                    => 'Thorax Lateral',
        'lateral'                       => 'Thorax Lateral',
        'waters'                        => 'Waters',
        'usg abdomen'                   => 'USG Abdomen',
        'usg pelvic'                    => 'USG Pelvic',
        // Label alias (teks yang terlihat user di dropdown)
        'thorax pa (rontgen dada)'       => 'Thorax PA',
        'rontgen dada'                   => 'Thorax PA',
        'rontgen dada (thorax pa)'       => 'Thorax PA',
        'foto thorax'                    => 'Thorax PA',
        'waters (sinus paranasal / spn)' => 'Waters',
        'sinus paranasal'                => 'Waters',
        'spn'                            => 'Waters',
    ];

    /**
     * Alias header Excel → nama field internal.
     */
    protected static array $headerAliases = [
        'nama_lengkap'          => 'nama_lengkap',
        'nama'                  => 'nama_lengkap',
        'patient_name'          => 'nama_lengkap',
        'nama_pasien'           => 'nama_lengkap',
        'nama_peserta'          => 'nama_lengkap',

        'mrn_nik'               => 'mrn_nik',
        'no_rm_nik'             => 'mrn_nik',
        'nik'                   => 'mrn_nik',
        'mrn'                   => 'mrn_nik',
        'no_rekam_medis'        => 'mrn_nik',
        'patient_id'            => 'mrn_nik',
        'no_rm'                 => 'mrn_nik',
        'nomor_rm'              => 'mrn_nik',

        'usia'                  => 'usia',
        'umur'                  => 'usia',
        'age'                   => 'usia',

        'jenis_kelamin'         => 'jenis_kelamin',
        'gender'                => 'jenis_kelamin',
        'jk'                    => 'jenis_kelamin',
        'kelamin'               => 'jenis_kelamin',
        'sex'                   => 'jenis_kelamin',

        'modalitas'             => 'modalitas',
        'modality'              => 'modalitas',
        'modalitas_pemeriksaan' => 'modalitas',
        'jenis_pemeriksaan'     => 'modalitas',

        'indikasi_klinis'       => 'indikasi_klinis',
        'indikasi'              => 'indikasi_klinis',
        'deskripsi_sesi'        => 'indikasi_klinis',
        'study_description'     => 'indikasi_klinis',
        'deskripsi'             => 'indikasi_klinis',

        'catatan_petugas'       => 'catatan_petugas',
        'catatan'               => 'catatan_petugas',
        'catatan_tambahan'      => 'catatan_petugas',
        'order_notes'           => 'catatan_petugas',
        'keterangan'            => 'catatan_petugas',

        'tanggal_pemeriksaan'   => 'tanggal_pemeriksaan',
        'tanggal'               => 'tanggal_pemeriksaan',
        'tgl_pemeriksaan'       => 'tanggal_pemeriksaan',
        'tgl'                   => 'tanggal_pemeriksaan',
        'date'                  => 'tanggal_pemeriksaan',
        'exam_date'             => 'tanggal_pemeriksaan',
        'jadwal_mcu'            => 'tanggal_pemeriksaan',
    ];

    /**
     * Mapping gender input → value form ('L' / 'P').
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
     */
    public static function normalizeHeaders(array $rawHeaders): array
    {
        $mapped = [];
        foreach ($rawHeaders as $header) {
            if ($header === null) {
                continue;
            }
            $headerStr = (string) $header;
            $key = strtolower(trim($headerStr));
            $key = preg_replace('/[^a-z0-9]+/i', '_', $key);
            $key = trim($key, '_');

            if (isset(self::$headerAliases[$key])) {
                $mapped[$headerStr] = self::$headerAliases[$key];
            }
        }
        return $mapped;
    }

    /**
     * Normalisasi satu baris data Excel.
     */
    public static function normalizeRow(array $row): array
    {
        $errors = [];
        $errorsMap = [];

        $data = [
            'nama'                => null,
            'nama_lengkap'        => null,
            'nik'                 => null,
            'mrn_nik'             => null,
            'usia'                => null,
            'jenis_kelamin'       => null,
            'modalitas'           => null,
            'indikasi'            => null,
            'indikasi_klinis'     => null,
            'catatan'             => null,
            'catatan_petugas'     => null,
            'tanggal_pemeriksaan' => date('Y-m-d'),
        ];

        // ── 1. nama (wajib, min 2 karakter) ──
        $nama = self::sanitizeString($row['nama_lengkap'] ?? null);
        if ($nama === null || mb_strlen($nama) < 2) {
            $msg = 'Nama lengkap wajib diisi (minimal 2 karakter).';
            $errors[] = $msg;
            $errorsMap['nama'] = $msg;
        } else {
            $data['nama'] = mb_substr($nama, 0, 255);
            $data['nama_lengkap'] = $data['nama'];
        }

        // ── 2. nik (opsional, jika diisi harus 16 digit angka) ──
        $mrn = $row['mrn_nik'] ?? null;
        if ($mrn !== null && $mrn !== '') {
            $mrnStr = self::sanitizeString(self::forceString($mrn));
            if ($mrnStr !== null && $mrnStr !== '') {
                $data['nik'] = mb_substr($mrnStr, 0, 100);
                $data['mrn_nik'] = $data['nik'];

                if (!preg_match('/^[0-9]{16}$/', $data['nik'])) {
                    $msg = 'NIK harus 16 digit angka.';
                    $errors[] = $msg;
                    $errorsMap['nik'] = $msg;
                }
            }
        }

        // ── 3. usia (wajib, 0–120 tahun) ──
        $usia = $row['usia'] ?? null;
        if ($usia === null || $usia === '' || !is_numeric($usia)) {
            $msg = 'Usia wajib diisi angka antara 0 dan 120 tahun.';
            $errors[] = $msg;
            $errorsMap['usia'] = $msg;
        } else {
            $usiaInt = (int) $usia;
            if ($usiaInt < 0 || $usiaInt > 120) {
                $msg = 'Usia harus antara 0 dan 120 tahun.';
                $errors[] = $msg;
                $errorsMap['usia'] = $msg;
            } else {
                $data['usia'] = $usiaInt;
            }
        }

        // ── 4. jenis_kelamin (wajib: L / P) ──
        $jk = $row['jenis_kelamin'] ?? null;
        if ($jk === null || $jk === '') {
            $msg = 'Jenis kelamin wajib dipilih (Laki-laki / Perempuan).';
            $errors[] = $msg;
            $errorsMap['jenis_kelamin'] = $msg;
        } else {
            $jkNorm = strtolower(trim((string) $jk));
            if (isset(self::$genderMap[$jkNorm])) {
                $data['jenis_kelamin'] = self::$genderMap[$jkNorm];
            } else {
                $msg = 'Jenis kelamin tidak dikenali. Pilih Laki-laki atau Perempuan.';
                $errors[] = $msg;
                $errorsMap['jenis_kelamin'] = $msg;
            }
        }

        // ── 5. modalitas (wajib, protokol terdaftar) ──
        $mod = self::sanitizeString($row['modalitas'] ?? null);
        if ($mod === null || $mod === '') {
            $msg = 'Modalitas pemeriksaan wajib dipilih.';
            $errors[] = $msg;
            $errorsMap['modalitas'] = $msg;
        } else {
            $modKey = strtolower(trim($mod));
            if (isset(self::$modalityMap[$modKey])) {
                $data['modalitas'] = self::$modalityMap[$modKey];
            } else {
                $data['modalitas'] = $mod;
                $msg = "Modalitas \"{$mod}\" tidak dikenali dalam daftar protokol.";
                $errors[] = $msg;
                $errorsMap['modalitas'] = $msg;
            }
        }

        // ── 6. indikasi_klinis (opsional) ──
        $indikasi = self::sanitizeString($row['indikasi_klinis'] ?? null);
        if ($indikasi !== null && $indikasi !== '') {
            $data['indikasi'] = mb_substr($indikasi, 0, 255);
            $data['indikasi_klinis'] = $data['indikasi'];
        }

        // ── 7. catatan_petugas (opsional) ──
        $catatan = self::sanitizeString($row['catatan_petugas'] ?? null);
        if ($catatan !== null && $catatan !== '') {
            $data['catatan'] = $catatan;
            $data['catatan_petugas'] = $data['catatan'];
        }

        // ── 8. tanggal_pemeriksaan ──
        $data['tanggal_pemeriksaan'] = self::parseDate($row['tanggal_pemeriksaan'] ?? null);

        return [
            'data'       => $data,
            'errors'     => array_values(array_unique($errors)),
            'errors_map' => $errorsMap,
        ];
    }

    /**
     * Sanitasi string: trim, buang karakter kontrol, netralisir formula injection.
     */
    public static function sanitizeString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $str = trim((string) $value);
        $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $str);
        if ($str === '') {
            return null;
        }

        // Netralisir formula injection jika diawali =, +, -, @
        if (in_array(substr($str, 0, 1), ['=', '+', '-', '@'], true)) {
            $str = "'" . $str;
        }

        return $str;
    }

    /**
     * Paksa value jadi string (tangani kasus float dari Excel untuk NIK 16 digit).
     */
    public static function forceString(mixed $value): string
    {
        if (is_float($value)) {
            if ($value == floor($value) && $value > 0) {
                return number_format($value, 0, '', '');
            }
        }
        $str = (string) $value;
        if (preg_match('/^[0-9]+(\.[0-9]+)?e\+[0-9]+$/i', $str)) {
            return number_format((float) $str, 0, '', '');
        }
        return $str;
    }

    /**
     * Parse tanggal dari format Excel atau string ke YYYY-MM-DD.
     */
    public static function parseDate(mixed $value): string
    {
        if ($value === null || $value === '') {
            return date('Y-m-d');
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        $str = trim((string) $value);
        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable $e) {
            return date('Y-m-d');
        }
    }

    /**
     * Daftar semua value modalitas yang valid.
     */
    public static function validModalities(): array
    {
        return array_unique(array_values(self::$modalityMap));
    }
}
