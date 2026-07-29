<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'User') - {{ config('app.name', 'Inventory Laravel') }}</title>
    <style>
        :root {
            --bg: #f8fafc;
            --panel: #ffffff;
            --line: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --accent: #0f766e;
            --accent-2: #2563eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, .08), transparent 28%),
                radial-gradient(circle at bottom left, rgba(15, 118, 110, .08), transparent 24%),
                var(--bg);
            color: var(--text);
        }
        a { color: inherit; text-decoration: none; }
        .page {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr;
        }
        header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(16px);
            background: rgba(255,255,255,.82);
            border-bottom: 1px solid rgba(226, 232, 240, .9);
        }
        .header-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
        }
        .brand { font-weight: 900; font-size: 20px; letter-spacing: .2px; }
        nav { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        nav a {
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 700;
            color: #334155;
        }
        nav a.active, nav a:hover { background: #e2e8f0; }
        .logout {
            border: 0;
            background: #0f172a;
            color: #fff;
            border-radius: 12px;
            padding: 10px 14px;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
        }
        main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 24px 40px;
            width: 100%;
        }
        .hero {
            background: linear-gradient(135deg, #0f766e, #2563eb);
            color: #fff;
            border-radius: 24px;
            padding: 26px;
            margin-bottom: 20px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .12);
        }
        .hero h1 { margin: 0 0 8px; font-size: clamp(28px, 4vw, 44px); }
        .hero p { margin: 0; color: rgba(255,255,255,.88); max-width: 720px; }
        .grid { display: grid; gap: 16px; }
        .cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .panel {
            background: rgba(255,255,255,.92);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 32px rgba(15, 23, 42, .05);
        }
        .muted { color: var(--muted); }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px; }
        .product-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
            display: grid;
        }
        .product-media { aspect-ratio: 4 / 3; background: #e2e8f0; }
        .product-body { padding: 16px; display: grid; gap: 10px; }
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 800;
        }
        .badge.ok { background: #dcfce7; color: #166534; }
        .badge.danger { background: #fee2e2; color: #b91c1c; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 12px;
            padding: 0 14px;
            font: inherit;
            font-weight: 800;
            border: 0;
            cursor: pointer;
        }
        .btn.primary { background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: #fff; }
        .btn.ghost { background: #e2e8f0; color: #0f172a; }
        .topbar { display:flex; justify-content:space-between; gap:16px; align-items:flex-end; margin-bottom:20px; }
        .topbar h1 { margin: 0; font-size: 30px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
        th { text-transform: uppercase; font-size: 12px; color: #64748b; }
        input, select, textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 11px 12px;
            font: inherit;
            background: #fff;
            color: var(--text);
        }
        textarea { min-height: 100px; resize: vertical; }
        .alerts { margin-bottom: 16px; }
        .alert {
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 12px;
            border: 1px solid #dbeafe;
            background: #eff6ff;
            color: #1d4ed8;
        }
        .error {
            border-color: #fecaca;
            background: #fff1f2;
            color: #b91c1c;
        }
        .pagination { margin-top: 16px; }
        @media (max-width: 900px) {
            .header-inner { flex-direction: column; align-items: flex-start; }
            .cols-2, .cols-3 { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
            nav { width: 100%; }
        }
    </style>
</head>
<body>
<div class="page">
    <header>
        <div class="header-inner">
            <div>
                <div class="brand">Inventory Market</div>
                <div class="muted">Belanja barang dan pantau pesanan dari satu tempat.</div>
            </div>
            <nav>
                <a href="{{ route('user.dashboard') }}" @class(['active' => request()->routeIs('user.dashboard')])>Home</a>
                <a href="{{ route('shop.products') }}" @class(['active' => request()->routeIs('shop.products')])>Produk</a>
                <a href="{{ route('shop.cart') }}" @class(['active' => request()->routeIs('shop.cart*')])>Keranjang</a>
                <a href="{{ route('user.chat.index') }}" @class(['active' => request()->routeIs('user.chat.*')])>Chat</a>
                <a href="{{ route('user.notifications.index') }}" @class(['active' => request()->routeIs('user.notifications.*')])>Notifikasi</a>
                <form method="post" action="{{ route('user.logout') }}">
                    @csrf
                    <button class="logout" type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <main>
        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                <strong>Periksa kembali input:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
