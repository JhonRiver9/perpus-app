<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    // ✅ BERANDA (LIST + SEARCH + FILTER)
    public function index(Request $request)
    {
        $query = Book::query();

        // 🔍 Search nama buku
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // 📚 Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // 📄 Pagination
        $books = $query->paginate(12);

        return view('welcome', compact('books'));
    }

    // ✅ DETAIL BUKU
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }
}