@extends('backend.app')
@section('title', 'Detail Monitoring Makanan')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card shadow-sm border-0">
                        {{-- HEADER --}}
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0 fw-bold text-primary">
                                    <i class="fa fa-utensils me-2"></i>Detail Monitoring Makanan Harian
                                </h4>
                                <a href="{{ route('monitoring_makanan.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            {{-- INFORMASI PASIEN & PETUGAS --}}
                            <div class="row g-3 mb-4 p-3 bg-light rounded border">
                                <div class="col-md-3 col-6">
                                    <span class="text-muted small d-block fw-semibold">Nama Peserta</span>
                                    <span class="fs-6 fw-bold text-dark">{{ $monitoring->peserta->nama ?? '-' }}</span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted small d-block fw-semibold">No. RM / BPJS</span>
                                    <span class="fs-6 fw-bold text-dark">
                                        {{ $monitoring->peserta->no_rm ?? '-' }} /
                                        {{ $monitoring->peserta->no_bpjs ?? '-' }}
                                    </span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted small d-block fw-semibold">Pendamping / Petugas</span>
                                    <div>
                                        <span class="badge bg-white text-dark border fw-normal">
                                            <i class="fa fa-user-md me-1 text-primary"></i>
                                            {{ $monitoring->petugas->nama ?? 'Mandiri (Pasien)' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted small d-block fw-semibold">Tanggal Catatan</span>
                                    <span class="fs-6 fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse($monitoring->tanggal)->isoFormat('DD MMMM YYYY') }}
                                    </span>
                                </div>
                            </div>
                            {{-- EVALUASI ASUPAN ENERGI (GRID FIX: 4 Kolom x 3 Grid = 12) --}}
                            <h5 class="fw-bold mb-3 text-secondary">
                                <i class="fa fa-chart-pie me-1"></i> Evaluasi Asupan Energi Harian
                            </h5>
                            @php
                                $totalAsupan = $monitoring->total_kalori ?? 0;
                                $targetKalori = $kebutuhanKalori ?? 0;
                                $persen = $persenKalori ?? 0;
                                // Selisih kalori
                                $selisih = $totalAsupan - $targetKalori;
                                if ($persen < 90) {
                                    $statusBadge = 'bg-warning text-dark';
                                    $statusText = 'Kalori Kurang';
                                    $selisihClass = 'text-warning';
                                    $borderClass = 'border-warning';
                                    $selisihText = abs($selisih) . ' Kkal di bawah target';
                                } elseif ($persen <= 110) {
                                    $statusBadge = 'bg-success';
                                    $statusText = 'Kalori Sesuai';
                                    $selisihClass = 'text-success';
                                    $borderClass = 'border-success';
                                    $selisihText = 'Target harian tercapai';
                                } else {
                                    $statusBadge = 'bg-danger';
                                    $statusText = 'Kalori Berlebih';
                                    $selisihClass = 'text-danger';
                                    $borderClass = 'border-danger';
                                    $selisihText = '+' . abs($selisih) . ' Kkal di atas target';
                                }
                            @endphp

                            <div class="row g-3 mb-4">
                                <div class="col-lg-3 col-sm-6">
                                    <div class="card border-primary h-100 mb-0 shadow-sm">
                                        <div class="card-body text-center p-3">
                                            <small class="text-muted text-uppercase fw-bold">Total Asupan</small>
                                            <h3 class="mb-0 text-primary fw-bold mt-1">
                                                {{ number_format($totalAsupan, 0, ',', '.') }}
                                            </h3>
                                            <small class="text-muted">Kkal</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6">
                                    <div class="card border-info h-100 mb-0 shadow-sm">
                                        <div class="card-body text-center p-3">
                                            <small class="text-muted text-uppercase fw-bold">Kebutuhan Target</small>
                                            <h3 class="mb-0 text-info fw-bold mt-1">
                                                {{ number_format($targetKalori, 0, ',', '.') }}
                                            </h3>
                                            <small class="text-muted">Kkal / Hari</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6">
                                    <div class="card {{ $borderClass }} h-100 mb-0 shadow-sm">
                                        <div class="card-body text-center p-3">
                                            <small class="text-muted text-uppercase fw-bold">Tingkat Kecukupan</small>
                                            <h3 class="mb-1 fw-bold mt-1 {{ $selisihClass }}">
                                                {{ number_format($persen, 1) }}%
                                            </h3>
                                            <span class="badge {{ $statusBadge }} fs-7">
                                                {{ $statusText }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6">
                                    <div class="card {{ $borderClass }} h-100 mb-0 shadow-sm">
                                        <div class="card-body text-center p-3">
                                            <small class="text-muted text-uppercase fw-bold">Selisih Kalori</small>
                                            <h3 class="fw-bold mt-1 mb-0 {{ $selisihClass }}">
                                                {{ number_format(abs($selisih), 0, ',', '.') }}
                                            </h3>
                                            <small class="text-muted">{{ $selisihText }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- DATA ANTROPOMETRI --}}
                            @if (isset($pemeriksaanTerakhir) && $pemeriksaanTerakhir)
                                <div class="alert alert-light border shadow-sm mb-4">
                                    <h6 class="fw-bold text-dark mb-3">
                                        <i class="fa fa-heartbeat text-danger me-1"></i>
                                        Data Antropometri Terakhir
                                    </h6>
                                    <div class="row text-center g-3">
                                        {{-- IMT --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-white h-100">
                                                <small class="text-muted d-block mb-1">
                                                    Indeks Massa Tubuh (IMT)
                                                </small>
                                                <h3 class="fw-bold mb-1">
                                                    {{ number_format($pemeriksaanTerakhir->bmi, 2) }}
                                                </h3>
                                                <span class="badge bg-{{ $pemeriksaanTerakhir->badge_imt }}">
                                                    {{ $pemeriksaanTerakhir->kategori_imt }}
                                                </span>
                                            </div>
                                        </div>
                                        {{-- TARGET BERAT BADAN --}}
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-white h-100">
                                                <small class="text-muted d-block mb-1">
                                                    Target Penurunan Berat Badan
                                                </small>
                                                <h4 class="fw-bold text-success">
                                                    {{ $pemeriksaanTerakhir->target_turun_bb ?? '-' }}
                                                </h4>
                                                <small class="text-muted">
                                                    Target yang disarankan setiap minggu
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- TABEL DAFTAR MAKANAN --}}
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fw-bold mb-0 text-secondary">
                                    <i class="fa fa-list me-1"></i> Rincian Konsumsi Makanan
                                </h5>
                            </div>
                            <div class="table-responsive rounded border mb-4">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th style="width: 5%">No</th>
                                            <th style="width: 15%">Waktu Makan</th>
                                            <th>Nama Makanan</th>
                                            <th style="width: 12%">Jumlah</th>
                                            <th style="width: 12%">Satuan</th>
                                            <th style="width: 18%" class="text-end">Estimasi Kalori</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($monitoring->detail as $item)
                                            <tr>
                                                <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-info text-dark fw-normal">
                                                        {{ $item->waktu_makan }}
                                                    </span>
                                                </td>
                                                <td class="fw-bold text-dark">{{ $item->nama_makanan }}</td>
                                                <td class="text-center">{{ (float) $item->jumlah }}</td>
                                                <td class="text-center text-muted">{{ $item->satuan }}</td>
                                                <td class="text-end fw-bold text-primary">
                                                    {{ number_format($item->kalori, 0, ',', '.') }} Kkal
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fa fa-exclamation-circle fa-2x mb-2 d-block text-warning"></i>
                                                    Tidak ada rincian data makanan untuk tanggal ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($monitoring->detail->count() > 0)
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="5" class="text-end">Total Energi Terkonsumsi:</td>
                                                <td class="text-end text-primary fs-6">
                                                    {{ number_format($monitoring->total_kalori, 0, ',', '.') }} Kkal
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>

                            {{-- CATATAN & EVALUASI NUTRIGENOMIK --}}
                            <div class="p-3 bg-light rounded border">
                                <label class="fw-bold text-dark mb-2 d-block">
                                    <i class="fa fa-sticky-note me-1 text-warning"></i> Catatan Evaluasi & Rekomendasi
                                    Nutrigenomik
                                </label>
                                <p class="mb-0 text-muted fst-italic">
                                    {{ $monitoring->catatan ?: 'Tidak ada catatan tambahan dari petugas.' }}
                                </p>
                            </div>
                        </div>

                        {{-- FOOTER --}}
                        <div class="card-footer bg-white text-end py-3 border-top">
                            @if (!auth()->user()->hasRole('Peserta'))
                                <a href="{{ route('monitoring_makanan.edit', $monitoring->id) }}"
                                    class="btn btn-warning me-1">
                                    <i class="fa fa-edit me-1"></i> Edit Data
                                </a>
                            @endif
                            <a href="{{ route('monitoring_makanan.index') }}" class="btn btn-secondary">
                                Tutup
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
