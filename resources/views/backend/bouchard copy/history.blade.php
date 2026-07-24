@extends('backend.app')
@section('title', 'Riwayat Kuisioner Bouchard')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Riwayat Kuisioner Latihan Fisik Bouchard
                    </h4>
                    <a href="{{ route('bouchard.index') }}"
                        class="btn text-white shadow-sm px-4 py-2"style="background:linear-gradient(to right,#667eea,#764ba2);border:none;">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali
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
                                    <td>
                                        {{ $peserta->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">

                                <tr>

                                    <th width="35%">Jenis Penyakit</th>

                                    <td>

                                        {{ $peserta->jenisPenyakit->nama_penyakit ?? '-' }}

                                    </td>

                                </tr>

                                <tr>

                                    <th>Dokter</th>

                                    <td>

                                        {{ $peserta->dokter->nama ?? '-' }}

                                    </td>

                                </tr>

                                <tr>

                                    <th>No HP</th>

                                    <td>

                                        {{ $peserta->no_hp }}

                                    </td>

                                </tr>

                                <tr>

                                    <th>Alamat</th>

                                    <td>

                                        {{ $peserta->alamat }}

                                    </td>

                                </tr>

                                <tr>

                                    <th>Total Kuisioner</th>

                                    <td>

                                        <span class="badge bg-primary">

                                            {{ $riwayat->count() }} Kali

                                        </span>

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h5 class="mb-0">

                            <i class="fa fa-history text-primary me-2"></i>

                            Riwayat Kuisioner Bouchard

                        </h5>

                        <a href="{{ route('bouchard.create', ['peserta_id' => $peserta->id]) }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                            Input Kuisioner Baru
                        </a>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th>No</th>

                                    <th>Tanggal</th>

                                    <th>Berat Badan</th>

                                    {{-- <th>Rata-rata Aktivitas</th>

                                    <th>Total Energi</th> --}}

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

                                        <td class="text-center">

                                            <span class="badge bg-info">

                                                {{ $item->berat_badan }} Kg

                                            </span>

                                        </td>

                                        {{-- <td class="text-center">

                                            <span class="badge bg-warning text-dark">

                                                {{ number_format($item->rata_aktivitas, 2) }}

                                            </span>

                                        </td>

                                        <td class="text-center">

                                            <span class="badge bg-success">

                                                {{ number_format($item->total_energi, 0) }} Kkal

                                            </span>

                                        </td> --}}

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
                                                        <a class="dropdown-item"
                                                            href="{{ route('bouchard.show', $item->id) }}">
                                                            <i class="fa fa-search me-2 text-primary"></i>
                                                            Detail Kuisioner
                                                        </a>
                                                    </li>

                                                    @if (!auth()->user()->hasRole('Peserta'))
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('bouchard.edit', $item->id) }}">
                                                                <i class="fa fa-pencil-alt me-2 text-info"></i>
                                                                Edit Kuisioner
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('bouchard.export.pdf', $item->id) }}">
                                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                                Export PDF
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('bouchard.export.excel', $item->id) }}">
                                                                <i class="fas fa-file-excel text-success me-2"></i>
                                                                Export Excel
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <button type="button" class="dropdown-item btn-delete"
                                                                data-id="{{ $item->id }}">
                                                                <i class="fa fa-trash me-2 text-danger"></i>
                                                                Hapus Kuisioner
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center text-muted py-4">

                                            <i class="fa fa-folder-open fa-2x mb-2"></i>

                                            <br>

                                            Belum ada riwayat Kuisioner Bouchard.

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
        $(function() {

            $('.btn-delete').click(function() {

                let id = $(this).data('id');

                Swal.fire({

                    title: 'Yakin?',

                    text: 'Data Kuisioner Bouchard akan dihapus!',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#d33',

                    cancelButtonColor: '#3085d6',

                    confirmButtonText: 'Ya, Hapus',

                    cancelButtonText: 'Batal'

                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({

                            url: "{{ route('bouchard.destroy', ':id') }}"
                                .replace(':id', id),

                            type: "POST",

                            data: {

                                _method: "DELETE",

                                _token: "{{ csrf_token() }}"

                            },

                            success: function(response) {

                                Swal.fire({

                                    title: 'Berhasil!',

                                    text: 'Data berhasil dihapus.',

                                    icon: 'success'

                                }).then(() => {

                                    location.reload();

                                });

                            },

                            error: function() {

                                Swal.fire(

                                    'Gagal',

                                    'Terjadi kesalahan.',

                                    'error'

                                );

                            }

                        });

                    }

                });

            });

        });
    </script>

@endsection
