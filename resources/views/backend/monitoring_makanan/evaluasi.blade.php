@extends('backend.app')
@section('title', 'Evaluasi Monitoring Makanan')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- HEADER PAGE --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-1">
                    <i class="fas fa-chart-line me-2"></i>Evaluasi Monitoring Makanan
                </h3>
                <p class="text-muted small mb-0">Laporan evaluasi & pencapaian target nutrisi harian peserta</p>
            </div>
            <a href="{{ route('monitoring_makanan.history', $peserta->id) }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- SECTION 1: PROFIL PASIEN --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 border-end-md">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            {{-- AVATAR INISIAL NAMA PASIEN --}}
                            <div class="rounded-circle fw-bold fs-3 d-flex align-items-center justify-content-center text-primary shadow-sm me-3 flex-shrink-0" 
                                 style="width: 60px; height: 60px; background-color: #e3f2fd; border: 2px solid #0d6efd;">
                                {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">{{ $peserta->nama }}</h5>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="badge bg-primary">RM: {{ $peserta->no_rm }}</span>
                                    <span class="badge bg-light text-dark border">BPJS: {{ $peserta->no_bpjs ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <div class="row g-2 text-sm">
                            <div class="col-6">
                                <small class="text-muted d-block">NIK</small>
                                <span class="fw-semibold text-dark">{{ $peserta->nik ?? '-' }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Penyakit</small>
                                <span class="fw-semibold text-dark">{{ $peserta->jenisPenyakit->nama_penyakit ?? 'tidak ada' }}</span>
                            </div>
                            <div class="col-12 mt-2">
                                <small class="text-muted d-block">Dokter Penanggung Jawab</small>
                                <span class="fw-semibold text-primary">
                                    <i class="fas fa-user-md me-1"></i>{{ $peserta->dokter->nama ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: METRIK STATISTIK UTAMA --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body p-3">
                        <small class="text-muted fw-bold d-block mb-1">TOTAL KALORI</small>
                        <h4 class="fw-bold text-success mb-0">{{ number_format($ringkasan['total_kalori']) }}</h4>
                        <small class="text-muted">Kkal terkumpul</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body p-3">
                        <small class="text-muted fw-bold d-block mb-1">RATA-RATA HARI</small>
                        <h4 class="fw-bold text-primary mb-0">{{ number_format($ringkasan['rata_kalori']) }}</h4>
                        <small class="text-muted">Kkal / hari</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                    <div class="card-body p-3">
                        <small class="text-muted fw-bold d-block mb-1">TARGET KALORI</small>
                        <h4 class="fw-bold text-info mb-0">{{ number_format($ringkasan['target_kalori']) }}</h4>
                        <small class="text-muted">Kkal / hari</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                    <div class="card-body p-3">
                        <small class="text-muted fw-bold d-block mb-1">TOTAL MONITORING</small>
                        <h4 class="fw-bold text-warning mb-0">{{ $ringkasan['jumlah_monitoring'] }}</h4>
                        <small class="text-muted">Hari tercatat</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: RINGKASAN CAPAIAN HARI --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background-color: #e8f5e9; border-left: 5px solid #2e7d32 !important;">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: #1b5e20;">Hari Sesuai Target</h6>
                            <small style="color: #2e7d32;">Tercapai ideal</small>
                        </div>
                        <h2 class="fw-bold mb-0" style="color: #1b5e20;">
                            {{ $ringkasan['jumlah_sesuai'] }} <span class="fs-6">Hari</span>
                        </h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background-color: #fff3e0; border-left: 5px solid #ef6c00 !important;">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: #e65100;">Hari Asupan Kurang</h6>
                            <small style="color: #ef6c00;">Di bawah target</small>
                        </div>
                        <h2 class="fw-bold mb-0" style="color: #e65100;">
                            {{ $ringkasan['jumlah_kurang'] }} <span class="fs-6">Hari</span>
                        </h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background-color: #ffebee; border-left: 5px solid #c62828 !important;">
                    <div class="card-body d-flex align-items-center justify-content-between p-3">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: #b71c1c;">Hari Asupan Berlebih</h6>
                            <small style="color: #c62828;">Melebihi target</small>
                        </div>
                        <h2 class="fw-bold mb-0" style="color: #b71c1c;">
                            {{ $ringkasan['jumlah_lebih'] }} <span class="fs-6">Hari</span>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: TABEL RIWAYAT MONITORING --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fas fa-list-alt me-2 text-primary"></i>Rincian Riwayat Harian
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">NO</th>
                                <th>TANGGAL</th>
                                <th>PETUGAS INPUT</th>
                                <th class="text-center">JUMLAH MENU</th>
                                <th class="text-center">TOTAL KALORI</th>
                                <th class="text-center">% TARGET</th>
                                <th class="text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monitoring as $item)
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">
                                        <i class="far fa-calendar-alt me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-user-edit me-1"></i>{{ $item->petugas->nama ?? 'Mandiri (Pasien)' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->detail->count() }} Menu</span>
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        {{ number_format($item->total_kalori) }} <small class="text-muted fw-normal">Kkal</small>
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ $item->persentase }}%
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $item->status_kalori['badge'] }} px-3 py-2">
                                            {{ $item->status_kalori['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        Belum ada data riwayat monitoring.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SECTION 5: KESIMPULAN EVALUASI MEDIS --}}
        @php
            $isSesuai = $ringkasan['jumlah_sesuai'] >= ($ringkasan['jumlah_monitoring'] * 0.7);
            $isKurang = $ringkasan['jumlah_kurang'] > $ringkasan['jumlah_lebih'];
            
            $boxStyle = $isSesuai 
                ? 'background-color: #e8f5e9; border: 1px solid #a5d6a7; color: #1b5e20;' 
                : ($isKurang 
                    ? 'background-color: #fff3e0; border: 1px solid #ffe0b2; color: #e65100;' 
                    : 'background-color: #ffebee; border: 1px solid #ffcdd2; color: #b71c1c;');
                    
            $iconClass = $isSesuai ? 'fa-check-circle' : ($isKurang ? 'fa-exclamation-triangle' : 'fa-times-circle');
        @endphp

        <div class="p-4 rounded-3 shadow-sm mb-4" style="{{ $boxStyle }}">
            <div class="d-flex align-items-start gap-3">
                <i class="fas {{ $iconClass }} fa-2x mt-1"></i>
                <div>
                    <h5 class="fw-bold mb-1">Kesimpulan Evaluasi Medis</h5>
                    @if ($isSesuai)
                        <p class="mb-0">
                            Asupan energi peserta secara umum <strong>sudah sesuai</strong> dengan target kebutuhan kalori harian. 
                            Sangat disarankan untuk terus mempertahankan pola makan dan aktivitas fisik yang telah berjalan saat ini.
                        </p>
                    @elseif($isKurang)
                        <p class="mb-0">
                            Asupan energi peserta lebih sering berada <strong>di bawah target (kurang)</strong>. 
                            Disarankan untuk meningkatkan konsumsi porsi makanan bergizi seimbang sesuai dengan acuan kalori harian.
                        </p>
                    @else
                        <p class="mb-0">
                            Asupan energi peserta lebih sering <strong>melebihi target</strong> yang ditentukan. 
                            Disarankan untuk membatasi konsumsi makanan tinggi gula & lemak, serta mengimbanginya dengan aktivitas fisik rutin.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- TOMBOL EXPORT --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="#" class="btn btn-outline-danger shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
            <a href="#" class="btn btn-outline-success shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
        </div>

    </div>
</div>
@endsection