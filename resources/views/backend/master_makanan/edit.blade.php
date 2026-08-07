@extends('backend.app')

@section('title', 'Edit Master Makanan')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-10 mx-auto">

                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">Edit Master Makanan</h4>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('master_makanan.update', $makanan->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                {{-- Kode --}}
                                <div class="col-md-6 mb-3">
                                    <label>Kode Makanan</label>
                                    <input type="text"
                                           name="kode"
                                           class="form-control"
                                           value="{{ old('kode', $makanan->kode) }}">
                                </div>

                                {{-- Nama --}}
                                <div class="col-md-6 mb-3">
                                    <label>Nama Makanan <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="nama"
                                           class="form-control"
                                           value="{{ old('nama', $makanan->nama) }}"
                                           required>
                                </div>

                                {{-- Kategori --}}
                                <div class="col-md-6 mb-3">
                                    <label>Kategori <span class="text-danger">*</span></label>

                                    <select name="kategori" class="form-select" required>

                                        @foreach([
                                            'Karbohidrat',
                                            'Protein Hewani',
                                            'Protein Nabati',
                                            'Sayuran',
                                            'Buah',
                                            'Minuman',
                                            'Snack',
                                            'Lainnya'
                                        ] as $kategori)

                                            <option value="{{ $kategori }}"
                                                {{ old('kategori', $makanan->kategori) == $kategori ? 'selected' : '' }}>
                                                {{ $kategori }}
                                            </option>

                                        @endforeach

                                    </select>
                                </div>

                                {{-- Satuan --}}
                                <div class="col-md-6 mb-3">
                                    <label>Satuan <span class="text-danger">*</span></label>

                                    <select name="satuan" class="form-select" required>

                                        @foreach([
                                            'Piring',
                                            'Mangkuk',
                                            'Potong',
                                            'Butir',
                                            'Gelas',
                                            'Cangkir',
                                            'Bungkus',
                                            'Buah',
                                            'Botol',
                                            'Gram'
                                        ] as $satuan)

                                            <option value="{{ $satuan }}"
                                                {{ old('satuan', $makanan->satuan) == $satuan ? 'selected' : '' }}>
                                                {{ $satuan }}
                                            </option>

                                        @endforeach

                                    </select>
                                </div>

                                {{-- Gram --}}
                                <div class="col-md-6 mb-3">
                                    <label>Berat / Satuan (Gram)</label>
                                    <input type="number"
                                           name="gram"
                                           class="form-control"
                                           step="0.01"
                                           value="{{ old('gram', $makanan->gram) }}"
                                           required>
                                </div>

                                {{-- Kalori --}}
                                <div class="col-md-6 mb-3">
                                    <label>Kalori / Satuan (Kkal)</label>
                                    <input type="number"
                                           name="kalori"
                                           class="form-control"
                                           step="0.01"
                                           value="{{ old('kalori', $makanan->kalori) }}"
                                           required>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6 mb-3">
                                    <label>Status</label>

                                    <select name="aktif" class="form-select">
                                        <option value="1"
                                            {{ old('aktif', $makanan->aktif) == 1 ? 'selected' : '' }}>
                                            Aktif
                                        </option>

                                        <option value="0"
                                            {{ old('aktif', $makanan->aktif) == 0 ? 'selected' : '' }}>
                                            Tidak Aktif
                                        </option>
                                    </select>
                                </div>

                                {{-- Keterangan --}}
                                <div class="col-md-12 mb-3">
                                    <label>Keterangan</label>

                                    <textarea name="keterangan"
                                              class="form-control"
                                              rows="4"
                                              placeholder="Keterangan (Opsional)">{{ old('keterangan', $makanan->keterangan) }}</textarea>
                                </div>

                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">

                                <a href="{{ route('master_makanan.index') }}"
                                    class="btn text-white shadow-sm px-4 py-2"
                                    style="background:linear-gradient(to right,#667eea,#764ba2);border:none;font-weight:500;">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Kembali
                                </a>

                                <div>

                                    <button type="reset"
                                        class="btn btn-light border shadow-sm px-4 py-2 me-2">
                                        <i class="fas fa-undo-alt me-2"></i>
                                        Reset
                                    </button>

                                    <button type="submit"
                                        class="btn text-white shadow-sm px-4 py-2"
                                        style="background:linear-gradient(to right,#36d1dc,#5b86e5);border:none;font-weight:500;">
                                        <i class="fas fa-save me-2"></i>
                                        Update
                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection