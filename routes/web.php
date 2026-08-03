<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\UnitLayananController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\PesertaController;
use App\Http\Controllers\Backend\PemeriksaanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend\BouchardController;
use App\Http\Controllers\Backend\DokterController;
use App\Http\Controllers\Backend\HomeVisitController;
use App\Http\Controllers\Backend\JenisPenyakitController;
use App\Http\Controllers\Backend\MasterMakananController;
use App\Http\Controllers\Backend\MonitoringMakananController;
use App\Http\Controllers\Backend\PasienController;
use App\Http\Controllers\Backend\PetugasController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\UserController;
// use App\Http\Controllers\LandingPageController;

Route::redirect('/', '/login');

// Autentikasi
Route::get('login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Backend Group
Route::prefix('backend')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('backend.dashboard');

    // Generate QR Code
    Route::post('/qr-code/store', [DashboardController::class, 'store'])->name('qrCode.store');


    /** Role Permission */
    Route::resource('roles', RoleController::class);

    // User
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // Unit Layanan
    Route::prefix('unit_layanan')->group(function () {
        Route::get('/', [UnitLayananController::class, 'index'])->name('unit_layanan.index');
        Route::get('/create', [UnitLayananController::class, 'create'])->name('unit_layanan.create');
        Route::post('/', [UnitLayananController::class, 'store'])->name('unit_layanan.store');
        Route::get('/{unit_layanan}/edit', [UnitLayananController::class, 'edit'])->name('unit_layanan.edit');
        Route::put('/{unit_layanan}', [UnitLayananController::class, 'update'])->name('unit_layanan.update');
        Route::delete('/{unit_layanan}', [UnitLayananController::class, 'destroy'])->name('unit_layanan.destroy');
    });

    // Data Dokter
    Route::prefix('dokter')->group(function () {
        Route::get('/', [DokterController::class, 'index'])->name('dokter.index');
        Route::get('/create', [DokterController::class, 'create'])->name('dokter.create');
        Route::post('/', [DokterController::class, 'store'])->name('dokter.store');
        Route::get('/{id}/edit', [DokterController::class, 'edit'])->name('dokter.edit');
        Route::put('/{id}', [DokterController::class, 'update'])->name('dokter.update');
        Route::get('/{id}', [DokterController::class, 'show'])->name('dokter.show');
        Route::delete('/{id}', [DokterController::class, 'destroy'])->name('dokter.destroy');
    });

    // Data Petugas
    Route::prefix('petugas')->group(function () {
        Route::get('/', [PetugasController::class, 'index'])->name('petugas.index');
        Route::get('/create', [PetugasController::class, 'create'])->name('petugas.create');
        Route::post('/', [PetugasController::class, 'store'])->name('petugas.store');
        Route::get('/{id}/edit', [PetugasController::class, 'edit'])->name('petugas.edit');
        Route::put('/{id}', [PetugasController::class, 'update'])->name('petugas.update');
        Route::get('/{id}', [PetugasController::class, 'show'])->name('petugas.show');
        Route::delete('/{id}', [PetugasController::class, 'destroy'])->name('petugas.destroy');
    });

    // Data Jenis Penyakit
    Route::prefix('jenis_penyakit')->group(function () {
        Route::get('/', [JenisPenyakitController::class, 'index'])->name('jenis_penyakit.index');
        Route::get('/create', [JenisPenyakitController::class, 'create'])->name('jenis_penyakit.create');
        Route::post('/', [JenisPenyakitController::class, 'store'])->name('jenis_penyakit.store');
        Route::get('/{id}/edit', [JenisPenyakitController::class, 'edit'])->name('jenis_penyakit.edit');
        Route::put('/{id}', [JenisPenyakitController::class, 'update'])->name('jenis_penyakit.update');
        Route::get('/{id}', [JenisPenyakitController::class, 'show'])->name('jenis_penyakit.show');
        Route::delete('/{id}', [JenisPenyakitController::class, 'destroy'])->name('jenis_penyakit.destroy');
    });

    // Data Master Makanan
    Route::prefix('master_makanan')->group(function () {
        Route::get('/', [MasterMakananController::class, 'index'])->name('master_makanan.index');
        Route::get('/create', [MasterMakananController::class, 'create'])->name('master_makanan.create');
        Route::post('/', [MasterMakananController::class, 'store'])->name('master_makanan.store');

        // Route Pencarian API
        Route::get('/search-api', [MasterMakananController::class, 'searchApi'])->name('master_makanan.search_api');
        Route::post('/master_makanan/import-excel', [App\Http\Controllers\Backend\MasterMakananController::class, 'importExcel'])->name('master_makanan.import_excel');

        Route::get('/{id}/edit', [MasterMakananController::class, 'edit'])->name('master_makanan.edit');
        Route::put('/{id}', [MasterMakananController::class, 'update'])->name('master_makanan.update');
        Route::get('/{id}', [MasterMakananController::class, 'show'])->name('master_makanan.show');
        Route::delete('/{id}', [MasterMakananController::class, 'destroy'])->name('master_makanan.destroy');
    });

    // Data Peserta
    Route::prefix('peserta')->group(function () {
        Route::get('/', [PesertaController::class, 'index'])->name('peserta.index');
        Route::get('/create', [PesertaController::class, 'create'])->name('peserta.create');
        Route::post('/', [PesertaController::class, 'store'])->name('peserta.store');
        Route::get('/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');
        Route::put('/{id}', [PesertaController::class, 'update'])->name('peserta.update');
        Route::get('/{id}', [PesertaController::class, 'show'])->name('peserta.show');
        Route::delete('/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');
    });

    // Data Pemeriksaan
    Route::prefix('pemeriksaan')->group(function () {
        Route::get('/', [PemeriksaanController::class, 'index'])->name('pemeriksaan.index');
        Route::get('/create', [PemeriksaanController::class, 'create'])->name('pemeriksaan.create');
        Route::post('/', [PemeriksaanController::class, 'store'])->name('pemeriksaan.store');
        Route::get('/{id}/edit', [PemeriksaanController::class, 'edit'])->name('pemeriksaan.edit');
        Route::put('/{id}', [PemeriksaanController::class, 'update'])->name('pemeriksaan.update');
        Route::get('/history/{peserta}', [PemeriksaanController::class, 'history'])->name('pemeriksaan.history');
        Route::get('/{id}', [PemeriksaanController::class, 'show'])->name('pemeriksaan.show');
        Route::delete('/{id}', [PemeriksaanController::class, 'destroy'])->name('pemeriksaan.destroy');
        Route::get('/pemeriksaan/{id}/pdf', [PemeriksaanController::class, 'exportPdf'])->name('pemeriksaan.pdf');
        Route::get('/pemeriksaan/{id}/excel', [PemeriksaanController::class, 'exportExcel'])->name('pemeriksaan.excel');
    });

    // Data Home Visit
    Route::prefix('homevisit')->group(function () {
        Route::get('/', [HomeVisitController::class, 'index'])->name('home_visit.index');
        Route::get('/create', [HomeVisitController::class, 'create'])->name('home_visit.create');
        Route::post('/', [HomeVisitController::class, 'store'])->name('home_visit.store');
        Route::get('/{id}/edit', [HomeVisitController::class, 'edit'])->name('home_visit.edit');
        Route::put('/{id}', [HomeVisitController::class, 'update'])->name('home_visit.update');
        Route::get('/{id}', [HomeVisitController::class, 'show'])->name('home_visit.show');
        Route::delete('/{id}', [HomeVisitController::class, 'destroy'])->name('home_visit.destroy');
    });



    // Data Monitoring Makanan
    Route::prefix('monitoring_makanan')->group(function () {
        Route::get('/', [MonitoringMakananController::class, 'index'])->name('monitoring_makanan.index');
        Route::get('/create', [MonitoringMakananController::class, 'create'])->name('monitoring_makanan.create');
        Route::post('/', [MonitoringMakananController::class, 'store'])->name('monitoring_makanan.store');
        Route::get('/{id}/edit', [MonitoringMakananController::class, 'edit'])->name('monitoring_makanan.edit');
        Route::put('/{id}', [MonitoringMakananController::class, 'update'])->name('monitoring_makanan.update');
        Route::get('/history/{peserta}', [MonitoringMakananController::class, 'history'])->name('monitoring_makanan.history');
        Route::get('/{id}', [MonitoringMakananController::class, 'show'])->name('monitoring_makanan.show');
        Route::delete('/{id}', [MonitoringMakananController::class, 'destroy'])->name('monitoring_makanan.destroy');
        Route::get(
    '/monitoring-makanan/{peserta}/evaluasi',
    [MonitoringMakananController::class, 'evaluasi']
)->name('monitoring_makanan.evaluasi');
        Route::get('monitoring-makanan/{id}/export-pdf', [MonitoringMakananController::class, 'exportPdf'])->name('monitoring_makanan.export.pdf');

        Route::get('monitoring-makanan/{id}/export-excel', [MonitoringMakananController::class, 'exportExcel'])->name('monitoring_makanan.export.excel');
    });

    // Data Monitoring AKtivitas
    Route::prefix('bouchard')->group(function () {
        Route::get('/', [BouchardController::class, 'index'])->name('bouchard.index');
        Route::get('/create', [BouchardController::class, 'create'])->name('bouchard.create');
        Route::post('/', [BouchardController::class, 'store'])->name('bouchard.store');
        Route::get('/{id}/edit', [BouchardController::class, 'edit'])->name('bouchard.edit');
        Route::put('/{id}', [BouchardController::class, 'update'])->name('bouchard.update');
        Route::get('/history/{peserta}', [BouchardController::class, 'history'])->name('bouchard.history');
        Route::get('/{id}', [BouchardController::class, 'show'])->name('bouchard.show');
        Route::delete('/{id}', [BouchardController::class, 'destroy'])->name('bouchard.destroy');
        Route::get('/bouchard/{id}/export-pdf', [BouchardController::class, 'exportPdf'])->name('bouchard.export.pdf');
        Route::get('/bouchard/{id}/export-excel', [BouchardController::class, 'exportExcel'])->name('bouchard.export.excel');
    });

    // Data Pasien
    Route::prefix('pasien')->group(function () {
        Route::get('/', [PasienController::class, 'index'])->name('pasien.index');
        Route::get('/create', [PasienController::class, 'create'])->name('pasien.create');
        Route::post('/', [PasienController::class, 'store'])->name('pasien.store');
        Route::get('/{id}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
        Route::put('/{id}', [PasienController::class, 'update'])->name('pasien.update');
        Route::get('/{id}', [PasienController::class, 'show'])->name('pasien.show');
        Route::delete('/{id}', [PasienController::class, 'destroy'])->name('pasien.destroy');
    });



    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
});
