<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('students')
            ->latest()
            ->get();

        return view('classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('classrooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:classrooms,name'],
        ], [
            'name.required' => 'Nama kelas wajib diisi.',
            'name.unique' => 'Nama kelas sudah tersedia.',
        ]);

        Classroom::create([
            'name' => $request->name,
        ]);

        return redirect()->route('classrooms.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classrooms', 'name')->ignore($classroom->id),
            ],
        ]);

        $classroom->update([
            'name' => $request->name,
        ]);

        return redirect()->route('classrooms.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->students()->exists()) {
            return redirect()->route('classrooms.index')
                ->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa.');
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
