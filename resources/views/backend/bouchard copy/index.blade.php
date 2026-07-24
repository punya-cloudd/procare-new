@extends('backend.app')
@section('title', 'Kuisioner Latihan Fisik Bouchard')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Kuisioner Latihan Fisik Bouchard</h4>
                        <a href="{{ route('bouchard.create') }}"
   class="btn btn-primary shadow-sm px-4 ms-auto">
    <i class="fas fa-plus-circle me-2"></i>
    Tambah Data Kuisioner
</a>
                    </div>
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Peserta</th>
                                    <th>Jumlah Kuisioner</th>
                                    <th>Aktivitas Terakhir</th>
                                    <th>Petugas</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
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
            let table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('bouchard.index') }}"
                },
                order: [
                    [1, 'asc']
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
                    { data: 'nama', name: 'peserta.nama'},
                    { data: 'jumlah', name: 'jumlah', searchable: false, orderable: false, className: 'text-center', render: function(data) { return `<span class="badge bg-info">${data} Kali</span>`;}},
                    { data: 'aktivitas', name: 'aktivitas', searchable: false, orderable: false, className: 'text-center'},
                    { data: 'petugas', name: 'petugas.nama'},
                    { data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center'}
                ]
            });
            // HAPUS
            $('#table').on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data kuisioner akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('bouchard.destroy', ':id') }}"
                                .replace(':id', id),
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function() {
                                Swal.fire(
                                    'Berhasil',
                                    'Data berhasil dihapus.',
                                    'success'
                                );
                                table.ajax.reload();
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
