<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f2a5e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SIRP Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --navy:       #0f2a5e;
            --navy-mid:   #1a3a6b;
            --blue:       #1e50a0;
            --blue-light: #2563eb;
            --blue-hover: #1d4ed8;
            --accent:     #f59e0b;
            --success:    #10b981;
            --danger:     #ef4444;
            --warning:    #f59e0b;
            --white:      #ffffff;
            --gray-50:    #f8fafc;
            --gray-100:   #f1f5f9;
            --gray-200:   #e2e8f0;
            --gray-300:   #cbd5e1;
            --gray-400:   #94a3b8;
            --gray-500:   #64748b;
            --gray-600:   #475569;
            --gray-700:   #334155;
            --charcoal:   #1e293b;
            --sidebar-w:  260px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--gray-50); color: var(--charcoal); display: flex; }

        /* ═══ SIDEBAR ═══ */
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh;
            background: var(--navy);
            position: fixed; left: 0; top: 0; bottom: 0; z-index: 50;
            display: flex; flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-logo {
            display: flex; align-items: center; gap: 12px;
        }

        .logo-icon {
            width: 42px; height: 42px; border-radius: 10px;
            background: rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        .logo-icon svg { width: 24px; height: 24px; }

        .logo-info h1 { font-size: 1rem; font-weight: 800; color: var(--white); }
        .logo-info p  { font-size: 0.7rem; color: rgba(255,255,255,0.5); font-weight: 500; letter-spacing: 0.5px; }

        .sidebar-menu {
            flex: 1; padding: 16px 12px; overflow-y: auto;
        }

        .menu-section-label {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; color: rgba(255,255,255,0.35);
            padding: 8px 8px 4px; margin-top: 8px;
        }

        .menu-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,0.65); font-size: 0.87rem; font-weight: 500;
            text-decoration: none; margin-bottom: 2px;
            transition: all 0.2s; position: relative;
        }

        .menu-item i { width: 18px; text-align: center; font-size: 0.9rem; flex-shrink: 0; }

        .menu-item:hover { background: rgba(255,255,255,0.08); color: var(--white); }

        .menu-item.active {
            background: linear-gradient(135deg, var(--blue-light), var(--blue));
            color: var(--white); font-weight: 600;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }

        .menu-item .badge {
            margin-left: auto; background: var(--accent); color: var(--navy);
            font-size: 0.65rem; font-weight: 700; padding: 2px 7px;
            border-radius: 99px; min-width: 20px; text-align: center;
        }

        /* Submenu */
        .submenu { display: none; padding-left: 18px; }
        .submenu.open { display: block; }
        .submenu .menu-item { font-size: 0.83rem; padding: 8px 12px; }

        .menu-arrow {
            margin-left: auto; font-size: 0.7rem;
            transition: transform 0.2s; color: rgba(255,255,255,0.4);
        }
        .menu-item.has-sub.open .menu-arrow { transform: rotate(90deg); }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            background: rgba(255,255,255,0.06);
        }

        .user-avatar {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--blue-light); display: flex;
            align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: var(--white);
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-info .u-name {
            font-size: 0.82rem; font-weight: 600; color: var(--white);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-info .u-level { font-size: 0.7rem; color: rgba(255,255,255,0.45); }

        .btn-logout {
            padding: 6px; border-radius: 8px; border: none;
            background: rgba(239,68,68,0.15); color: #fca5a5;
            cursor: pointer; transition: all 0.2s; font-size: 0.8rem;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.3); color: #f87171; }

        /* ═══ TOPBAR ═══ */
        .topbar {
            position: fixed; top: 0; right: 0;
            left: var(--sidebar-w); height: 64px;
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 40;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; }

        .btn-toggle {
            display: none; width: 36px; height: 36px;
            border: none; background: var(--gray-100);
            border-radius: 8px; cursor: pointer;
            align-items: center; justify-content: center;
            font-size: 0.9rem; color: var(--gray-600);
        }

        .page-title { font-size: 1.1rem; font-weight: 700; color: var(--charcoal); }
        .breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 0.78rem; color: var(--gray-400);
        }
        .breadcrumb a { color: var(--blue-light); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .topbar-time { font-size: 0.82rem; color: var(--gray-500); font-weight: 500; }

        .notif-btn {
            position: relative; width: 36px; height: 36px;
            border: none; background: var(--gray-100); border-radius: 8px;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            font-size: 0.88rem; color: var(--gray-600);
        }

        /* ═══ MAIN CONTENT ═══ */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: 64px;
            padding: 28px 28px;
            min-height: calc(100vh - 64px);
            flex: 1;
        }

        /* ═══ CARDS ═══ */
        .card {
            background: var(--white); border-radius: 16px;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .card-header {
            padding: 18px 24px; border-bottom: 1px solid var(--gray-100);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-header h4 { font-size: 0.95rem; font-weight: 700; color: var(--charcoal); }
        .card-body { padding: 24px; }

        /* ═══ BUTTONS ═══ */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 10px;
            font-family: inherit; font-size: 0.84rem; font-weight: 600;
            border: none; cursor: pointer; transition: all 0.2s;
            text-decoration: none; white-space: nowrap;
        }
        .btn-primary   { background: var(--blue-light); color: var(--white); }
        .btn-primary:hover { background: var(--blue-hover); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .btn-danger    { background: var(--danger); color: var(--white); }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning   { background: var(--warning); color: var(--navy); }
        .btn-success   { background: var(--success); color: var(--white); }
        .btn-secondary { background: var(--gray-100); color: var(--gray-700); border: 1px solid var(--gray-200); }
        .btn-secondary:hover { background: var(--gray-200); }
        .btn-sm { padding: 6px 12px; font-size: 0.78rem; border-radius: 8px; }
        .btn-icon { padding: 8px; min-width: 34px; justify-content: center; }

        /* ═══ TABLE ═══ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.87rem; }
        thead th {
            padding: 11px 16px; text-align: left;
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px; color: var(--gray-500);
            background: var(--gray-50); border-bottom: 1px solid var(--gray-200);
        }
        tbody td {
            padding: 13px 16px; border-bottom: 1px solid var(--gray-100);
            color: var(--charcoal); vertical-align: middle;
        }
        tbody tr:hover { background: var(--gray-50); }
        tbody tr:last-child td { border-bottom: none; }

        /* ═══ BADGES ═══ */
        .badge-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 99px;
            font-size: 0.72rem; font-weight: 600;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger  { background: #fee2e2; color: #b91c1c; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-gray    { background: var(--gray-100); color: var(--gray-600); }

        /* ═══ FORM ═══ */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 0.83rem; font-weight: 600; color: var(--charcoal); margin-bottom: 7px; }
        .form-control {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid var(--gray-200); border-radius: 10px;
            font-family: inherit; font-size: 0.88rem; color: var(--charcoal);
            background: var(--white); transition: all 0.2s; outline: none;
        }
        .form-control:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        select.form-control { cursor: pointer; }

        /* ═══ ALERTS ═══ */
        .alert {
            padding: 13px 16px; border-radius: 12px; margin-bottom: 20px;
            font-size: 0.86rem; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-danger  { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

        /* ═══ STAT CARDS ═══ */
        .stat-card {
            background: var(--white); border-radius: 16px;
            padding: 20px; border: 1px solid var(--gray-200);
            position: relative; overflow: hidden;
        }
        .stat-card-icon {
            width: 46px; height: 46px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; margin-bottom: 14px;
        }
        .stat-card-value { font-size: 1.7rem; font-weight: 800; color: var(--charcoal); line-height: 1; }
        .stat-card-label { font-size: 0.8rem; color: var(--gray-500); margin-top: 4px; font-weight: 500; }
        .stat-card-trend {
            font-size: 0.75rem; margin-top: 10px;
            display: flex; align-items: center; gap: 4px;
        }

        /* Pagination */
        .pagination {
            display: flex; gap: 4px; justify-content: center;
            margin-top: 20px; flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 7px 12px; border-radius: 8px;
            font-size: 0.82rem; font-weight: 500; text-decoration: none;
            border: 1px solid var(--gray-200); color: var(--gray-600);
            background: var(--white); transition: all 0.15s;
        }
        .pagination a:hover { background: var(--blue-light); color: var(--white); border-color: var(--blue-light); }
        .pagination .active span { background: var(--blue-light); color: var(--white); border-color: var(--blue-light); }

        /* Avatar img */
        .avatar-sm { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
            .btn-toggle { display: flex; }
        }

        .overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 49;
        }
        .overlay.show { display: block; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <svg viewBox="0 0 56 56" fill="none">
                    <path d="M8 36h40M10 36l4-10H42l4 10" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="13" y="26" width="30" height="8" rx="2" fill="rgba(255,255,255,0.2)" stroke="white" stroke-width="1.5"/>
                    <circle cx="15" cy="38" r="3" fill="#f59e0b"/>
                    <circle cx="41" cy="38" r="3" fill="#f59e0b"/>
                    <path d="M22 22h6a4 4 0 0 1 0 8h-6v-8z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 22v12" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="logo-info">
                <h1>SIRP</h1>
                <p>Retribusi Parkir</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>

        <div class="menu-section-label">Data Master</div>

        <div class="menu-item has-sub {{ request()->routeIs('admin.lokasi.*') || request()->routeIs('admin.tarif.*') || request()->routeIs('admin.metode.*') ? 'active open' : '' }}"
             onclick="toggleSub(this)">
            <i class="fa-solid fa-database"></i> Data Master
            <i class="fa-solid fa-chevron-right menu-arrow"></i>
        </div>
        <div class="submenu {{ request()->routeIs('admin.lokasi.*') || request()->routeIs('admin.tarif.*') || request()->routeIs('admin.metode.*') ? 'open' : '' }}">
            <a href="{{ route('admin.lokasi.index') }}" class="menu-item {{ request()->routeIs('admin.lokasi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-map-location-dot"></i> Lokasi Parkir
            </a>
            <a href="{{ route('admin.tarif.index') }}" class="menu-item {{ request()->routeIs('admin.tarif.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i> Tarif Parkir
            </a>
            <a href="{{ route('admin.metode.index') }}" class="menu-item {{ request()->routeIs('admin.metode.*') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card"></i> Metode Pembayaran
            </a>
        </div>

        <div class="menu-section-label">Kepegawaian</div>

        <a href="{{ route('admin.petugas.index') }}" class="menu-item {{ request()->routeIs('admin.petugas.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Data Petugas
        </a>

        <div class="menu-section-label">Laporan</div>

        <a href="{{ route('admin.transaksi.index') }}" class="menu-item {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i> Data Transaksi
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ substr(Auth::user()->nama, 0, 1) }}</div>
            <div class="user-info">
                <div class="u-name">{{ Auth::user()->nama }}</div>
                <div class="u-level">{{ Auth::user()->level_label }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- Topbar -->
<header class="topbar">
    <div class="topbar-left">
        <button class="btn-toggle" id="sidebarToggle" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">SIRP</a>
                <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i>
                @yield('breadcrumb', 'Dashboard')
            </div>
        </div>
    </div>
    <div class="topbar-right">
        <span class="topbar-time" id="clock"></span>
        <div style="font-size:.82rem;color:var(--gray-500);font-weight:500;">
            Halo, <strong style="color:var(--charcoal)">{{ Auth::user()->nama }}</strong>
        </div>
    </div>
</header>

<!-- Main -->
<main class="main-content">
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
    </div>
    @endif

    @yield('content')
</main>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
}
function toggleSub(el) {
    el.classList.toggle('open');
    el.nextElementSibling.classList.toggle('open');
}

// Clock
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + ' | ' + now.toLocaleDateString('id-ID', {weekday:'short',day:'numeric',month:'short'});
}
setInterval(updateClock, 1000); updateClock();
</script>
@stack('scripts')
</body>
</html>
