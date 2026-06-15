<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Book;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BorrowExport;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $filter = $request->filter;

        $borrows = Borrow::with(['book', 'student'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('kelas', 'like', "%{$search}%");
                })
                ->orWhereHas('book', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%")
                      ->orWhere('jenis', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('tanggal_pinjam', 'like', "%{$search}%")
                ->orWhere('tanggal_kembali', 'like', "%{$search}%");
            })

            ->when($filter == 'dipinjam', function ($query) {
                $query->where('status', 'dipinjam');
            })

            ->when($filter == 'dikembalikan', function ($query) {
                $query->where('status', 'dikembalikan');
            })

            ->when($filter == 'terlambat', function ($query) {
                $query->where('status', 'dipinjam')
                      ->whereDate('tanggal_kembali', '<', now());
            })

            ->latest()
            ->paginate(10);

        return view('borrows.index', compact('borrows', 'search', 'filter'));
    }

    public function create()
    {
        $books = Book::where('status', 'tersedia')->get();
        $students = Student::all();

        return view('borrows.create', compact('books', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'tanggal_pinjam' => 'required|date',
        ]);

        $tanggal_pinjam = Carbon::parse($request->tanggal_pinjam);
        $tanggal_kembali = $tanggal_pinjam->copy()->addWeeks(3);

        Borrow::create([
            'book_id' => $request->book_id,
            'student_id' => $request->student_id,
            'tanggal_pinjam' => $tanggal_pinjam,
            'tanggal_kembali' => $tanggal_kembali,
            'status' => 'dipinjam'
        ]);

        Book::where('id', $request->book_id)->update([
            'status' => 'dipinjam'
        ]);

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Peminjaman berhasil dicatat!');
    }

    public function update(Request $request, Borrow $borrow)
    {
        $borrow->update([
            'status' => 'dikembalikan'
        ]);

        Book::where('id', $borrow->book_id)->update([
            'status' => 'tersedia'
        ]);

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Buku berhasil dikembalikan!');
    }

    public function exportPdf()
    {
        $borrows = Borrow::with(['book', 'student'])->get();

        $pdf = Pdf::loadView('borrows.pdf', compact('borrows'));

        return $pdf->download('laporan-peminjaman.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new BorrowExport, 'laporan-peminjaman.xlsx');
    }
}