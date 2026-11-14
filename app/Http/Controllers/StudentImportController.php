<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentImport;

class StudentImportController extends Controller
{
    public function create()
    {
        return view('students.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new StudentImport();

        Excel::import($import, $request->file('file'));

        return redirect()
            ->route('students.index')
            ->with('success', 'Import Excel berhasil!')
            ->with('imported', $import->imported)
            ->with('skipped', $import->skipped)
            ->with('import_errors', $import->errors);
    }
}