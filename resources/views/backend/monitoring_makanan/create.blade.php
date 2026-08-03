@extends('backend.app')
@section('title', 'Tambah Monitoring Makanan')

@section('content')
    <div class="container-fluid py-3">
        <div class="page-inner">
            <!-- Header Page -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="fas fa-utensils text-primary me-2"></i>Tambah Monitoring Makanan
                    </h3>
                    <p class="text-muted mb-0 small">Catat dan pantau asupan kalori harian peserta secara real-time.</p>
                </div>
                <a href="{{ route('monitoring_makanan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>

            <form action="{{ route('monitoring_makanan.store') }}" method="POST" id="formMonitoring">
                @csrf

                <div class="row g-4">
                    <!-- Main Form Section -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom border-light">
                                <h5 class="card-title fw-bold text-dark mb-0 fs-6">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Informasi Utama
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    @if (!empty($selectedPeserta))
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-secondary small">Peserta <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ $peserta->firstWhere('id', $selectedPeserta)->nama ?? '-' }}"
                                                readonly>
                                            <input type="hidden" name="peserta_id" value="{{ $selectedPeserta }}">
                                        </div>
                                    @else
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-secondary small">Peserta <span
                                                    class="text-danger">*</span></label>
                                            <select name="peserta_id" class="form-select select-custom" required>
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

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary small">
                                            Petugas
                                        </label>
                                        <select name="petugas_id" class="form-select select-custom">
                                            <option value="">-- Pilih Petugas --</option>
                                            @foreach ($petugas as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('petugas_id') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary small">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control"
                                            value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold text-secondary small">Catatan Tambahan</label>
                                        <textarea name="catatan" rows="3" class="form-control" placeholder="Tambahkan catatan khusus jika ada...">{{ old('catatan') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Widget Sidebar -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 text-white card-gradient-summary mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="text-uppercase tracking-wider small opacity-75 fw-bold">Ringkasan
                                        Kalori</span>
                                    <i class="fas fa-fire fs-4 opacity-75"></i>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <input type="number" id="totalKalori"
                                        class="display-kalori fw-bold bg-transparent border-0 text-white w-100"
                                        value="0" readonly>
                                </div>
                                <span class="fs-6 opacity-75">kcal / Total Estimasi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Makanan Section -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div
                        class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center justify-content-between">
                        <h5 class="card-title fw-bold text-dark mb-0 fs-6">
                            <i class="fas fa-hamburger me-2 text-primary"></i>Rincian Makanan
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm rounded-2 shadow-sm px-3" id="btnTambah">
                            <i class="fas fa-plus me-1"></i> Tambah Makanan
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="detailTable">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th style="width: 18%;" class="ps-4">Waktu Makan</th>
                                        <th style="width: 32%;">Nama Makanan</th>
                                        <th style="width: 10%;">Jumlah</th>
                                        <th style="width: 20%;">Satuan Porsi</th>
                                        <th style="width: 12%;">Kalori (kcal)</th>
                                        <th style="width: 8%;" class="text-center pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <!-- Baris Ditambahkan Via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="card border-0 shadow-sm rounded-3 p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <a href="{{ route('monitoring_makanan.index') }}"
                            class="btn btn-light border w-100 w-md-auto px-4">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <button type="reset" class="btn btn-outline-secondary w-50 w-md-auto px-4">
                                <i class="fas fa-undo me-2"></i> Reset
                            </button>
                            <button type="submit"
                                class="btn btn-primary-gradient text-white w-50 w-md-auto px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Format Option Tampilan Select2 Dropdown
        function formatMakananOption(state) {
            if (!state.id) {
                return state.text;
            }

            let element = $(state.element);
            let kalori = element.data('kalori') || 0;
            let satuan = element.data('satuan') || 'Porsi';

            return $(`
            <div class="d-flex align-items-center justify-content-between py-1">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:26px; height:26px; font-size:11px;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <span class="fw-semibold text-dark fs-7">${state.text}</span>
                </div>
                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill fs-8">
                    🔥 ${kalori} kcal / ${satuan}
                </span>
            </div>
        `);
        }

        function formatMakananSelection(state) {
            return state.text;
        }

        // Inisialisasi Select2
        function initSelectMakanan() {
            $('.selectMakanan').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        placeholder: "-- Pilih Makanan --",
                        allowClear: true,
                        width: '100%',
                        templateResult: formatMakananOption,
                        templateSelection: formatMakananSelection
                    });
                }
            });
        }

        // Fungsi Menambah Baris Tabel
        function tambahBaris() {
            let html = `
        <tr class="align-middle">
            <td class="ps-4">
                <select name="waktu_makan[]" class="form-select form-select-sm" required>
                    <option value="Makan Pagi">Makan Pagi</option>
                    <option value="Snack Pagi">Snack Pagi</option>
                    <option value="Makan Siang">Makan Siang</option>
                    <option value="Snack Siang">Snack Siang</option>
                    <option value="Makan Malam">Makan Malam</option>
                    <option value="Snack Malam">Snack Malam</option>
                </select>
            </td>
            <td>
                <div class="food-select-wrapper">
                    <select name="master_makanan_id[]" class="form-select form-select-sm masterMakanan selectMakanan" required>
                        <option value="">-- Pilih Makanan --</option>
                        @foreach ($masterMakanan as $item)
                            <option value="{{ $item->id }}"
                                data-nama="{{ $item->nama }}"
                                data-satuan="{{ $item->satuan }}"
                                data-kalori="{{ $item->kalori }}"
                                data-gram="{{ $item->gram }}">
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="nama_makanan[]" class="namaMakanan">
                    
                    <div class="food-info-badge mt-1 d-none align-items-center gap-1">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-normal px-2 py-1 fs-8">
                            <i class="fas fa-fire me-1"></i>Master DB: <span class="badge-kalori">0</span> kcal / <span class="badge-satuan">porsi</span>
                        </span>
                    </div>
                </div>
            </td>
            <td>
                <input type="number" name="jumlah[]" class="form-control form-control-sm jumlah" value="1" min="0.1" step="0.1" required>
            </td>
            <td>
                <select name="satuan[]" class="form-select form-select-sm satuan" required>
                    <option value="Porsi">Porsi</option>
                </select>
            </td>
            <td>
                <input type="number" name="kalori[]" class="form-control form-control-sm kalori bg-light" value="0" readonly>
            </td>
            <td class="text-center pe-4">
                <button type="button" class="btn btn-outline-danger btn-sm border-0 btnHapus rounded-circle">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;

            $('#detailTable tbody').append(html);
            initSelectMakanan();
        }

        // Hitung Total Kalori Keseluruhan
        function hitungKalori() {
            let total = 0;
            $('.kalori').each(function() {
                let nilai = parseFloat($(this).val());
                if (!isNaN(nilai)) {
                    total += nilai;
                }
            });
            $('#totalKalori').val(total.toFixed(0));
        }

        // Kalkulasi Konversi Kalori per Baris
        function kalkulasiBaris(row) {

            let selected = row.find('.masterMakanan option:selected');

            let baseKalori = parseFloat(selected.data('kalori')) || 0;
            let baseGram = parseFloat(selected.data('gram')) || 100;

            let jumlah = parseFloat(row.find('.jumlah').val()) || 0;
            let satuanDipilih = row.find('.satuan').val();

            let berat = baseGram;

            switch (satuanDipilih) {

                case 'Centong':
                    berat = 75;
                    break;

                case 'Porsi':
                case 'Piring':
                    berat = 150;
                    break;

                case 'Mangkuk':
                    berat = 200;
                    break;

                case 'Gram':
                    berat = baseGram;
                    break;
            }

            let kaloriPerGram = baseKalori / baseGram;

            let totalKaloriBaris =
                kaloriPerGram *
                berat *
                jumlah;

            row.find('.kalori').val(
                Math.round(totalKaloriBaris)
            );

            hitungKalori();
        }

        $(document).ready(function() {
            // Baris awal saat halaman dimuat
            tambahBaris();

            // Tombol Tambah Baris
            $('#btnTambah').click(function() {
                tambahBaris();
            });

            // Hapus Baris
            $(document).on('click', '.btnHapus', function() {
                if ($('#detailTable tbody tr').length > 1) {
                    $(this).closest('tr').fadeOut(200, function() {
                        $(this).remove();
                        hitungKalori();
                    });
                } else {
                    alert('Minimal harus ada 1 rincian makanan.');
                }
            });

            // Event saat Memilih Item Makanan dari Select2
            $(document).on('change', '.masterMakanan', function() {
                let row = $(this).closest('tr');
                let selected = $(this).find(':selected');

                let nama = selected.data('nama') || '';
                let satuanDb = $.trim(selected.data('satuan') || 'Porsi');
                let kalori = selected.data('kalori') || 0;

                row.find('.namaMakanan').val(nama);

                // Dinamisasi Dropdown Satuan Sesuai Tipe Makanan
                let selectSatuan = row.find('.satuan');
                selectSatuan.empty();

                selectSatuan.append(`
    <option value="${satuanDb}" selected>${satuanDb}</option>
`);

                // Update Micro Badge Informasi Master
                let badgeWrapper = row.find('.food-info-badge');
                if (nama) {
                    badgeWrapper.find('.badge-kalori').text(kalori);
                    badgeWrapper.find('.badge-satuan').text(satuanDb);
                    badgeWrapper.removeClass('d-none').addClass('d-flex');
                } else {
                    badgeWrapper.addClass('d-none').removeClass('d-flex');
                }

                kalkulasiBaris(row);
            });

            // Event listener saat ubah Jumlah atau Satuan Porsi
            $(document).on('input change', '.jumlah, .satuan', function() {
                let row = $(this).closest('tr');
                kalkulasiBaris(row);
            });
        });
    </script>

    <style>
        /* Gradient Header & Buttons */
        .card-gradient-summary {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary-gradient:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .display-kalori {
            font-size: 2.75rem;
            line-height: 1;
            outline: none;
        }

        /* Clean Form Styling */
        .form-control,
        .form-select {
            border-color: #e2e8f0;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* Select2 Bootstrap Integration Fixes */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            padding-left: 12px;
            color: #334155;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }

        .select2-dropdown {
            border-color: #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .select2-search--dropdown {
            padding: 6px;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            padding: 4px 8px;
            outline: none;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #eef2ff !important;
            color: #4338ca !important;
        }

        .select2-results__option {
            border-bottom: 1px solid #f8fafc;
            padding: 6px 12px !important;
        }

        /* Font & Badge Helpers */
        .fs-7 {
            font-size: 0.825rem;
        }

        .fs-8 {
            font-size: 0.75rem;
        }

        .bg-primary-subtle {
            background-color: #e0e7ff !important;
        }

        .bg-warning-subtle {
            background-color: #fef3c7 !important;
        }

        .text-warning-emphasis {
            color: #b45309 !important;
        }

        /* Table Styling */
        #detailTable th {
            font-weight: 600;
            letter-spacing: 0.05em;
            background-color: #f8fafc;
        }

        .tracking-wider {
            letter-spacing: 0.05em;
        }
    </style>
@endsection
