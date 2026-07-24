@extends('backend.app')
@section('title', 'Edit Kuisioner Bouchard')

@section('content')
    <div class="container">
        <div class="page-inner">
            <form action="{{ route('bouchard.update', $bouchard->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card shadow">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Edit Kuisioner Latihan Fisik Bouchard</h4>
                            <a href="{{ route('bouchard.history', $bouchard->peserta_id) }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i>Kembali
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

                        {{-- Section Data Utama --}}
                        <div class="row">
                            {{-- Peserta (Read-only) --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Peserta</label>
                                <input type="text" class="form-control"
                                    value="{{ $bouchard->peserta->nama }} - {{ $bouchard->peserta->no_bpjs }}" readonly>
                                <input type="hidden" name="peserta_id" value="{{ $bouchard->peserta_id }}">
                            </div>

                            {{-- Petugas --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Petugas</label>
                                <select name="petugas_id" class="form-select">
                                    <option value="">-- Pilih Petugas --</option>
                                    @foreach ($petugas as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('petugas_id', $bouchard->petugas_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tanggal --}}
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ old('tanggal', optional($bouchard->tanggal)->format('Y-m-d')) }}" required>
                            </div>

                            {{-- Berat Badan --}}
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Berat Badan (Kg) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="1" name="berat_badan" class="form-control"
                                    value="{{ old('berat_badan', $bouchard->berat_badan) }}" required>
                            </div>
                        </div>

                        <hr>

                        {{-- Petunjuk Pengisian --}}
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

                        @php
                            $detail = $bouchard->detail->keyBy('jam');
                        @endphp

                        {{-- Card Auto Fill Grid --}}
                        <div class="card border-info shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <strong>
                                    <i class="fas fa-magic me-2"></i>Isi Grid Otomatis
                                </strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Aktivitas</label>
                                        <select class="form-select" id="auto_kategori">
                                            @foreach ($aktivitas as $kategori => $item)
                                                <optgroup label="{{ $item['label'] }}">
                                                    @foreach ($item['items'] as $index => $nama)
                                                        <option value="{{ $kategori }}-{{ $index }}">
                                                            {{ $nama }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Jam Awal</label>
                                        <select class="form-select" id="auto_jam">
                                            @for ($i = 0; $i <= 23; $i++)
                                                <option value="{{ $i }}">
                                                    {{ sprintf('%02d', $i) }}:00
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Menit Awal</label>
                                        <select class="form-select" id="auto_menit">
                                            <option value="0">00</option>
                                            <option value="15">15</option>
                                            <option value="30">30</option>
                                            <option value="45">45</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Durasi</label>
                                        <select class="form-select" id="auto_durasi">
                                            <option value="15">15 Menit</option>
                                            <option value="30">30 Menit</option>
                                            <option value="45">45 Menit</option>
                                            <option value="60">60 Menit</option>
                                            <option value="90">90 Menit</option>
                                            <option value="120">120 Menit</option>
                                            <option value="180">180 Menit</option>
                                            <option value="240">240 Menit</option>
                                            <option value="300">300 Menit</option>
                                            <option value="360">360 Menit</option>
                                            <option value="480">480 Menit</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-success w-100" id="btnAutoIsi">
                                            <i class="fas fa-bolt me-1"></i>Isikan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Grid Aktivitas Harian --}}
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
                                    @for ($jam = 0; $jam <= 23; $jam++)
                                        @php
                                            $row = $detail->get($jam);
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold">
                                                {{ sprintf('%02d', $jam) }}:00
                                                <input type="hidden" name="jam[]" value="{{ $jam }}">
                                            </td>
                                            @foreach (['m00', 'm15', 'm30', 'm45'] as $field)
                                                @php
                                                    $dbVal = $row ? $row->$field : '';
                                                    $value = old($field . '.' . $jam, $dbVal);
                                                @endphp
                                                <td width="260">
                                                    <select class="form-select aktivitas-select"
                                                        data-hidden="{{ $field }}_{{ $jam }}"
                                                        data-energi="{{ $field }}_energi_{{ $jam }}">
                                                        <option value="">-- Pilih Aktivitas --</option>
                                                        @foreach ($aktivitas as $kategori => $item)
                                                            <optgroup label="{{ $item['label'] }}">
                                                                @foreach ($item['items'] as $index => $nama)
                                                                    @php
                                                                        $optValue = $kategori . '-' . $index;
                                                                        $isSelected = false;
                                                                        if ($value != '') {
                                                                            if ($value == $optValue) {
                                                                                $isSelected = true;
                                                                            } elseif (
                                                                                $value == $kategori &&
                                                                                $index == 0
                                                                            ) {
                                                                                $isSelected = true;
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <option value="{{ $optValue }}"
                                                                        data-kategori="{{ $kategori }}"
                                                                        data-energi="{{ $item['energi'] }}"
                                                                        {{ $isSelected ? 'selected' : '' }}>
                                                                        {{ $nama }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" id="{{ $field }}_{{ $jam }}"
                                                        name="{{ $field }}[]" value="{{ $value }}">
                                                    <small id="{{ $field }}_energi_{{ $jam }}"
                                                        class="text-primary fw-bold d-block mt-1">
                                                        -
                                                    </small>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        {{-- ================= RINGKASAN BOUCHARD ================= --}}
                        <div class="card shadow-sm border-0 mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line text-primary me-2"></i>
                                    Ringkasan Monitoring Bouchard
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg col-md-6">
                                        <div
                                            class="summary-card border-start border-5 border-primary p-2 bg-light rounded">
                                            <small class="text-muted d-block">Berat Badan</small>
                                            <h3 id="preview_berat" class="mb-0 fw-bold">0.00</h3>
                                            <span class="text-muted">Kg</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div
                                            class="summary-card border-start border-5 border-success p-2 bg-light rounded">
                                            <small class="text-muted d-block">Total Energi</small>
                                            <h3 id="total_energi" class="mb-0 fw-bold">0.00</h3>
                                            <span class="text-muted">kcal/kg</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div
                                            class="summary-card border-start border-5 border-warning p-2 bg-light rounded">
                                            <small class="text-muted d-block">Total Kalori</small>
                                            <h3 id="total_kalori" class="mb-0 fw-bold">0.00</h3>
                                            <span class="text-muted">Kkal</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div class="summary-card border-start border-5 border-info p-2 bg-light rounded">
                                            <small class="text-muted d-block">Rata-rata MET</small>
                                            <h3 id="nilai_met" class="mb-0 fw-bold">-</h3>
                                            <span class="text-muted">MET/hari</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div class="summary-card border-start border-5 border-danger p-2 bg-light rounded">
                                            <small class="text-muted d-block">PAL</small>
                                            <h3 id="nilai_pal" class="mb-0 fw-bold">-</h3>
                                            <span class="text-muted">EE / BMR</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div
                                            class="summary-card border-start border-5 border-secondary p-2 bg-light rounded">

                                            <small class="text-muted d-block">
                                                Kategori Aktivitas
                                            </small>

                                            <div class="kategori-wrapper">

                                                <div id="badge_pal">

                                                    <span class="activity-dot bg-secondary"></span>

                                                    <span class="kategori-text">
                                                        -
                                                    </span>

                                                </div>

                                            </div>

                                            <div class="text-center mt-3">
                                                <span class="text-muted">
                                                    Bouchard / PAL
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="progress" style="height:28px">
                                    <div class="progress-bar bg-success fw-bold" id="progress_isian" role="progressbar"
                                        style="width:0%">
                                        0%
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    Kelengkapan pengisian kuisioner (96 slot aktivitas)
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 d-none" id="alertBelumLengkap">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong>
                            Kuisioner belum terisi lengkap.
                            <br>
                            Slot terisi :
                            <strong>
                                <span id="slotTerisi">0</span>/96
                            </strong>
                            <br>
                            Data tetap dapat disimpan, namun hasil perhitungan PAL dan estimasi energi mungkin belum akurat.
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan tambahan (opsional)">{{ old('catatan', $bouchard->catatan) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 g-2">
                            <div class="col-12 col-md-4">
                                <a href="{{ route('bouchard.history', $bouchard->peserta_id) }}"
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
                                        Update
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

                let totalEnergiKcalKg = 0;
                let totalMetSlot = 0;
                let slotTerisi = 0;

                $('.aktivitas-select').each(function() {

                    let energi = parseFloat($(this).find(':selected').data('energi'));

                    if (!isNaN(energi) && energi > 0) {

                        totalEnergiKcalKg += energi;

                        // Konversi ke MET
                        totalMetSlot += (energi / 0.25);

                        slotTerisi++;

                    }

                });

                //------------------------------------------------
                // Berat Badan
                //------------------------------------------------
                let bb = parseFloat($('input[name="berat_badan"]').val()) || 0;

                //------------------------------------------------
                // Total Kalori
                //------------------------------------------------
                let totalKalori = totalEnergiKcalKg * bb;

                //------------------------------------------------
                // MET & PAL
                //------------------------------------------------
                let met = slotTerisi > 0 ?
                    totalMetSlot / slotTerisi :
                    0;

                let pal = met;

                //------------------------------------------------
                // Preview
                //------------------------------------------------
                $('#preview_berat').text(bb.toFixed(2));
                $('#total_energi').text(totalEnergiKcalKg.toFixed(2));
                $('#total_kalori').text(totalKalori.toFixed(2));

                $('#nilai_met').text(
                    slotTerisi > 0 ?
                    met.toFixed(2) :
                    '-'
                );

                $('#nilai_pal').text(
                    pal > 0 ?
                    pal.toFixed(2) :
                    '-'
                );

                //------------------------------------------------
                // Progress
                //------------------------------------------------
                let persen = Math.round((slotTerisi / 96) * 100);

                $('#progress_isian')
                    .css('width', persen + '%')
                    .text(persen + '%');

                $('#slotTerisi').text(slotTerisi);

                $('#progress_isian')
                    .removeClass('bg-success bg-warning bg-danger');

                if (persen < 50) {

                    $('#progress_isian').addClass('bg-danger');

                } else if (persen < 100) {

                    $('#progress_isian').addClass('bg-warning');

                } else {

                    $('#progress_isian').addClass('bg-success');

                }

                if (slotTerisi < 96) {

                    $('#alertBelumLengkap').removeClass('d-none');

                } else {

                    $('#alertBelumLengkap').addClass('d-none');

                }

                //------------------------------------------------
                // Kategori PAL
                //------------------------------------------------

                let kategori = "-";
                let warna = "secondary";

                if (pal > 0) {

                    if (pal < 1.40) {

                        kategori = "Sangat Ringan\n(Sedentary)";
                        warna = "secondary";

                    } else if (pal < 1.70) {

                        kategori = "Ringan\n(Light)";
                        warna = "info";

                    } else if (pal < 2.00) {

                        kategori = "Sedang\n(Moderate)";
                        warna = "warning";

                    } else {

                        kategori = "Berat\n(Vigorous)";
                        warna = "danger";

                    }

                }

                $('#badge_pal .kategori-text').text(kategori);

                $('#badge_pal .activity-dot')
                    .removeClass(
                        'bg-secondary bg-info bg-warning bg-danger bg-success'
                    )
                    .addClass('bg-' + warna);

            }
            // Saat Aktivitas Diganti
            $('.aktivitas-select').on('change', function() {

                let value = $(this).val();

                let hidden = $(this).data('hidden');

                let label = $(this).data('energi');

                if (!value) {

                    $('#' + hidden).val('');

                    $('#' + label).html('-');

                    hitungTotal();

                    return;

                }

                let energi = parseFloat(
                    $(this).find(':selected').data('energi')
                ) || 0;

                $('#' + hidden).val(value);

                $('#' + label).html(

                    energi.toFixed(2) +
                    ' kcal/kg/15m'

                );

                hitungTotal();

            });
            // Berat badan berubah
            $('input[name="berat_badan"]')
                .on('keyup change input', hitungTotal);

            $('form').on('submit', function() {

                let slotTerisi = 0;

                $('.aktivitas-select').each(function() {

                    if ($(this).val() !== '') {

                        slotTerisi++;

                    }

                });

                if (slotTerisi < 96) {

                    return confirm(

                        "Kuisioner baru terisi " +

                        slotTerisi +

                        "/96 slot.\n\nData tetap akan disimpan.\n\nLanjutkan?"

                    );

                }

            });
            // Trigger awal
            $('.aktivitas-select').trigger('change');
            // AUTO FILL
            $('#btnAutoIsi').click(function() {
                let value = $('#auto_kategori').val();
                let jam = parseInt($('#auto_jam').val());
                let menit = parseInt($('#auto_menit').val());
                let durasi = parseInt($('#auto_durasi').val());
                let slotAwal = (jam * 4) + (menit / 15);
                let jumlahSlot = durasi / 15;
                for (let i = 0; i < jumlahSlot; i++) {
                    let slot = slotAwal + i;
                    if (slot >= 96) {
                        break;
                    }
                    let jamGrid = Math.floor(slot / 4);
                    let kolom = slot % 4;
                    let field = ['m00', 'm15', 'm30', 'm45'][kolom];
                    let select = $('#' + field + '_' + jamGrid)
                        .prev('.aktivitas-select');
                    select.val(value).trigger('change');
                }
            });
        });
    </script>

    <style>
        .summary-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .06);
            transition: .25s;
            height: 100%;
        }

        .summary-card:hover {
            transform: translateY(-4px);
        }

        .summary-card small {
            color: #6c757d;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .summary-card h3 {
            margin: 8px 0;
            font-size: 30px;
            font-weight: 700;
            color: #2c3e50;
        }

        .summary-card span {
            color: #7b809a;
        }

        #badge_pal {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-height: 70px;
        }

        .activity-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .kategori-text {
            white-space: pre-line;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.3;
        }

        .kategori-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 85px;
        }

        #badge_pal {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .activity-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            box-shadow: 0 0 8px rgba(0, 0, 0, .18);
        }

        .kategori-text {
            white-space: pre-line;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.35;
            color: #344767;
            text-align: left;
        }
    </style>
@endsection
