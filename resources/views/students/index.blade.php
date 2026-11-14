
@extends('layouts.app')

@section('title', 'Data Siswa - Absensi Barcode')

@section('content')

    {{-- JUDUL DAN TOMBOL --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1">
                @if($classroom)
                    Data Siswa Kelas {{ $classroom->name }}
                @else
                    Data Seluruh Siswa
                @endif
            </h3>

            <p class="text-muted mb-0">
                Kelola data siswa dan barcode absensi.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('students.import.create') }}" class="btn btn-success">
                <i class="me-1">↑</i> Upload Excel
            </a>

            <a href="{{ route('students.create') }}" class="btn btn-primary">
                + Tambah Siswa
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI HASIL IMPORT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>{{ session('success') }}</strong>

            @if(session()->has('imported'))
                <hr>
                Berhasil ditambahkan:
                <strong>{{ session('imported') }} siswa</strong>
                <br>
                Dilewati:
                <strong>{{ session('skipped', 0) }} siswa</strong>
            @endif

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- DETAIL DATA YANG DILEWATI --}}
    @if(session('import_errors'))
        <div class="alert alert-warning">
            <strong>Detail data yang dilewati:</strong>

            <ul class="mb-0 mt-2">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- FORM PENCARIAN --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('students.index') }}" method="GET">

                @if(request('classroom_id'))
                    <input type="hidden" name="classroom_id"
                           value="{{ request('classroom_id') }}">
                @endif

                <label for="search" class="form-label fw-bold">
                    Cari Data Siswa
                </label>

                <div class="row g-2">
                    <div class="col-md-9">
                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            placeholder="Masukkan NIS atau nama siswa..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            Cari
                        </button>

                        <a href="{{ route('students.index', request('classroom_id') ? ['classroom_id' => request('classroom_id')] : []) }}"
                           class="btn btn-secondary flex-fill">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA SISWA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold mb-0">Daftar Siswa</h5>

                <span class="text-muted small">
                    Menampilkan {{ $students->firstItem() ?? 0 }}
                    - {{ $students->lastItem() ?? 0 }}
                    dari {{ $students->total() }} siswa
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Kelas</th>
                            <th>Barcode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    {{ $students->firstItem() + $loop->index }}
                                </td>

                                <td>{{ $student->nis }}</td>

                                <td class="fw-semibold">
                                    {{ $student->name }}
                                </td>

                                <td>
                                    {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </td>

                                <td>
                                    {{ $student->classroom->name ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {!! (new \Picqer\Barcode\Renderers\SvgRenderer())->render(
                                        (new \Picqer\Barcode\Types\TypeCode128())->getBarcode($student->barcode)
                                    ) !!}

                                    <br>
                                    <small>{{ $student->barcode }}</small>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('students.edit', $student->id) }}"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <a href="{{ route('students.card', $student->id) }}"
                                           class="btn btn-success btn-sm"
                                           target="_blank">
                                            Cetak Kartu
                                        </a>

                                        <form action="{{ route('students.destroy', $student->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">

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
                                <td colspan="7" class="text-center text-muted py-4">
                                    @if(request('search'))
                                        Siswa dengan kata kunci
                                        "<strong>{{ request('search') }}</strong>"
                                        tidak ditemukan.
                                    @else
                                        Belum ada data siswa.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- PAGINATION --}}
            @if($students->hasPages())
                <div class="mt-3">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
