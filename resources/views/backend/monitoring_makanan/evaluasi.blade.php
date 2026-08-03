@extends('backend.app')
@section('title', 'Evaluasi Monitoring Makanan')

@section('content')
<div class="container">
    <div class="page-inner">

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fas fa-chart-line text-primary"></i>
                    Evaluasi Monitoring Makanan
                </h4>

                <a href="{{ route('monitoring_makanan.history',$peserta->id) }}"
                    class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <div class="card-body">

                {{-- ========================= --}}
                {{-- BIODATA --}}
                {{-- ========================= --}}

                <div class="row mb-4">

                    <div class="col-md-6">

                        <table class="table table-bordered">

                            <tr>
                                <th width="35%">Nama</th>
                                <td>{{ $peserta->nama }}</td>
                            </tr>

                            <tr>
                                <th>No RM</th>
                                <td>{{ $peserta->no_rm }}</td>
                            </tr>

                            <tr>
                                <th>NIK</th>
                                <td>{{ $peserta->nik }}</td>
                            </tr>

                            <tr>
                                <th>BPJS</th>
                                <td>{{ $peserta->no_bpjs }}</td>
                            </tr>

                        </table>

                    </div>

                    <div class="col-md-6">

                        <table class="table table-bordered">

                            <tr>
                                <th width="35%">Penyakit</th>
                                <td>{{ $peserta->jenisPenyakit->nama_penyakit ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Dokter</th>
                                <td>{{ $peserta->dokter->nama ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Jumlah Monitoring</th>
                                <td>{{ $ringkasan['jumlah_monitoring'] }}</td>
                            </tr>

                            <tr>
                                <th>Rata-rata Kalori</th>
                                <td>{{ number_format($ringkasan['rata_kalori']) }} Kkal</td>
                            </tr>

                        </table>

                    </div>

                </div>

                {{-- ========================= --}}
                {{-- RINGKASAN --}}
                {{-- ========================= --}}

                <div class="row text-center mb-4">

                    <div class="col-md-3">

                        <div class="card border-success">

                            <div class="card-body">

                                <h6>Total Kalori</h6>

                                <h3 class="text-success">
                                    {{ number_format($ringkasan['total_kalori']) }}
                                </h3>

                                <small>Kkal</small>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="card border-primary">

                            <div class="card-body">

                                <h6>Rata-rata</h6>

                                <h3 class="text-primary">
                                    {{ number_format($ringkasan['rata_kalori']) }}
                                </h3>

                                <small>Kkal</small>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="card border-warning">

                            <div class="card-body">

                                <h6>Tertinggi</h6>

                                <h3 class="text-warning">
                                    {{ number_format($ringkasan['kalori_tertinggi']) }}
                                </h3>

                                <small>Kkal</small>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="card border-danger">

                            <div class="card-body">

                                <h6>Terendah</h6>

                                <h3 class="text-danger">
                                    {{ number_format($ringkasan['kalori_terendah']) }}
                                </h3>

                                <small>Kkal</small>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ========================= --}}
                {{-- TABEL --}}
                {{-- ========================= --}}

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-primary">

                            <tr>

                                <th>No</th>

                                <th>Tanggal</th>

                                <th>Jumlah Menu</th>

                                <th>Total Kalori</th>

                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($monitoring as $item)

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>

                                <td>{{ $item->detail->count() }}</td>

                                <td>{{ number_format($item->total_kalori) }} Kkal</td>

                                <td>

                                    @if($item->total_kalori>2200)

                                        <span class="badge bg-danger">

                                            Berlebih

                                        </span>

                                    @elseif($item->total_kalori<1800)

                                        <span class="badge bg-warning text-dark">

                                            Kurang

                                        </span>

                                    @else

                                        <span class="badge bg-success">

                                            Normal

                                        </span>

                                    @endif

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- ========================= --}}
                {{-- BUTTON --}}
                {{-- ========================= --}}

                <div class="text-end mt-4">

                    <a href="#"
                        class="btn btn-danger">

                        <i class="fas fa-file-pdf"></i>

                        Export PDF

                    </a>

                    <a href="#"
                        class="btn btn-success">

                        <i class="fas fa-file-excel"></i>

                        Export Excel

                    </a>

                </div>

            </div>

        </div>

    </div>
</div>
@endsection