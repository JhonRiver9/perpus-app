<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis',
        'penulis',
        'tahun_terbit',
        'kondisi',
        'sinopsis',
        'status',
        'stok',
        'cover'
    ];

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}