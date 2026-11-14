
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Siswa - {{ $student->name }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eeeeee;
            margin: 0;
            padding: 30px;
        }

        /* TAMPILAN KARTU DI LAYAR */
        .card-student {
            width: 600px;
            height: 378px;
            background: white;
            border: 1px solid #cccccc;
            border-radius: 18px;
            overflow: hidden;
            margin: 20px auto;
            position: relative;
            color: #222;
            box-shadow: 0 5px 18px rgba(0,0,0,0.15);
        }

        .card-header {
            background: #0d6efd;
            color: white;
            text-align: center;
            padding: 22px 10px;
        }

        .card-header h3 {
            margin: 0;
            font-size: 25px;
        }

        .card-header p {
            margin: 7px 0 0;
            font-size: 15px;
        }

        .card-body {
            padding: 22px 30px;
            font-size: 17px;
        }

        .student-name {
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .student-info {
            line-height: 1.8;
            font-size: 17px;
        }

        .barcode {
            text-align: center;
            margin-top: 12px;
        }

        .barcode svg {
            width: 300px;
            height: 65px;
            max-width: 100%;
        }

        .barcode small {
            display: block;
            font-size: 14px;
            letter-spacing: 2px;
            margin-top: 3px;
        }

        .print-button {
            display: block;
            margin: 20px auto;
            padding: 12px 30px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .back-button {
            display: block;
            width: fit-content;
            margin: 10px auto;
            padding: 12px 30px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        /* UKURAN CETAK TETAP STANDAR KARTU */
        @media print {
            @page {
                size: 85.6mm 54mm;
                margin: 0;
            }

            html, body {
                width: 85.6mm;
                height: 54mm;
                padding: 0;
                margin: 0;
                background: white;
            }

            .card-student {
                width: 85.6mm;
                height: 54mm;
                margin: 0;
                border: none;
                border-radius: 0;
                box-shadow: none;
                page-break-after: avoid;
            }

            .card-header {
                padding: 8px 5px;
            }

            .card-header h3 {
                font-size: 13px;
            }

            .card-header p {
                font-size: 8px;
                margin-top: 3px;
            }

            .card-body {
                padding: 9px 12px;
                font-size: 9px;
            }

            .student-name {
                font-size: 13px;
                margin-bottom: 6px;
            }

            .student-info {
                font-size: 9px;
                line-height: 1.7;
            }

            .barcode {
                margin-top: 5px;
            }

            .barcode svg {
                width: 48mm;
                height: 12mm;
            }

            .barcode small {
                font-size: 8px;
                letter-spacing: 1px;
                margin-top: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        /* RESPONSIVE UNTUK LAYAR KECIL */
        @media screen and (max-width: 650px) {
            body {
                padding: 15px;
            }

            .card-student {
                width: 100%;
                height: auto;
                aspect-ratio: 600 / 378;
            }

            .card-header {
                padding: 4% 2%;
            }

            .card-header h3 {
                font-size: clamp(13px, 4vw, 25px);
            }

            .card-header p {
                font-size: clamp(9px, 2.5vw, 15px);
            }

            .card-body {
                padding: 3% 5%;
            }

            .student-name {
                font-size: clamp(14px, 4vw, 25px);
                margin-bottom: 2%;
            }

            .student-info {
                font-size: clamp(10px, 2.8vw, 17px);
                line-height: 1.6;
            }

            .barcode {
                margin-top: 2%;
            }

            .barcode svg {
                width: 55%;
                height: auto;
                max-height: 65px;
            }

            .barcode small {
                font-size: clamp(8px, 2.3vw, 14px);
            }
        }
    </style>
</head>

<body>

    <div class="card-student">

        <div class="card-header">
            <h3>KARTU IDENTITAS SISWA</h3>
            <p>ABSENSI BARCODE</p>
        </div>

        <div class="card-body">

            <div class="student-name">
                {{ strtoupper($student->name) }}
            </div>

            <div class="student-info">
                <div>NIS: {{ $student->nis }}</div>

                <div>
                    Kelas: {{ $student->classroom->name ?? '-' }}
                </div>

                <div>
                    Jenis Kelamin:
                    {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                </div>
            </div>

            <div class="barcode">
                {!! (new \Picqer\Barcode\Renderers\SvgRenderer())->render(
                    (new \Picqer\Barcode\Types\TypeCode128())->getBarcode($student->barcode)
                ) !!}

                <small>{{ $student->barcode }}</small>
            </div>

        </div>
    </div>

    <button class="print-button no-print" onclick="window.print()">
        Cetak Kartu
    </button>

    <a href="{{ route('students.index') }}" class="back-button no-print">
        Kembali ke Data Siswa
    </a>

</body>
</html>
