<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Peserta;
use App\Models\Pemeriksaan;
use App\Models\MonitoringMakanan;
use App\Models\Bouchard;
use App\Models\Dokter;
use App\Models\JenisPenyakit;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // CARD STATISTIK
        $tanggal = $request->tanggal
            ? Carbon::parse($request->tanggal)->toDateString()
            : Carbon::today()->toDateString();
        // Total Peserta
        $totalPeserta = Peserta::count();

        // Total Pemeriksaan
        $totalPemeriksaan = Pemeriksaan::count();

        // Total Monitoring Makanan
        $totalMonitoring = MonitoringMakanan::count();

        // Total Kuisioner Bouchard
        $totalBouchard = Bouchard::count();

        // Total Dokter Aktif
        $totalDokter = Dokter::where('status', 1)->count();

        // Total Jenis Penyakit
        $totalJenisPenyakit = JenisPenyakit::count();

        // Total Risiko Tinggi
        $risikoTinggi = Pemeriksaan::where('risk_level', 'Tinggi')->count();

        /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI RISIKO
        |--------------------------------------------------------------------------
        */

        $risiko = Pemeriksaan::selectRaw('risk_level, COUNT(*) as total')
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level');

        $risikoLabels = [
            'Rendah',
            'Sedang',
            'Tinggi'
        ];

        $risikoData = [
            $risiko['Rendah'] ?? 0,
            $risiko['Sedang'] ?? 0,
            $risiko['Tinggi'] ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | TREN PEMERIKSAAN 7 HARI
        |--------------------------------------------------------------------------
        */

        $days = collect();

        for ($i = 6; $i >= 0; $i--) {

            $days->push(
                Carbon::today()->subDays($i)->toDateString()
            );
        }

        $perHari = Pemeriksaan::selectRaw('DATE(tanggal) as tgl, COUNT(*) as total')
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $labelTanggal = $days->map(function ($tgl) {

            return Carbon::parse($tgl)->format('d M');
        })->toArray();

        $dataJumlah = $days->map(function ($tgl) use ($perHari) {

            return $perHari[$tgl] ?? 0;
        })->toArray();

        
// MONITORING HARIAN PESERTA

$today = $tanggal;

// Query peserta
$queryPeserta = Peserta::with('jenisPenyakit');

// Jika login sebagai peserta, tampilkan hanya dirinya sendiri
if (Auth::user()->peserta_id != null) {
    $queryPeserta->where('id', Auth::user()->peserta_id);
}

$monitoringHarian = $queryPeserta
    ->orderBy('nama')
    ->get()
    ->map(function ($peserta) use ($today) {

        $makanan = MonitoringMakanan::where('peserta_id', $peserta->id)
            ->whereDate('tanggal', $today)
            ->exists();

        $aktivitas = Bouchard::where('peserta_id', $peserta->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($makanan && $aktivitas) {
            $status = 'Lengkap';
        } elseif ($makanan || $aktivitas) {
            $status = 'Belum Lengkap';
        } else {
            $status = 'Belum Dipantau';
        }

        return (object)[
            'id' => $peserta->id,
            'nama' => $peserta->nama,
            'jenis_penyakit' => optional($peserta->jenisPenyakit)->nama_penyakit,
            'makanan' => $makanan,
            'aktivitas' => $aktivitas,
            'status' => $status,
        ];
    });

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('backend.dasboard.index', compact(

            // Card Statistik
            'totalPeserta',
            'totalPemeriksaan',
            'totalMonitoring',
            'totalBouchard',
            'totalDokter',
            'totalJenisPenyakit',
            'risikoTinggi',

            // Pie Chart
            'risikoLabels',
            'risikoData',

            // Bar Chart
            'labelTanggal',
            'dataJumlah',

            // Monitoring Harian
            'monitoringHarian',

            // Tanggal monitoring
            'tanggal'

        ));
    }
}
