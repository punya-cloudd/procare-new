<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" href="{{ url('backend/assets/img/kaiadmin/logo_tambah.png') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ url('backend/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons"
                ],
                urls: ["{{ url('backend/assets/css/fonts.min.css') }}"]
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- Main CSS Files -->
    <link rel="stylesheet" href="{{ url('backend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('backend/assets/css/plugins.min.css') }}">
    <link rel="stylesheet" href="{{ url('backend/assets/css/kaiadmin.min.css') }}">
    <link rel="stylesheet" href="{{ url('backend/assets/css/custom.css') }}">

    <link rel="stylesheet" href="{{ url('backend/assets/css/demo.css') }}">

    <link href="tom-select.css" rel="stylesheet">
    <script src="tom-select.complete.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    @yield('styles')
</head>

<body>

    <div class="wrapper">
        {{-- Sidebar --}}
        @include('layouts.components.sidebar')

        <div class="main-panel">
            {{-- Header --}}
            @include('layouts.components.header')
            @yield('content')
            @include('layouts.components.footer')
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ url('backend/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ url('backend/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ url('backend/assets/js/core/bootstrap.min.js') }}"></script>

    <!-- Plugins -->
    <script src="{{ url('backend/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ url('backend/assets/js/kaiadmin.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ url('backend/assets/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart JS -->
    <script src="{{ url('backend/assets/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- 🔥 TAMBAHKAN INI -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
        $('.logout-btn').click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit logout form
                    window.location.href = "{{ route('logout') }}";
                }
            });
        });

        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    </script>

    @yield('script')
</body>

</html>
