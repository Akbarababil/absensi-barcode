<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
{
    public $imported = 0;
    public $skipped = 0;
    public $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {

            // Nomor baris Excel
            $rowNumber = $index + 2;

            $nis = trim((string) ($row['nis'] ?? ''));
            $name = trim((string) ($row['nama'] ?? ''));
            $gender = strtoupper(trim((string) ($row['jenis_kelamin'] ?? '')));
            $className = trim((string) ($row['kelas'] ?? ''));
            $address = trim((string) ($row['alamat'] ?? ''));

            // Lewati baris kosong
            if (
                $nis === '' &&
                $name === '' &&
                $gender === '' &&
                $className === '' &&
                $address === ''
            ) {
                continue;
            }

            // Validasi NIS
            if ($nis === '') {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNumber}: NIS wajib diisi.";
                continue;
            }

            // Validasi nama
            if ($name === '') {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNumber}: Nama wajib diisi.";
                continue;
            }

            // Validasi jenis kelamin
            if (!in_array($gender, ['L', 'P'])) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNumber}: Jenis kelamin harus L atau P.";
                continue;
            }

            // Cari kelas berdasarkan nama
            $classroom = Classroom::where('name', $className)->first();

            if (!$classroom) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNumber}: Kelas {$className} tidak ditemukan.";
                continue;
            }

            // Cek NIS ganda
            if (Student::where('nis', $nis)->exists()) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNumber}: NIS {$nis} sudah terdaftar.";
                continue;
            }

            // Buat barcode otomatis
            $barcode = 'ABS' . $nis;

            // Cek barcode ganda
            if (Student::where('barcode', $barcode)->exists()) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNumber}: Barcode {$barcode} sudah digunakan.";
                continue;
            }

            // Simpan data siswa
            Student::create([
                'nis' => $nis,
                'name' => $name,
                'gender' => $gender,
                'classroom_id' => $classroom->id,
                'address' => $address,
                'barcode' => $barcode,
            ]);

            $this->imported++;
        }
    }
}
