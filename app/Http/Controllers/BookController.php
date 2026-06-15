<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BookImport;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $books = Book::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('jenis', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('tahun_terbit', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10);

        return view('books.index', compact('books', 'search'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'         => 'required|string|max:255',
            'jenis'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:255',
            'tahun_terbit' => 'required|digits:4|integer',
            'kondisi'      => 'required|in:baik,sedang,rusak',
            'sinopsis'     => 'nullable|string',
            'stok'         => 'required|integer|min:1',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['status'] = 'tersedia';

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Book $book)
    {
        $book->load(['borrows.student']);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'nama'         => 'required|string|max:255',
            'jenis'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:255',
            'tahun_terbit' => 'required|digits:4|integer',
            'kondisi'      => 'required|in:baik,sedang,rusak',
            'sinopsis'     => 'nullable|string',
            'status'       => 'required|in:tersedia,dipinjam',
            'stok'         => 'required|integer|min:0',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validated['stok'] <= 0) {
            $validated['status'] = 'dipinjam';
        }

        if ($validated['stok'] > 0 && $validated['status'] == 'dipinjam') {
            $validated['status'] = 'tersedia';
        }

        if ($request->hasFile('cover')) {
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }

            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy(Book $book)
    {
        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new BookImport, $request->file('file'));

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil diimport!');
    }
}