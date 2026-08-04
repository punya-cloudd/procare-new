<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MonitoringMakanan;
use App\Models\MonitoringMakananDetail;
use App\Models\Peserta;
use App\Models\Petugas;
use App\Models\MasterMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\Pemeriksaan;
use Carbon\Carbon;

class MonitoringMakananController extends Controller
{
    /**
     * Menampilkan daftar monitoring makanan
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Peserta::with('jenisPenyakit')
                ->whereHas('monitoringMakanan')
                ->withCount('monitoringMakanan');

            // Jika role Peserta, hanya lihat data milik sendiri
            if (auth()->user()->hasRole('Peserta')) {
                $query->where('id', auth()->user()->peserta_id);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return '
                        <a href="' . route('monitoring_makanan.history', $row->id) . '" class="fw-bold text-primary text-decoration-none">
                            <i class="fa fa-user-circle me-1"></i>
                            ' . e($row->nama) . '
                        </a>
                    ';
                })
                ->addColumn('penyakit', function ($row) {
                    return $row->jenisPenyakit->nama_penyakit ?? '-';
                })
                ->addColumn('jumlah', function ($row) {
                    $last = MonitoringMakanan::withCount('detail')
                        ->where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    return $last ? $last->detail_count : 0;
                })
                ->addColumn('terakhir', function ($row) {
                    $last = MonitoringMakanan::where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    return $last
                        ? \Carbon\Carbon::parse($last->tanggal)->format('d-m-Y')
                        : '-';
                })
                ->addColumn('kalori', function ($row) {

                    $last = MonitoringMakanan::where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();

                    if (!$last) {
                        return '-';
                    }

                    $status = $this->getStatusKaloriPersonal(
                        $last->total_kalori,
                        $row->id
                    );

                    return '
                        <div class="text-center">
                            <span class="badge bg-' . $status['badge'] . '">
                                ' . number_format($last->total_kalori, 0) . ' Kkal
                            </span>
                            <br>
                            <small class="text-muted">
                                ' . $status['persen'] . '%
                            </small>
                            <br>
                            <small>
                                ' . $status['label'] . '
                            </small>
                        </div>
                    ';
                })

                ->addColumn('status', function ($row) {

                    $last = MonitoringMakanan::where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();

                    if (!$last) {
                        return '<span class="badge bg-secondary">Belum Ada Data</span>';
                    }

                    $status = $this->getStatusKaloriPersonal(
                        $last->total_kalori,
                        $row->id
                    );

                    $color = match ($status['badge']) {
                        'success' => 'bg-success',
                        'warning' => 'bg-warning text-dark',
                        'danger' => 'bg-danger',
                        default => 'bg-secondary'
                    };

                    return '<span class="badge ' . $color . '">' . $status['label'] . '</span>';
                })

                ->addColumn('petugas', function ($row) {
                    $last = MonitoringMakanan::with('petugas')
                        ->where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    return $last->petugas
                        ? '<span class="badge bg-primary">' . e($last->petugas->nama) . '</span>'
                        : '<span class="badge bg-secondary">Mandiri (Pasien)</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="dropdown">
                        <button
                            class="btn btn-link p-0 text-primary"
                            type="button"
                            data-bs-toggle="dropdown">
                            <i class="fa fa-eye" style="font-size:18px;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="' . route('monitoring_makanan.history', $row->id) . '">
                                    <i class="fa fa-history me-2 text-primary"></i>
                                    Riwayat Monitoring
                                </a>
                            </li>
                        </ul>
                    </div>
                    ';
                })
                ->rawColumns(['nama', 'petugas', 'kalori', 'status', 'action'])
                ->make(true);
        }

        return view('backend.monitoring_makanan.index');
    }

    /**
     * Form Tambah Monitoring
     */
    public function create(Request $request)
    {
        $petugas = Petugas::orderBy('nama')->get();

        $masterMakanan = MasterMakanan::where('aktif', 1)
            ->orderBy('nama')
            ->get();

        if (Auth::user()->hasRole('Peserta')) {
            $peserta = Peserta::where('id', Auth::user()->peserta_id)->get();
            $selectedPeserta = Auth::user()->peserta_id;
        } else {
            $peserta = Peserta::orderBy('nama')->get();
            $selectedPeserta = $request->peserta_id;
        }

        return view(
            'backend.monitoring_makanan.create',
            compact('peserta', 'petugas', 'selectedPeserta', 'masterMakanan')
        );
    }

