<?php

namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BookImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Book([
            'nama'         => $row['nama'],
            'jenis'        => $row['jenis'],
            'penulis'      => $row['penulis'],
            'tahun_terbit' => $row['tahun_terbit'],
            'kondisi'      => $row['kondisi'],
            'sinopsis'     => $row['sinopsis'],
            'status'       => 'tersedia', // Otomatis tersedia saat diimport
        ]);
    }
}