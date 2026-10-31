<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportWorklistRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk melakukan permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk data import batch worklist.
     */
    public function rules(): array
    {
        return [
            'pasien'                       => 'required|array|min:1|max:500',
            'pasien.*.id'                  => 'nullable|string',
            'pasien.*.nama'                => 'required|string|min:2|max:255',
            'pasien.*.nik'                 => ['nullable', 'string', 'max:100', 'regex:/^[0-9]{16}$/'],
            'pasien.*.usia'                => 'required|integer|min:0|max:120',
            'pasien.*.jenis_kelamin'       => 'required|string|in:L,P,Laki-laki,Perempuan',
            'pasien.*.modalitas'           => 'required|string|in:Thorax PA,Thorax AP,Thorax Lateral,Waters,USG Abdomen,USG Pelvic',
            'pasien.*.indikasi'            => 'nullable|string|max:255',
            'pasien.*.catatan'             => 'nullable|string|max:1000',
            'pasien.*.tanggal_pemeriksaan' => 'nullable|date',
        ];
    }

    /**
     * Pesan error kustom dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'pasien.required'                  => 'Daftar pasien wajib dikirim.',
            'pasien.array'                     => 'Format data pasien tidak valid.',
            'pasien.min'                       => 'Minimal 1 pasien harus dipilih untuk dikirim.',
            'pasien.max'                       => 'Maksimal 500 pasien per pengiriman.',
            'pasien.*.nama.required'           => 'Nama lengkap wajib diisi.',
            'pasien.*.nama.min'                => 'Nama lengkap minimal 2 karakter.',
            'pasien.*.nik.regex'               => 'NIK harus 16 digit angka.',
            'pasien.*.usia.required'           => 'Usia wajib diisi.',
            'pasien.*.usia.integer'            => 'Usia harus berupa angka bulat.',
            'pasien.*.usia.min'                => 'Usia minimal 0 tahun.',
            'pasien.*.usia.max'                => 'Usia maksimal 120 tahun.',
            'pasien.*.jenis_kelamin.required'  => 'Jenis kelamin wajib dipilih.',
            'pasien.*.jenis_kelamin.in'        => 'Jenis kelamin tidak valid.',
            'pasien.*.modalitas.required'      => 'Modalitas pemeriksaan wajib dipilih.',
            'pasien.*.modalitas.in'            => 'Modalitas tidak dikenal dalam daftar protokol.',
        ];
    }

    /**
     * Pastikan respon selalu JSON 422 saat validasi gagal, tanpa redirect HTML.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'message' => 'Ada data yang perlu diperbaiki.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