    /**
     * Menyimpan Monitoring Makanan
     */
    public function store(Request $request)
    {
        $isPeserta = Auth::user()->hasRole('Peserta');
        $pesertaId = $isPeserta ? Auth::user()->peserta_id : $request->peserta_id;

        // Jika dipanggil oleh pasien, petugas_id opsional
        $request->validate([
            'peserta_id' => $isPeserta ? 'nullable' : 'required',
            'petugas_id' => $isPeserta ? 'nullable' : 'required',
            'tanggal'    => 'required|date',
        ], [
            'peserta_id.required' => 'Pasien wajib dipilih.',
            'petugas_id.required' => 'Petugas wajib dipilih.',
            'tanggal.required'    => 'Tanggal wajib diisi.',
        ]);

        $cek = MonitoringMakanan::where('peserta_id', $pesertaId)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($cek) {
            return back()
                ->withInput()
                ->with('error', 'Monitoring makanan Anda pada tanggal tersebut sudah dicatat. Silakan edit data yang ada.');
        }

        DB::beginTransaction();

        try {
            $monitoring = MonitoringMakanan::create([
                'peserta_id' => $pesertaId,
                'petugas_id' => $request->petugas_id ?? null,
                'tanggal'    => $request->tanggal,
                'catatan'    => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            if ($request->has('waktu_makan')) {
                foreach ($request->waktu_makan as $key => $value) {
                    if (!empty($request->nama_makanan[$key])) {
                        MonitoringMakananDetail::create([
                            'monitoring_makanan_id' => $monitoring->id,
                            'waktu_makan'           => $request->waktu_makan[$key],
                            'nama_makanan'          => $request->nama_makanan[$key],
                            'jumlah'                => $request->jumlah[$key] ?? 1,
                            'satuan'                => $request->satuan[$key] ?? 'Porsi',
                            'kalori'                => $request->kalori[$key] ?? 0,
                        ]);
                    }
                }
            }

            $this->hitungTotalKalori($monitoring->id);

            DB::commit();

            return redirect()
                ->route('monitoring_makanan.index')
                ->with('success', 'Catatan monitoring makanan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Detail Monitoring
     */
    public function show($id)
    {
        $monitoring = MonitoringMakanan::with([
            'peserta',
            'petugas',
            'detail'
        ])->findOrFail($id);

        if (
            auth()->user()->hasRole('Peserta')
            && $monitoring->peserta_id != auth()->user()->peserta_id
        ) {
            abort(403, 'Akses Ditolak');
        }

        $kebutuhanKalori = $this->hitungKebutuhanKalori(
            $monitoring->peserta_id
        );

        $persenKalori = $kebutuhanKalori > 0
            ? round(($monitoring->total_kalori / $kebutuhanKalori) * 100)
            : 0;

        $selisihKalori = $monitoring->total_kalori - $kebutuhanKalori;

        $saranNutrisi = $this->getSaranNutrisi(
            $monitoring->peserta_id,
            $monitoring->total_kalori
        );

        // PEMERIKSAAN TERAKHIR
        $pemeriksaanTerakhir = Pemeriksaan::where(
            'peserta_id',
            $monitoring->peserta_id
        )
            ->orderByDesc('tanggal')
            ->first();

        if ($pemeriksaanTerakhir) {

            $imt = $pemeriksaanTerakhir->bmi;

            if ($imt < 18.5) {
                $pemeriksaanTerakhir->kategori_imt = 'Berat Badan Kurang';
                $pemeriksaanTerakhir->badge_imt = 'warning text-dark';
            } elseif ($imt < 23) {
                $pemeriksaanTerakhir->kategori_imt = 'Normal';
                $pemeriksaanTerakhir->badge_imt = 'success';
            } elseif ($imt < 25) {
                $pemeriksaanTerakhir->kategori_imt = 'Berisiko Overweight';
                $pemeriksaanTerakhir->badge_imt = 'info';
            } elseif ($imt < 30) {
                $pemeriksaanTerakhir->kategori_imt = 'Obesitas I';
                $pemeriksaanTerakhir->badge_imt = 'warning text-dark';
            } else {
                $pemeriksaanTerakhir->kategori_imt = 'Obesitas II';
                $pemeriksaanTerakhir->badge_imt = 'danger';
            }
        }

        return view(
            'backend.monitoring_makanan.show',
            compact(
                'monitoring',
                'kebutuhanKalori',
                'persenKalori',
                'pemeriksaanTerakhir',
                'saranNutrisi',
                'selisihKalori'
            )
        );
    }

    /**
     * Form Edit Monitoring
     */
    public function edit($id)
    {
        $monitoring = MonitoringMakanan::with([
            'detail',
            'peserta',
            'petugas'
        ])->findOrFail($id);

        if (auth()->user()->hasRole('Peserta') && $monitoring->peserta_id != auth()->user()->peserta_id) {
            abort(403, 'Akses Ditolak');
        }

        $peserta = Peserta::orderBy('nama')->get();
        $petugas = Petugas::orderBy('nama')->get();

        $masterMakanan = MasterMakanan::where('aktif', 1)
            ->orderBy('nama')
            ->get();

        return view(
            'backend.monitoring_makanan.edit',
            compact('monitoring', 'peserta', 'petugas', 'masterMakanan')
        );
    }

    /**
     * Update Monitoring
     */
    public function update(Request $request, $id)
    {
        $monitoring = MonitoringMakanan::findOrFail($id);

        if (auth()->user()->hasRole('Peserta') && $monitoring->peserta_id != auth()->user()->peserta_id) {
            abort(403, 'Akses Ditolak');
        }

        $isPeserta = auth()->user()->hasRole('Peserta');

        $request->validate([
            'peserta_id' => $isPeserta ? 'nullable' : 'required',
            'petugas_id' => $isPeserta ? 'nullable' : 'required',
            'tanggal'    => 'required|date'
        ]);

        $pesertaId = $isPeserta ? $monitoring->peserta_id : $request->peserta_id;

        $cek = MonitoringMakanan::where('peserta_id', $pesertaId)
            ->whereDate('tanggal', $request->tanggal)
            ->where('id', '!=', $id)
            ->first();

        if ($cek) {
            return back()
                ->withInput()
                ->with('error', 'Monitoring makanan pada tanggal tersebut sudah ada.');
        }

        DB::beginTransaction();

        try {
            $monitoring->update([
                'peserta_id' => $pesertaId,
                'petugas_id' => $request->petugas_id ?? $monitoring->petugas_id,
                'tanggal'    => $request->tanggal,
                'catatan'    => $request->catatan,
                'updated_by' => Auth::id()
            ]);

            // Hapus detail lama dan ganti baru
            MonitoringMakananDetail::where('monitoring_makanan_id', $monitoring->id)->delete();

            if ($request->has('waktu_makan')) {
                foreach ($request->waktu_makan as $key => $value) {
                    if (!empty($request->nama_makanan[$key])) {
                        MonitoringMakananDetail::create([
                            'monitoring_makanan_id' => $monitoring->id,
                            'waktu_makan'           => $request->waktu_makan[$key],
                            'nama_makanan'          => $request->nama_makanan[$key],
                            'jumlah'                => $request->jumlah[$key] ?? 1,
                            'satuan'                => $request->satuan[$key] ?? 'Porsi',
                            'kalori'                => $request->kalori[$key] ?? 0,
                        ]);
                    }
                }
            }

            $this->hitungTotalKalori($monitoring->id);

            DB::commit();

            return redirect()
                ->route('monitoring_makanan.index')
                ->with('success', 'Monitoring makanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Data Monitoring
     */
    public function destroy($id)
    {
        $monitoring = MonitoringMakanan::findOrFail($id);

        if (auth()->user()->hasRole('Peserta') && $monitoring->peserta_id != auth()->user()->peserta_id) {
            abort(403, 'Akses Ditolak');
        }

        $monitoring->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Riwayat Monitoring Pasien
     */
    public function history($peserta)
    {
        if (auth()->user()->hasRole('Peserta') && auth()->user()->peserta_id != $peserta) {
            abort(403, 'Akses Ditolak');
        }

        $peserta = Peserta::findOrFail($peserta);

        $riwayat = MonitoringMakanan::with(['petugas', 'detail'])
            ->where('peserta_id', $peserta->id)
            ->orderByDesc('tanggal')
            ->get();

        return view('backend.monitoring_makanan.history', compact('peserta', 'riwayat'));
    }

    /**
     * ==========================================================
     * HALAMAN EVALUASI MONITORING
     * ==========================================================
     */
    public function evaluasi(Request $request, $peserta)
    {
        // Cek hak akses peserta
        if (auth()->user()->hasRole('Peserta') && auth()->user()->peserta_id != $peserta) {
            abort(403, 'Akses Ditolak');
        }

        $peserta = Peserta::with([
            'jenisPenyakit',
            'dokter'
        ])->findOrFail($peserta);

        $query = MonitoringMakanan::with([
            'detail',
            'petugas'
        ])
            ->where('peserta_id', $peserta->id)
            ->orderByDesc('tanggal');

        /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

        if (
            $request->filled('dari') &&
            $request->filled('sampai')
        ) {

            $query->whereBetween('tanggal', [
                $request->dari,
                $request->sampai
            ]);
        } else {

            $jumlah = $request->jumlah ?? 3;

            $query->take($jumlah);
        }

        $monitoring = $query->get();

        $targetKalori = $this->hitungKebutuhanKalori($peserta->id);

        $jumlahSesuai = 0;
        $jumlahKurang = 0;
        $jumlahLebih  = 0;

        foreach ($monitoring as $item) {

            $status = $this->getStatusKaloriPersonal(
                $item->total_kalori,
                $peserta->id
            );

            $item->status_kalori = $status;

            if ($status['badge'] == 'success') {

                $jumlahSesuai++;
            } elseif ($status['badge'] == 'warning') {

                $jumlahKurang++;
            } else {

                $jumlahLebih++;
            }

            $item->persentase = $targetKalori > 0
                ? round(($item->total_kalori / $targetKalori) * 100)
                : 0;
        }

        /*
    |--------------------------------------------------------------------------
    | RINGKASAN
    |--------------------------------------------------------------------------
    */

        $ringkasan = [

            'jumlah_monitoring' => $monitoring->count(),

            'target_kalori' => $targetKalori,

            'total_kalori' => $monitoring->sum('total_kalori'),

            'rata_kalori' => $monitoring->count()
                ? round($monitoring->avg('total_kalori'))
                : 0,

            'kalori_tertinggi' => $monitoring->max('total_kalori'),

            'kalori_terendah' => $monitoring->min('total_kalori'),

            'jumlah_sesuai' => $jumlahSesuai,

            'jumlah_kurang' => $jumlahKurang,

            'jumlah_lebih' => $jumlahLebih,

        ];

        $pemeriksaan = Pemeriksaan::where('peserta_id', $peserta->id)
            ->latest('tanggal')
            ->first();

        return view(
            'backend.monitoring_makanan.evaluasi',
            compact(
                'peserta',
                'monitoring',
                'ringkasan',
                'pemeriksaan'
            )
        );
    }

    /**
     * API Autocomplete Pencarian Makanan di Form Input (Interaktif)
     */
    public function searchMasterMakanan(Request $request)
    {
        $q = trim($request->q);

        if (!$q) {
            return response()->json([]);
        }

        $data = MasterMakanan::where('aktif', 1)
            ->where(function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get(['id', 'nama', 'kategori', 'satuan', 'gram', 'kalori']);

        return response()->json($data);
    }

    /**
     * API Pencarian Peserta (Select2 / Autocomplete)
     */
    public function searchPeserta(Request $request)
    {
        $q = trim($request->q);

        return Peserta::where('nama', 'like', "%{$q}%")
            ->orWhere('no_bpjs', 'like', "%{$q}%")
            ->orWhere('no_rm', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'nama', 'no_rm', 'no_bpjs']);
    }

    /**
     * API Pencarian Petugas
     */
    public function searchPetugas(Request $request)
    {
        $q = trim($request->q);

        return Petugas::where('nama', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'nama', 'nip']);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */
    public function exportPdf($id)
    {
        $monitoring = MonitoringMakanan::with([
            'peserta',
            'petugas',
            'detail'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('backend.monitoring_makanan.pdf', compact('monitoring'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download(
            'Monitoring_Makanan_' . $monitoring->peserta->nama . '_' . $monitoring->tanggal . '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */
    public function exportExcel($id)
    {
        $monitoring = MonitoringMakanan::with([
            'peserta',
            'petugas',
            'detail'
        ])->findOrFail($id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul Laporan
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN MONITORING MAKANAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Biodata Pasien
        $sheet->setCellValue('A3', 'Nama');
        $sheet->setCellValue('B3', $monitoring->peserta->nama);
        $sheet->setCellValue('A4', 'No RM');
        $sheet->setCellValue('B4', $monitoring->peserta->no_rm);
        $sheet->setCellValue('A5', 'No BPJS');
        $sheet->setCellValue('B5', $monitoring->peserta->no_bpjs);
        $sheet->setCellValue('A6', 'Tanggal');
        $sheet->setCellValue('B6', $monitoring->tanggal);
        $sheet->setCellValue('A7', 'Petugas');
        $sheet->setCellValue('B7', $monitoring->petugas->nama ?? 'Mandiri (Pasien)');

        $sheet->getStyle('A3:B7')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Header Tabel
        $row = 9;
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Waktu');
        $sheet->setCellValue('C' . $row, 'Nama Makanan');
        $sheet->setCellValue('D' . $row, 'Jumlah');
        $sheet->setCellValue('E' . $row, 'Satuan');
        $sheet->setCellValue('F' . $row, 'Kalori (Kkal)');

        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Isi Detail Makanan
        $no = 1;
        $row++;
        foreach ($monitoring->detail as $detail) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $detail->waktu_makan);
            $sheet->setCellValue('C' . $row, $detail->nama_makanan);
            $sheet->setCellValue('D' . $row, $detail->jumlah);
            $sheet->setCellValue('E' . $row, $detail->satuan);
            $sheet->setCellValue('F' . $row, $detail->kalori);
            $row++;
        }

        // Total
        $sheet->setCellValue('E' . $row, 'Total Kalori');
        $sheet->setCellValue('F' . $row, $monitoring->total_kalori);

        $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Border Tabel Detail
        $sheet->getStyle("A9:F{$row}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Auto Size Kolom
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Monitoring_Makanan_' . $monitoring->peserta->nama . '_' . $monitoring->tanggal . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    private function hitungTotalKalori($monitoringId)
    {
        $total = MonitoringMakananDetail::where('monitoring_makanan_id', $monitoringId)->sum('kalori');

        MonitoringMakanan::where('id', $monitoringId)->update([
            'total_kalori' => $total
        ]);

        return $total;
    }

    private function getStatusKalori($kalori)
    {
        if ($kalori > 2200) {
            return 'Berlebih';
        }
        if ($kalori < 1800) {
            return 'Kurang';
        }
        return 'Normal';
    }

    private function getBadgeKalori($kalori)
    {
        if ($kalori > 2200) {
            return 'danger';
        }
        if ($kalori < 1800) {
            return 'warning text-dark';
        }
        return 'success';
    }

    private function getStatusKaloriPersonal($asupan, $pesertaId)
    {
        $kebutuhan = $this->hitungKebutuhanKalori($pesertaId);

        if ($kebutuhan <= 0) {
            return [
                'badge' => 'secondary',
                'label' => 'Belum ada data antropometri',
                'persen' => 0
            ];
        }

        $persen = round(($asupan / $kebutuhan) * 100);

        if ($persen < 90) {

            return [
                'badge' => 'warning',
                'label' => 'Asupan Kurang',
                'persen' => $persen
            ];
        }

        if ($persen <= 110) {

            return [
                'badge' => 'success',
                'label' => 'Sesuai Target Diet',
                'persen' => $persen
            ];
        }

        if ($persen <= 130) {

            return [
                'badge' => 'danger',
                'label' => 'Melebihi Target',
                'persen' => $persen
            ];
        }

        return [
            'badge' => 'danger',
            'label' => 'Sangat Melebihi Target',
            'persen' => $persen
        ];
    }

    private function getSaranNutrisi($pesertaId, $asupan)
    {
        $target = $this->hitungKebutuhanKalori($pesertaId);

        if ($target == 0) {
            return "Lengkapi data antropometri terlebih dahulu.";
        }

        if ($asupan > $target + 300) {

            return "Kurangi asupan sekitar "
                . round($asupan - $target)
                . " kkal/hari. Hindari minuman manis, gorengan, makanan tinggi gula dan lemak.";
        }

        if ($asupan < $target - 300) {

            return "Tambahkan sekitar "
                . round($target - $asupan)
                . " kkal/hari dari sumber protein tanpa lemak, buah dan karbohidrat kompleks.";
        }

        return "Asupan sudah sesuai target. Pertahankan pola makan dan aktivitas fisik.";
    }

    private function hitungKebutuhanKalori($pesertaId)
    {
        $pemeriksaan = Pemeriksaan::where('peserta_id', $pesertaId)
            ->latest('tanggal')
            ->first();

        $peserta = Peserta::find($pesertaId);

        if (
            !$pemeriksaan ||
            !$peserta ||
            !$pemeriksaan->berat_badan ||
            !$pemeriksaan->tinggi_badan ||
            !$peserta->tgl_lahir
        ) {
            return 0;
        }

        $bb = $pemeriksaan->berat_badan;
        $tb = $pemeriksaan->tinggi_badan;
        $umur = Carbon::parse($peserta->tgl_lahir)->age;

        // L = Laki-laki
        if ($peserta->jk == 'L') {
            $bmr = (10 * $bb) + (6.25 * $tb) - (5 * $umur) + 5;
        } else {
            $bmr = (10 * $bb) + (6.25 * $tb) - (5 * $umur) - 161;
        }

        // Aktivitas ringan
        $tdee = $bmr * 1.3;

        // Jika overweight/obesitas → defisit kalori
        if ($pemeriksaan->bmi >= 25) {
            $target = $tdee - 500;
        } else {
            $target = $tdee;
        }

        return round($target);
    }
}
