<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MasterMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory; // <-- Ditambahkan untuk import Excel/CSV

class MasterMakananController extends Controller
{
    /**
     * Menampilkan halaman utama master makanan & Server-Side DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $makanan = MasterMakanan::query();

            return DataTables::of($makanan)

                ->addIndexColumn()

                ->editColumn('gram', function ($row) {
                    return number_format($row->gram, 0) . ' gr';
                })

                ->editColumn('kalori', function ($row) {
                    return number_format($row->kalori, 0) . ' kkal';
                })

                ->addColumn('status', function ($row) {

                    return $row->aktif
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-danger">Tidak Aktif</span>';
                })

                ->addColumn('action', function ($row) {

                    return '
                    <div class="dropdown">

                        <button class="btn btn-link p-0 text-primary"
                                type="button"
                                data-bs-toggle="dropdown">

                            <i class="fa fa-eye" style="font-size:18px;"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a class="dropdown-item"
                                   href="' . route('master_makanan.show', $row->id) . '">

                                    <i class="fa fa-search me-2 text-primary"></i>
                                    Detail Master Makanan

                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="' . route('master_makanan.edit', $row->id) . '">

                                    <i class="fa fa-pencil-alt me-2 text-info"></i>
                                    Edit Master Makanan

                                </a>
                            </li>

                            <li>
                                <button type="button"
                                        class="dropdown-item btn-delete"
                                        data-id="' . $row->id . '">

                                    <i class="fa fa-trash me-2 text-danger"></i>
                                    Hapus Master Makanan

                                </button>
                            </li>

                        </ul>

                    </div>';
                })

                ->rawColumns(['status', 'action'])

                ->make(true);
        }

        return view('backend.master_makanan.index');
    }

    /**
     * Menampilkan form tambah makanan (Batch Input + Integrasi API)
     */
    public function create()
    {
        return view('backend.master_makanan.create');
    }

