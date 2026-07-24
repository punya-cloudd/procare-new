@extends('backend.app')
@section('title', 'Detail Kuisioner Bouchard')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        Detail Kuisioner Latihan Fisik Bouchard
                    </h4>
                    <a href="{{ route('bouchard.history', $bouchard->peserta_id) }}"
                        class="btn text-white shadow-sm px-4 py-2"
                        style="background:linear-gradient(to right,#667eea,#764ba2);border:none;">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">No RM</th>
                                    <td>{{ $bouchard->peserta->no_rm }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Peserta</th>
                                    <td>{{ $bouchard->peserta->nama }}</td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>{{ $bouchard->peserta->nik }}</td>
                                </tr>
                                <tr>
                                    <th>No BPJS</th>
                                    <td>{{ $bouchard->peserta->no_bpjs }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>
                                        {{ $bouchard->peserta->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Tanggal</th>
                                    <td>
                                        {{ \Carbon\Carbon::parse($bouchard->tanggal)->format('d-m-Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Petugas</th>
                                    <td>
                                        {{ $bouchard->petugas->nama ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Berat Badan</th>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ number_format($bouchard->berat_badan, 2) }} Kg
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $bouchard->kategori ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <h4 class="mb-3">
                        Monitoring Aktivitas Harian
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th width="70">Jam</th>
                                    <th>00-15</th>
                                    <th>15-30</th>
                                    <th>30-45</th>
                                    <th>45-60</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $detail = $bouchard->detail->keyBy('jam');
                                @endphp
                                @for ($jam = 0; $jam <= 23; $jam++)
                                    @php
                                        $row = $detail->get($jam);
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold">
                                            {{ sprintf('%02d', $jam) }}:00
                                        </td>
                                        {{-- 00 - 15 --}}
                                        <td class="text-center">
                                            @if ($row && $row->m00)
                                                <div class="fw-bold">
                                                    {{ $row->aktivitas($row->m00) }}
                                                </div>
                                                <small class="text-primary">
                                                    {{ number_format($row->energi($row->m00), 2) }}
                                                    kcal/kg/15 menit
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        {{-- 15 - 30 --}}
                                        <td class="text-center">
                                            @if ($row && $row->m15)
                                                <div class="fw-bold">
                                                    {{ $row->aktivitas($row->m15) }}
                                                </div>
                                                <small class="text-primary">
                                                    {{ number_format($row->energi($row->m15), 2) }}
                                                    kcal/kg/15 menit
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        {{-- 30 - 45 --}}
                                        <td class="text-center">
                                            @if ($row && $row->m30)
                                                <div class="fw-bold">
                                                    {{ $row->aktivitas($row->m30) }}
                                                </div>
                                                <small class="text-primary">
                                                    {{ number_format($row->energi($row->m30), 2) }}
                                                    kcal/kg/15 menit
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        {{-- 45 - 60 --}}
                                        <td class="text-center">
                                            @if ($row && $row->m45)
                                                <div class="fw-bold">
                                                    {{ $row->aktivitas($row->m45) }}
                                                </div>
                                                <small class="text-primary">
                                                    {{ number_format($row->energi($row->m45), 2) }}
                                                    kcal/kg/15 menit
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    @php
                        $detailModel = new \App\Models\BouchardDetail();

                        $totalEnergi = 0;
                        $totalMET = 0;
                        $slotTerisi = 0;

                        foreach ($bouchard->detail as $det) {
                            foreach ([$det->m00, $det->m15, $det->m30, $det->m45] as $val) {
                                if (empty($val)) {
                                    continue;
                                }

                                $energi = $detailModel->energi($val);

                                $totalEnergi += $energi;

                                $totalMET += $energi / 0.25;

                                $slotTerisi++;
                            }
                        }

                        $totalKalori = $totalEnergi * $bouchard->berat_badan;

                        $met = $slotTerisi > 0 ? round($totalMET / $slotTerisi, 2) : 0;

                        $pal = $met;

                        if ($pal <= 0) {
                            $kategori = '-';
                            $badge = 'secondary';
                        } elseif ($pal < 1.4) {
                            $kategori = "Sangat Ringan\n(Sedentary)";
                            $badge = 'secondary';
                        } elseif ($pal < 1.7) {
                            $kategori = "Ringan\n(Light)";
                            $badge = 'info';
                        } elseif ($pal < 2.0) {
                            $kategori = "Sedang\n(Moderate)";
                            $badge = 'warning';
                        } else {
                            $kategori = "Berat\n(Vigorous)";
                            $badge = 'danger';
                        }
                    @endphp

                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                Ringkasan Monitoring Bouchard
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-lg col-md-6">
                                    <div class="summary-card border-start border-5 border-primary">
                                        <small>Berat Badan</small>
                                        <h3>{{ number_format($bouchard->berat_badan, 2) }}</h3>
                                        <span>Kg</span>
                                    </div>
                                </div>

                                <div class="col-lg col-md-6">
                                    <div class="summary-card border-start border-5 border-success">
                                        <small>Total Energi</small>
                                        <h3>{{ number_format($totalEnergi, 2) }}</h3>
                                        <span>kcal/kg</span>
                                    </div>
                                </div>

                                <div class="col-lg col-md-6">
                                    <div class="summary-card border-start border-5 border-warning">
                                        <small>Total Kalori</small>
                                        <h3>{{ number_format($totalKalori, 2) }}</h3>
                                        <span>Kkal</span>
                                    </div>
                                </div>

                                <div class="col-lg col-md-6">
                                    <div class="summary-card border-start border-5 border-info">
                                        <small>Rata-rata MET</small>
                                        <h3>{{ number_format($met, 2) }}</h3>
                                        <span>MET</span>
                                    </div>
                                </div>

                                <div class="col-lg col-md-6">
                                    <div class="summary-card border-start border-5 border-danger">
                                        <small>PAL</small>
                                        <h3>{{ number_format($pal, 2) }}</h3>
                                        <span>EE / BMR</span>
                                    </div>
                                </div>

                                <div class="col-lg col-md-6">
                                    <div class="summary-card border-start border-5 border-secondary">

                                        <small class="text-muted d-block">
                                            Kategori Aktivitas
                                        </small>

                                        <div class="kategori-wrapper">

                                            <div id="badge_pal">

                                                <span class="activity-dot bg-{{ explode(' ', $badge)[0] }}"></span>

                                                <span class="kategori-text">
                                                    {{ $kategori }}
                                                </span>

                                            </div>

                                        </div>

                                        <div class="mt-2 kategori-footer">
                                            <span class="text-muted">
                                                Bouchard / PAL
                                            </span>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <hr>

                            <div class="row mt-3">

                                <div class="col-md-6">
                                    <strong>Catatan</strong>
                                    <div class="border rounded p-3 mt-2">
                                        {{ $bouchard->catatan ?: '-' }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th width="40%">Dibuat</th>
                                            <td>{{ optional($bouchard->created_at)->format('d-m-Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Terakhir Diubah</th>
                                            <td>{{ optional($bouchard->updated_at)->format('d-m-Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('bouchard.history', $bouchard->peserta_id) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-2"></i>
                            Kembali ke Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .summary-card {
        background: #fff;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        transition: .25s;
        height: 100%;
    }

    .summary-card:hover {
        transform: translateY(-3px);
    }

    .summary-card small {
        color: #7d8ca3;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .summary-card h3 {
        margin: 8px 0 3px;
        font-size: 30px;
        font-weight: 700;
        color: #344767;
    }

    .summary-card span {
        color: #7b809a;
        font-size: 13px;
    }

    .kategori-wrapper {
        display: flex;
        align-items: center;
        height: 70px;
        padding-left: 10px;
    }

    #badge_pal {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .activity-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        box-shadow: 0 0 8px rgba(0, 0, 0, .18);
    }

    .kategori-text {
        white-space: pre-line;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.35;
        color: #344767;
        text-align: left;
    }
</style>
