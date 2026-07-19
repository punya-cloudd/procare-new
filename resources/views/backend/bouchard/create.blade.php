@extends('backend.app')
@section('title', 'Tambah Kuisioner Bouchard')

@section('content')
    <div class="container">
        <div class="page-inner">
            <form action="{{ route('bouchard.store') }}" method="POST">
                @csrf
                <div class="card shadow">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title"> Input Kuisioner Latihan Fisik Bouchard </h4>
                            <a href="{{ route('bouchard.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i>Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            {{-- Peserta --}}
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
                            {{-- Petugas --}}
                            <div class="col-md-6 mb-3">
                                <label>Petugas <span class="text-danger">*</span></label>
                                <select name="petugas_id" class="form-select" required>
                                    <option value="">-- Pilih Petugas --</option>
                                    @foreach ($petugas as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('petugas_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Tanggal --}}
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Monitoring <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>
                            {{-- Berat Badan --}}
                            <div class="col-md-6 mb-3">
                                <label>Berat Badan (Kg) <span class="text-danger">*</span></label>
                                <input type="number" name="berat_badan" class="form-control" step="0.01" min="1"
                                    value="{{ old('berat_badan') }}" required>
                            </div>
                        </div>
                        <hr>
                        <div class="alert alert-info">
                            <strong>Petunjuk Pengisian</strong>
                            <ul class="mb-0 mt-2">
                                <li>Monitoring dilakukan selama <strong>1 hari (24 jam)</strong>.</li>
                                <li>Setiap baris mewakili <strong>1 jam</strong>.</li>
                                <li>Setiap kolom mewakili <strong>15 menit</strong>.</li>
                                <li>Pilih aktivitas sesuai metode <strong>Bouchard</strong>.</li>
                                <li>Nilai energi akan muncul otomatis setelah aktivitas dipilih.</li>
                            </ul>
                        </div>
                        <h4 class="mb-3">Monitoring Aktivitas Harian</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle">
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
                                        $aktivitas = [
                                            1 => ['nama' => 'Tidur / Berbaring', 'energi' => '0.26'],
                                            2 => ['nama' => 'Duduk', 'energi' => '0.30'],
                                            3 => ['nama' => 'Berdiri (Aktivitas Ringan)', 'energi' => '0.38'],
                                            4 => ['nama' => 'Berjalan / Berpakaian', 'energi' => '0.57'],
                                            5 => ['nama' => 'Pekerjaan Manual Ringan', 'energi' => '0.83'],
                                            6 => ['nama' => 'Olahraga Ringan', 'energi' => '1.00'],
                                            7 => ['nama' => 'Pekerjaan Manual Sedang', 'energi' => '1.20'],
                                            8 => ['nama' => 'Olahraga Sedang', 'energi' => '1.40'],
                                            9 => ['nama' => 'Olahraga Berat', 'energi' => '1.95'],
                                        ];
                                    @endphp
                                    @for ($jam = 0; $jam <= 23; $jam++)
                                        <tr>
                                            <td class="text-center fw-bold">
                                                {{ sprintf('%02d', $jam) }}:00
                                                <input type="hidden" name="jam[]" value="{{ $jam }}">
                                            </td>
                                            @foreach (['m00', 'm15', 'm30', 'm45'] as $field)
                                                <td width="260">
                                                    <select class="form-select aktivitas-select"
                                                        data-hidden="{{ $field }}_{{ $jam }}"
                                                        data-energi="{{ $field }}_energi_{{ $jam }}"
                                                        required>
                                                        <option value="">-- Pilih Aktivitas --</option>
                                                        @foreach ($aktivitas as $kategori => $item)
                                                            <option value="{{ $kategori }}"
                                                                data-energi="{{ $item['energi'] }}">
                                                                {{ $item['nama'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" id="{{ $field }}_{{ $jam }}"
                                                        name="{{ $field }}[]">
                                                    <small id="{{ $field }}_energi_{{ $jam }}"
                                                        class="text-primary fw-bold d-block mt-1">-</small>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <strong>Ringkasan Monitoring</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Berat Badan</label>
                                                    <div class="form-control bg-light">
                                                        <span id="preview_berat">-</span>
                                                        Kg
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Estimasi Total Energi</label>
                                                    <div class="form-control bg-light">
                                                        <span id="total_energi">0.00</span>
                                                        kcal/kg
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Estimasi Total Kalori</label>
                                                    <div class="form-control bg-light">
                                                        <span id="total_kalori">0.00</span>
                                                        Kkal
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4 g-2">

                            <div class="col-12 col-md-4">
                                <a href="{{ route('bouchard.index') }}" class="btn text-white shadow-sm w-100"
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
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {
            function hitungTotal() {
                let totalEnergi = 0;
                $('.aktivitas-select').each(function() {
                    let energi = parseFloat($(this).find(':selected').data('energi'));
                    if (!isNaN(energi)) {
                        totalEnergi += energi;
                    }
                });
                $('#total_energi').text(totalEnergi.toFixed(2));
                let bb = parseFloat($('input[name="berat_badan"]').val());
                if (isNaN(bb)) {
                    bb = 0;
                }
                $('#preview_berat').text(bb.toFixed(2));
                let totalKalori = totalEnergi * bb;
                $('#total_kalori').text(totalKalori.toFixed(2));
            }
            $('.aktivitas-select').on('change', function() {
                let kategori = $(this).val();
                let energi = $(this).find(':selected').data('energi');
                let hidden = $(this).data('hidden');
                let label = $(this).data('energi');
                $('#' + hidden).val(kategori);
                if (kategori == '') {
                    $('#' + label).html('-');
                } else {
                    $('#' + label).html(
                        energi + ' kcal/kg/15 menit'
                    );
                }
                hitungTotal();
            });
            $('input[name="berat_badan"]').on('keyup change', function() {
                hitungTotal();
            });
            /*
            |--------------------------------------------------------------------------
            | Default Jam 00:00 - 00:15 = Tidur
            |--------------------------------------------------------------------------
            */
            let awal = $('#m00_0');
            if (awal.length) {
                let select = awal.prev('.aktivitas-select');
                select.val(1).trigger('change');
            }
            hitungTotal();
        });
    </script>

@endsection