    /**
     * Menyimpan multiple data makanan dari form tabel batch
     */
    public function store(Request $request)
    {
        // Validasi input array
        $request->validate([
            'kode.*'       => 'nullable|string|max:50',
            'nama.*'       => 'required|string|max:255',
            'kategori.*'   => 'required|string',
            'satuan.*'     => 'required|string',
            'gram.*'       => 'required|numeric|min:0',
            'kalori.*'     => 'required|numeric|min:0',
            'keterangan.*' => 'nullable|string',
        ], [
            'nama.*.required'     => 'Nama makanan wajib diisi.',
            'kategori.*.required' => 'Kategori wajib dipilih.',
            'satuan.*.required'   => 'Satuan wajib dipilih.',
            'gram.*.required'     => 'Gram wajib diisi.',
            'kalori.*.required'   => 'Kalori wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            if (is_array($request->nama)) {
                foreach ($request->nama as $key => $namaMakanan) {
                    if (!empty($namaMakanan)) {
                        MasterMakanan::create([
                            'kode'       => $request->kode[$key] ?? null,
                            'nama'       => $namaMakanan,
                            'kategori'   => $request->kategori[$key] ?? null,
                            'satuan'     => $request->satuan[$key] ?? null,
                            'gram'       => $request->gram[$key] ?? 0,
                            'kalori'     => $request->kalori[$key] ?? 0,
                            'keterangan' => $request->keterangan[$key] ?? null,
                            'aktif'      => $request->aktif[$key] ?? 1,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('master_makanan.index')
                ->with('success', 'Semua data master makanan berhasil disimpan sekaligus!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Import Data Master Makanan via File Excel (.xlsx, .xls) atau CSV (.csv)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:4096'
        ], [
            'file.required' => 'File tidak boleh kosong!',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv',
            'file.max'      => 'Ukuran file maksimal 4 MB'
        ]);

        try {
            $file = $request->file('file');
            
            // Membaca file spreadsheet menggunakan PhpSpreadsheet
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            DB::beginTransaction();

            $insertedCount = 0;

            // Loop dan abaikan baris pertama (Header)
            foreach (array_slice($rows, 1) as $row) {
                
                // Kolom B / Index 1 = Nama Makanan (Abaikan jika kosong)
                $namaMakanan = $row[1] ?? null;
                if (empty(trim($namaMakanan))) {
                    continue;
                }

                MasterMakanan::create([
                    'kode'       => !empty($row[0]) ? trim($row[0]) : ('MK-' . rand(1000, 9999)),
                    'nama'       => trim($namaMakanan),
                    'kategori'   => !empty($row[2]) ? trim($row[2]) : 'Lainnya',
                    'satuan'     => !empty($row[3]) ? trim($row[3]) : 'Gram',
                    'gram'       => is_numeric($row[4]) ? (float)$row[4] : 100,
                    'kalori'     => is_numeric($row[5]) ? (float)$row[5] : 0,
                    'keterangan' => !empty($row[6]) ? trim($row[6]) : 'Hasil Import Excel',
                    'aktif'      => 1,
                ]);

                $insertedCount++;
            }

            DB::commit();

            return redirect()
                ->route('master_makanan.index')
                ->with('success', "Berhasil mengimpor {$insertedCount} data master makanan!");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal mengimpor file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Mencari data makanan khusus wilayah Indonesia & Lampung
     */
    public function searchApi(Request $request)
    {
        $keyword = trim($request->query('q'));

        if (!$keyword) {
            return response()->json([]);
        }

        try {
            // Request ke Open Food Facts API dengan filter khusus produk Indonesia
            $response = Http::withHeaders([
                'User-Agent' => 'LaravelApp-LampungFoodDatabase/1.0 (contact@domain.com)',
            ])->timeout(10)->get("https://world.openfoodfacts.org/cgi/search.pl", [
                'search_terms'   => $keyword,
                'search_simple'  => 1,
                'action'         => 'process',
                'json'           => 1,
                'page_size'      => 20,
                'tagtype_0'      => 'countries',
                'tag_contains_0' => 'contains',
                'tag_0'          => 'indonesia',
            ]);

            $dataFormatted = [];

            if ($response->successful()) {
                $products = $response->json()['products'] ?? [];

                foreach ($products as $item) {
                    $nama = $item['product_name_id'] 
                        ?? $item['product_name'] 
                        ?? $item['product_name_en'] 
                        ?? null;

                    if ($nama) {
                        // Ambil nilai kalori
                        $kalori = $item['nutriments']['energy-kcal_100g'] 
                            ?? $item['nutriments']['energy-kcal'] 
                            ?? $item['nutriments']['energy-kcal_value']
                            ?? 0;

                        $dataFormatted[] = [
                            'kode'       => $item['code'] ?? 'API-' . rand(1000, 9999),
                            'nama'       => $nama,
                            'kategori'   => isset($item['categories_tags'][0]) 
                                            ? ucwords(str_replace(['en:', 'id:', '-'], ['', '', ' '], $item['categories_tags'][0])) 
                                            : 'Lainnya',
                            'satuan'     => 'Gram',
                            'gram'       => 100,
                            'kalori'     => round($kalori, 2),
                            'keterangan' => 'Diambil dari Database Makanan Indonesia (API)',
                        ];
                    }
                }
            }

            // Jika dari API Internasional kosong, gabungkan dengan / panggil Database Makanan Lokal
            if (empty($dataFormatted)) {
                $dataFormatted = $this->getLokalDatabaseFallback($keyword);
            } else {
                // Jika ada hasil API, tetap gabungkan dengan pencarian masakan lokal agar lebih kaya
                $lokal = $this->getLokalDatabaseFallback($keyword);
                $dataFormatted = array_merge($lokal, $dataFormatted);
            }

            return response()->json($dataFormatted);

        } catch (\Exception $e) {
            // Jika koneksi internet/API bermasalah, kembalikan data masakan lokal
            return response()->json($this->getLokalDatabaseFallback($keyword));
        }
    }

    /**
     * Helper untuk menangani masakan rumahan / khas lokal Lampung & Indonesia
     */
    private function getLokalDatabaseFallback($keyword)
    {
        // Kamus data makanan lokal/tradisional Indonesia & Lampung (Estimasi Rata-rata)
        $makananLokal = [
            // Olahan Ayam & Daging
            ['nama' => 'Ayam Goreng', 'kategori' => 'Protein Hewani', 'kalori' => 246, 'satuan' => 'Potong', 'gram' => 100],
            ['nama' => 'Ayam Bakar', 'kategori' => 'Protein Hewani', 'kalori' => 195, 'satuan' => 'Potong', 'gram' => 100],
            ['nama' => 'Ayam Penyet', 'kategori' => 'Protein Hewani', 'kalori' => 260, 'satuan' => 'Potong', 'gram' => 100],
            ['nama' => 'Ayam Ungkep', 'kategori' => 'Protein Hewani', 'kalori' => 210, 'satuan' => 'Potong', 'gram' => 100],
            ['nama' => 'Gulai Ayam', 'kategori' => 'Protein Hewani', 'kalori' => 230, 'satuan' => 'Potong', 'gram' => 100],
            ['nama' => 'Daging Sapi Rendang', 'kategori' => 'Protein Hewani', 'kalori' => 193, 'satuan' => 'Potong', 'gram' => 80],

            // Khas Lampung & Olahan Ikan
            ['nama' => 'Seruit / Ikan Bakar Khas Lampung', 'kategori' => 'Protein Hewani', 'kalori' => 165, 'satuan' => 'Potong', 'gram' => 100],
            ['nama' => 'Pindang Ikan Patin / Baung', 'kategori' => 'Protein Hewani', 'kalori' => 140, 'satuan' => 'Mangkuk', 'gram' => 150],
            ['nama' => 'Ikan Goreng', 'kategori' => 'Protein Hewani', 'kalori' => 210, 'satuan' => 'Potong', 'gram' => 100],
            ['nama' => 'Sambal Rampai / Terasi', 'kategori' => 'Lainnya', 'kalori' => 45, 'satuan' => 'Gram', 'gram' => 30],

            // Karbohidrat
            ['nama' => 'Nasi Putih', 'kategori' => 'Karbohidrat', 'kalori' => 130, 'satuan' => 'Piring', 'gram' => 100],
            ['nama' => 'Nasi Uduk', 'kategori' => 'Karbohidrat', 'kalori' => 170, 'satuan' => 'Piring', 'gram' => 100],
            ['nama' => 'Nasi Goreng', 'kategori' => 'Karbohidrat', 'kalori' => 250, 'satuan' => 'Piring', 'gram' => 150],

            // Lauk Tahu, Tempe, Telur
            ['nama' => 'Tahu Goreng', 'kategori' => 'Protein Nabati', 'kalori' => 115, 'satuan' => 'Potong', 'gram' => 50],
            ['nama' => 'Tempe Goreng', 'kategori' => 'Protein Nabati', 'kalori' => 193, 'satuan' => 'Potong', 'gram' => 50],
            ['nama' => 'Telur Dadar', 'kategori' => 'Protein Hewani', 'kalori' => 154, 'satuan' => 'Butir', 'gram' => 60],
            ['nama' => 'Telur Ceplok', 'kategori' => 'Protein Hewani', 'kalori' => 110, 'satuan' => 'Butir', 'gram' => 50],

            // Sayuran & Snack
            ['nama' => 'Sayur Asem', 'kategori' => 'Sayuran', 'kalori' => 29, 'satuan' => 'Mangkuk', 'gram' => 100],
            ['nama' => 'Tumis Kangkung', 'kategori' => 'Sayuran', 'kalori' => 60, 'satuan' => 'Mangkuk', 'gram' => 100],
            ['nama' => 'Pempek Palembang / Lampung', 'kategori' => 'Snack', 'kalori' => 180, 'satuan' => 'Potong', 'gram' => 80],
        ];

        $hasil = [];
        foreach ($makananLokal as $item) {
            // Match kata kunci pencarian
            if (stripos($item['nama'], $keyword) !== false) {
                $hasil[] = [
                    'kode'       => 'LKL-' . rand(100, 999),
                    'nama'       => $item['nama'],
                    'kategori'   => $item['kategori'],
                    'satuan'     => $item['satuan'],
                    'gram'       => $item['gram'],
                    'kalori'     => $item['kalori'],
                    'keterangan' => 'Database Makanan Lokal Indonesia',
                ];
            }
        }

        return $hasil;
    }

    /**
     * Menampilkan detail data makanan berdasarkan ID
     */
    public function show($id)
    {
        $makanan = MasterMakanan::findOrFail($id);

        return view('backend.master_makanan.show', compact('makanan'));
    }

    /**
     * Menampilkan form edit makanan
     */
    public function edit($id)
    {
        $makanan = MasterMakanan::findOrFail($id);

        return view('backend.master_makanan.edit', compact('makanan'));
    }

    /**
     * Mengubah data makanan tunggal
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode'       => 'nullable|unique:master_makanan,kode,' . $id,
            'nama'       => 'required|string|max:255',
            'kategori'   => 'required',
            'satuan'     => 'required',
            'gram'       => 'required|numeric|min:0',
            'kalori'     => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        MasterMakanan::findOrFail($id)->update([
            'kode'       => $request->kode,
            'nama'       => $request->nama,
            'kategori'   => $request->kategori,
            'satuan'     => $request->satuan,
            'gram'       => $request->gram,
            'kalori'     => $request->kalori,
            'keterangan' => $request->keterangan,
            'aktif'      => $request->aktif,
        ]);

        return redirect()
            ->route('master_makanan.index')
            ->with('success', 'Data master makanan berhasil diupdate.');
    }

    /**
     * Menghapus data makanan
     */
    public function destroy($id)
    {
        MasterMakanan::findOrFail($id)->delete();

        return response()->json([
            'success' => true
        ]);
    }
}