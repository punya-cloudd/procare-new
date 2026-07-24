<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kuisioner Bouchard</title>

    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        body {
            margin: 20px;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 0;
        }

        h4 {
            margin-top: 3px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: normal;
        }

        hr {
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info td,
        table.info th {
            border: 1px solid #000;
            padding: 6px;
        }

        table.aktivitas {
            margin-top: 10px;
        }

        table.aktivitas th,
        table.aktivitas td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        table.aktivitas th {
            background: #d9edf7;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .badge {
            border: 1px solid #000;
            padding: 2px 6px;
            font-weight: bold;
        }

        .section-title {
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        .w50 {
            width: 49%;
        }

        .left {
            float: left;
        }

        .right {
            float: right;
        }

        .clearfix {
            clear: both;
        }
    </style>

</head>

<body>

    <h2>KUISIONER LATIHAN FISIK MENURUT BOUCHARD</h2>

    <h4>Laporan Hasil Monitoring Aktivitas Harian</h4>

    <div class="w50 left">

        <table class="info">

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

    <div class="w50 right">

        <table class="info">

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
                    {{ number_format($bouchard->berat_badan, 2) }} Kg
                </td>
            </tr>

            <tr>
                <th>Total Kalori</th>
                <td>
                    {{ number_format($bouchard->total_kalori, 2) }} Kkal
                </td>
            </tr>

            <tr>
                <th>Kategori</th>
                <td>
                    {{ $bouchard->kategori }}
                </td>
            </tr>

        </table>

    </div>

    <div class="clearfix"></div>

    <hr>

    <div class="section-title">
        Monitoring Aktivitas Harian
    </div>

    <table class="aktivitas">

        <thead>

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

                            <div>
                                {{ number_format($row->energi($row->m00), 2) }}
                                kcal/kg/15 menit
                            </div>
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

                            <div>
                                {{ number_format($row->energi($row->m15), 2) }}
                                kcal/kg/15 menit
                            </div>
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

                            <div>
                                {{ number_format($row->energi($row->m30), 2) }}
                                kcal/kg/15 menit
                            </div>
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

                            <div>
                                {{ number_format($row->energi($row->m45), 2) }}
                                kcal/kg/15 menit
                            </div>
                        @else
                            -
                        @endif

                    </td>

                </tr>
            @endfor

        </tbody>

    </table>

    <br>
    <div style="width:48%; float:left;">

        <table class="info">

            <tr>
                <th width="40%">Berat Badan</th>
                <td>
                    {{ number_format($bouchard->berat_badan, 2) }} Kg
                </td>
            </tr>

            <tr>
                <th>Total Kalori</th>
                <td>
                    {{ number_format($bouchard->total_kalori, 2) }} Kkal
                </td>
            </tr>

            <tr>
                <th>Kategori Aktivitas</th>
                <td>
                    {{ $bouchard->kategori ?? '-' }}
                </td>
            </tr>

        </table>

    </div>

    <div style="width:48%; float:right;">

        <table class="info">

            <tr>
                <th width="35%">Catatan</th>
                <td>
                    {{ $bouchard->catatan ?: '-' }}
                </td>
            </tr>

            <tr>
                <th>Dibuat</th>
                <td>
                    {{ $bouchard->created_at?->format('d-m-Y H:i') }}
                </td>
            </tr>

            <tr>
                <th>Terakhir Diubah</th>
                <td>
                    {{ $bouchard->updated_at?->format('d-m-Y H:i') }}
                </td>
            </tr>

        </table>

    </div>

    <div class="clearfix"></div>

    <br><br><br>

    <table style="width:100%; border:none;">
        <tr>

            <td style="width:60%; border:none;"></td>

            <td style="border:none; text-align:center;">

                <p>
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>

                <br><br><br><br>

                <strong>
                    {{ $bouchard->petugas->nama ?? '-' }}
                </strong>

                <br>

                <small>Petugas</small>

            </td>

        </tr>
    </table>

</body>

</html>
