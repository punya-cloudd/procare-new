<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Evaluasi Monitoring Makanan - {{ $peserta->nama }}</title>

    <style>
        @page {
            margin: 1.2cm 1.2cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #2c3e50;
            line-height: 1.4;
        }

        /* HEADER KOP */
        .header {
            border-bottom: 3px double #0d6efd;
            padding-bottom: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 16px;
            text-transform: uppercase;
            color: #0d6efd;
            letter-spacing: 0.5px;
        }

        .header .subtitle {
            font-size: 11px;
            color: #6c757d;
            margin-top: 3px;
        }

        /* SECTION TITLES */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0d6efd;
            text-transform: uppercase;
            border-bottom: 1.5px solid #0d6efd;
            padding-bottom: 3px;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        /* TABLES GENERAL */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* INFO PESERTA TABLE */
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            width: 18%;
        }

        .info-value {
            width: 32%;
        }

        /* METRICS & RINGKASAN BOXES */
        .table-metrics,
        .table-ringkasan,
        .table-data {
            width: 100%;
            margin-bottom: 12px;
        }

        .table-metrics td {
            padding: 8px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .metric-title {
            font-size: 9px;
            font-weight: bold;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .metric-value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .metric-sub {
            font-size: 9px;
            color: #6c757d;
        }

        /* BORDERED DATA TABLE */
        .table-data th,
        .table-data td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
        }

        .table-data th {
            background-color: #f8f9fa;
            color: #343a40;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        /* UTILITIES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-12 { margin-bottom: 12px; }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
        }
        .bg-success { background-color: #198754; color: #fff; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger  { background-color: #dc3545; color: #fff; }
        .bg-info    { background-color: #0dcaf0; color: #000; }
        .bg-secondary{ background-color: #6c757d; color: #fff; }

        /* KESIMPULAN BOX STYLES */
        .kesimpulan-box {
            padding: 10px 12px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 11px;
            line-height: 1.5;
        }

        /* FOOTER & SIGNATURE */
        .footer-table {
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-title {
            margin-bottom: 45px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h2>EVALUASI MONITORING MAKANAN</h2>
        <div class="subtitle">Laporan Evaluasi & Pencapaian Target Nutrisi Harian Peserta</div>
    </div>

    {{-- SECTION 1: PROFIL PASIEN --}}
    <div class="section-title">Profil Peserta</div>
    <table class="info-table mb-12">
        <tr>
            <td class="info-label">Nama Peserta</td>
            <td class="info-value">: <strong>{{ $peserta->nama }}</strong></td>
            <td class="info-label">No. Rekam Medis</td>
            <td class="info-value">: {{ $peserta->no_rm }}</td>
        </tr>
        <tr>
            <td class="info-label">NIK</td>
            <td class="info-value">: {{ $peserta->nik ?? '-' }}</td>
            <td class="info-label">No. BPJS</td>
            <td class="info-value">: {{ $peserta->no_bpjs ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Penyakit</td>
            <td class="info-value">: {{ $peserta->jenisPenyakit->nama_penyakit ?? 'tidak ada' }}</td>
            <td class="info-label">Dokter PJ</td>
            <td class="info-value">: {{ $peserta->dokter->nama ?? '-' }}</td>
        </tr>
    </table>

    {{-- SECTION 2: METRIK STATISTIK UTAMA --}}
    <div class="section-title">Ringkasan Statistik Nutrisi</div>
    <table class="table-metrics mb-12">
        <tr>
            <td width="25%" style="border-left: 4px solid #198754;">
                <div class="metric-title">Total Kalori</div>
                <div class="metric-value" style="color: #198754;">{{ number_format($ringkasan['total_kalori']) }}</div>
                <div class="metric-sub">Kkal terkumpul</div>
            </td>
            <td width="25%" style="border-left: 4px solid #0d6efd;">
                <div class="metric-title">Rata-Rata Hari</div>
                <div class="metric-value" style="color: #0d6efd;">{{ number_format($ringkasan['rata_kalori']) }}</div>
                <div class="metric-sub">Kkal / hari</div>
            </td>
            <td width="25%" style="border-left: 4px solid #0dcaf0;">
                <div class="metric-title">Target Kalori</div>
                <div class="metric-value" style="color: #0dcaf0;">{{ number_format($ringkasan['target_kalori']) }}</div>
                <div class="metric-sub">Kkal / hari</div>
            </td>
            <td width="25%" style="border-left: 4px solid #ffc107;">
                <div class="metric-title">Total Monitoring</div>
                <div class="metric-value" style="color: #ffc107;">{{ $ringkasan['jumlah_monitoring'] }}</div>
                <div class="metric-sub">Hari tercatat</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 3: RINGKASAN CAPAIAN HARI --}}
    <table class="table-metrics mb-12">
        <tr>
            <td width="33.3%" style="background-color: #e8f5e9; border: 1px solid #a5d6a7; border-left: 5px solid #2e7d32;">
                <div style="color: #1b5e20; font-weight: bold; font-size: 11px;">Hari Sesuai Target</div>
                <div style="color: #2e7d32; font-size: 9px; margin-bottom: 4px;">Tercapai ideal</div>
                <div style="color: #1b5e20; font-size: 15px; font-weight: bold;">
                    {{ $ringkasan['jumlah_sesuai'] }} <span style="font-size: 10px; font-weight: normal;">Hari</span>
                </div>
            </td>
            <td width="33.3%" style="background-color: #fff3e0; border: 1px solid #ffe0b2; border-left: 5px solid #ef6c00;">
                <div style="color: #e65100; font-weight: bold; font-size: 11px;">Hari Asupan Kurang</div>
                <div style="color: #ef6c00; font-size: 9px; margin-bottom: 4px;">Di bawah target</div>
                <div style="color: #e65100; font-size: 15px; font-weight: bold;">
                    {{ $ringkasan['jumlah_kurang'] }} <span style="font-size: 10px; font-weight: normal;">Hari</span>
                </div>
            </td>
            <td width="33.3%" style="background-color: #ffebee; border: 1px solid #ffcdd2; border-left: 5px solid #c62828;">
                <div style="color: #b71c1c; font-weight: bold; font-size: 11px;">Hari Asupan Berlebih</div>
                <div style="color: #c62828; font-size: 9px; margin-bottom: 4px;">Melebihi target</div>
                <div style="color: #b71c1c; font-size: 15px; font-weight: bold;">
                    {{ $ringkasan['jumlah_lebih'] }} <span style="font-size: 10px; font-weight: normal;">Hari</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 4: TABEL RIWAYAT MONITORING --}}
    <div class="section-title">Rincian Riwayat Harian</div>
    <table class="table-data mb-12">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Tanggal</th>
                <th width="25%">Petugas Input</th>
                <th width="13%" class="text-center">Jumlah Menu</th>
                <th width="17%" class="text-center">Total Kalori</th>
                <th width="10%" class="text-center">% Target</th>
                <th width="15%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($monitoring as $item)
                <tr>
                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                    <td>{{ $item->petugas->nama ?? 'Mandiri (Pasien)' }}</td>
                    <td class="text-center">{{ $item->detail->count() }} Menu</td>
                    <td class="text-center fw-bold">{{ number_format($item->total_kalori) }} <small style="color: #6c757d; font-weight: normal;">Kkal</small></td>
                    <td class="text-center fw-bold">{{ $item->persentase }}%</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $item->status_kalori['badge'] }}">
                            {{ $item->status_kalori['label'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #6c757d;">
                        Belum ada data riwayat monitoring.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SECTION 5: KESIMPULAN EVALUASI MEDIS --}}
    @php
        $isSesuai = $ringkasan['jumlah_sesuai'] >= $ringkasan['jumlah_monitoring'] * 0.7;
        $isKurang = $ringkasan['jumlah_kurang'] > $ringkasan['jumlah_lebih'];

        $boxStyle = $isSesuai
            ? 'background-color: #e8f5e9; border: 1px solid #a5d6a7; color: #1b5e20;'
            : ($isKurang
                ? 'background-color: #fff3e0; border: 1px solid #ffe0b2; color: #e65100;'
                : 'background-color: #ffebee; border: 1px solid #ffcdd2; color: #b71c1c;');
    @endphp

    <div class="section-title">Kesimpulan Evaluasi Medis</div>
    <div class="kesimpulan-box" style="{{ $boxStyle }}">
        <strong style="font-size: 12px; display: block; margin-bottom: 3px;">Kesimpulan Evaluasi Medis</strong>
        @if ($isSesuai)
            <p style="margin: 0;">
                Asupan energi peserta secara umum <strong>sudah sesuai</strong> dengan target kebutuhan kalori harian.
                Sangat disarankan untuk terus mempertahankan pola makan dan aktivitas fisik yang telah berjalan saat ini.
            </p>
        @elseif($isKurang)
            <p style="margin: 0;">
                Asupan energi peserta lebih sering berada <strong>di bawah target (kurang)</strong>.
                Disarankan untuk meningkatkan konsumsi porsi makanan bergizi seimbang sesuai dengan acuan kalori harian.
            </p>
        @else
            <p style="margin: 0;">
                Asupan energi peserta lebih sering <strong>melebihi target</strong> yang ditentukan.
                Disarankan untuk membatasi konsumsi makanan tinggi gula & lemak, serta mengimbanginya dengan aktivitas fisik rutin.
            </p>
        @endif
    </div>

    {{-- FOOTER CETAK & TANDA TANGAN --}}
    <table class="footer-table">
        <tr>
            <td width="60%" style="vertical-align: bottom; font-size: 9px; color: #6c757d;">
                Dicetak pada: {{ now()->format('d-m-Y H:i') }} WIB<br>
                Petugas: {{ auth()->user()->nama ?? auth()->user()->name ?? '-' }}
            </td>
            <td width="40%" class="text-center">
                <div class="signature-title">
                    Petugas Penanggung Jawab,
                </div>
                <div class="fw-bold" style="text-decoration: underline;">
                    ( {{ auth()->user()->nama ?? auth()->user()->name ?? '....................................' }} )
                </div>
            </td>
        </tr>
    </table>

</body>
</html>