<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1a3a6b">
    <meta name="description" content="Sistem Informasi Retribusi Parkir">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIRP">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>Login — SIRP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --navy:      #0f2a5e;
            --navy-mid:  #1a3a6b;
            --blue:      #1e50a0;
            --blue-light:#2563eb;
            --accent:    #f59e0b;
            --white:     #ffffff;
            --gray-50:   #f8fafc;
            --gray-100:  #f1f5f9;
            --gray-200:  #e2e8f0;
            --gray-400:  #94a3b8;
            --gray-600:  #475569;
            --charcoal:  #1e293b;
            --danger:    #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--gray-50);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        .bg-scene {
            position: fixed; inset: 0; z-index: 0;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 40%, var(--blue) 100%);
            overflow: hidden;
        }

        .bg-circle {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.03);
            animation: float 8s ease-in-out infinite;
        }
        .bg-circle:nth-child(1) { width: 600px; height: 600px; top: -200px; right: -200px; animation-delay: 0s; }
        .bg-circle:nth-child(2) { width: 400px; height: 400px; bottom: -100px; left: -100px; animation-delay: 3s; }
        .bg-circle:nth-child(3) { width: 200px; height: 200px; top: 40%; left: 20%; animation-delay: 6s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Grid overlay */
        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Main card */
        .login-wrapper {
            position: relative; z-index: 10;
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 900px; width: 94%;
            min-height: 540px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.1);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Left panel */
        .panel-left {
            background: linear-gradient(160deg, var(--navy) 0%, var(--blue-light) 100%);
            padding: 48px 40px;
            display: flex; flex-direction: column;
            justify-content: space-between;
            position: relative; overflow: hidden;
        }

        .panel-left::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .logo-wrap {
            display: flex; align-items: center; gap: 14px;
            position: relative; z-index: 1;
        }

        /* SVG Logo inline */
        .logo-svg {
            width: 56px; height: 56px; flex-shrink: 0;
        }

        .logo-text-block h1 {
            font-size: 1.35rem; font-weight: 800;
            color: var(--white); line-height: 1.1;
            letter-spacing: -0.5px;
        }
        .logo-text-block span {
            font-size: 0.7rem; font-weight: 500;
            color: rgba(255,255,255,0.6); text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .panel-hero { position: relative; z-index: 1; }
        .panel-hero h2 {
            font-size: 1.8rem; font-weight: 800;
            color: var(--white); line-height: 1.25;
            margin-bottom: 12px;
        }
        .panel-hero p {
            font-size: 0.9rem; color: rgba(255,255,255,0.65);
            line-height: 1.6;
        }

        .feature-list {
            list-style: none; position: relative; z-index: 1;
        }
        .feature-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.82rem; color: rgba(255,255,255,0.8);
            margin-bottom: 10px;
        }
        .feature-list li i {
            width: 28px; height: 28px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; color: var(--accent); flex-shrink: 0;
        }

        /* Right panel (form) */
        .panel-right {
            background: var(--white);
            padding: 48px 44px;
            display: flex; flex-direction: column; justify-content: center;
        }

        .form-header { margin-bottom: 32px; }
        .form-header h3 {
            font-size: 1.6rem; font-weight: 800;
            color: var(--charcoal); margin-bottom: 6px;
        }
        .form-header p { font-size: 0.875rem; color: var(--gray-600); }

        .alert-error {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 12px; padding: 12px 16px;
            margin-bottom: 20px; font-size: 0.85rem;
            color: var(--danger); display: flex; gap: 8px; align-items: center;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 0.82rem; font-weight: 600;
            color: var(--charcoal); margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--gray-400); font-size: 0.9rem;
            transition: color 0.2s;
        }
        .input-wrap input {
            width: 100%; padding: 13px 14px 13px 42px;
            border: 1.5px solid var(--gray-200);
            border-radius: 12px; font-size: 0.9rem;
            font-family: inherit; color: var(--charcoal);
            background: var(--gray-50);
            transition: all 0.2s; outline: none;
        }
        .input-wrap input:focus {
            border-color: var(--blue-light);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .input-wrap input:focus + i,
        .input-wrap:focus-within i { color: var(--blue-light); }
        .input-wrap .toggle-pw {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            left: auto; cursor: pointer; font-size: 0.85rem;
            color: var(--gray-400); transition: color 0.2s;
        }
        .input-wrap .toggle-pw:hover { color: var(--blue-light); }

        .form-options {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; font-size: 0.82rem;
        }
        .check-label {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; color: var(--gray-600);
        }
        .check-label input[type=checkbox] {
            width: 16px; height: 16px; accent-color: var(--blue-light);
            cursor: pointer;
        }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--navy-mid), var(--blue-light));
            color: var(--white); border: none; border-radius: 12px;
            font-size: 0.95rem; font-weight: 700; font-family: inherit;
            cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            position: relative; overflow: hidden;
        }
        .btn-login::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--blue-light), var(--navy-mid));
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-login:hover::before { opacity: 1; }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(30,80,160,0.4); }
        .btn-login:active { transform: translateY(0); }
        .btn-login span, .btn-login i { position: relative; z-index: 1; }

        .footer-note {
            text-align: center; margin-top: 24px;
            font-size: 0.78rem; color: var(--gray-400);
        }

        /* PWA install modal — selalu muncul saat login dibuka di browser */
        .pwa-modal-overlay {
            position: fixed; inset: 0; z-index: 200;
            background: rgba(15,42,94,0.65); backdrop-filter: blur(6px);
            display: none; align-items: center; justify-content: center;
            padding: 16px; animation: fadeIn .25s ease;
        }
        .pwa-modal-overlay.show { display: flex; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes popIn  { from { opacity: 0; transform: translateY(20px) scale(.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .pwa-modal {
            background: #fff; border-radius: 20px; max-width: 420px; width: 100%;
            overflow: hidden; animation: popIn .35s cubic-bezier(.2,.9,.3,1.2);
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
            max-height: 92vh; display: flex; flex-direction: column;
        }
        .pwa-modal-head {
            background: linear-gradient(135deg, var(--navy), var(--blue-light));
            color: #fff; padding: 22px 22px 18px; text-align: center;
            position: relative;
        }
        .pwa-modal-head .close-btn {
            position: absolute; top: 12px; right: 12px;
            width: 30px; height: 30px; border-radius: 50%;
            background: rgba(255,255,255,0.18); color: #fff;
            border: none; cursor: pointer; font-size: .85rem;
        }
        .pwa-modal-head .app-icon {
            width: 64px; height: 64px; margin: 0 auto 10px;
            background: rgba(255,255,255,0.18);
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
        }
        .pwa-modal-head h3 { font-size: 1.15rem; font-weight: 800; margin-bottom: 4px; }
        .pwa-modal-head p  { font-size: .82rem; color: rgba(255,255,255,0.8); }

        .pwa-modal-body {
            padding: 18px 22px 12px; overflow-y: auto;
        }
        .platform-tabs {
            display: flex; gap: 6px; background: var(--gray-50);
            border-radius: 10px; padding: 4px; margin-bottom: 14px;
        }
        .platform-tab {
            flex: 1; padding: 8px 6px; border: none; background: transparent;
            border-radius: 7px; font-family: inherit; font-size: .78rem;
            font-weight: 700; color: var(--gray-600); cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }
        .platform-tab.active { background: #fff; color: var(--blue-light); box-shadow: 0 1px 3px rgba(0,0,0,0.08); }

        .step-list {
            list-style: none; padding: 0; margin: 0;
        }
        .step-item {
            display: flex; gap: 12px; align-items: flex-start;
            padding: 10px 0; border-bottom: 1px solid var(--gray-100);
        }
        .step-item:last-child { border-bottom: none; }
        .step-num {
            flex-shrink: 0; width: 26px; height: 26px;
            background: linear-gradient(135deg, var(--navy), var(--blue-light));
            color: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 800;
        }
        .step-text { font-size: .85rem; color: var(--charcoal); line-height: 1.4; padding-top: 3px; }
        .step-text strong { color: var(--blue-light); font-weight: 700; }
        .step-text .ic-inline {
            display: inline-flex; align-items: center; justify-content: center;
            width: 22px; height: 22px; background: var(--gray-100);
            border-radius: 5px; margin: 0 2px; vertical-align: middle;
            color: var(--blue-light);
        }
        .step-text .ic-inline svg { width: 13px; height: 13px; }

        .pwa-modal-foot {
            padding: 14px 22px 20px;
            display: flex; flex-direction: column; gap: 8px;
            border-top: 1px solid var(--gray-100);
        }
        .btn-pwa-install {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, var(--navy-mid), var(--blue-light));
            color: #fff; border: none; border-radius: 12px;
            font-size: .9rem; font-weight: 700; font-family: inherit;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all .2s;
        }
        .btn-pwa-install:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(37,99,235,0.35); }
        .btn-pwa-install:disabled { opacity: .5; cursor: not-allowed; }
        .btn-pwa-skip {
            width: 100%; padding: 11px;
            background: transparent; color: var(--gray-500);
            border: none; cursor: pointer; font-family: inherit;
            font-size: .8rem; font-weight: 600;
        }
        .btn-pwa-skip:hover { color: var(--charcoal); }

        .pwa-tip {
            font-size: .72rem; color: var(--gray-500);
            text-align: center; padding: 6px 0 0;
            font-style: italic;
        }

        /* Mobile responsive */
        @media (max-width: 680px) {
            .login-wrapper { grid-template-columns: 1fr; max-width: 440px; min-height: auto; }
            .panel-left { padding: 32px 28px; }
            .panel-hero h2 { font-size: 1.4rem; }
            .feature-list { display: none; }
            .panel-right { padding: 36px 28px; }
        }
    </style>
</head>
<body>

<div class="bg-scene">
    <div class="bg-grid"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
</div>

<div class="login-wrapper">
    <!-- Left Panel -->
    <div class="panel-left">
        <div class="logo-wrap">
            <!-- Inline SVG Logo -->
            <svg class="logo-svg" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="56" height="56" rx="14" fill="rgba(255,255,255,0.15)"/>
                <rect x="2" y="2" width="52" height="52" rx="12" fill="rgba(255,255,255,0.08)"/>
                <!-- Car icon -->
                <path d="M14 34h28M16 34l3-8h18l3 8" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="19" y="26" width="18" height="6" rx="2" fill="rgba(255,255,255,0.25)" stroke="white" stroke-width="1.5"/>
                <circle cx="20" cy="36" r="2.5" fill="#f59e0b"/>
                <circle cx="36" cy="36" r="2.5" fill="#f59e0b"/>
                <!-- P letter -->
                <path d="M26 20h4a3 3 0 0 1 0 6h-4v-6z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M26 20v10" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div class="logo-text-block">
                <h1>SIRP</h1>
                <span>Sistem Retribusi Parkir</span>
            </div>
        </div>

        <div class="panel-hero">
            <h2>Kelola Retribusi<br>Parkir dengan Mudah</h2>
            <p>Platform digital terintegrasi untuk manajemen parkir yang efisien dan transparan.</p>
        </div>

        <ul class="feature-list">
            <li><i><span class="fa-solid fa-location-dot"></span></i> Monitoring multi-lokasi parkir</li>
            <li><i><span class="fa-solid fa-chart-line"></span></i> Laporan pendapatan real-time</li>
            <li><i><span class="fa-solid fa-qrcode"></span></i> Pembayaran Cash & QRIS</li>
            <li><i><span class="fa-solid fa-camera"></span></i> Scan nomor polisi otomatis</li>
        </ul>
    </div>

    <!-- Right Panel -->
    <div class="panel-right">
        <div class="form-header">
            <h3>Selamat Datang</h3>
            <p>Masukkan kredensial Anda untuk melanjutkan</p>
        </div>

        @if($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#16a34a;display:flex;gap:8px;align-items:center;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="username" name="username"
                           placeholder="Masukkan username"
                           value="{{ old('username') }}" autocomplete="username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password"
                           placeholder="Masukkan password"
                           autocomplete="current-password" required>
                    <i class="fa-solid fa-eye toggle-pw" id="togglePw" onclick="togglePassword()"></i>
                </div>
            </div>

            <div class="form-options">
                <label class="check-label">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login">
                <span>Masuk ke Sistem</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <p class="footer-note">
            © {{ date('Y') }} SIRP — Sistem Informasi Retribusi Parkir<br>
            <span id="pwa-hint" style="color:#94a3b8;"></span>
        </p>
    </div>
</div>

<!-- PWA Install Modal — selalu muncul saat dibuka di browser -->
<div class="pwa-modal-overlay" id="pwaModal">
    <div class="pwa-modal">
        <div class="pwa-modal-head">
            <button type="button" class="close-btn" onclick="closePwaModal()" aria-label="Tutup">✕</button>
            <div class="app-icon">
                <svg width="42" height="42" viewBox="0 0 56 56" fill="none">
                    <path d="M14 34h28M16 34l3-8h18l3 8" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="20" cy="36" r="2.8" fill="#f59e0b"/>
                    <circle cx="36" cy="36" r="2.8" fill="#f59e0b"/>
                    <path d="M26 20h4a3 3 0 0 1 0 6h-4v-6z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M26 20v10" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h3>Install Aplikasi SIRP</h3>
            <p>Akses lebih cepat tanpa buka browser</p>
        </div>

        <div class="pwa-modal-body">
            <div class="platform-tabs" id="platformTabs">
                <button type="button" class="platform-tab" data-tab="android" onclick="switchTab('android')">
                    <i class="fa-brands fa-android"></i> Android
                </button>
                <button type="button" class="platform-tab" data-tab="ios" onclick="switchTab('ios')">
                    <i class="fa-brands fa-apple"></i> iPhone / iPad
                </button>
                <button type="button" class="platform-tab" data-tab="desktop" onclick="switchTab('desktop')">
                    <i class="fa-solid fa-desktop"></i> Desktop
                </button>
            </div>

            {{-- Android instructions --}}
            <div class="step-pane" id="pane-android" style="display:none">
                <ol class="step-list">
                    <li class="step-item">
                        <div class="step-num">1</div>
                        <div class="step-text">
                            Tap tombol <strong>Install Sekarang</strong> di bawah, atau buka menu browser
                            <span class="ic-inline"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                        </div>
                    </li>
                    <li class="step-item">
                        <div class="step-num">2</div>
                        <div class="step-text">Pilih <strong>"Install aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong></div>
                    </li>
                    <li class="step-item">
                        <div class="step-num">3</div>
                        <div class="step-text">Konfirmasi dengan tap <strong>Install</strong>. Aplikasi SIRP akan muncul di home screen.</div>
                    </li>
                </ol>
            </div>

            {{-- iOS instructions --}}
            <div class="step-pane" id="pane-ios" style="display:none">
                <ol class="step-list">
                    <li class="step-item">
                        <div class="step-num">1</div>
                        <div class="step-text">
                            Tap ikon <strong>Bagikan</strong>
                            <span class="ic-inline" title="Share">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 16V4M12 4l-4 4M12 4l4 4"/><path d="M4 14v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-6"/></svg>
                            </span>
                            di bagian bawah Safari.
                        </div>
                    </li>
                    <li class="step-item">
                        <div class="step-num">2</div>
                        <div class="step-text">Gulir ke bawah, lalu pilih <strong>"Tambahkan ke Layar Utama"</strong> / <strong>"Add to Home Screen"</strong>.</div>
                    </li>
                    <li class="step-item">
                        <div class="step-num">3</div>
                        <div class="step-text">Tap <strong>"Tambah"</strong> / <strong>"Add"</strong> di pojok kanan atas.</div>
                    </li>
                </ol>
                <p class="pwa-tip">Pastikan menggunakan browser <strong>Safari</strong> (bukan Chrome) di iOS.</p>
            </div>

            {{-- Desktop instructions --}}
            <div class="step-pane" id="pane-desktop" style="display:none">
                <ol class="step-list">
                    <li class="step-item">
                        <div class="step-num">1</div>
                        <div class="step-text">Tap tombol <strong>Install Sekarang</strong> di bawah, atau klik ikon install
                            <span class="ic-inline"><i class="fa-solid fa-download"></i></span>
                            di kanan address bar.
                        </div>
                    </li>
                    <li class="step-item">
                        <div class="step-num">2</div>
                        <div class="step-text">Pilih <strong>"Install"</strong> pada dialog yang muncul.</div>
                    </li>
                    <li class="step-item">
                        <div class="step-num">3</div>
                        <div class="step-text">Aplikasi SIRP akan tersedia sebagai aplikasi desktop terpisah.</div>
                    </li>
                </ol>
            </div>
        </div>

        <div class="pwa-modal-foot">
            <button type="button" class="btn-pwa-install" id="btnPwaInstall">
                <i class="fa-solid fa-download"></i>
                <span id="btnInstallText">Install Sekarang</span>
            </button>
            <button type="button" class="btn-pwa-skip" onclick="closePwaModal()">Nanti saja</button>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pw = document.getElementById('password');
    const ic = document.getElementById('togglePw');
    if (pw.type === 'password') {
        pw.type = 'text';
        ic.className = 'fa-solid fa-eye-slash toggle-pw';
    } else {
        pw.type = 'password';
        ic.className = 'fa-solid fa-eye toggle-pw';
    }
}

// ─── PWA Install Popup ─────────────────────────────────────────
let deferredPrompt = null;

function detectPlatform() {
    const ua = navigator.userAgent || '';
    if (/iPhone|iPad|iPod/i.test(ua) || (/Mac/i.test(ua) && navigator.maxTouchPoints > 1)) return 'ios';
    if (/Android/i.test(ua)) return 'android';
    return 'desktop';
}

function isStandalone() {
    return window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true
        || document.referrer.startsWith('android-app://');
}

function switchTab(name) {
    document.querySelectorAll('.platform-tab').forEach(t => {
        t.classList.toggle('active', t.dataset.tab === name);
    });
    document.querySelectorAll('.step-pane').forEach(p => {
        p.style.display = p.id === 'pane-' + name ? '' : 'none';
    });

    // Tombol install hanya aktif untuk Android/Desktop dengan deferredPrompt;
    // untuk iOS selalu disabled (instruksi manual saja)
    const btn = document.getElementById('btnPwaInstall');
    const txt = document.getElementById('btnInstallText');
    if (name === 'ios') {
        btn.disabled = true;
        txt.textContent = 'Ikuti langkah di atas';
    } else if (deferredPrompt) {
        btn.disabled = false;
        txt.textContent = 'Install Sekarang';
    } else {
        btn.disabled = true;
        txt.textContent = 'Buka menu browser';
    }
}

function showPwaModal() {
    if (isStandalone()) return; // sudah terinstall — jangan tampilkan
    document.getElementById('pwaModal').classList.add('show');
}

function closePwaModal() {
    document.getElementById('pwaModal').classList.remove('show');
}

// Tangkap event install Android/Chrome supaya tombol "Install Sekarang" aktif
window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredPrompt = e;
    // Jika tab Android/Desktop aktif, refresh state tombol
    const active = document.querySelector('.platform-tab.active')?.dataset.tab;
    if (active === 'android' || active === 'desktop') switchTab(active);
});

document.getElementById('btnPwaInstall').addEventListener('click', async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    deferredPrompt = null;
    if (outcome === 'accepted') closePwaModal();
});

// Sembunyikan modal jika user berhasil install
window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    closePwaModal();
});

// Init: deteksi platform & tampilkan tab yang sesuai, lalu auto-show modal
document.addEventListener('DOMContentLoaded', () => {
    if (isStandalone()) return; // jangan ganggu user yang sudah install
    const platform = detectPlatform();
    switchTab(platform);
    // delay 700ms biar halaman login tampil dulu, baru popup muncul
    setTimeout(showPwaModal, 700);
});

// Tutup modal saat klik overlay (di luar kotak)
document.getElementById('pwaModal').addEventListener('click', e => {
    if (e.target.id === 'pwaModal') closePwaModal();
});

// Register SW
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}
</script>
</body>
</html>
