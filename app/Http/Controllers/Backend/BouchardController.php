<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bouchard;
use App\Models\BouchardDetail;
use App\Models\Peserta;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BouchardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Peserta::with([
                'jenisPenyakit'
            ])
                ->whereHas('bouchard')
                ->withCount('bouchard');
            if (Auth::user()->hasRole('Peserta')) {
                $query->where('id', Auth::user()->peserta_id);
            }
            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return '
                        <a href="' . route('bouchard.history', $row->id) . '"
                            class="fw-bold text-primary text-decoration-none">
                            <i class="fa fa-user-circle me-1"></i>
                            ' . $row->nama . '
                        </a>
                    ';
                })
                ->addColumn('penyakit', function ($row) {
                    return $row->jenisPenyakit->nama_penyakit ?? '-';
                })
                ->addColumn('jumlah', function ($row) {
                    return $row->bouchard_count . ' Kali';
                })
                ->addColumn('terakhir', function ($row) {
                    $last = Bouchard::where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    return $last
                        ? $last->tanggal->format('d-m-Y')
                        : '-';
                })
                ->addColumn('aktivitas', function ($row) {
                    $last = Bouchard::where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    if (!$last) {
                        return '-';
                    }
                    $badge = 'success';
                    if ($last->kategori == 'Berat') {
                        $badge = 'danger';
                    } elseif ($last->kategori == 'Sedang') {
                        $badge = 'warning text-dark';
                    }
                    return '
                        <span class="badge bg-' . $badge . '">
                            ' . $last->kategori . '
                        </span>
                    ';
                })
                ->addColumn('petugas', function ($row) {
                    $last = Bouchard::with('petugas')
                        ->where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    return $last->petugas->nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="dropdown">
                        <button
                            class="btn btn-link p-0 text-primary"
                            type="button"
                            data-bs-toggle="dropdown">

                            <i class="fa fa-eye"
                               style="font-size:18px;"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>

                                <a class="dropdown-item"
                                    href="' . route('bouchard.history', $row->id) . '">

                                    <i class="fa fa-history me-2 text-primary"></i>

                                    Riwayat Bouchard

                                </a>

                            </li>

                        </ul>

                    </div>

                    ';
                })

                ->rawColumns([
                    'nama',
                    'aktivitas',
                    'action'
                ])

                ->make(true);
        }

        return view('backend.bouchard.index');
    }


    public function create(Request $request)
    {
        $petugas = Petugas::orderBy('nama')->get();
        if (auth()->user()->hasRole('Peserta')) {
            $peserta = Peserta::where('id', auth()->user()->peserta_id)->get();
            $selectedPeserta = auth()->user()->peserta_id;
        } else {
            $peserta = Peserta::orderBy('nama')->get();
            $selectedPeserta = $request->peserta_id;
        }

        // MASTER AKTIVITAS BOUCHARD
        $aktivitas = BouchardDetail::aktivitasList();
        return view('backend.bouchard.create', compact('peserta', 'petugas', 'selectedPeserta', 'aktivitas'));
    }


    public function store(Request $request)
    {
        if (auth()->user()->hasRole('Peserta')) {
            $request->merge([
                'peserta_id' => auth()->user()->peserta_id
            ]);
        }

        $request->validate([
            'peserta_id'   => 'required|exists:peserta,id',
            'petugas_id'   => 'nullable|exists:petugas,id',
            'tanggal'      => 'required|date',
            'berat_badan'  => 'required|numeric|min:1',

            'jam.*' => 'nullable|integer|min:0|max:23',
            'm00.*' => 'nullable|string|max:10',
            'm15.*' => 'nullable|string|max:10',
            'm30.*' => 'nullable|string|max:10',
            'm45.*' => 'nullable|string|max:10',
        ]);

        $cek = Bouchard::where('peserta_id', $request->peserta_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();
        if ($cek) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Kuisioner Bouchard pada tanggal tersebut sudah ada.'
                );
        }
        DB::beginTransaction();
        try {
            $bouchard = Bouchard::create([
                'peserta_id' => $request->peserta_id,
                'petugas_id' => $request->petugas_id,
                'tanggal' => $request->tanggal,
                'berat_badan' => $request->berat_badan,
                'catatan' => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            if ($request->has('jam')) {
                foreach ($request->jam as $i => $jam) {
                    BouchardDetail::create([
                        'bouchard_id' => $bouchard->id,
                        'jam' => $jam,
                        'm00' => $request->m00[$i] ?? null,
                        'm15' => $request->m15[$i] ?? null,
                        'm30' => $request->m30[$i] ?? null,
                        'm45' => $request->m45[$i] ?? null,
                    ]);
                }
            }
            $this->hitungHasil($bouchard->id);
            DB::commit();
            return redirect()->route('bouchard.index')->with('success', 'Kuisioner Bouchard berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $bouchard = Bouchard::with([
            'peserta',
            'petugas',
            'detail'
        ])->findOrFail($id);
        if (
            Auth::user()->hasRole('Peserta') &&
            $bouchard->peserta_id != Auth::user()->peserta_id
        ) {
            abort(403);
        }
        // Ambil master aktivitas dari Model
        $aktivitas = BouchardDetail::aktivitasList();

        return view('backend.bouchard.show', compact('bouchard', 'aktivitas'));
    }


    public function edit($id)
    {
        $bouchard = Bouchard::with([
            'detail',
            'peserta',
            'petugas'
        ])->findOrFail($id);


        // Proteksi peserta agar hanya bisa edit miliknya sendiri
        if (
            Auth::user()->hasRole('Peserta') &&
            $bouchard->peserta_id != Auth::user()->peserta_id
        ) {
            abort(403);
        }


        // Jika role Peserta hanya tampilkan dirinya sendiri
        if (Auth::user()->hasRole('Peserta')) {

            $peserta = Peserta::where(
                'id',
                Auth::user()->peserta_id
            )->get();
        } else {

            // Admin / petugas bisa memilih semua peserta
            $peserta = Peserta::orderBy('nama')->get();
        }


        // Master petugas
        $petugas = Petugas::orderBy('nama')->get();


        // Master aktivitas Bouchard
        $aktivitas = BouchardDetail::aktivitasList();


        return view(
            'backend.bouchard.edit',
            compact(
                'bouchard',
                'peserta',
                'petugas',
                'aktivitas'
            )
        );
    }


    public function update(Request $request, $id)
    {
        $bouchard = Bouchard::findOrFail($id);
        /*
    |--------------------------------------------------------------------------
    | PROTEKSI ROLE PESERTA
    |--------------------------------------------------------------------------
    */
        if (
            Auth::user()->hasRole('Peserta') &&
            $bouchard->peserta_id != Auth::user()->peserta_id
        ) {
            abort(403);
        }

        /*
    |--------------------------------------------------------------------------
    | PESERTA TIDAK BOLEH PINDAH DATA
    |--------------------------------------------------------------------------
    */
        if (Auth::user()->hasRole('Peserta')) {
            $request->merge([
                'peserta_id' => Auth::user()->peserta_id
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'petugas_id' => 'nullable|exists:petugas,id',
            'tanggal' => 'required|date',
            'berat_badan' => 'required|numeric|min:1',
            'jam.*' => 'nullable|integer|min:0|max:23',

            'm00.*' => 'nullable|string|max:10',
            'm15.*' => 'nullable|string|max:10',
            'm30.*' => 'nullable|string|max:10',
            'm45.*' => 'nullable|string|max:10',
        ]);

        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKAT TANGGAL
    |--------------------------------------------------------------------------
    */

        $cek = Bouchard::where('peserta_id', $request->peserta_id)
            ->whereDate('tanggal', $request->tanggal)
            ->where('id', '!=', $id)
            ->first();


        if ($cek) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Kuisioner Bouchard pada tanggal tersebut sudah ada.'
                );
        }



        DB::beginTransaction();

        try {


            /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER
        |--------------------------------------------------------------------------
        */

            $bouchard->update([

                'peserta_id' => $request->peserta_id,

                'petugas_id' => $request->petugas_id,

                'tanggal' => $request->tanggal,

                'berat_badan' => $request->berat_badan,

                'catatan' => $request->catatan,

                'updated_by' => Auth::id(),

            ]);



            /*
        |--------------------------------------------------------------------------
        | HAPUS DETAIL LAMA
        |--------------------------------------------------------------------------
        */

            BouchardDetail::where(
                'bouchard_id',
                $bouchard->id
            )->delete();



            /*
        |--------------------------------------------------------------------------
        | SIMPAN DETAIL BARU
        |--------------------------------------------------------------------------
        */

            if ($request->has('jam')) {


                foreach ($request->jam as $i => $jam) {


                    BouchardDetail::create([

                        'bouchard_id' => $bouchard->id,

                        'jam' => $jam,

                        'm00' => $request->m00[$i] ?? null,
                        'm15' => $request->m15[$i] ?? null,
                        'm30' => $request->m30[$i] ?? null,
                        'm45' => $request->m45[$i] ?? null,

                    ]);
                }
            }



            /*
        |--------------------------------------------------------------------------
        | HITUNG ULANG HASIL
        |--------------------------------------------------------------------------
        */

            $this->hitungHasil($bouchard->id);



            DB::commit();


            return redirect()
                ->route('bouchard.index')
                ->with(
                    'success',
                    'Kuisioner Bouchard berhasil diperbarui.'
                );
        } catch (\Exception $e) {


            DB::rollBack();


            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function destroy($id)
    {
        // PESERTA TIDAK BOLEH HAPUS
        if (Auth::user()->hasRole('Peserta')) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $bouchard = Bouchard::findOrFail($id);

            // HAPUS DETAIL TERLEBIH DAHULU
            BouchardDetail::where(
                'bouchard_id',
                $bouchard->id
            )->delete();

            // HAPUS HEADER
            $bouchard->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data Bouchard berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function searchPetugas(Request $request)
    {
        $query = Petugas::query();

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($x) use ($q) {
                $x->where('nama', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%");
            });
        }

        return $query->limit(10)->get([
            'id',
            'nama',
            'nip'
        ]);
    }

    public function history($peserta)
    {
        if (Auth::user()->hasRole('Peserta')) {
            $peserta = Auth::user()->peserta_id;
        }

        $peserta = Peserta::findOrFail($peserta);

        $riwayat = Bouchard::with([
            'petugas',
            'detail'
        ])
            ->where('peserta_id', $peserta->id)
            ->orderByDesc('tanggal')
            ->get();

        return view(
            'backend.bouchard.history',
            compact(
                'peserta',
                'riwayat'
            )
        );
    }

    public function exportPdf($id)
    {

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA BOUCHARD
    |--------------------------------------------------------------------------
    */

        $bouchard = Bouchard::with([
            'peserta',
            'petugas',
            'detail'
        ])->findOrFail($id);



        /*
    |--------------------------------------------------------------------------
    | PROTEKSI PESERTA
    |--------------------------------------------------------------------------
    */

        if (
            Auth::user()->hasRole('Peserta') &&
            $bouchard->peserta_id != Auth::user()->peserta_id
        ) {

            abort(403);
        }



        /*
    |--------------------------------------------------------------------------
    | GENERATE PDF
    |--------------------------------------------------------------------------
    */

        $pdf = Pdf::loadView(
            'backend.bouchard.pdf',
            compact('bouchard')
        )
            ->setPaper('A4', 'landscape');



        return $pdf->download(

            'Kuisioner_Bouchard_' .
                ($bouchard->peserta->nama ?? 'Peserta') .
                '.pdf'

        );
    }

    public function exportExcel($id)
    {
        $bouchard = Bouchard::with([
            'peserta',
            'petugas',
            'detail'
        ])->findOrFail($id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /*
    |--------------------------------------------------------------------------
    | JUDUL
    |--------------------------------------------------------------------------
    */

        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'KUISIONER LATIHAN FISIK MENURUT BOUCHARD');

        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', 'Laporan Hasil Monitoring Aktivitas Harian');

        $sheet->getStyle('A1:E2')->getFont()->setBold(true)->setSize(14);

        $sheet->getStyle('A1:E2')->getAlignment()->setHorizontal(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        );

        /*
    |--------------------------------------------------------------------------
    | IDENTITAS PESERTA
    |--------------------------------------------------------------------------
    */

        $sheet->setCellValue('A4', 'No RM');
        $sheet->setCellValue('B4', $bouchard->peserta->no_rm);

        $sheet->setCellValue('A5', 'Nama Peserta');
        $sheet->setCellValue('B5', $bouchard->peserta->nama);

        $sheet->setCellValue('A6', 'NIK');
        $sheet->setCellValue('B6', $bouchard->peserta->nik);

        $sheet->setCellValue('A7', 'No BPJS');
        $sheet->setCellValue('B7', $bouchard->peserta->no_bpjs);

        $sheet->setCellValue('A8', 'Jenis Kelamin');
        $sheet->setCellValue(
            'B8',
            $bouchard->peserta->jk == 'L'
                ? 'Laki-laki'
                : 'Perempuan'
        );

        /*
    |--------------------------------------------------------------------------
    | INFORMASI PEMERIKSAAN
    |--------------------------------------------------------------------------
    */

        $sheet->setCellValue('D4', 'Tanggal');
        $sheet->setCellValue(
            'E4',
            \Carbon\Carbon::parse($bouchard->tanggal)
                ->format('d-m-Y')
        );

        $sheet->setCellValue('D5', 'Petugas');
        $sheet->setCellValue(
            'E5',
            $bouchard->petugas->nama ?? '-'
        );

        $sheet->setCellValue('D6', 'Berat Badan');
        $sheet->setCellValue(
            'E6',
            number_format($bouchard->berat_badan, 2) . ' Kg'
        );

        $sheet->setCellValue('D7', 'Total Kalori');
        $sheet->setCellValue(
            'E7',
            number_format($bouchard->total_kalori, 2) . ' Kkal'
        );

        $sheet->setCellValue('D8', 'Kategori');
        $sheet->setCellValue(
            'E8',
            $bouchard->kategori
        );

        /*
    |--------------------------------------------------------------------------
    | HEADER TABEL
    |--------------------------------------------------------------------------
    */

        $row = 11;

        $sheet->setCellValue('A' . $row, 'Jam');
        $sheet->setCellValue('B' . $row, '00-15');
        $sheet->setCellValue('C' . $row, '15-30');
        $sheet->setCellValue('D' . $row, '30-45');
        $sheet->setCellValue('E' . $row, '45-60');

        $sheet->getStyle('A' . $row . ':E' . $row)
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A' . $row . ':E' . $row)
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );

        $sheet->getStyle('A' . $row . ':E' . $row)
            ->getFill()
            ->setFillType(
                \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setARGB('D9EAF7');

        $row++;
        /*
|--------------------------------------------------------------------------
| DATA AKTIVITAS 24 JAM
|--------------------------------------------------------------------------
*/

        $detail = $bouchard->detail->keyBy('jam');

        for ($jam = 0; $jam <= 23; $jam++) {

            $item = $detail->get($jam);

            $sheet->setCellValue(
                'A' . $row,
                sprintf('%02d', $jam) . ':00'
            );

            /*
    |--------------------------------------------------------------------------
    | 00 - 15
    |--------------------------------------------------------------------------
    */

            if ($item && $item->m00) {

                $sheet->setCellValue(
                    'B' . $row,
                    $item->aktivitas($item->m00)
                        . "\n(" .
                        number_format($item->energi($item->m00), 2)
                        . " kcal/kg/15 menit)"
                );
            } else {

                $sheet->setCellValue('B' . $row, '-');
            }

            /*
    |--------------------------------------------------------------------------
    | 15 - 30
    |--------------------------------------------------------------------------
    */

            if ($item && $item->m15) {

                $sheet->setCellValue(
                    'C' . $row,
                    $item->aktivitas($item->m15)
                        . "\n(" .
                        number_format($item->energi($item->m15), 2)
                        . " kcal/kg/15 menit)"
                );
            } else {

                $sheet->setCellValue('C' . $row, '-');
            }

            /*
    |--------------------------------------------------------------------------
    | 30 - 45
    |--------------------------------------------------------------------------
    */

            if ($item && $item->m30) {

                $sheet->setCellValue(
                    'D' . $row,
                    $item->aktivitas($item->m30)
                        . "\n(" .
                        number_format($item->energi($item->m30), 2)
                        . " kcal/kg/15 menit)"
                );
            } else {

                $sheet->setCellValue('D' . $row, '-');
            }

            /*
    |--------------------------------------------------------------------------
    | 45 - 60
    |--------------------------------------------------------------------------
    */

            if ($item && $item->m45) {

                $sheet->setCellValue(
                    'E' . $row,
                    $item->aktivitas($item->m45)
                        . "\n(" .
                        number_format($item->energi($item->m45), 2)
                        . " kcal/kg/15 menit)"
                );
            } else {

                $sheet->setCellValue('E' . $row, '-');
            }

            /*
    |--------------------------------------------------------------------------
    | STYLE BARIS
    |--------------------------------------------------------------------------
    */

            $sheet->getStyle('A' . $row . ':E' . $row)
                ->getAlignment()
                ->setWrapText(true);

            $sheet->getStyle('A' . $row . ':E' . $row)
                ->getAlignment()
                ->setVertical(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                );

            $sheet->getRowDimension($row)
                ->setRowHeight(35);

            $row++;
        }
        /*
|--------------------------------------------------------------------------
| RINGKASAN HASIL
|--------------------------------------------------------------------------
*/

        $row += 2;

        $sheet->setCellValue('A' . $row, 'Berat Badan');
        $sheet->setCellValue('B' . $row, number_format($bouchard->berat_badan, 2) . ' Kg');

        $row++;

        $sheet->setCellValue('A' . $row, 'Total Kalori');
        $sheet->setCellValue('B' . $row, number_format($bouchard->total_kalori, 2) . ' Kkal');

        $row++;

        $sheet->setCellValue('A' . $row, 'Kategori Aktivitas');
        $sheet->setCellValue('B' . $row, $bouchard->kategori);

        $row++;

        $sheet->setCellValue('A' . $row, 'Catatan');
        $sheet->setCellValue('B' . $row, $bouchard->catatan ?: '-');

        $row++;

        $sheet->setCellValue('A' . $row, 'Petugas');
        $sheet->setCellValue('B' . $row, $bouchard->petugas->nama ?? '-');

        $row++;

        $sheet->setCellValue('A' . $row, 'Dibuat');
        $sheet->setCellValue(
            'B' . $row,
            optional($bouchard->created_at)->format('d-m-Y H:i')
        );

        $row++;

        $sheet->setCellValue('A' . $row, 'Terakhir Diubah');
        $sheet->setCellValue(
            'B' . $row,
            optional($bouchard->updated_at)->format('d-m-Y H:i')
        );

        /*
|--------------------------------------------------------------------------
| BORDER TABEL AKTIVITAS
|--------------------------------------------------------------------------
*/

        $lastTableRow = 35;

        $sheet->getStyle('A11:E' . $lastTableRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        /*
|--------------------------------------------------------------------------
| BORDER RINGKASAN
|--------------------------------------------------------------------------
*/

        $sheet->getStyle(
            'A' . ($lastTableRow + 2) . ':B' . $row
        )->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        /*
|--------------------------------------------------------------------------
| AUTO WIDTH
|--------------------------------------------------------------------------
*/

        foreach (range('A', 'E') as $column) {

            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        /*
|--------------------------------------------------------------------------
| ALIGNMENT
|--------------------------------------------------------------------------
*/

        $sheet->getStyle('A1:E' . $row)
            ->getAlignment()
            ->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );

        /*
|--------------------------------------------------------------------------
| EXPORT
|--------------------------------------------------------------------------
*/

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {

            $writer->save('php://output');
        }, 'Kuisioner_Bouchard_' . $bouchard->peserta->nama . '.xlsx', [

            'Content-Type' =>
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function hitungHasil($bouchardId)
    {
        $bouchard = Bouchard::with('detail')->findOrFail($bouchardId);
        $detailModel = new BouchardDetail();

        $totalEnergiKcalKg = 0; // Total energi per kg (penjumlahan langsung nilai kategori Bouchard)
        $totalMET = 0;
        $slotTerisi = 0;

        foreach ($bouchard->detail as $detail) {
            foreach (
                [
                    $detail->m00,
                    $detail->m15,
                    $detail->m30,
                    $detail->m45,
                ] as $aktivitas
            ) {
                if (empty($aktivitas)) {
                    continue;
                }

                $energi = $detailModel->energi($aktivitas);

                // 1. Nilai energi Bouchard sudah dalam unit kcal/kg/15m -> Dijumlahkan langsung
                $totalEnergiKcalKg += $energi;

                // 2. Konversi ke MET per slot (1 MET = 0.25 kcal/kg/15m)
                $totalMET += ($energi / 0.25);

                $slotTerisi++;
            }
        }

        // Total Kalori (EE/TEE) = Total kcal/kg * Berat Badan
        $totalKalori = $totalEnergiKcalKg * $bouchard->berat_badan;

        // Rata-rata MET
        $met = $slotTerisi > 0 ? round($totalMET / $slotTerisi, 2) : 0;
        
        // PAL (Physical Activity Level) setara dengan rata-rata MET harian
        $pal = $met;

        // Klasifikasi Standar PAL (WHO / Bouchard)
        if ($pal < 1.40) {
            $kategori = 'Sangat Ringan';
        } elseif ($pal < 1.70) {
            $kategori = 'Ringan';
        } elseif ($pal < 2.00) {
            $kategori = 'Sedang';
        } else {
            $kategori = 'Berat';
        }

        // Simpan Hasil ke Database
        $bouchard->update([
            'total_kalori' => round($totalKalori, 2),
            'kategori'     => $kategori,
        ]);
    }
}
