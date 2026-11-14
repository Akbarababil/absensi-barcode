
@extends('layouts.app')

@section('title', 'Dashboard - Absensi Barcode')

@section('content')

    <!-- SELAMAT DATANG -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h3 class="fw-bold mb-2">
                Selamat Datang, {{ auth()->user()->name }}!
            </h3>

            <p class="text-muted mb-2">
                Anda berhasil masuk ke aplikasi Absensi Barcode.
            </p>

            <span class="badge bg-primary">
                Role: {{ auth()->user()->role }}
            </span>
        </div>
    </div>

    <!-- MENU DASHBOARD -->
    <div class="mb-4">
        <h4 class="fw-bold">Menu Utama</h4>
        <p class="text-muted">
            Pilih menu untuk mengelola sistem absensi siswa.
        </p>
    </div>

    <div class="row g-4">

        <!-- DATA KELAS -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span class="badge bg-primary p-2">
                            DATA KELAS
                        </span>
                    </div>

                    <h5 class="fw-bold">Data Kelas</h5>

                    <p class="text-muted">
                        Kelola informasi kelas siswa.
                    </p>

                    <a href="{{ route('classrooms.index') }}"
                       class="btn btn-primary w-100">
                        Kelola Kelas
                    </a>
                </div>
            </div>
        </div>

        <!-- DATA SISWA -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span class="badge bg-success p-2">
                            DATA SISWA
                        </span>
                    </div>

                    <h5 class="fw-bold">Data Siswa</h5>

                    <p class="text-muted">
                        Kelola data siswa dan barcode absensi.
                    </p>

                    <a href="{{ route('students.index') }}"
                       class="btn btn-success w-100">
                        Kelola Siswa
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection
