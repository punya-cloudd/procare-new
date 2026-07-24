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
                                <label>Petugas</label>
                                <select name="petugas_id" class="form-select">
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

                        <div class="card border-info shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <strong>
                                    <i class="fas fa-magic me-2"></i>
                                    Isi Grid Otomatis
                                </strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label>Aktivitas</label>
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
                                        <label>Jam</label>
                                        <select class="form-select" id="auto_jam">
                                            @for ($i = 0; $i <= 23; $i++)
                                                <option value="{{ $i }}">
                                                    {{ sprintf('%02d', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label>Menit</label>
                                        <select class="form-select" id="auto_menit">
                                            <option value="0">00</option>
                                            <option value="15">15</option>
                                            <option value="30">30</option>
                                            <option value="45">45</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label>Durasi</label>
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
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-success w-100" id="btnAutoIsi">
                                            <i class="fas fa-bolt me-1"></i>
                                            Isikan
                                        </button>
                                    </div>
                                </div>
                            </div>
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
                                                        data-energi="{{ $field }}_energi_{{ $jam }}">
                                                        <option value="">-- Pilih Aktivitas --</option>
                                                        @foreach ($aktivitas as $kategori => $item)
                                                            <optgroup label="{{ $item['label'] }}">
                                                                @foreach ($item['items'] as $index => $nama)
                                                                    <option
                                                                        value="{{ $kategori }}-{{ $index }}"
                                                                        data-kategori="{{ $kategori }}"
                                                                        data-energi="{{ $item['energi'] }}">
                                                                        {{ $nama }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
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
                                        <div class="summary-card border-start border-5 border-primary">
                                            <small>Berat Badan</small>
                                            <h3 id="preview_berat">0.00</h3>
                                            <span>Kg</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div class="summary-card border-start border-5 border-success">
                                            <small>Total Energi</small>
                                            <h3 id="total_energi">0.00</h3>
                                            <span>kcal/kg</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div class="summary-card border-start border-5 border-warning">
                                            <small>Total Kalori</small>
                                            <h3 id="total_kalori">0.00</h3>
                                            <span>Kkal</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div class="summary-card border-start border-5 border-info">
                                            <small>Rata-rata MET</small>
                                            <h3 id="nilai_met">-</h3>
                                            <span>MET/hari</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div class="summary-card border-start border-5 border-danger">
                                            <small>PAL</small>
                                            <h3 id="nilai_pal">-</h3>
                                            <span>EE / BMR</span>
                                        </div>
                                    </div>
                                    <div class="col-lg col-md-6">
                                        <div class="summary-card border-start border-5 border-secondary">
                                            <small class="text-uppercase fw-semibold">
                                                Kategori Aktivitas
                                            </small>
                                            <div id="badge_pal" class="activity-status mt-3">
                                                <div class="activity-dot bg-secondary"></div>
                                                <div class="activity-info">
                                                    <div class="kategori-text">
                                                        Belum dihitung
                                                    </div>

                                                    <small class="text-muted">
                                                        Physical Activity Level (PAL)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="progress" style="height:28px">
                                    <div class="progress-bar bg-success" id="progress_isian" role="progressbar"
                                        style="width:0%">
                                        0%
                                    </div>
                                </div>
                                <small class="text-muted">
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
                let totalEnergiKcalKg = 0; // Total energi per kg (sum nilai kategori Bouchard)
                let totalMetSlot = 0;
                let slotTerisi = 0;

                $('.aktivitas-select').each(function() {
                    let nilaiKategori = parseFloat($(this).find(':selected').data('energi'));

                    if (!isNaN(nilaiKategori) && nilaiKategori > 0) {
                        // 1. Setiap nilai kategori Bouchard sudah dalam unit kcal/kg/15-menit
                        totalEnergiKcalKg += nilaiKategori;

                        // 2. Konversi Nilai Kategori ke MET (1 MET = 0.25 kcal/kg/15m)
                        let metSlot = nilaiKategori / 0.25;
                        totalMetSlot += metSlot;

                        slotTerisi++;
                    }
                });

                let bb = parseFloat($('input[name="berat_badan"]').val()) || 0;

                // Total Kalori (EE/TEE) = Total kcal/kg * Berat Badan (kg)
                let totalKalori = totalEnergiKcalKg * bb;

                $('#preview_berat').text(bb.toFixed(2));
                $('#total_energi').text(totalEnergiKcalKg.toFixed(2));
                $('#total_kalori').text(totalKalori.toFixed(2));

                //----------------------------------
                // Progress Bar Pengisian
                //----------------------------------
                let persen = Math.round((slotTerisi / 96) * 100);

                $('#progress_isian')
                    .css('width', persen + '%')
                    .text(persen + '%');

                $('#slotTerisi').text(slotTerisi);

                $('#progress_isian').removeClass('bg-success bg-warning bg-danger');

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

                //----------------------------------
                // MET Rata-rata & PAL (Physical Activity Level)
                //----------------------------------
                let metRataRata = 0;
                let pal = 0;

                if (slotTerisi > 0) {
                    metRataRata = totalMetSlot / slotTerisi;
                    // Secara matematis pada durasi 24 jam penuh, Rata-rata MET = Nilai PAL
                    pal = metRataRata;
                }

                $('#nilai_met').text(slotTerisi > 0 ? metRataRata.toFixed(2) : '-');
                $('#nilai_pal').text(pal > 0 ? pal.toFixed(2) : '-');

                //----------------------------------
                // Klasifikasi Standar PAL (WHO / Bouchard)
                //----------------------------------
                let kategori = "-";
                let warna = "secondary";

                if (pal > 0) {
                    if (pal < 1.40) {
                        kategori = "Sangat Ringan<br><small>(Sedentary)</small>";
                        warna = "secondary";
                    } else if (pal < 1.70) {
                        kategori = "Ringan<br><small>(Light)</small>";
                        warna = "info";
                    } else if (pal < 2.00) {
                        kategori = "Sedang<br><small>(Moderate)</small>";
                        warna = "warning";
                    } else {
                        kategori = "Berat<br><small>(Vigorous)</small>";
                        warna = "danger";
                    }
                }

                $('#badge_pal .kategori-text').html(kategori);

                $('#badge_pal .activity-dot')
                    .removeClass('bg-secondary bg-info bg-warning bg-danger bg-success')
                    .addClass('bg-' + warna);
            }

            // Event listener dropdown aktivitas
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

                let energi = parseFloat($(this).find(':selected').data('energi')) || 0;

                $('#' + hidden).val(value);

                // Label menampilkan nilai kategori energi per slot 15 menit
                $('#' + label).html(
                    energi.toFixed(2) + ' kcal/kg/15m'
                );

                hitungTotal();
            });

            // Re-calculate jika berat badan diubah
            $('input[name="berat_badan"]').on('keyup change input', hitungTotal);

            // Auto-fill Grid
            $('#btnAutoIsi').on('click', function() {
                let value = $('#auto_kategori').val();
                let jam = parseInt($('#auto_jam').val());
                let menit = parseInt($('#auto_menit').val());
                let durasi = parseInt($('#auto_durasi').val());
                let slotAwal = jam * 4 + (menit / 15);
                let jumlahSlot = durasi / 15;

                for (let i = 0; i < jumlahSlot; i++) {
                    let slot = slotAwal + i;
                    if (slot >= 96) break;

                    let jamGrid = Math.floor(slot / 4);
                    let kolom = slot % 4;
                    let field = ['m00', 'm15', 'm30', 'm45'][kolom];

                    $('#' + field + '_' + jamGrid)
                        .closest('td')
                        .find('.aktivitas-select')
                        .val(value)
                        .trigger('change');
                }
            });

            // Konfirmasi Submit Form jika belum 96 slot
            $('form').on('submit', function(e) {
                let slotTerisi = 0;
                $('.aktivitas-select').each(function() {
                    if ($(this).val() !== '') slotTerisi++;
                });

                if (slotTerisi < 96) {
                    return confirm(
                        "Kuisioner baru terisi " + slotTerisi + "/96 slot.\n\n" +
                        "Data tetap dapat disimpan, namun rata-rata MET dan PAL akan dihitung berdasarkan slot terisi saja.\n\n" +
                        "Apakah Anda yakin ingin menyimpan?"
                    );
                }
            });

            // Inisialisasi awal
            hitungTotal();
        });
    </script>
    <style>
        .summary-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
            transition: .25s;
            height: 100%;
        }

        .activity-status {
            display: flex;
            align-items: center;
            gap: 14px;

            width: 100%;
            padding: 14px 16px;

            background: #f8fafc;
            border: 1px solid #e9ecef;
            border-radius: 14px;
        }

        .activity-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            flex-shrink: 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        }

        .activity-info {
            flex: 1;
        }

        .kategori-text {
            font-size: 18px;
            font-weight: 700;
            color: #344767;
            white-space: normal;
        }

        .activity-info small {
            display: block;
            margin-top: 3px;
            font-size: 12px;
            color: #7b809a;
            text-transform: none;
            letter-spacing: 0;
        }

        .summary-card:hover {
            transform: translateY(-3px);
        }

        .summary-card small {
            color: #7d8ca3;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-size: 12px;
        }

        .summary-card h3 {
            margin: 8px 0 3px;
            font-size: 30px;
            font-weight: 700;
            color: #344767;
        }

        .summary-card span {
            color: #7b809a;
            font-size: 13px;
        }

        #badge_pal .kategori-text {
            line-height: 1.3;
            text-align: left;
            font-size: 16px;
            font-weight: 700;
        }

        #badge_pal .kategori-text small {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #6c757d;
        }
    </style>
@endsection
