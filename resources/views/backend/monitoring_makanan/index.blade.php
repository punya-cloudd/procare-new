@extends('backend.app')
@section('title', 'Monitoring Makanan Harian')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="card-title mb-0 fw-bold text-primary">
                        <i class="fa fa-utensils me-2"></i>Monitoring Makanan Harian
                    </h4>
                    <a href="{{ route('monitoring_makanan.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus me-1"></i> Tambah Monitoring
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="table table-striped table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Peserta</th>
                                <th class="text-center">Petugas</th>
                                <th class="text-center">Monitoring Terakhir</th>
                                <th class="text-center">Asupan Hari Ini</th>
                                <th class="text-center">Status</th>
                                <th width="100" class="text-center">Aksi</th>
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
    {{-- SweetAlert Flash Message Handling --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    <script>
        $(function() {
            let table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('monitoring_makanan.index') }}"
                },
                order: [[3, 'desc']], // Default sorting berdasarkan 'Monitoring Terakhir' terbaru
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama',
                        name: 'peserta.nama'
                    },
                    {
                        data: 'petugas',
                        name: 'petugas',
                        className: 'text-center'
                    },
                    {
                        data: 'terakhir',
                        name: 'updated_at',
                        className: 'text-center'
                    },
                    {
                        data: 'kalori',
                        name: 'kalori',
                        className: 'text-center',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false,
                        className: 'text-center'
                    }
                ]
            });

            // PROCESS HAPUS DATA
            $('#table').on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data monitoring ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-trash me-1"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('monitoring_makanan.destroy', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Data berhasil dihapus.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false); // Keep pagination status
                            },
                            error: function(xhr) {
                                let errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem saat menghapus data.';
                                Swal.fire(
                                    'Gagal!',
                                    errorMsg,
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