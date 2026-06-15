<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis', 'nama', 'kelas'
    ];

    // Relasi: Satu siswa bisa memiliki banyak riwayat peminjaman
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}