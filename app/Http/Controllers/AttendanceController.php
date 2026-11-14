<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class AttendanceController extends Controller
{
    // HALAMAN ABSENSI ADMIN
    public function index()
    {
        $today = now()->toDateString();

        $attendances = Attendance::with('student.classroom')
            ->whereDate('attendance_date', $today)
            ->orderByDesc('check_in')
            ->get();

        $totalHadir = $attendances->count();

        return view('attendance.index', compact(
            'attendances',
            'totalHadir'
        ));
    }

    // HALAMAN SCAN KHUSUS PETUGAS
    public function petugas()
    {
        return view('attendance.petugas');
    }

    // PROSES SCAN BARCODE
    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:255',
        ]);

        $student = Student::where('barcode', $request->barcode)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak ditemukan.',
            ], 404);
        }

        $today = now()->toDateString();

        $existing = Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ini sudah melakukan absensi hari ini.',
            ], 409);
        }

        try {
            $attendance = DB::transaction(function () use ($student, $today) {
                return Attendance::create([
                    'student_id' => $student->id,
                    'attendance_date' => $today,
                    'check_in' => now()->format('H:i:s'),
                    'status' => 'hadir',
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil!',
                'student' => [
                    'name' => $student->name,
                    'time' => $attendance->check_in,
                ],
            ]);

        } catch (QueryException $e) {
            $duplicate = Attendance::where('student_id', $student->id)
                ->whereDate('attendance_date', $today)
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa ini sudah melakukan absensi hari ini.',
                ], 409);
            }

            throw $e;
        }
    }
}
