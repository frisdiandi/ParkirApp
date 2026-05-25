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
            --navy:        #0f2a5e;
            --navy-mid:    #1a3a6b;
            --blue:        #1e50a0;
            --blue-light:  #2563eb;
            --blue-soft:   #eff6ff;
            --accent:      #f59e0b;
            --success:     #10b981;
            --success-soft:#d1fae5;
            --danger:      #ef4444;
            --danger-soft: #fee2e2;
            --warning:     #f59e0b;
            --warning-soft:#fef3c7;
            --white:       #ffffff;
            --gray-50:     #f8fafc;
            --gray-100:    #f1f5f9;
            --gray-200:    #e2e8f0;
            --gray-300:    #cbd5e1;
            --gray-400:    #94a3b8;
            --gray-500:    #64748b;
            --gray-600:    #475569;
            --gray-700:    #334155;
            --charcoal:    #1e293b;
            --nav-h:       68px;
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
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; color: inherit; }
        button { font-family: inherit; }

        /* ── TOP BAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 50;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
            padding: 10px 16px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,.18);
        }
        .topbar-brand { display: flex; align-items: center; gap: 10px; }
        .topbar-brand svg { width: 30px; height: 30px; }
        .topbar-brand-text h1 { font-size: .95rem; font-weight: 800; color: var(--white); letter-spacing: .5px; }
        .topbar-brand-text p  { font-size: .65rem; color: rgba(255,255,255,.6); }

        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .topbar-user { text-align: right; }
        .topbar-user .name { font-size: .78rem; font-weight: 700; color: var(--white); line-height: 1.2; }
        .topbar-user .role { font-size: .62rem; color: rgba(255,255,255,.55); }

        .btn-logout-top {
            width: 34px; height: 34px; border-radius: 10px;
            background: rgba(239,68,68,.18); color: #fca5a5;
            border: none; cursor: pointer; display: flex;
            align-items: center; justify-content: center; font-size: .85rem;
            transition: background .15s;
        }
        .btn-logout-top:hover { background: rgba(239,68,68,.3); color: #fff; }

        /* ── CONTENT ── */
        .page-content {
            padding: 16px 16px calc(var(--nav-h) + var(--safe-bottom) + 24px);
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
            box-shadow: 0 -6px 20px rgba(0,0,0,.06);
        }
        .nav-item {
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            padding: 6px 16px;
            color: var(--gray-400); font-size: .65rem; font-weight: 700;
            border-radius: 10px; transition: color .2s; position: relative;
        }
        .nav-item i { font-size: 1.15rem; transition: transform .2s; }
        .nav-item.active { color: var(--blue-light); }
        .nav-item.active i { transform: translateY(-2px); }
        .nav-item.active::after {
            content: ''; position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%);
            width: 24px; height: 3px; background: var(--blue-light); border-radius: 3px;
        }

        /* ── CARDS ── */
        .card {
            background: var(--white); border-radius: 16px;
            border: 1px solid var(--gray-100);
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            overflow: hidden;
        }
        .card-body { padding: 16px; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 20px; border-radius: 12px;
            font-family: inherit; font-size: .88rem; font-weight: 700;
            border: none; cursor: pointer; transition: all .2s;
            white-space: nowrap; justify-content: center;
        }
        .btn-primary { background: linear-gradient(135deg, var(--navy-mid), var(--blue-light)); color: var(--white); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,.35); }
        .btn-primary:active { transform: scale(.98); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-success:hover { background: #059669; }
        .btn-danger  { background: var(--danger); color: var(--white); }
        .btn-secondary { background: var(--gray-100); color: var(--gray-700); border: 1.5px solid var(--gray-200); }
        .btn-outline { background: transparent; border: 1.5px solid var(--blue-light); color: var(--blue-light); }
        .btn-full { width: 100%; }
        .btn:disabled { opacity: .55; cursor: not-allowed; }

        /* ── FORM ── */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: .82rem; font-weight: 700; color: var(--charcoal); margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 12px 14px;
            border: 1.5px solid var(--gray-200); border-radius: 12px;
            font-family: inherit; font-size: .92rem; color: var(--charcoal);
            background: var(--white); outline: none; transition: all .2s;
            -webkit-appearance: none;
        }
        .form-control:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(37,99,235,.12); }

        /* ── ALERT ── */
        .alert {
            padding: 12px 14px; border-radius: 12px; margin-bottom: 14px;
            font-size: .85rem; display: flex; align-items: center; gap: 9px;
            font-weight: 600;
        }
        .alert-success { background: var(--success-soft); border: 1px solid #a7f3d0; color: #065f46; }
        .alert-danger  { background: var(--danger-soft); border: 1px solid #fecaca; color: #991b1b; }
        .alert-warning { background: var(--warning-soft); border: 1px solid #fde68a; color: #92400e; }
        .alert-info    { background: var(--blue-soft); border: 1px solid #bfdbfe; color: #1e40af; }

        /* ── BADGE ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 99px;
            font-size: .68rem; font-weight: 700; letter-spacing: .2px;
        }
        .badge-success { background: var(--success-soft); color: #065f46; }
        .badge-warning { background: var(--warning-soft); color: #92400e; }
        .badge-info    { background: var(--blue-soft); color: #1e40af; }
        .badge-danger  { background: var(--danger-soft); color: #991b1b; }
        .badge-light   { background: var(--gray-100); color: var(--gray-700); }

        /* ── SECTION HEADER ── */
        .section-hd {
            display: flex; align-items: center; justify-content: space-between;
            margin: 4px 0 10px;
        }
        .section-hd h3 { font-size: .92rem; font-weight: 800; color: var(--charcoal); display: flex; align-items: center; gap: 8px; }
        .section-hd a  { font-size: .76rem; color: var(--blue-light); font-weight: 700; }

        /* ── PLATE CHIP ── */
        .plate-chip {
            display: inline-flex; align-items: center;
            background: var(--navy); color: var(--white);
            font-weight: 800; font-family: 'Courier New', monospace;
            font-size: .92rem; padding: 4px 10px; border-radius: 6px;
            letter-spacing: 1.5px;
        }

        /* ── SPINNER ── */
        .spinner {
            width: 32px; height: 32px;
            border: 3px solid var(--gray-200);
            border-top-color: var(--blue-light);
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── DIALOG ── */
        dialog {
            border: none; padding: 0; background: var(--white);
            border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,.25);
            max-width: 92%; width: 420px;
        }
        dialog::backdrop { background: rgba(15,42,94,.5); backdrop-filter: blur(2px); }

        /* utilities */
        .text-center { text-align: center; }
        .text-muted  { color: var(--gray-400); }
        .text-success { color: var(--success); }
        .text-danger  { color: var(--danger); }
        .text-warning { color: var(--warning); }
        .text-primary { color: var(--blue-light); }
        .mt-1 { margin-top: 4px; } .mt-2 { margin-top: 8px; } .mt-3 { margin-top: 12px; } .mt-4 { margin-top: 16px; }
        .mb-1 { margin-bottom: 4px; } .mb-2 { margin-bottom: 8px; } .mb-3 { margin-bottom: 12px; } .mb-4 { margin-bottom: 16px; }
        .flex { display: flex; } .flex-col { flex-direction: column; }
        .gap-1 { gap: 4px; } .gap-2 { gap: 8px; } .gap-3 { gap: 12px; }
        .items-center { align-items: center; } .justify-between { justify-content: space-between; }
        .w-full { width: 100%; }
    </style>
    @stack('styles')
</head>
<body>

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
            <div class="name">{{ Str::limit(Auth::user()->nama, 14) }}</div>
            <div class="role"><i class="fa-solid fa-circle" style="font-size:5px;color:#34d399;"></i> Online</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin keluar?');">
            @csrf
            <button type="submit" class="btn-logout-top" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </form>
    </div>
</header>

<main class="page-content">
    @if(session('success'))
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <span>{{ session('error') }}</span></div>
    @endif
    @if($errors->any() && !in_array(request()->route()?->getName(), ['petugas.tambah']))
    <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i>
        <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    </div>
    @endif

    @yield('content')
</main>

<nav class="bottom-nav">
    <a href="{{ route('petugas.dashboard') }}" class="nav-item {{ request()->routeIs('petugas.dashboard') || request()->routeIs('petugas.tambah') || request()->routeIs('petugas.scan-checkout') ? 'active' : '' }}">
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
