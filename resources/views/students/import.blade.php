
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Data Siswa</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 750px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
            color: #222;
        }

        .description {
            color: #666;
            line-height: 1.6;
        }

        .upload-box {
            border: 2px dashed #0d6efd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 25px 0;
            background: #f8fbff;
        }

        input[type="file"] {
            margin-top: 15px;
            max-width: 100%;
        }

        .btn {
            display: inline-block;
            padding: 11px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #0d6efd;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-success {
            background: #198754;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f1f3f5;
        }

        .alert {
            padding: 13px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #842029;
        }

        .note {
            background: #fff3cd;
            padding: 12px;
            border-radius: 5px;
            color: #664d03;
            font-size: 14px;
            line-height: 1.6;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Import Data Siswa dari Excel</h2>

    <p class="description">
        Upload file Excel untuk menambahkan banyak siswa sekaligus
        tanpa perlu mengisi formulir satu per satu.
    </p>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="upload-box">

        <h3>Pilih File Excel</h3>

        <p>Format yang didukung: XLSX, XLS, CSV</p>

        <form action="{{ route('students.import.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <input type="file"
                   name="file"
                   accept=".xlsx,.xls,.csv"
                   required>

            <br><br>

            <button type="submit" class="btn btn-success">
                Import Data Siswa
            </button>

        </form>

    </div>

    <h3>Format Kolom Excel</h3>

    <p>Baris pertama Excel harus menggunakan nama kolom berikut:</p>

    <table>
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Kelas</th>
                <th>Alamat</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>123456</td>
                <td>Akbar</td>
                <td>L</td>
                <td>9A</td>
                <td>Semarang</td>
            </tr>

            <tr>
                <td>123457</td>
                <td>Andi</td>
                <td>L</td>
                <td>9A</td>
                <td>Demak</td>
            </tr>

            <tr>
                <td>123458</td>
                <td>Siti</td>
                <td>P</td>
                <td>9A</td>
                <td>Sayung</td>
            </tr>
        </tbody>
    </table>

    <div class="note">
        <strong>Perhatian:</strong>
        <ul>
            <li>Nama kolom harus sama dengan contoh di atas.</li>
            <li>Jenis kelamin hanya menggunakan L atau P.</li>
            <li>Nama kelas harus sudah tersedia di menu Data Kelas.</li>
            <li>NIS sebaiknya diatur sebagai format Text di Excel agar angka nol di depan tidak hilang.</li>
            <li>NIS yang sudah terdaftar akan dilewati, bukan menimpa data lama.</li>
        </ul>
    </div>

    <br>

    <a href="{{ route('students.index') }}" class="btn btn-secondary">
        Kembali ke Data Siswa
    </a>

</div>

</body>
</html>
