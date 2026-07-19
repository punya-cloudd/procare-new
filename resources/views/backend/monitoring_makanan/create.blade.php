@extends('backend.app')
@section('title', 'Tambah Monitoring Makanan')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tambah Monitoring Makanan Harian</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('monitoring_makanan.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    @if (!empty($selectedPeserta))
                                        <div class="col-md-6 mb-3">
                                            <label>Peserta <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"
                                                value="{{ $peserta->firstWhere('id', $selectedPeserta)->nama }}" readonly>
                                            <input type="hidden" name="peserta_id" value="{{ $selectedPeserta }}">
                                        </div>
                                    @else
                                        <div class="col-md-6 mb-3">
                                            <label>Peserta <span class="text-danger">*</span></label>
                                            <select name="peserta_id" class="form-select" required>
                                                <option value="">-- Pilih Peserta --</option>
                                                @foreach ($peserta as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('peserta_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="col-md-6 mb-3">
                                        <label>Petugas <span class="text-danger">*</span></label>
                                        <select name="petugas_id" class="form-select" required>
                                            <option value="">-- Pilih Petugas --</option>
                                            @foreach ($petugas as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Total Kalori</label>
                                        <input type="number" id="totalKalori" class="form-control" value="0" readonly>
                                    </div>
                                    <div class="col-md-12">
                                        <label>Catatan</label>
                                        <textarea name="catatan" rows="3" class="form-control" placeholder="Catatan monitoring..."></textarea>
                                    </div>
                                </div>
                                <hr>
                                <h5>
                                    Detail Makanan
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="detailTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="15%">Waktu</th>
                                                <th>Nama Makanan</th>
                                                <th width="10%">Jumlah</th>
                                                <th width="10%">Satuan</th>
                                                <th width="15%">Kalori</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" id="btnTambah">
                                    <i class="fa fa-plus"></i>
                                    Tambah Makanan
                                </button>
                                <hr>
                                {{-- <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-save"></i>
                                    Simpan
                                </button>
                                <a href="{{ route('monitoring_makanan.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i>
                                    Kembali
                                </a> --}}
                                <div class="row mt-4 g-2 align-items-center">

                                    <div class="col-12 col-md-4">
                                        <a href="{{ route('monitoring_makanan.index') }}"
                                            class="btn text-white shadow-sm w-100"
                                            style="background:linear-gradient(to right,#667eea,#764ba2);border:none;font-weight:500;">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Kembali
                                        </a>
                                    </div>

                                    <div class="col-12 col-md-8">
                                        <div class="d-grid d-md-flex justify-content-md-end gap-2">

                                            <button type="reset" class="btn btn-light border shadow-sm">
                                                <i class="fas fa-undo-alt me-2"></i>
                                                Reset
                                            </button>

                                            <button type="submit" class="btn text-white shadow-sm"
                                                style="background:linear-gradient(to right,#36d1dc,#5b86e5);border:none;font-weight:500;">
                                                <i class="fas fa-save me-2"></i>
                                                Simpan
                                            </button>

                                        </div>
                                    </div>

                                </div>

                                {{-- <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mt-4">
                                    <a href="{{ route('monitoring_makanan.index') }}"
                                        class="btn text-white w-100 w-md-auto shadow-sm px-4 py-2"
                                        style="background:linear-gradient(to right,#667eea,#764ba2);border:none;font-weight:500;">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Kembali
                                    </a>
                                    <div class="d-flex flex-column flex-sm-row gap-2">
                                        <button type="reset" class="btn btn-light border shadow-sm px-4 py-2 w-100">
                                            <i class="fas fa-undo-alt me-2"></i>
                                            Reset
                                        </button>
                                        <button type="submit" class="btn text-white shadow-sm px-4 py-2  w-100"
                                            style="background:linear-gradient(to right,#36d1dc,#5b86e5);border:none;font-weight:500;">
                                            <i class="fas fa-save me-2"></i>
                                            Simpan
                                        </button>
                                    </div>
                                </div> --}}
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        $(document).ready(function() {

            tambahBaris();

            $('#btnTambah').click(function() {

                tambahBaris();

            });

            $(document).on('click', '.btnHapus', function() {

                $(this).closest('tr').remove();

                hitungKalori();

            });
            $(document).on('keyup change', '.kalori', function() {

                hitungKalori();

            });

        });


        function tambahBaris() {

            let html = `

    <tr>

        <td>

            <select
                name="waktu_makan[]"
                class="form-select"
                required>

                <option value="Makan Pagi">Makan Pagi</option>

                <option value="Snack Pagi">Snack Pagi</option>

                <option value="Makan Siang">Makan Siang</option>

                <option value="Snack Siang">Snack Siang</option>

                <option value="Makan Malam">Makan Malam</option>

                <option value="Snack Malam">Snack Malam</option>

            </select>

        </td>

        <td>
            <input type="text" name="nama_makanan[]" class="form-control" required>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control" value="1" min="0.25" step="0.25" required>
        </td>

        <td>

            <select name="satuan[]" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="mkk">Mangkuk</option>
                <option value="ckr">Cangkir</option>
                <option value="btr">Butir</option>
                <option value="ptg">Potong</option>
                <option value="prg">Piring</option>
                <option value="bks">Bungkus</option>
                <option value="gls">Gelas</option>
            </select>

        </td>

        <td>

            <input
                type="number"
                name="kalori[]"
                class="form-control kalori"
                value="0">

        </td>

        <td class="text-center">

            <button
                type="button"
                class="btn btn-danger btn-sm btnHapus">

                <i class="fa fa-trash"></i>

            </button>

        </td>

    </tr>

    `;

            $('#detailTable tbody').append(html);

        }


        function hitungKalori() {

            let total = 0;

            $('.kalori').each(function() {

                let nilai = parseInt($(this).val());

                if (!isNaN(nilai)) {

                    total += nilai;

                }

            });

            $('#totalKalori').val(total);

        }
    </script>

    <style>
        @media (max-width: 768px) {

            .card-header h4 {
                font-size: 18px;
            }

            .table td,
            .table th {
                white-space: nowrap;
                vertical-align: middle;
            }

            #btnTambah {
                width: 100%;
                margin-top: 10px;
            }

            .btn {
                margin-bottom: 8px;
            }

        }
    </style>

@endsection
