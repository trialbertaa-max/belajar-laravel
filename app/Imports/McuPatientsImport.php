<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;

class McuPatientsImport implements ToCollection, WithHeadingRow, WithColumnFormatting
{
    public Collection $rows;

    public function collection(Collection $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Paksa kolom MRN/NIK dibaca sebagai teks agar tidak menjadi notasi ilmiah.
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // kolom mrn_nik biasanya di kolom B
        ];
    }
}
