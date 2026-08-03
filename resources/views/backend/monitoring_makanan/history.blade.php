@extends('backend.app')
@section('title', 'Riwayat Monitoring Makanan')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Riwayat Monitoring Makanan</h4>
                    <a href="{{ route('monitoring_makanan.index') }}"
                        class="btn text-white shadow-sm px-4 py-2"style="background:linear-gradient(to right,#667eea,#764ba2);border:none;">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: {!! json_encode(session('success')) !!},
                                    icon: 'success'
                                });
                            });
                        </script>
                    @endif
                    {{-- Biodata Peserta --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">No RM</th>
                                    <td>{{ $peserta->no_rm }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Peserta</th>
                                    <td>{{ $peserta->nama }}</td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>{{ $peserta->nik }}</td>
                                </tr>
                                <tr>
                                    <th>No BPJS</th>
                                    <td>{{ $peserta->no_bpjs }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>{{ $peserta->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Jenis Penyakit</th>
                                    <td>{{ $peserta->jenisPenyakit->nama_penyakit ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dokter</th>
                                    <td>{{ $peserta->dokter->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No HP</th>
                                    <td>{{ $peserta->no_hp }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $peserta->alamat }}</td>
                                </tr>
                                <tr>
                                    <th>Total Monitoring</th>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $riwayat->count() }} Kali
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Evaluasi Monitoring Makanan
                            </h5>
                        </div>

                        <div class="card-body">

                            <form action="{{ route('monitoring_makanan.evaluasi', $peserta->id) }}" method="GET">

                                <div class="row">

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">
                                            Jumlah Monitoring
                                        </label>

                                        <input type="number" class="form-control" name="jumlah" value="3"
                                            min="1" required>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-end">

                                        <div class="form-check">

                                            <input class="form-check-input" type="checkbox" id="gunakanTanggal">

                                            <label class="form-check-label" for="gunakanTanggal">

                                                Gunakan Rentang Tanggal

                                            </label>

                                        </div>

                                    </div>

                                    <div class="col-md-2">
                                        <label>Dari</label>

                                        <input type="date" class="form-control tanggal" name="dari" disabled>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Sampai</label>

                                        <input type="date" class="form-control tanggal" name="sampai" disabled>
                                    </div>

                                    <div class="col-md-2 d-flex align-items-end">

                                        <button class="btn btn-success w-100">

                                            <i class="fas fa-chart-bar me-1"></i>

                                            Analisis

                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fa fa-history text-primary me-2"></i>Riwayat Monitoring
                        </h5>
                        <a href="{{ route('monitoring_makanan.create', ['peserta_id' => $peserta->id]) }}"
                            class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                            Input Monitoring Baru
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah Menu</th>
                                    <th>Total Kalori</th>
                                    <th>Petugas</th>
                                    <th width="170">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $item->detail->count() }} Menu
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ $item->total_kalori }} Kkal
                                            </span>
                                        </td>
                                        <td>
                                            {{ $item->petugas->nama ?? '-' }}
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-link p-0 text-primary" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="fa fa-eye" style="font-size:18px;"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a
                                                            class="dropdown-item"href="{{ route('monitoring_makanan.show', $item->id) }}">
                                                            <i class="fa fa-search me-2 text-primary"></i>Detail Monitoring
                                                        </a>
                                                    </li>
                                                    @if (!auth()->user()->hasRole('Peserta'))
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('monitoring_makanan.edit', $item->id) }}">
                                                                <i class="fa fa-pencil-alt me-2 text-info"></i>
                                                                Edit Monitoring
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('monitoring_makanan.export.pdf', $item->id) }}">
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                                Export PDF
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('monitoring_makanan.export.excel', $item->id) }}">
                                                                <i class="fas fa-file-excel text-success me-2"></i>
                                                                Export Excel
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <button type="button" class="dropdown-item btn-delete"
                                                                data-id="{{ $item->id }}">
                                                                <i class="fa fa-trash me-2 text-danger"></i>
                                                                Hapus Monitoring
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fa fa-folder-open fa-2x mb-2"></i><br>Belum ada riwayat monitoring.
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
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.btn-delete').click(function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data monitoring akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('monitoring_makanan.destroy', ':id') }}"
                                .replace(':id', id),
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: "DELETE"
                            },
                            success: function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data monitoring berhasil dihapus.'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Data tidak dapat dihapus.'
                                });
                            }
                        });
                    }
                });
            });
        });

        $('#gunakanTanggal').change(function () {

    if ($(this).is(':checked')) {

        $('.tanggal').prop('disabled', false);

    } else {

        $('.tanggal').prop('disabled', true);

        $('.tanggal').val('');

    }

});
    </script>
@endsection
