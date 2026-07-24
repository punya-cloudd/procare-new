<!-- Sidebar -->
<div class="sidebar" data-background-color="white">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header">
            <a href="{{ route('backend.dashboard') }}" class="logo">
                <img src="{{ url('backend/assets/img/kaiadmin/LOGO3.png') }}" alt="navbar brand" class="navbar-brand"
                    height="215" />
            </a>

            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ Route::currentRouteName() == 'backend.dashboard' ? 'active' : '' }}">
                    <a href="{{ route('backend.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @can('menu-data-master')
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#dataMaster" role="button"
                    aria-expanded="false" aria-controls="dataMaster">

                        <i class="fas fa-database"></i>
                        <p>Data Master</p>
                        <span class="caret"></span>
                    </a>

                    <div class="collapse {{ 
                        Route::is('dokter.*') || 
                        Route::is('petugas.*') || 
                        Route::is('jenis_penyakit.*') 
                        ? 'show' : '' 
                    }}" id="dataMaster">

                        <ul class="nav nav-collapse">

                            @can('dokter-list')
                            <li class="{{ Route::is('dokter.*') ? 'active' : '' }}">
                                <a href="{{ route('dokter.index') }}">
                                    <span class="sub-item">Data Dokter</span>
                                </a>
                            </li>
                            @endcan

                            @can('petugas-list')
                            <li class="{{ Route::is('petugas.*') ? 'active' : '' }}">
                                <a href="{{ route('petugas.index') }}">
                                    <span class="sub-item">Data Petugas</span>
                                </a>
                            </li>
                            @endcan

                            @can('jenis-penyakit-list')
                            <li class="{{ Route::is('jenis_penyakit.*') ? 'active' : '' }}">
                                <a href="{{ route('jenis_penyakit.index') }}">
                                    <span class="sub-item">Data Jenis Penyakit</span>
                                </a>
                            </li>
                            @endcan

                            @can('master-makanan-list')
                            <li class="{{ Route::is('master_makanan.*') ? 'active' : '' }}">
                                <a href="{{ route('master_makanan.index') }}">
                                    <span class="sub-item">Data Master Makanan</span>
                                </a>
                            </li>
                            @endcan

                            @can('unit-layanan-list')
                            <li class="{{ Route::is('unit_layanan.*') ? 'active' : '' }}">
                                <a href="{{ route('unit_layanan.index') }}">
                                    <span class="sub-item">Data Peran</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                        </div>
                </li>
                @endcan 

                @can('peserta-list')
                <li class="nav-item {{ Route::currentRouteName() == 'peserta.index' ? 'active' : '' }}">
                    <a href="{{ route('peserta.index') }}">
                        <i class="fas fa-dot-circle"></i>
                        <p>Data Pasien</p>
                    </a>
                </li>
                @endcan

                {{-- @can('pasien-list')
                <li class="nav-item {{ Route::currentRouteName() == 'pasien.index' ? 'active' : '' }}">
                    <a href="{{ route('pasien.index') }}">
                        <i class="fas fa-dot-circle"></i>
                        <p>Data Pasien</p>
                    </a>
                </li>
                @endcan --}}

                @can('pemeriksaan-list')
                <li class="nav-item {{ Route::currentRouteName() == 'pemeriksaan.index' ? 'active' : '' }}">
                    <a href="{{ route('pemeriksaan.index') }}">
                        <i class="fas fa-dot-circle"></i>
                        <p>Data Pemeriksaan</p>
                    </a>
                </li>
                @endcan

                {{-- @can('homevisit-list')
                <li class="nav-item {{ Route::currentRouteName() == 'home_visit.index' ? 'active' : '' }}">
                    <a href="{{ route('home_visit.index') }}">
                        <i class="fas fa-dot-circle"></i>
                        <p>Home Visit</p>
                    </a>
                </li>
                @endcan --}}

                @can('monitoring-makanan-list')
                <li class="nav-item {{ Route::currentRouteName() == 'monitoring_makanan.index' ? 'active' : '' }}">
                    <a href="{{ route('monitoring_makanan.index') }}">
                        <i class="fas fa-dot-circle"></i>
                        <p>Monitoring Makanan</p>
                    </a>
                </li>
                @endcan

                @can('bouchard-list')
                <li class="nav-item {{ Route::currentRouteName() == 'bouchard.index' ? 'active' : '' }}">
                    <a href="{{ route('bouchard.index') }}">
                        <i class="fas fa-dot-circle"></i>
                        <p>Monitoring Aktivitas Fisik</p>
                    </a>
                </li>
                @endcan

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Lain-lain</h4>
                </li>

                @can('user-list')
                    <li class="nav-item {{ Route::currentRouteName() == 'user.index' ? 'active' : '' }}">
                        <a href="{{ route('user.index') }}">
                            <i class="fas fa-user-friends"></i>
                            <p>Data Pengguna</p>
                        </a>
                    </li>
                @endcan

                @can('role-list')
                <li class="nav-item {{ Route::currentRouteName() == 'roles.index' ? 'active' : '' }}">
                    <a href="{{ route('roles.index') }}">
                        <i class="fas fa-user-shield"></i>
                        <p>Role & Permission</p>
                    </a>
                </li>
                @endcan

                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="logout-btn">
                        <i class="fas fa-lock-open"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->