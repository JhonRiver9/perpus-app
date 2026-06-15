<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Student([
            // Sesuaikan dengan nama kolom di file Excel Anda
            'nis'   => $row['nis'],
            'nama'  => $row['nama'],
            'kelas' => $row['kelas'],
        ]);
    }
}