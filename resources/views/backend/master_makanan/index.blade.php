@extends('backend.app')

@section('title', 'Data Master Makanan')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Data Master Makanan</h4>

                            <a href="{{ route('master_makanan.create') }}"
                                class="btn btn-primary btn-sm ms-auto">
                                <i class="fa fa-plus"></i>
                                Tambah Master Makanan
                            </a>

                        </div>
                    </div>

                    <div class="card-body">

                        @if(session('success'))
                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: {!! json_encode(session('success')) !!},
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            </script>
                        @endif

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped table-hover align-middle"
                                id="masterMakananTable">

                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Makanan</th>
                                        <th>Kategori</th>
                                        <th>Satuan</th>
                                        <th>Gram</th>
                                        <th>Kalori</th>
                                        <th>Status</th>
                                        <th width="80" class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script>
$(document).ready(function () {

    var table = $('#masterMakananTable').DataTable({

        processing: true,
        serverSide: true,
        responsive: true,

        ajax: "{{ route('master_makanan.index') }}",

        columns: [

            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable:false,
                searchable:false
            },

            {
                data:'kode',
                name:'kode'
            },

            {
                data:'nama',
                name:'nama'
            },

            {
                data:'kategori',
                name:'kategori'
            },

            {
                data:'satuan',
                name:'satuan'
            },

            {
                data:'gram',
                name:'gram'
            },

            {
                data:'kalori',
                name:'kalori'
            },

            {
                data:'status',
                name:'status',
                orderable:false,
                searchable:false
            },

            {
                data:'action',
                name:'action',
                orderable:false,
                searchable:false,
                className:'text-center'
            }

        ],

        pageLength:5,

        lengthMenu:[
            [5,10,25,50,100],
            [5,10,25,50,100]
        ],

        language:{
            searchPlaceholder:"Cari makanan...",
            search:"",
            lengthMenu:"Tampilkan _MENU_ data",
            info:"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty:"Tidak ada data ditemukan",
            zeroRecords:"Data tidak ditemukan",
            emptyTable:"Belum ada data",
            paginate:{
                first:"Pertama",
                last:"Terakhir",
                next:"Berikutnya",
                previous:"Sebelumnya"
            }
        }

    });

    $('#masterMakananTable').on('click','.btn-delete',function(){

        var id=$(this).data('id');

        Swal.fire({

            title:'Yakin mau hapus?',
            text:'Data yang dihapus tidak bisa dikembalikan!',
            icon:'warning',

            showCancelButton:true,

            confirmButtonText:'Ya, Hapus!',
            cancelButtonText:'Batal',

            confirmButtonColor:'#d33',
            cancelButtonColor:'#3085d6'

        }).then((result)=>{

            if(result.isConfirmed){

                $.ajax({

                    url:"{{ route('master_makanan.destroy',':id') }}".replace(':id',id),

                    type:'POST',

                    data:{
                        _method:'DELETE',
                        _token:'{{ csrf_token() }}'
                    },

                    success:function(){

                        Swal.fire(
                            'Berhasil!',
                            'Data berhasil dihapus.',
                            'success'
                        );

                        table.ajax.reload();

                    },

                    error:function(){

                        Swal.fire(
                            'Gagal!',
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