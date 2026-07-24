@extends('backend.app')
@section('title', 'Tambah Master Makanan (Banyak Data)')

@section('content')
    <div class="container-fluid py-3">
        <div class="page-inner">
            <div class="row">
                <div class="col-12">
                    
                    <!-- WIDGET INTEGRASI API & UPLOAD EXCEL -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body bg-light rounded-3 p-3">
                            <div class="row align-items-center">
                                <!-- Bagian Cari API -->
                                <div class="col-lg-7 mb-3 mb-lg-0">
                                    <label class="fw-bold mb-2 text-dark">
                                        <i class="fas fa-globe text-info me-1"></i> Cari & Import Data Makanan dari API Luar:
                                    </label>
                                    <div class="input-group">
                                        <input type="text" id="inputSearchApi" class="form-control" placeholder="Ketik nama makanan (contoh: Indomie, Milk, Nasi)...">
                                        <button class="btn btn-info text-white px-3" id="btnCariApi" type="button">
                                            <i class="fas fa-search me-1"></i> Cari API
                                        </button>
                                    </div>
                                </div>

                                <!-- Bagian Tombol Upload Excel -->
                                <div class="col-lg-5 text-lg-end">
                                    <label class="fw-bold mb-2 text-dark d-block">Atau Gunakan File:</label>
                                    <button type="button" class="btn btn-success px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                                        <i class="fas fa-file-excel me-1"></i> Import dari Excel / CSV
                                    </button>
                                </div>
                            </div>

                            <!-- Area Tampilan Hasil Pencarian API -->
                            <div id="containerHasilApi" class="mt-3 d-none">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="fw-bold text-muted">Hasil Pencarian API (Klik + untuk memasukkan ke tabel di bawah):</small>
                                    <button type="button" class="btn-close btn-sm" id="btnTutupApi"></button>
                                </div>
                                <div id="hasilSearchApi" class="list-group shadow-sm" style="max-height: 250px; overflow-y: auto;">
                                    <!-- Dynamic API Results -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FORM TABEL BATCH MAKANAN -->
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                            <h4 class="card-title fw-bold text-dark mb-0">
                                <i class="fas fa-utensils text-primary me-2"></i>Tambah Master Makanan (Banyak Data)
                            </h4>
                            <button type="button" class="btn btn-success btn-sm rounded-2 px-3" id="btnTambahBaris">
                                <i class="fas fa-plus me-1"></i> Tambah Baris Manual
                            </button>
                        </div>

                        <div class="card-body p-0">
                            <form action="{{ route('master_makanan.store') }}" method="POST" id="formMasterMakanan">
                                @csrf

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="tableMasterMakanan">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th style="width: 12%;" class="ps-3">Kode</th>
                                                <th style="width: 20%;">Nama Makanan <span class="text-danger">*</span></th>
                                                <th style="width: 15%;">Kategori <span class="text-danger">*</span></th>
                                                <th style="width: 13%;">Satuan <span class="text-danger">*</span></th>
                                                <th style="width: 10%;">Gram <span class="text-danger">*</span></th>
                                                <th style="width: 10%;">Kalori (Kkal) <span class="text-danger">*</span></th>
                                                <th style="width: 15%;">Keterangan</th>
                                                <th style="width: 5%;" class="text-center pe-3">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top-0">
                                            <!-- Baris akan di-generate via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
                                    <a href="{{ route('master_makanan.index') }}"
                                       class="btn text-white shadow-sm px-4 py-2"
                                       style="background:linear-gradient(to right,#667eea,#764ba2);border:none;font-weight:500;">
                                        <i class="fas fa-arrow-left me-2"></i> Kembali
                                    </a>

                                    <div>
                                        <button type="button" class="btn btn-light border shadow-sm px-4 py-2 me-2" id="btnReset">
                                            <i class="fas fa-undo-alt me-2"></i> Reset
                                        </button>

                                        <button type="submit"
                                                class="btn text-white shadow-sm px-4 py-2"
                                                style="background:linear-gradient(to right,#36d1dc,#5b86e5);border:none;font-weight:500;">
                                            <i class="fas fa-save me-2"></i> Simpan Semua Data
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

    <!-- MODAL IMPORT EXCEL -->
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('master_makanan.import_excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalImportExcelLabel">
                            <i class="fas fa-file-excel text-success me-2"></i> Import Data Master Makanan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileExcel" class="form-label fw-bold">Pilih File Excel / CSV</label>
                            <input class="form-control" type="file" id="fileExcel" name="file" accept=".xlsx, .xls, .csv" required>
                            <div class="form-text text-muted mt-2">
                                Format file: <b>.xlsx, .xls, .csv</b>.<br>
                                Pastikan urutan kolom sesuai format:<br>
                                <code>[0] Kode, [1] Nama, [2] Kategori, [3] Satuan, [4] Gram, [5] Kalori, [6] Keterangan</code>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-upload me-1"></i> Upload & Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script>
    let rowIndex = 0;

    // Fungsi Tambah Baris (Manual / dari API)
    function tambahBaris(data = null) {
        rowIndex++;
        
        let kode = data ? data.kode : `MK00${rowIndex}`;
        let nama = data ? data.nama : '';
        let kategori = data ? data.kategori : '';
        let satuan = data ? data.satuan : '';
        let gram = data ? data.gram : '';
        let kalori = data ? data.kalori : '';
        let keterangan = data ? data.keterangan : '';

        let html = `
        <tr class="align-middle">
            <td class="ps-3">
                <input type="text" name="kode[]" class="form-control form-control-sm" value="${kode}" placeholder="Kode">
            </td>
            <td>
                <input type="text" name="nama[]" class="form-control form-control-sm" value="${nama}" placeholder="Nama Makanan" required>
            </td>
            <td>
                <select name="kategori[]" class="form-select form-select-sm" required>
                    <option value="">-- Kategori --</option>
                    <option value="Karbohidrat" ${kategori == 'Karbohidrat' ? 'selected' : ''}>Karbohidrat</option>
                    <option value="Protein Hewani" ${kategori == 'Protein Hewani' ? 'selected' : ''}>Protein Hewani</option>
                    <option value="Protein Nabati" ${kategori == 'Protein Nabati' ? 'selected' : ''}>Protein Nabati</option>
                    <option value="Sayuran" ${kategori == 'Sayuran' ? 'selected' : ''}>Sayuran</option>
                    <option value="Buah" ${kategori == 'Buah' ? 'selected' : ''}>Buah</option>
                    <option value="Minuman" ${kategori == 'Minuman' ? 'selected' : ''}>Minuman</option>
                    <option value="Snack" ${kategori == 'Snack' ? 'selected' : ''}>Snack</option>
                    <option value="Lainnya" ${!['Karbohidrat','Protein Hewani','Protein Nabati','Sayuran','Buah','Minuman','Snack'].includes(kategori) && kategori ? 'selected' : ''}>Lainnya</option>
                </select>
            </td>
            <td>
                <select name="satuan[]" class="form-select form-select-sm" required>
                    <option value="">-- Satuan --</option>
                    <option value="Piring" ${satuan == 'Piring' ? 'selected' : ''}>Piring</option>
                    <option value="Mangkuk" ${satuan == 'Mangkuk' ? 'selected' : ''}>Mangkuk</option>
                    <option value="Potong" ${satuan == 'Potong' ? 'selected' : ''}>Potong</option>
                    <option value="Butir" ${satuan == 'Butir' ? 'selected' : ''}>Butir</option>
                    <option value="Gelas" ${satuan == 'Gelas' ? 'selected' : ''}>Gelas</option>
                    <option value="Cangkir" ${satuan == 'Cangkir' ? 'selected' : ''}>Cangkir</option>
                    <option value="Bungkus" ${satuan == 'Bungkus' ? 'selected' : ''}>Bungkus</option>
                    <option value="Buah" ${satuan == 'Buah' ? 'selected' : ''}>Buah</option>
                    <option value="Gram" ${satuan == 'Gram' || !satuan ? 'selected' : ''}>Gram</option>
                </select>
            </td>
            <td>
                <input type="number" name="gram[]" step="0.01" class="form-control form-control-sm" value="${gram}" placeholder="Gram" required>
            </td>
            <td>
                <input type="number" name="kalori[]" step="0.01" class="form-control form-control-sm" value="${kalori}" placeholder="Kkal" required>
            </td>
            <td>
                <input type="text" name="keterangan[]" class="form-control form-control-sm" value="${keterangan}" placeholder="Opsional">
                <input type="hidden" name="aktif[]" value="1">
            </td>
            <td class="text-center pe-3">
                <button type="button" class="btn btn-outline-danger btn-sm border-0 btnHapusBaris rounded-circle">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;

        $('#tableMasterMakanan tbody').append(html);
    }

    $(document).ready(function() {
        for (let i = 0; i < 2; i++) {
            tambahBaris();
        }

        $('#btnTambahBaris').click(function() {
            tambahBaris();
        });

        $(document).on('click', '.btnHapusBaris', function() {
            if ($('#tableMasterMakanan tbody tr').length > 1) {
                $(this).closest('tr').fadeOut(150, function() {
                    $(this).remove();
                });
            } else {
                alert('Minimal harus mengisi 1 data makanan!');
            }
        });

        $('#btnReset').click(function() {
            $('#tableMasterMakanan tbody').empty();
            rowIndex = 0;
            for (let i = 0; i < 2; i++) {
                tambahBaris();
            }
        });

        // Script Pencarian API
        $('#btnCariApi').click(function() {
            let q = $('#inputSearchApi').val().trim();
            if (!q) {
                alert('Ketik kata kunci makanan terlebih dahulu!');
                return;
            }

            $('#containerHasilApi').removeClass('d-none');
            $('#hasilSearchApi').html('<div class="p-3 text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Mencari data ke server API...</div>');

            $.get("{{ route('master_makanan.search_api') }}", { q: q }, function(res) {
                $('#hasilSearchApi').empty();

                if (!res || res.length === 0) {
                    $('#hasilSearchApi').html('<div class="p-3 text-center text-danger">Makanan tidak ditemukan di API!</div>');
                    return;
                }

                res.forEach(function(item) {
                    let itemString = encodeURIComponent(JSON.stringify(item));
                    let listHtml = `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark">${item.nama}</strong>
                                <div class="small text-muted">
                                    Kategori: <span class="badge bg-secondary">${item.kategori}</span> | 
                                    Kalori: <b class="text-success">${item.kalori} Kkal</b> / ${item.gram} gr
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary btnPilihApi" data-item="${itemString}">
                                <i class="fas fa-plus me-1"></i> Masukkan ke Tabel
                            </button>
                        </div>
                    `;
                    $('#hasilSearchApi').append(listHtml);
                });
            }).fail(function() {
                $('#hasilSearchApi').html('<div class="p-3 text-center text-danger">Gagal terhubung ke API server.</div>');
            });
        });

        $('#inputSearchApi').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#btnCariApi').click();
            }
        });

        $('#btnTutupApi').click(function() {
            $('#containerHasilApi').addClass('d-none');
        });

        $(document).on('click', '.btnPilihApi', function() {
            let encodedItem = $(this).attr('data-item');
            let item = JSON.parse(decodeURIComponent(encodedItem));

            $('#tableMasterMakanan tbody tr').each(function() {
                let namaVal = $(this).find('input[name="nama[]"]').val();
                if (!namaVal) {
                    $(this).remove();
                }
            });

            tambahBaris(item);
            $(this).removeClass('btn-outline-primary').addClass('btn-success').html('<i class="fas fa-check me-1"></i> Ditempatkan').prop('disabled', true);
        });
    });
</script>
@endsection