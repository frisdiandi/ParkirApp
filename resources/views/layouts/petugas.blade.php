<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f2a5e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIRP Petugas">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>@yield('title', 'Dashboard') — SIRP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --navy:       #0f2a5e;
            --navy-mid:   #1a3a6b;
            --blue:       #1e50a0;
            --blue-light: #2563eb;
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
            --charcoal:   #1e293b;
            --nav-h:      64px;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        html, body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--gray-50);
            color: var(--charcoal);
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            overscroll-behavior: none;
        }

        /* ── TOP BAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 50;
            background: var(--navy);
            padding: 0 16px;
            height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }

        .topbar-brand { display: flex; align-items: center; gap: 10px; }
        .topbar-brand svg { width: 28px; height: 28px; }
        .topbar-brand-text h1 { font-size: .92rem; font-weight: 800; color: var(--white); }
        .topbar-brand-text p  { font-size: .65rem; color: rgba(255,255,255,.5); }

        .topbar-right { display: flex; align-items: center; gap: 8px; }
        .topbar-user { text-align: right; }
        .topbar-user .name { font-size: .78rem; font-weight: 600; color: var(--white); }
        .topbar-user .role { font-size: .65rem; color: rgba(255,255,255,.5); }

        .btn-logout-top {
            width: 32px; height: 32px; border-radius: 8px;
            background: rgba(239,68,68,.15); color: #fca5a5;
            border: none; cursor: pointer; display: flex;
            align-items: center; justify-content: center; font-size: .8rem;
        }

        /* ── PAGE CONTENT ── */
        .page-content {
            padding: 16px 16px calc(var(--nav-h) + var(--safe-bottom) + 16px);
            min-height: calc(100vh - 56px);
        }

        /* ── BOTTOM NAV ── */
        .bottom-nav {
            position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: 480px;
            height: calc(var(--nav-h) + var(--safe-bottom));
            background: var(--white);
            border-top: 1px solid var(--gray-200);
            display: flex; align-items: flex-start; justify-content: space-around;
            padding-top: 8px;
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,.08);
        }

        .nav-item {
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            padding: 6px 16px; text-decoration: none;
            color: var(--gray-400); font-size: .65rem; font-weight: 600;
            border-radius: 10px; transition: all .2s; position: relative;
        }
        .nav-item i { font-size: 1.1rem; transition: transform .2s; }
        .nav-item.active { color: var(--blue-light); }
        .nav-item.active i { transform: translateY(-2px); }

        /* Special center button */
        .nav-item.nav-center {
            margin-top: -20px;
        }
        .nav-center-btn {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg, var(--navy-mid), var(--blue-light));
            display: flex; align-items: center; justify-content: center;
            color: var(--white); font-size: 1.2rem;
            box-shadow: 0 4px 16px rgba(37,99,235,.4);
            margin-bottom: 4px;
        }

        /* ── CARDS ── */
        .card {
            background: var(--white); border-radius: 16px;
            border: 1px solid var(--gray-100);
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            overflow: hidden;
        }

        .card-body { padding: 16px; }

        /* ── STAT CARDS ── */
        .stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
        .stat-card-mobile {
            background: var(--white); border-radius: 14px; padding: 14px 12px;
            border: 1px solid var(--gray-100);
        }
        .stat-card-mobile .s-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; margin-bottom: 10px;
        }
        .stat-card-mobile .s-val { font-size: 1.4rem; font-weight: 800; line-height: 1; }
        .stat-card-mobile .s-lbl { font-size: .7rem; color: var(--gray-400); margin-top: 2px; font-weight: 500; }

        /* ── BTN ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 11px 20px; border-radius: 12px;
            font-family: inherit; font-size: .88rem; font-weight: 600;
            border: none; cursor: pointer; transition: all .2s;
            text-decoration: none; white-space: nowrap; justify-content: center;
        }
        .btn-primary { background: linear-gradient(135deg,var(--navy-mid),var(--blue-light)); color: var(--white); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,.35); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-danger  { background: var(--danger); color: var(--white); }
        .btn-secondary { background: var(--gray-100); color: var(--gray-700); }
        .btn-outline { background: transparent; border: 1.5px solid var(--blue-light); color: var(--blue-light); }
        .btn-full { width: 100%; }

        /* ── FORM ── */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: .82rem; font-weight: 600; color: var(--charcoal); margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 12px 14px;
            border: 1.5px solid var(--gray-200); border-radius: 12px;
            font-family: inherit; font-size: .9rem; color: var(--charcoal);
            background: var(--white); outline: none; transition: all .2s;
            -webkit-appearance: none;
        }
        .form-control:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

        /* ── ALERT ── */
        .alert { padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: .85rem; display: flex; align-items: center; gap: 9px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-danger  { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }

        /* ── LIST ITEM ── */
        .list-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 0; border-bottom: 1px solid var(--gray-100);
        }
        .list-item:last-child { border-bottom: none; }

        /* ── BADGE ── */
        .badge { display: inline-flex; align-items: center; gap: 3px; padding: 3px 9px; border-radius: 99px; font-size: .7rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-danger  { background: #fee2e2; color: #b91c1c; }

        /* ── SECTION HEADER ── */
        .section-hd {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 10px;
        }
        .section-hd h3 { font-size: .88rem; font-weight: 700; color: var(--charcoal); }
        .section-hd a  { font-size: .78rem; color: var(--blue-light); text-decoration: none; font-weight: 600; }

        .plate-chip {
            display: inline-block; background: var(--navy);
            color: var(--white); font-weight: 700; font-family: monospace;
            font-size: .92rem; padding: 3px 10px; border-radius: 6px;
            letter-spacing: 1px;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Topbar -->
<header class="topbar">
    <div class="topbar-brand">
        <svg viewBox="0 0 56 56" fill="none">
            <path d="M8 36h40M10 36l4-10H42l4 10" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="15" cy="38" r="3" fill="#f59e0b"/>
            <circle cx="41" cy="38" r="3" fill="#f59e0b"/>
            <path d="M22 22h6a4 4 0 0 1 0 8h-6v-8z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M22 22v12" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <div class="topbar-brand-text">
            <h1>SIRP</h1>
            <p>Petugas Parkir</p>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-user">
            <div class="name">{{ Auth::user()->nama }}</div>
            <div class="role">Petugas</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout-top" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </form>
    </div>
</header>

<!-- Content -->
<main class="page-content">
    @if(session('success'))
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    @yield('content')
</main>

<!-- Bottom Nav -->
<nav class="bottom-nav">
    <a href="{{ route('petugas.dashboard') }}" class="nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('petugas.riwayat') }}" class="nav-item {{ request()->routeIs('petugas.riwayat') ? 'active' : '' }}">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Riwayat</span>
    </a>
    <a href="{{ route('petugas.profile') }}" class="nav-item {{ request()->routeIs('petugas.profile') ? 'active' : '' }}">
        <i class="fa-solid fa-user"></i>
        <span>Profil</span>
    </a>
</nav>

@stack('scripts')
</body>
</html>
