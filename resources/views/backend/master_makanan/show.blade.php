@extends('backend.app')

@section('title', 'Detail Master Makanan')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-10 mx-auto">

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Detail Master Makanan</h4>

                        <a href="{{ route('master_makanan.index') }}"
                            class="btn text-white shadow-sm px-4 py-2"
                            style="background: linear-gradient(to right, #667eea, #764ba2); border:none; font-weight:500;">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-8">

                                <table class="table table-bordered">

                                    <tbody>

                                        <tr>
                                            <th width="35%">Kode Makanan</th>
                                            <td>{{ $makanan->kode ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Nama Makanan</th>
                                            <td>{{ $makanan->nama }}</td>
                                        </tr>

                                        <tr>
                                            <th>Kategori</th>
                                            <td>{{ $makanan->kategori }}</td>
                                        </tr>

                                        <tr>
                                            <th>Satuan</th>
                                            <td>{{ $makanan->satuan }}</td>
                                        </tr>

                                        <tr>
                                            <th>Berat / Satuan</th>
                                            <td>{{ $makanan->gram }} Gram</td>
                                        </tr>

                                        <tr>
                                            <th>Kalori / Satuan</th>
                                            <td>{{ $makanan->kalori }} Kkal</td>
                                        </tr>

                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($makanan->aktif)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $makanan->keterangan ?? '-' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Dibuat</th>
                                            <td>{{ $makanan->created_at->format('d-m-Y H:i') }}</td>
                                        </tr>

                                        <tr>
                                            <th>Terakhir Diubah</th>
                                            <td>{{ $makanan->updated_at->format('d-m-Y H:i') }}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                        <div class="d-flex justify-content-start mt-3">

                            <a href="{{ route('master_makanan.edit', $makanan->id) }}"
                                class="btn shadow-sm px-4 py-2 text-white"
                                style="background: linear-gradient(to right, #36d1dc, #5b86e5); border:none; font-weight:500;">
                                <i class="fas fa-edit me-2"></i>
                                Edit Master Makanan
                            </a>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection