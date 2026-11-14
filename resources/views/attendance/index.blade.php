
@extends('layouts.app')

@section('title', 'Absensi Siswa')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Absensi Siswa</h3>
        <p class="text-muted">
            Scan barcode kartu siswa menggunakan kamera.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Scan Barcode</h5>

                    <div id="reader" style="width:100%;"></div>

                    <div class="d-flex gap-2 mt-3">
                        <button id="startButton"
                            class="btn btn-primary"
                            onclick="startScanner()">
                            Aktifkan Kamera
                        </button>

                        <button id="stopButton"
                            class="btn btn-danger"
                            onclick="stopScanner()"
                            disabled>
                            Matikan Kamera
                        </button>
                    </div>

                    <div id="scanMessage" class="alert alert-secondary mt-3 mb-0">
                        Kamera belum diaktifkan.
                    </div>

                    <div id="studentResult" class="mt-3"></div>

                </div>
            </div>
        </div>

        <div class="col-lg-5">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold">Rekap Hari Ini</h5>
                    <p class="text-muted">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>

                    <div class="bg-light rounded p-4 text-center">
                        <h1 class="text-success fw-bold">
                            {{ $totalHadir }}
                        </h1>
                        <div class="text-muted">Siswa Sudah Hadir</div>
                    </div>

                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">Informasi</h5>

                    <ul class="text-muted mb-0">
                        <li class="mb-2">
                            Aktifkan kamera terlebih dahulu.
                        </li>
                        <li class="mb-2">
                            Arahkan kamera ke barcode kartu siswa.
                        </li>
                        <li class="mb-2">
                            Data siswa akan ditemukan otomatis.
                        </li>
                        <li>
                            Satu siswa hanya dapat absen sekali sehari.
                        </li>
                    </ul>

                </div>
            </div>

        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">

            <h5 class="fw-bold mb-3">Daftar Kehadiran Hari Ini</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attendance->student->nis }}</td>
                                <td>{{ $attendance->student->name }}</td>
                                <td>{{ $attendance->student->classroom->name ?? '-' }}</td>
                                <td>{{ $attendance->check_in }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $attendance->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada siswa yang melakukan absensi.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5QrCode = null;
    let isProcessing = false;

    const messageBox = document.getElementById('scanMessage');
    const resultBox = document.getElementById('studentResult');

    
async function startScanner() {
    try {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const cameras = await Html5Qrcode.getCameras();

        if (!cameras || cameras.length === 0) {
            messageBox.className = 'alert alert-danger mt-3';
            messageBox.innerText = 'Kamera tidak ditemukan.';
            return;
        }

        const camera = cameras.find(c =>
            /back|rear|environment/i.test(c.label)
        ) || cameras[0];

        await html5QrCode.start(
            camera.id,
            {
                fps: 15,

                qrbox: {
                    width: 350,
                    height: 180
                },

                aspectRatio: 16 / 9,

                formatsToSupport: [
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.QR_CODE
                ],

                videoConstraints: {
                    width: { ideal: 1920 },
                    height: { ideal: 1080 },
                    focusMode: "continuous"
                }
            },
            onScanSuccess,
            () => {}
        );

        document.getElementById('startButton').disabled = true;
        document.getElementById('stopButton').disabled = false;

        messageBox.className = 'alert alert-success mt-3';
        messageBox.innerText =
            'Kamera aktif. Arahkan barcode ke area pemindaian.';

    } catch (error) {
        console.error(error);

        messageBox.className = 'alert alert-danger mt-3';
        messageBox.innerText =
            'Kamera gagal diaktifkan. Periksa izin dan pengaturan kamera.';
    }
}


    async function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            await html5QrCode.stop();
            document.getElementById('startButton').disabled = false;
            document.getElementById('stopButton').disabled = true;

            messageBox.className = 'alert alert-secondary mt-3';
            messageBox.innerText = 'Kamera telah dimatikan.';
        }
    }

    async function onScanSuccess(decodedText) {
        if (isProcessing) return;

        isProcessing = true;

        messageBox.className = 'alert alert-info mt-3';
        messageBox.innerText = 'Barcode terbaca. Sedang menyimpan absensi...';

        try {
            const response = await fetch(
                "{{ route('attendance.scan') }}",
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        barcode: decodedText
                    })
                }
            );

            const data = await response.json();

            if (data.success) {
                messageBox.className = 'alert alert-success mt-3';
                messageBox.innerText = data.message;

                resultBox.innerHTML = `
                    <div class="card border-success">
                        <div class="card-body">
                            <h5 class="text-success fw-bold">
                                ABSENSI BERHASIL
                            </h5>
                            <p><strong>Nama:</strong> ${escapeHtml(data.student.name)}</p>
                            <p><strong>NIS:</strong> ${escapeHtml(data.student.nis)}</p>
                            <p><strong>Kelas:</strong> ${escapeHtml(data.student.classroom)}</p>
                            <p><strong>Waktu:</strong> ${escapeHtml(data.student.time)}</p>
                        </div>
                    </div>
                `;

                setTimeout(() => location.reload(), 1800);

            } else {
                messageBox.className = 'alert alert-warning mt-3';
                messageBox.innerText = data.message;
                resultBox.innerHTML = '';
            }

        } catch (error) {
            messageBox.className = 'alert alert-danger mt-3';
            messageBox.innerText = 'Terjadi kesalahan saat mengirim data absensi.';
        }

        setTimeout(() => {
            isProcessing = false;
        }, 2500);
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function(char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }
</script>

@endsection
