<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // Menampilkan data siswa dan pencarian
    public function index(Request $request)
    {
        $query = Student::with('classroom');

        // Filter berdasarkan kelas
        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        // Pencarian berdasarkan NIS atau nama
        if ($request->filled('search')) {
            $keyword = trim($request->search);

            $query->where(function ($q) use ($keyword) {
                $q->where('nis', 'like', '%' . $keyword . '%')
                  ->orWhere('name', 'like', '%' . $keyword . '%');
            });
        }

        // Ambil data dengan pagination 20 siswa per halaman
        $students = $query->latest()->paginate(20)->withQueryString();

        $classroom = null;

        if ($request->filled('classroom_id')) {
            $classroom = Classroom::findOrFail($request->classroom_id);
        }

        return view('students.index', compact(
            'students',
            'classroom'
        ));
    }

    // Menampilkan formulir tambah siswa
    public function create()
    {
        $classrooms = Classroom::orderBy('name')->get();

        return view('students.create', compact('classrooms'));
    }

    // Menyimpan data siswa baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:students,nis'],
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'address' => ['nullable', 'string'],
        ]);

        $validated['barcode'] = 'ABS' . $validated['nis'];

        Student::create($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    // Menampilkan formulir edit siswa
    public function edit(Student $student)
    {
        $classrooms = Classroom::orderBy('name')->get();

        return view('students.edit', compact('student', 'classrooms'));
    }

    // Memperbarui data siswa
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => [
                'required',
                'string',
                'max:30',
                Rule::unique('students', 'nis')->ignore($student->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'address' => ['nullable', 'string'],
        ]);

        $student->update($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    // Menghapus data siswa
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    // Menampilkan kartu siswa untuk dicetak
    public function card(Student $student)
    {
        $student->load('classroom');

        return view('students.card', compact('student'));
    }
}
