
@extends('layouts.app')

@section('title', 'Scan Absensi Petugas')

@section('content')

<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Absensi Siswa</h3>
                    <p class="text-muted mb-0">
                        Silakan scan barcode kartu siswa.
                    </p>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        Logout
                    </button>
                </form>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3 text-center">
                        Scan Barcode Siswa
                    </h5>

                    <div id="reader" style="width:100%;"></div>

                    <div class="d-grid gap-2 mt-3">
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

                    <div id="scanMessage"
                        class="alert alert-secondary mt-3 mb-0 text-center">
                        Kamera belum diaktifkan.
                    </div>

                    <div id="studentResult" class="mt-3"></div>

                </div>
            </div>

            <p class="text-center text-muted mt-3">
                Satu siswa hanya dapat melakukan absensi satu kali sehari.
            </p>

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

            resultBox.innerHTML = '';

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
                'Kamera gagal diaktifkan. Periksa izin kamera.';
        }
    }

    async function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            await html5QrCode.stop();
        }

        document.getElementById('startButton').disabled = false;
        document.getElementById('stopButton').disabled = true;
    }

    async function onScanSuccess(decodedText) {
        if (isProcessing) return;

        isProcessing = true;

        messageBox.className = 'alert alert-info mt-3';
        messageBox.innerText = 'Barcode terbaca. Memproses absensi...';

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
                messageBox.className =
                    'alert alert-success mt-3 text-center fw-bold';

                messageBox.innerText = 'ABSENSI BERHASIL';

                resultBox.innerHTML = `
                    <div class="alert alert-success text-center">
                        <h4 class="fw-bold mb-2">
                            ✓ BERHASIL
                        </h4>
                        <p class="mb-0">
                            ${escapeHtml(data.message)}
                        </p>
                    </div>
                `;

            } else {
                messageBox.className =
                    'alert alert-danger mt-3 text-center fw-bold';

                messageBox.innerText = 'ABSENSI GAGAL';

                resultBox.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <h4 class="fw-bold mb-2">
                            ✕ GAGAL
                        </h4>
                        <p class="mb-0">
                            ${escapeHtml(data.message)}
                        </p>
                    </div>
                `;
            }

        } catch (error) {
            console.error(error);

            messageBox.className =
                'alert alert-danger mt-3 text-center fw-bold';

            messageBox.innerText = 'ABSENSI GAGAL';

            resultBox.innerHTML = `
                <div class="alert alert-danger text-center">
                    Terjadi kesalahan saat mengirim data.
                    Silakan coba kembali.
                </div>
            `;
        }

        // Matikan kamera setelah satu barcode diproses
        try {
            if (html5QrCode && html5QrCode.isScanning) {
                await html5QrCode.stop();
            }
        } catch (error) {
            console.error(error);
        }

        document.getElementById('startButton').disabled = false;
        document.getElementById('startButton').innerText = 'Scan Berikutnya';
        document.getElementById('stopButton').disabled = true;

        isProcessing = false;
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
