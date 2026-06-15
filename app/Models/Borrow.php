<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id', 'student_id', 'tanggal_pinjam', 'tanggal_kembali', 'status'
    ];

    // Relasi balikan: Peminjaman ini milik buku apa?
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relasi balikan: Peminjaman ini dilakukan oleh siswa siapa?
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}