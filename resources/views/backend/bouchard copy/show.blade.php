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

                                {{-- <tr>

                                    <th>Total Kalori</th>

                                    <td>

                                        <span class="badge bg-success">

                                            {{ number_format($bouchard->total_kalori, 2) }} Kkal

                                        </span>

                                    </td>

                                </tr> --}}

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

    $totalEnergi = 0;

    foreach ($bouchard->detail as $detail) {

        foreach ([
            $detail->m00,
            $detail->m15,
            $detail->m30,
            $detail->m45,
        ] as $kategori) {

            switch ($kategori) {

                case 1: $totalEnergi += 0.26; break;
                case 2: $totalEnergi += 0.30; break;
                case 3: $totalEnergi += 0.38; break;
                case 4: $totalEnergi += 0.57; break;
                case 5: $totalEnergi += 0.83; break;
                case 6: $totalEnergi += 1.00; break;
                case 7: $totalEnergi += 1.20; break;
                case 8: $totalEnergi += 1.40; break;
                case 9: $totalEnergi += 1.95; break;

            }

        }

    }

    $totalKalori = $totalEnergi * $bouchard->berat_badan;

@endphp

<div class="row mt-4">

    <div class="col-md-6">

        <table class="table table-bordered">

            <tr>

                <th width="40%">Berat Badan</th>

                <td>

                    {{ number_format($bouchard->berat_badan, 2) }} Kg

                </td>

            </tr>

            <tr>

                <th>Rata-rata Aktivitas</th>

                <td>

                    <span class="badge bg-warning text-dark fs-6">

                        {{ number_format($totalEnergi,2) }}

                    </span>

                </td>

            </tr>

            <tr>

                <th>Total Energi</th>

                <td>

                    <span class="badge bg-success fs-6">

                        {{ number_format($totalKalori,2) }} Kkal

                    </span>

                </td>

            </tr>

            <tr>

                <th>Kategori Aktivitas</th>

                <td>

                    <span class="badge bg-primary fs-6">

                        {{ $bouchard->kategori ?? '-' }}

                    </span>

                </td>

            </tr>

        </table>

    </div>

    <div class="col-md-6">

        <table class="table table-bordered">

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
