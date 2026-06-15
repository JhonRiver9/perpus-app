<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Book;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $trendPeminjaman = Borrow::selectRaw('DATE(tanggal_pinjam) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->pluck('total', 'date');

        $detailTerlambat = Borrow::with(['student', 'book'])
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', '<', now())
            ->get();

        $totalBuku = Book::count();
        $totalSiswa = Student::count();
        $totalDipinjam = Borrow::where('status', 'dipinjam')->count();
        $totalTerlambat = $detailTerlambat->count();

        $statJenisBuku = Book::selectRaw('jenis, COUNT(*) as total')
            ->groupBy('jenis')
            ->pluck('total', 'jenis');

        $statStatusBuku = Book::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $aktivitasTerbaru = Borrow::with(['student', 'book'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'trendPeminjaman',
            'detailTerlambat',
            'totalBuku',
            'totalSiswa',
            'totalDipinjam',
            'totalTerlambat',
            'statJenisBuku',
            'statStatusBuku',
            'aktivitasTerbaru'
        ));
    }
}