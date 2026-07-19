@extends('backend.app')
@php
    \Carbon\Carbon::setLocale('id');
@endphp
@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="page-inner">
            {{-- <!-- HEADER -->
            <div class="d-flex align-items-left flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-3">Dashboard ProCare</h3>
                    <h6 class="op-7 mb-2">Monitoring Program Pengelolaan Penyakit Kronis (Prolanis)</h6>
                </div>
            </div> --}}
            <!-- WELCOME CARD DISINI -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0"
                        style="background: linear-gradient(135deg,#1572E8,#48ABF7); color:white;">
                        <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">

                            <div>
                                <h2 class="fw-bold mb-2">
                                    Selamat Datang,
                                    {{ Auth::user()->name }} 👋
                                </h2>

                                <p class="mb-1">
                                    Selamat datang di
                                    <strong>ProCare - Sistem Monitoring Prolanis</strong>.
                                </p>

                                <small>
                                    Semoga aktivitas hari ini berjalan lancar.
                                    Jangan lupa melakukan monitoring peserta secara berkala.
                                </small>
                            </div>

                            <div class="text-start text-lg-end mt-3 mt-lg-0">
                                <h5>{{ now()->translatedFormat('l') }}</h5>
                                <h3>{{ now()->translatedFormat('d F Y') }}</h3>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD -->
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3">
                                    <div class="numbers">
                                        <p class="card-category">Total Peserta</p>
                                        <h4 class="card-title">{{ $totalPeserta }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-notes-medical"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3">
                                    <div class="numbers">
                                        <p class="card-category">Pemeriksaan</p>
                                        <h4 class="card-title">{{ $totalPemeriksaan }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-danger bubble-shadow-small">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3">
                                    <div class="numbers">
                                        <p class="card-category">Risiko Tinggi</p>
                                        <h4 class="card-title">{{ $risikoTinggi }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3">
                                    <div class="numbers">
                                        <p class="card-category">Monitoring Makanan</p>
                                        <h4 class="card-title">{{ $totalMonitoring }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Bouchard -->
                <div class="col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3">
                                    <div class="numbers">
                                        <p class="card-category">Kuisioner Aktivitas Fisik</p>
                                        <h4 class="card-title">{{ $totalBouchard }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dokter -->
                <div class="col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3">
                                    <div class="numbers">
                                        <p class="card-category">Dokter Aktif</p>
                                        <h4 class="card-title">{{ $totalDokter }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jenis Penyakit -->
                <div class="col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                        <i class="fas fa-stethoscope"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3">
                                    <div class="numbers">
                                        <p class="card-category">Jenis Penyakit</p>
                                        <h4 class="card-title">{{ $totalJenisPenyakit }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHART -->
            <div class="row mt-4">
                <!-- PIE CHART (FIX SIZE) -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Distribusi Risiko Pasien</div>
                        </div>

                        <div class="card-body d-flex justify-content-center">
                            <div style="width: 260px; height: 260px;">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAR CHART -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Tren Pemeriksaan 7 Hari</div>
                        </div>
                        <div class="card-body">
                            <canvas id="barChart" style="height:260px;"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TOP RISK -->
            <div class="row mt-4">

                <div class="col-md-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">

                                <div>
                                    <h4 class="card-title mb-1">
                                        Monitoring Harian Peserta Prolanis
                                    </h4>

                                    <small class="text-muted">
                                        Status pemeriksaan, monitoring makanan, dan aktivitas fisik peserta
                                    </small>
                                </div>

                                <div class="mt-3 mt-lg-0">
                                    <form method="GET" action="{{ route('backend.dashboard') }}">
                                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                                            class="form-control" onchange="this.form.submit()">
                                    </form>
                                </div>

                            </div>
                        </div>

                        <div class="card-body p-2 p-md-3">

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>

                                            <th width="5%">No</th>

                                            <th>Nama Peserta</th>

                                            <th>Jenis Penyakit</th>

                                            <th class="text-center">
                                                Monitoring Makanan
                                            </th>

                                            <th class="text-center">
                                                Aktivitas Fisik
                                            </th>

                                            <th class="text-center">
                                                Status
                                            </th>

                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($monitoringHarian as $row)
                                            <tr>

                                                <td>{{ $loop->iteration }}</td>

                                                <td>
                                                    {{ $row->nama }}
                                                </td>

                                                <td>
                                                    {{ $row->jenis_penyakit ?? '-' }}
                                                </td>

                                                <td class="text-center">

                                                    @if ($row->makanan)
                                                        <span class="badge bg-success">
                                                            <i class="fa fa-check"></i> Sudah
                                                        </span>
                                                    @else
                                                        <a href="{{ route('monitoring_makanan.create', ['peserta_id' => $row->id]) }}"
                                                            class="badge bg-danger text-decoration-none">
                                                            <i class="fa fa-plus-circle"></i> Belum
                                                        </a>
                                                    @endif

                                                </td>

                                                <td class="text-center">

                                                    @if ($row->aktivitas)
                                                        <span class="badge bg-success">
                                                            <i class="fa fa-check"></i> Sudah
                                                        </span>
                                                    @else
                                                        <a href="{{ route('bouchard.create', ['peserta_id' => $row->id]) }}"
                                                            class="badge bg-danger text-decoration-none">
                                                            <i class="fa fa-plus-circle"></i> Belum
                                                        </a>
                                                    @endif

                                                </td>

                                                <td class="text-center">

                                                    @if ($row->status == 'Lengkap')
                                                        <span class="badge bg-success">
                                                            Lengkap
                                                        </span>
                                                    @elseif($row->status == 'Belum Lengkap')
                                                        <span class="badge bg-warning text-dark">
                                                            Belum Lengkap
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            Belum Dipantau
                                                        </span>
                                                    @endif

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="7" class="text-center">
                                                    Belum ada data monitoring hari ini.
                                                </td>

                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>

    @endsection

    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // PIE CHART (FIX SIZE)
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($risikoLabels),
                    datasets: [{
                        data: @json($risikoData),
                        backgroundColor: ['#2ecc71', '#f39c12', '#e74c3c']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // BAR CHART
            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: @json($labelTanggal),
                    datasets: [{
                        label: 'Pemeriksaan',
                        data: @json($dataJumlah),
                        backgroundColor: '#1572E8'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        </script>
    @endsection
