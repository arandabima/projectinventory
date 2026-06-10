<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Inventory Laravel') }}</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #17202a;
            --muted: #667085;
            --line: #d8dee8;
            --panel: #ffffff;
            --surface: #f4f7fb;
            --brand: #166534;
            --brand-strong: #14532d;
            --accent: #0f766e;
            --danger: #b42318;
            --warning: #b54708;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--surface);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.5;
        }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 248px 1fr; }
        .sidebar {
            background: #10201a;
            color: #f8fafc;
            padding: 24px 18px;
        }
        .brand { font-size: 19px; font-weight: 800; letter-spacing: 0; margin-bottom: 6px; }
        .service { color: #b7c7bf; font-size: 13px; margin-bottom: 26px; }
        .nav { display: grid; gap: 8px; }
        .nav a {
            border-radius: 8px;
            color: #d9e6df;
            padding: 11px 12px;
            font-weight: 700;
        }
        .nav a.active, .nav a:hover { background: #1f3c31; color: #ffffff; }
        main { padding: 28px; min-width: 0; }
        .topbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }
        h1 { margin: 0; font-size: 28px; letter-spacing: 0; }
        h2 { margin: 0 0 14px; font-size: 18px; letter-spacing: 0; }
        h3 { margin: 0 0 8px; font-size: 15px; letter-spacing: 0; }
        .muted { color: var(--muted); font-size: 14px; }
        .grid { display: grid; gap: 16px; }
        .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
        }
        .stat { display: grid; gap: 4px; }
        .stat strong { font-size: 28px; line-height: 1.1; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; }
        .logout-form { margin-top: 24px; }
        .logout-button {
            background: #e2e8f0;
            color: #1e293b;
            width: 100%;
        }
        .logout-button:hover { background: #cbd5e1; }
        form { margin: 0; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 11px;
            background: #ffffff;
            color: var(--ink);
            font: inherit;
        }
        textarea { min-height: 92px; resize: vertical; }
        button, .button {
            border: 0;
            border-radius: 8px;
            background: var(--brand);
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 14px;
            font-weight: 800;
            font: inherit;
        }
        button:hover, .button:hover { background: var(--brand-strong); }
        .button.secondary { background: #334155; }
        .button.ghost { background: #e2e8f0; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { border-bottom: 1px solid var(--line); padding: 11px 8px; text-align: left; vertical-align: top; }
        th { color: #475467; font-size: 12px; text-transform: uppercase; }
        .badge {
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            padding: 3px 8px;
            background: #e2e8f0;
            color: #334155;
            white-space: nowrap;
        }
        .badge.ok { background: #dcfce7; color: #166534; }
        .badge.warn { background: #ffedd5; color: var(--warning); }
        .badge.danger { background: #fee2e2; color: var(--danger); }
        .alert {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            border-radius: 8px;
            color: #166534;
            margin-bottom: 16px;
            padding: 12px 14px;
            font-weight: 700;
        }
        .errors {
            border: 1px solid #fecaca;
            background: #fff1f2;
            border-radius: 8px;
            color: #991b1b;
            margin-bottom: 16px;
            padding: 12px 14px;
        }
        .stack { display: grid; gap: 16px; }
        .pagination { margin-top: 14px; }
        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            main { padding: 18px; }
            .grid.cols-2, .grid.cols-3, .form-grid { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Inventory Laravel</div>
            <div class="service">Service: {{ env('INVENTORY_SERVICE', 'gateway') }}</div>
            <nav class="nav" aria-label="Navigasi utama">
                <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard</a>
                <a href="{{ route('categories.index') }}" @class(['active' => request()->routeIs('categories.*')])>Kategori</a>
                <a href="{{ route('items.index') }}" @class(['active' => request()->routeIs('items.*') || request()->routeIs('movements.*')])>Pencatatan</a>
                <a href="{{ route('orders.index') }}" @class(['active' => request()->routeIs('orders.*')])>Order</a>
                <a href="{{ route('payments.index') }}" @class(['active' => request()->routeIs('payments.*')])>Pembayaran</a>
                <a href="{{ route('reports.index') }}" @class(['active' => request()->routeIs('reports.*')])>Cetak Laporan</a>
                <a href="{{ route('notifications.index') }}" @class(['active' => request()->routeIs('notifications.*')])>Notif & Komunikasi</a>
            </nav>
            <form method="post" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <div class="service">Login: {{ auth()->user()?->username ?? auth()->user()?->name }} · {{ strtoupper(auth()->user()?->role ?? 'user') }}</div>
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </aside>
        <main>
            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">
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
