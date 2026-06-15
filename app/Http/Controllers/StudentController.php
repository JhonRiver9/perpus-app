<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentImport;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $students = Student::when($search, function ($query) use ($search) {
            $query->where('nis', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10);

        return view('students.index', compact('students', 'search'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis|max:20',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function show(Student $student)
    {
    $student->load(['borrows.book']);

    return view('students.show', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:students,nis,' . $student->id,
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus!');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new StudentImport, $request->file('file'));
        
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diimport!');
    }
}