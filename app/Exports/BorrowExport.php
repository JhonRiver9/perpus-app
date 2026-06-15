<?php

namespace App\Exports;

use App\Models\Borrow;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BorrowExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Borrow::with(['book', 'student'])->get();
    }

    // Mengatur judul kolom di Excel
    public function headings(): array
    {
        return ['Nama Peminjam', 'Judul Buku', 'Tanggal Pinjam', 'Tenggat Waktu', 'Status'];
    }

    // Mengatur isi baris per kolom
    public function map($borrow): array
    {
        return [
            $borrow->student->nama,
            $borrow->book->nama,
            $borrow->tanggal_pinjam,
            $borrow->tanggal_kembali,
            strtoupper($borrow->status),
        ];
    }
}