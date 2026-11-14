
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Absensi Barcode')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        /* HEADER */
        .main-header {
                background: #102a43;
                color: white;
                padding: 18px 30px;
            }

            .brand {
                display: flex;
                align-items: center;
                flex-direction: row;
                gap: 15px;
            }

            .school-logo {
                width: 65px;
                height: 65px;
                object-fit: contain;
                background: white;
                padding: 4px;
                border-radius: 8px;
                flex-shrink: 0;
            }

            .brand-text h4 {
                margin: 0;
                font-weight: bold;
            }

            .brand-text small {
                color: #cbd5e1;
        }
        /* NAVBAR */
        .main-navbar {
            background: #ffffff;
            padding: 0 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        }

        .main-navbar .nav-link {
            color: #334155;
            font-weight: 600;
            padding: 17px 18px;
            border-bottom: 3px solid transparent;
        }

        .main-navbar .nav-link:hover,
        .main-navbar .nav-link.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
        }

        .page-content {
            padding: 30px;
        }

        @media (max-width: 768px) {
            .main-header {
                padding: 15px;
            }

            .main-navbar {
                padding: 0 15px;
            }

            .page-content {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header class="main-header">
    <div class="container-fluid">
        <div class="brand">
            <!-- LOGO DI KIRI -->
            <img src="{{ asset('images/PP.jpg') }}"
                 alt="Logo SMP 1 Demak"
                 class="school-logo">

            <!-- TEKS DI KANAN -->
            <div class="brand-text">
                <h4>ABSENSI SISWA SMP 1 DEMAK</h4>
                <small>Sistem Informasi Kehadiran Siswa</small>
            </div>
        </div>
    </div>
    </header>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg main-navbar">
        <div class="container-fluid">

            <button class="navbar-toggler my-2" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
                           href="{{ route('students.index') }}">
                            Data Siswa
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}"
                           href="{{ route('classrooms.index') }}">
                            Data Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}"
                        href="{{ route('attendance.index') }}">
                            Absensi
                        </a>
                    </li>

                </ul>

                <div class="d-flex align-items-center gap-3 py-2">
                    <span class="text-secondary">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- KONTEN HALAMAN -->
    <main class="page-content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close"
                        data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
