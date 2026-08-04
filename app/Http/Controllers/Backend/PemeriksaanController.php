<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use App\Models\Peserta;
use App\Models\Petugas;
use App\Models\JenisPenyakit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Storage;

class PemeriksaanController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Peserta::with(['jenisPenyakit'])
                ->whereHas('pemeriksaan')
                ->withCount('pemeriksaan')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return '
                        <a href="' . route('pemeriksaan.history', $row->id) . '"
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
                    return $row->pemeriksaan_count . ' Kali';
                })
                ->addColumn('terakhir', function ($row) {
                    $last = Pemeriksaan::where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    return $last
                        ? \Carbon\Carbon::parse($last->tanggal)->format('d-m-Y')
                        : '-';
                })
                ->addColumn('risk', function ($row) {
                    $last = Pemeriksaan::where('peserta_id', $row->id)
                        ->latest('tanggal')
                        ->first();
                    if (!$last) {
                        return '-';
                    }
                    $color = 'success';
                    if ($last->risk_score >= 70) {
                        $color = 'danger';
                    } elseif ($last->risk_score >= 40) {
                        $color = 'warning text-dark';
                    }
                    return '
                        <span class="badge bg-' . $color . '">
                            ' . $last->risk_score . ' (' . $last->risk_level . ')
                        </span>
                    ';
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
                                href="' . route('pemeriksaan.history', $row->id) . '">
                                    <i class="fa fa-history me-2 text-primary"></i>
                                    Riwayat Pemeriksaan
                                </a>
                            </li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['nama', 'risk', 'action'])
                ->make(true);
        }
        return view('backend.pemeriksaan.index');
    }


    public function create(Request $request)
    {
        $peserta = Peserta::orderBy('nama')->get();
        $petugas = Petugas::orderBy('nama')->get();
        $jenisPenyakit = JenisPenyakit::where('status', 1)
            ->orderBy('nama_penyakit')
            ->get();

        $selectedPeserta = $request->peserta_id;

        return view('backend.pemeriksaan.create', compact('peserta', 'petugas', 'jenisPenyakit', 'selectedPeserta'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required',
            'petugas_id' => 'required',
            'tanggal' => 'required',

            'petugas_tambahan' => 'nullable|array',
            'petugas_tambahan.*' => 'nullable|exists:petugas,id',

            'riwayat_penyakit' => 'nullable|string',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        // 🔥 HITUNG SCORE
        $score = $this->calculateRisk($request);
        $level = $this->getRiskLevel($score);
        $breakdown = $this->getRiskBreakdown($request);

        $petugasTambahan = collect($request->petugas_tambahan ?? [])
            ->filter(function ($value) {
                return !is_null($value) && $value !== '';
            })
            ->toArray();
        $dokumen = null;

        if ($request->hasFile('dokumen')) {
            $dokumen = $request
                ->file('dokumen')
                ->store('pemeriksaan', 'public');
        }
        // SIMPAN PENYAKIT BARU KE MASTER JENIS PENYAKIT
        if ($request->filled('riwayat_penyakit')) {

            $riwayat = json_decode($request->riwayat_penyakit, true);

            if (is_array($riwayat)) {

                foreach ($riwayat as &$item) {

                    // Ambil nama penyakit
                    $namaPenyakit = is_array($item)
                        ? ($item['value'] ?? null)
                        : $item;


                    if (!$namaPenyakit) {
                        continue;
                    }


                    // Cek apakah sudah ada di master
                    $penyakit = JenisPenyakit::where(
                        'nama_penyakit',
                        $namaPenyakit
                    )->first();


                    // Kalau belum ada, buat baru
                    if (!$penyakit) {

                        $penyakit = JenisPenyakit::create([
                            'kode' => 'P' . str_pad(
                                JenisPenyakit::count() + 1,
                                4,
                                '0',
                                STR_PAD_LEFT
                            ),

                            'nama_penyakit' => $namaPenyakit,

                            'keterangan' => null,

                            'status' => 1,
                        ]);
                    }


                    // Simpan ID master juga
                    if (is_array($item)) {
                        $item['id'] = $penyakit->id;
                    }
                }


                // Update kembali JSON riwayat penyakit
                $request->merge([
                    'riwayat_penyakit' => json_encode($riwayat)
                ]);
            }
        }

        Pemeriksaan::create([

            // IDENTITAS
            'peserta_id' => $request->peserta_id,
            'petugas_id' => $request->petugas_id,
            'petugas_tambahan' => !empty($petugasTambahan)
                ? $petugasTambahan
                : null,
            'tanggal'    => $request->tanggal,

            // TANDA VITAL
            'suhu'        => $request->suhu,
            'sistol'      => $request->sistol,
            'diastol'     => $request->diastol,
            'nadi'        => $request->nadi,
            'respirasi'   => $request->respirasi,
            'spo2'        => $request->spo2,

            // ANTROPOMETRI
            'berat_badan'   => $request->berat_badan,
            'tinggi_badan'  => $request->tinggi_badan,
            'bmi'           => $request->bmi,
            'lingkar_perut' => $request->lingkar_perut,

            // LABORATORIUM
            'gds'               => $request->gds,
            'gdp'               => $request->gdp,
            'g2jpp'             => $request->g2jpp,
            'hba1c'             => $request->hba1c,

            'kolesterol_total'  => $request->kolesterol_total,
            'ldl'               => $request->ldl,
            'hdl'               => $request->hdl,
            'trigliserida'      => $request->trigliserida,

            'ureum'             => $request->ureum,
            'kreatinin'         => $request->kreatinin,
            'egfr'              => $request->egfr,
            'asam_urat'         => $request->asam_urat,

            // ANAMNESIS
            'keluhan_utama'          => $request->keluhan_utama,
            'hamil'                  => $request->has('hamil'),
            'menyusui'               => $request->has('menyusui'),
            'status_perokok'         => $request->status_perokok,

            // sementara disimpan JSON
            'riwayat_penyakit' => $request->riwayat_penyakit,

            'riwayat_alergi_obat'    => $request->riwayat_alergi_obat,
            'riwayat_alergi_lainnya' => $request->riwayat_alergi_lainnya,
            'obat_dikonsumsi'        => $request->obat_dikonsumsi,

            // CATATAN PROFESIONAL
            'catatan_dokter' => $request->catatan_dokter,
            'catatan_gizi' => $request->catatan_gizi,
            'aktivitas_fisik' => $request->aktivitas_fisik,
            'catatan' => $request->catatan,

            // DOKUMEN
            'dokumen' => $dokumen,

            // RISK
            'risk_score'      => $score,
            'risk_level'      => $level,
            'risk_breakdown'  => json_encode($breakdown),

        ]);

        return redirect()
            ->route('pemeriksaan.index')
            ->with('success', 'Data pemeriksaan berhasil disimpan.');
    }


    public function edit($id)
    {
        $pemeriksaan = Pemeriksaan::findOrFail($id);
        $peserta = Peserta::orderBy('nama')->get();
        $petugas = Petugas::orderBy('nama')->get();
        $petugasTambahan = $pemeriksaan->petugas_tambahan ?? [];
        $jenisPenyakit = JenisPenyakit::where('status', 1)
            ->orderBy('nama_penyakit')
            ->get();

        return view('backend.pemeriksaan.edit', compact('pemeriksaan', 'peserta', 'petugas', 'jenisPenyakit', 'petugasTambahan'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'peserta_id' => 'required',
            'petugas_id' => 'required',
            'tanggal'    => 'required',

            'petugas_tambahan' => 'nullable|array',
            'petugas_tambahan.*' => 'nullable|exists:petugas,id',

            'riwayat_penyakit' => 'nullable|string',
            'dokumen'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $pemeriksaan = Pemeriksaan::findOrFail($id);

        // Upload dokumen baru
        if ($request->hasFile('dokumen')) {

            if (
                $pemeriksaan->dokumen &&
                Storage::disk('public')->exists($pemeriksaan->dokumen)
            ) {

                Storage::disk('public')->delete($pemeriksaan->dokumen);
            }

            $pemeriksaan->dokumen = $request
                ->file('dokumen')
                ->store('pemeriksaan', 'public');
        }

        // Hitung ulang risk
        $score = $this->calculateRisk($request);
        $level = $this->getRiskLevel($score);
        $breakdown = $this->getRiskBreakdown($request);

        $petugasTambahan = collect($request->petugas_tambahan ?? [])
            ->filter(function ($value) {
                return !is_null($value) && $value !== '';
            })
            ->toArray();

        // SIMPAN PENYAKIT BARU KE MASTER JENIS PENYAKIT
        if ($request->filled('riwayat_penyakit')) {

            $riwayat = json_decode($request->riwayat_penyakit, true);

            if (is_array($riwayat)) {

                foreach ($riwayat as &$item) {

                    // Ambil nama penyakit
                    $namaPenyakit = is_array($item)
                        ? ($item['value'] ?? null)
                        : $item;


                    if (!$namaPenyakit) {
                        continue;
                    }


                    // Cek apakah sudah ada di master
                    $penyakit = JenisPenyakit::where(
                        'nama_penyakit',
                        $namaPenyakit
                    )->first();


                    // Kalau belum ada, buat baru
                    if (!$penyakit) {

                        $penyakit = JenisPenyakit::create([
                            'kode' => 'P' . str_pad(
                                JenisPenyakit::count() + 1,
                                4,
                                '0',
                                STR_PAD_LEFT
                            ),

                            'nama_penyakit' => $namaPenyakit,

                            'keterangan' => null,

                            'status' => 1,
                        ]);
                    }


                    // Simpan ID master juga
                    if (is_array($item)) {
                        $item['id'] = $penyakit->id;
                    }
                }


                // Update kembali JSON riwayat penyakit
                $request->merge([
                    'riwayat_penyakit' => $riwayat
                ]);
            }
        }

        $pemeriksaan->update([

            // IDENTITAS
            'peserta_id' => $request->peserta_id,
            'petugas_id' => $request->petugas_id,
            'petugas_tambahan' => !empty($petugasTambahan)
                ? $petugasTambahan
                : null,
            'tanggal'    => $request->tanggal,

            // TANDA VITAL
            'suhu'        => $request->suhu,
            'sistol'      => $request->sistol,
            'diastol'     => $request->diastol,
            'nadi'        => $request->nadi,
            'respirasi'   => $request->respirasi,
            'spo2'        => $request->spo2,

            // ANTROPOMETRI
            'berat_badan'   => $request->berat_badan,
            'tinggi_badan'  => $request->tinggi_badan,
            'bmi'           => $request->bmi,
            'lingkar_perut' => $request->lingkar_perut,

            // LABORATORIUM
            'gds'               => $request->gds,
            'gdp'               => $request->gdp,
            'g2jpp'             => $request->g2jpp,
            'hba1c'             => $request->hba1c,

            'kolesterol_total'  => $request->kolesterol_total,
            'ldl'               => $request->ldl,
            'hdl'               => $request->hdl,
            'trigliserida'      => $request->trigliserida,

            'ureum'             => $request->ureum,
            'kreatinin'         => $request->kreatinin,
            'egfr'              => $request->egfr,
            'asam_urat'         => $request->asam_urat,

            // ANAMNESIS
            'keluhan_utama'          => $request->keluhan_utama,
            'hamil'                  => $request->has('hamil'),
            'menyusui'               => $request->has('menyusui'),
            'status_perokok'         => $request->status_perokok,

            'riwayat_penyakit' => $request->riwayat_penyakit,

            'riwayat_alergi_obat'    => $request->riwayat_alergi_obat,
            'riwayat_alergi_lainnya' => $request->riwayat_alergi_lainnya,
            'obat_dikonsumsi'        => $request->obat_dikonsumsi,

            // CATATAN PROFESIONAL
            'catatan_dokter' => $request->catatan_dokter,
            'catatan_gizi' => $request->catatan_gizi,
            'aktivitas_fisik' => $request->aktivitas_fisik,
            'catatan' => $request->catatan,

            // DOKUMEN
            'dokumen' => $pemeriksaan->dokumen,

            // RISK
            'risk_score'      => $score,
            'risk_level'      => $level,
            'risk_breakdown'  => json_encode($breakdown),

        ]);

        return redirect()
            ->route('pemeriksaan.index')
            ->with('success', 'Data berhasil diupdate.');
    }


    public function destroy($id)
    {
        Pemeriksaan::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    public function searchPeserta(Request $request)
    {
        $q = $request->q;

        return Peserta::where('nama', 'like', "%$q%")
            ->orWhere('no_bpjs', 'like', "%$q%")
            ->limit(10)
            ->get([
                'id',
                'nama',
                'no_bpjs'
            ]);
    }

    public function searchPetugas(Request $request)
    {
        $q = $request->q;

        return Petugas::where('nama', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->limit(10)
            ->get([
                'id',
                'nama',
                'nip'
            ]);
    }


    // =========================
    // 🔥 RISK FUNCTIONS
    // =========================

    private function calculateRisk($r)
    {
        $score = 0;

        // VITAL
        if ($r->sistol >= 140 || $r->diastol >= 90)
            $score += 25;
        elseif ($r->sistol >= 130 || $r->diastol >= 85)
            $score += 15;

        if ($r->bmi >= 30)
            $score += 20;
        elseif ($r->bmi >= 25)
            $score += 10;

        if ($r->spo2 && $r->spo2 < 92)
            $score += 15;

        if ($r->nadi && ($r->nadi > 100 || $r->nadi < 60))
            $score += 10;

        // GLIKEMIK
        if ($r->gdp >= 126)
            $score += 20;

        if ($r->hba1c >= 6.5)
            $score += 25;

        if ($r->gds >= 200)
            $score += 20;

        if ($r->g2jpp >= 200)
            $score += 15;

        // LIPID
        if ($r->ldl >= 160)
            $score += 20;
        elseif ($r->ldl >= 100)
            $score += 10;

        if ($r->hdl && $r->hdl < 40)
            $score += 15;

        if ($r->trigliserida >= 200)
            $score += 20;
        elseif ($r->trigliserida >= 150)
            $score += 10;

        // GINJAL
        if ($r->egfr && $r->egfr < 30)
            $score += 30;
        elseif ($r->egfr && $r->egfr < 60)
            $score += 15;

        if ($r->kreatinin && $r->kreatinin > 1.3)
            $score += 15;

        // ASAM URAT
        if ($r->asam_urat > 9)
            $score += 20;
        elseif ($r->asam_urat > 7)
            $score += 10;

        return min($score, 100);
    }


    private function getRiskLevel($score)
    {
        if ($score >= 70) return 'Tinggi';
        if ($score >= 40) return 'Sedang';
        return 'Rendah';
    }


    private function getRiskBreakdown($r)
    {
        return [
            'vital' => ($r->sistol >= 140 || $r->diastol >= 90) ? 25 : 0,
            'bmi' => ($r->bmi >= 30) ? 20 : (($r->bmi >= 25) ? 10 : 0),
            'glikemik' => ($r->gdp >= 126 || $r->hba1c >= 6.5) ? 30 : 0,
            'lipid' => ($r->ldl >= 160 || $r->trigliserida >= 200) ? 20 : 0,
            'ginjal' => ($r->egfr < 60) ? 20 : 0,
        ];
    }

    public function show($id)
    {
        $pemeriksaan = Pemeriksaan::with([
            'peserta',
            'petugas'
        ])->findOrFail($id);

        $pemeriksaan->riwayat_penyakit =
            collect($pemeriksaan->riwayat_penyakit ?? [])
            ->map(function ($item) {
                return is_array($item)
                    ? ($item['value'] ?? '')
                    : $item;
            })
            ->filter()
            ->values()
            ->toArray();

        return view(
            'backend.pemeriksaan.show',
            compact('pemeriksaan')
        );
    }

    public function history($peserta)
    {
        $peserta = Peserta::findOrFail($peserta);

        $riwayat = Pemeriksaan::with('petugas')
            ->where('peserta_id', $peserta->id)
            ->orderByDesc('tanggal')
            ->get();

        return view(
            'backend.pemeriksaan.history',
            compact(
                'peserta',
                'riwayat'
            )
        );
    }
    /*
|--------------------------------------------------------------------------
| EXPORT PDF
|--------------------------------------------------------------------------
*/

    public function exportPdf($id)
    {
        $pemeriksaan = Pemeriksaan::with([
            'peserta',
            'petugas',
        ])->findOrFail($id);

        // Jika role pasien hanya boleh melihat miliknya sendiri
        if (
            auth()->user()->hasRole('Pasien') &&
            $pemeriksaan->peserta_id != auth()->user()->peserta_id
        ) {
            abort(403);
        }

        $pdf = Pdf::loadView(
            'backend.pemeriksaan.pdf',
            compact('pemeriksaan')
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download(
            'Pemeriksaan_' .
                str_replace(' ', '_', $pemeriksaan->peserta->nama) .
                '_' .
                \Carbon\Carbon::parse($pemeriksaan->tanggal)->format('Y-m-d') .
                '.pdf'
        );
    }
    /*
|--------------------------------------------------------------------------
| EXPORT EXCEL
|--------------------------------------------------------------------------
*/

    public function exportExcel($id)
    {
        $pemeriksaan = Pemeriksaan::with([
            'peserta',
            'petugas'
        ])->findOrFail($id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /*
    |--------------------------------------------------------------------------
    | JUDUL
    |--------------------------------------------------------------------------
    */

        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', 'LAPORAN HASIL PEMERIKSAAN PROLANIS');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | RIWAYAT PENYAKIT
    |--------------------------------------------------------------------------
    */

        $riwayat = '-';

        if (!empty($pemeriksaan->riwayat_penyakit)) {

            $riwayat = collect($pemeriksaan->riwayat_penyakit)
                ->map(function ($item) {

                    if (is_array($item)) {
                        return $item['value'] ?? '';
                    }

                    return $item;
                })
                ->filter()
                ->implode(', ');
        }

        /*
    |--------------------------------------------------------------------------
    | PETUGAS TAMBAHAN
    |--------------------------------------------------------------------------
    */

        $petugasTambahan = '-';

        if (!empty($pemeriksaan->petugas_tambahan)) {

            $petugasTambahan = Petugas::whereIn(
                'id',
                $pemeriksaan->petugas_tambahan
            )->pluck('nama')->implode(', ');
        }

        /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

        $row = 3;

        $data = [

            'Nama Peserta'            => $pemeriksaan->peserta->nama,
            'No RM'                   => $pemeriksaan->peserta->no_rm,
            'No BPJS'                 => $pemeriksaan->peserta->no_bpjs,
            'Tanggal Pemeriksaan'     => $pemeriksaan->tanggal->format('d-m-Y'),

            'Petugas Pemeriksa'       => $pemeriksaan->petugas->nama ?? '-',
            'Petugas Tambahan'        => $petugasTambahan,

            '' => '',

            'Keluhan Utama'           => $pemeriksaan->keluhan_utama,
            'Status Perokok'          => $pemeriksaan->status_perokok,
            'Hamil'                   => $pemeriksaan->hamil ? 'Ya' : 'Tidak',
            'Menyusui'                => $pemeriksaan->menyusui ? 'Ya' : 'Tidak',

            'Riwayat Penyakit'        => $riwayat,
            'Alergi Obat'             => $pemeriksaan->riwayat_alergi_obat,
            'Alergi Lainnya'          => $pemeriksaan->riwayat_alergi_lainnya,
            'Obat Dikonsumsi'         => $pemeriksaan->obat_dikonsumsi,

            '' => '',

            'Suhu'                    => $pemeriksaan->suhu,
            'Sistol'                  => $pemeriksaan->sistol,
            'Diastol'                 => $pemeriksaan->diastol,
            'Nadi'                    => $pemeriksaan->nadi,
            'Respirasi'               => $pemeriksaan->respirasi,
            'SpO2'                    => $pemeriksaan->spo2,

            '' => '',

            'Berat Badan'             => $pemeriksaan->berat_badan,
            'Tinggi Badan'            => $pemeriksaan->tinggi_badan,
            'BMI'                     => $pemeriksaan->bmi,
            'Lingkar Perut'           => $pemeriksaan->lingkar_perut,

            '' => '',

            'GDS'                     => $pemeriksaan->gds,
            'GDP'                     => $pemeriksaan->gdp,
            'G2JPP'                   => $pemeriksaan->g2jpp,
            'HbA1c'                   => $pemeriksaan->hba1c,

            'Kolesterol Total'        => $pemeriksaan->kolesterol_total,
            'LDL'                     => $pemeriksaan->ldl,
            'HDL'                     => $pemeriksaan->hdl,
            'Trigliserida'            => $pemeriksaan->trigliserida,

            'Ureum'                   => $pemeriksaan->ureum,
            'Kreatinin'               => $pemeriksaan->kreatinin,
            'eGFR'                    => $pemeriksaan->egfr,
            'Asam Urat'               => $pemeriksaan->asam_urat,

            '' => '',

            'Kepatuhan Minum Obat'    => $pemeriksaan->kepatuhan,

            'Catatan Dokter'          => $pemeriksaan->catatan_dokter,
            'Catatan Gizi'            => $pemeriksaan->catatan_gizi,
            'Exercise Prescription'   => $pemeriksaan->aktivitas_fisik,
            'Catatan Tambahan'        => $pemeriksaan->catatan,

            '' => '',

            'Risk Score'              => $pemeriksaan->risk_score,
            'Risk Level'              => $pemeriksaan->risk_level,
        ];

        foreach ($data as $label => $value) {

            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);

            $row++;
        }

        /*
    |--------------------------------------------------------------------------
    | STYLE
    |--------------------------------------------------------------------------
    */

        $sheet->getStyle("A3:B{$row}")
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

        $sheet->getStyle("A3:A{$row}")
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'D9EAD3',
                    ],
                ],
            ]);

        foreach (range('A', 'B') as $column) {

            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        /*
    |--------------------------------------------------------------------------
    | DOWNLOAD
    |--------------------------------------------------------------------------
    */

        $writer = new Xlsx($spreadsheet);

        $filename =
            'Pemeriksaan_' .
            str_replace(' ', '_', $pemeriksaan->peserta->nama) .
            '_' .
            $pemeriksaan->tanggal->format('Y-m-d') .
            '.xlsx';

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
    
}
