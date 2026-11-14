
@extends('layouts.app')

@section('title', 'Data Kelas - Absensi Barcode')

@section('content')

    <!-- JUDUL DAN TOMBOL TAMBAH -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h3 class="fw-bold mb-1">Data Kelas</h3>
            <p class="text-muted mb-0">
                Kelola data kelas siswa.
            </p>
        </div>

        <a href="{{ route('classrooms.create') }}" class="btn btn-primary">
            + Tambah Kelas
        </a>

    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}

            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- TABEL DATA KELAS -->
    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Daftar Kelas</h5>

                <span class="text-muted small">
                    Total {{ $classrooms->count() }} kelas
                </span>
            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th width="60">No</th>
                            <th>Nama Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th width="230">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($classrooms as $classroom)

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td class="fw-semibold">
                                    {{ $classroom->name }}
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $classroom->students_count }} siswa
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1">

                                        <a href="{{ route('students.index', ['classroom_id' => $classroom->id]) }}"
                                           class="btn btn-info btn-sm">
                                            Lihat Siswa
                                        </a>

                                        <a href="{{ route('classrooms.edit', $classroom->id) }}"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form action="{{ route('classrooms.destroy', $classroom->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data kelas.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
