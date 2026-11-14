
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Siswa - Absensi Barcode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            ABSENSI BARCODE
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-light btn-sm">Logout</button>
        </form>
    </div>
</nav>

<div class="container mt-4">

    <div class="mb-4">
        <h3>Tambah Data Siswa</h3>
        <p class="text-muted">Masukkan identitas siswa yang akan didaftarkan.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('students.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">NIS</label>
                    <input type="text"
                           name="nis"
                           class="form-control"
                           value="{{ old('nis') }}"
                           placeholder="Masukkan NIS siswa"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           placeholder="Masukkan nama lengkap siswa"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-select" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>
                            Laki-laki
                        </option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>
                            Perempuan
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="classroom_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>

                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}"
                                {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </select>

                    @if($classrooms->isEmpty())
                        <small class="text-danger">
                            Belum ada kelas. Tambahkan data kelas terlebih dahulu.
                        </small>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="address"
                              class="form-control"
                              rows="3"
                              placeholder="Masukkan alamat siswa">{{ old('address') }}</textarea>
                </div>

                <div class="alert alert-info">
                    <strong>Informasi Barcode:</strong>
                    <br>
                    Barcode akan dibuat otomatis menggunakan format
                    <strong>ABS + NIS</strong>.
                    <br>
                    Contoh: NIS 12345 menjadi ABS12345.
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Data Siswa
                </button>

                <a href="{{ route('students.index') }}" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
